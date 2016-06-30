<?php
/**
 *
 */
namespace aae\util {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\util
	 */
	class DbCLRunner {
        public $dbConnection = null, $login = null, $configDir = null, $execLog = array(), $_useFile = true;

        public function __construct($fileDir, $password = false) {
            if (!file_exists($fileDir)) throw new \Exception("config.cnf file with path '$fileDir' does not exist", 1114141642);
            $this->login = $this->getLoginFromConfig($fileDir);
            $this->configDir = $fileDir;
            if ($password) {
                $this->login["password"] = $password;
                $this->_useFile = false;
            }
        }

        protected function _initDbCLRunner($fileDir) {
            if (!file_exists($fileDir)) throw new \Exception("No Login Credentials in test directory", 1015142031);
            $this->login = json_decode(file_get_contents($fileDir), true);
        }
        public function runSql($sqlStatements) {
            $dbName  = $this->getDbName();
            $conn    = $this->getDb();
            $result  = $conn->query("CREATE DATABASE IF NOT EXISTS $dbName;");

            $command = $this->_getMySQLShellCommand("-e'$sqlStatements'");
            $output  = shell_exec($command);
        }

        public function query($queryString) {
            $db     = $this->getDb();
            $dbName = $this->getDbName();
            //echo "\n".$query."\n";
            return $db->query($queryString);
        }

        public function runSqlFile($fileName) {
            $dbName  = $this->getDbName();
            $conn    = $this->getDb();
            $result  = $conn->query("CREATE DATABASE IF NOT EXISTS $dbName;");

            $command = $this->_getMySQLShellCommand("< $fileName");
            $this->execLog[] = $command;
            $output  = shell_exec($command);
            if (strlen($output) > 0) {
                $this->execLog[] = $output;
            }
        }


        public function getDbUser() {
            if (!is_null($this->login)) return $this->login["userName"];
            else throw new \Exception("Login information does not have userName", 1114141020);

        }

        public function getDbName() {
            if (!is_null($this->login) &&
                array_key_exists("dbName", $this->login)
            ) return $this->login["dbName"];
            else throw new \Exception("Login information does not have dbName", 1114141021);

        }
        public function getDbPassword() {
            if (!is_null($this->login)) return $this->login["password"];
            else throw new \Exception("Login information does not have password", 1114141022);
        }
        public function getDbHost() {
            if (array_key_exists("host", $this->login) && !is_null($this->login)) return $this->login["host"];
            else return "127.0.0.1";
        }


        protected function _getMySQLShellCommand($setting) {
            $dbName   = $this->getDbName();
            $userName = $this->getDbUser();
            $password = $this->getDbPassword();
            $host     = $this->getDbHost();
            $errorHandling = " 2>&1";
            if (file_exists("/usr/local/bin/mysql")) {
                $mysql = "/usr/local/bin/mysql";
            } else if (file_exists("/usr/bin/mysql")) {
                $mysql = "/usr/bin/mysql";
            } else {
                $mysql = "mysql";
            }
            if ($this->_useFile) {
                return $mysql." --defaults-extra-file={$this->configDir} $setting".$errorHandling;
            } else {
                return $mysql." -D $dbName $setting -h $host -u $userName --password=\"$password\"$errorHandling";
            }
        }
        public function getDb() {
            if (is_null($this->dbConnection)) {
                date_default_timezone_set('America/Los_Angeles');
                try {
                    $this->dbConnection = new \PDO('mysql:host='.$this->getDbHost(), $this->getDbUser(), $this->getDbPassword());
                    $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } catch (\PDOException $e) {
                    throw new \Exception("Could not connect to database with username: ".$this->getDbUser()." and Password: ".$this->getDbPassword()."\nError message: ".$e->getMessage(), 1);
                }
            }
            return $this->dbConnection;
        }

        public function getLoginFromConfig($dir) {
            $result = null;
            if (file_exists($dir)) {
                $contents = file_get_contents($dir);
                $regex = '/
                    (user=)(?P<userName>.*)(\n)
                    (password=)(?P<password>.*)(\n)
                    (host=)(?P<host>.*)(\n)
                    (database=)(?P<dbName>.*)
                /sx';
                $list  = array();
                $match = preg_match_all($regex, $contents, $list);
                $result = [];
                $result["userName"] = $list["userName"][0];
                $result["password"] = $list["password"][0];
                $result["host"]     = $list["host"][0];
                $result["dbName"]   = $list["dbName"][0];
            }
            if (!is_string($result["userName"]) ||
                strlen($result["userName"]) < 1
            ) throw new \Exception("config.cnf file has incorrect format.", 1114141635);
            return $result;
        }
    }
}