<?php
/**
 *
 */
namespace aae\util {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\util
	 */
	class Bootstrapper {
		protected $_mysqlUser, $_mysqlPassword, $_mysqlHost, $_database, $_filesRun = [], $_messageLog = array();


		public function __construct($configDir, $password = false) {
			$this->_dbRunner = new \aae\util\DbCLRunner($configDir, $password);
		}

        public function setModelDir($modelDir) {
            \aae\autoload\AutoLoader::addDir($modelDir);
        }

		public function getMessageLog() {
			return implode("\n", $this->_messageLog)."\n".implode("\n", $this->_dbRunner->execLog)."\n";
		}

		protected function _getDataDir($className) {
			$reflected = new \ReflectionClass($className);
            $dataDir = dirname($reflected->getFilename())
            	.DIRECTORY_SEPARATOR
            	.\aae\std\std::classFromNSClassName($className)
            	."Data";
            return $dataDir;
		}

		public function runSqlFile($dirName) {
			$this->_dbRunner->runSqlFile($dirName);
		}

		public function intallMySQLComponentFunctions($className) {
			$dataDir      = $this->_getDataDir($className);
			$setupSqlDir  = $dataDir.DIRECTORY_SEPARATOR."functions.sql";
			$setupJsonDir = $dataDir.DIRECTORY_SEPARATOR."setup.json";

			if (file_exists($setupSqlDir)) $this->_dbRunner->runSqlFile($setupSqlDir);
			return true;
		}
        public function intallMySQLComponentTriggers($className) {
            $dataDir      = $this->_getDataDir($className);
            $setupSqlDir  = $dataDir.DIRECTORY_SEPARATOR."triggers.sql";
            $setupJsonDir = $dataDir.DIRECTORY_SEPARATOR."setup.json";

            if (file_exists($setupSqlDir)) $this->_dbRunner->runSqlFile($setupSqlDir);
            return true;
        }
        public function intallMySQLComponentSetup($className) {
            $dataDir      = $this->_getDataDir($className);
            $setupSqlDir  = $dataDir.DIRECTORY_SEPARATOR."setup.sql";
            $setupJsonDir = $dataDir.DIRECTORY_SEPARATOR."setup.json";

            if (file_exists($setupSqlDir)) $this->_dbRunner->runSqlFile($setupSqlDir);
            return true;
        }
        public function intallMySQLComponentTables($className) {
            $dataDir      = $this->_getDataDir($className);
            $tablesSqlDir = $dataDir.DIRECTORY_SEPARATOR."tables.sql";
            $setupJsonDir = $dataDir.DIRECTORY_SEPARATOR."setup.json";

            if (file_exists($tablesSqlDir)) $this->_dbRunner->runSqlFile($tablesSqlDir);
            return true;
        }

		public function unIntallMySQLComponent($className) {
			$dataDir      = $this->_getDataDir($className);
			$setupSqlDir  = $dataDir.DIRECTORY_SEPARATOR."remove.sql";
			$setupJsonDir = $dataDir.DIRECTORY_SEPARATOR."setup.json";

			$this->_dbRunner->runSqlFile($setupSqlDir);
			return true;
			/*if (file_exists($setupSqlDir) &&
            	!array_key_exists($setupSqlDir, $this->_filesRun)
            ) {
            	$this->_dbRunner->runSqlFile($setupSqlDir);
            	$this->_filesRun[$setupSqlDir] = true;
            	$this->_messageLog[] = "Run file $setupSqlDir";
        	}
        	if (file_exists($setupJsonDir)) {
				$json  = file_get_contents($setupJsonDir);
				$assoc = json_decode($json, true);
				foreach ($assoc as $childClassName) {
					$this->unIntallMySQLComponent($childClassName);
				}
            	return true;
			}

        	return false;*/
		}
	}
}