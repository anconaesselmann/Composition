<?php
/**
 *
 */
namespace aae\util {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\util
	 */
    const HOSTS_FILE_DIR    = "/private/etc/hosts";
    const VIRTUAL_HOSTS_DIR = "/private/etc/apache2/extra/httpd-vhosts.conf";
    const SERVER_EMAIL      = "axel@anconaesselmann.com";
    const HARD_LINK_SOURCE  = "/Dropbox/WebServer/protected";

	class InitialSiteSetup {
        private static $_filesNames = [
            'protected/{:siteName:}/app/bootstrap/bootstrap.php'           => 'bootstrapPhp',
            'protected/{:siteName:}/app/bootstrap/config.json'             => 'bootstrapConfigJson',
            'protected/{:siteName:}/app/bootstrap/dev/.htaccess'           => 'devHtaccess',
            'protected/{:siteName:}/app/bootstrap/dev/config.json'         => 'devConfigJson',
            'protected/{:siteName:}/app/bootstrap/dev/rootDbConfig.cnf'    => 'devRootDbConfigCnf',
            'protected/{:siteName:}/app/bootstrap/dev/dbSetup.sql'         => 'setupDbSetupSql',
            'protected/{:siteName:}/app/bootstrap/web/dbSetup.sql'         => 'setupDbSetupSqlWeb',
            'protected/{:siteName:}/app/bootstrap/setup/postSetupInitialization.sql' => 'postSetupInitializationSql',
            'protected/{:siteName:}/app/bootstrap/setup/setup.json'        => 'setupSetupJson',
            'protected/{:siteName:}/app/bootstrap/web/.htaccess'           => 'webHtaccess',
            'protected/{:siteName:}/app/bootstrap/web/config.json'         => 'webConfigJson',
            'protected/{:siteName:}/app/bootstrap/web/rootDbConfig.cnf'    => 'webRootDbConfigCnf',
            'protected/{:siteName:}/ui/I18n/eng/default.json'              => 'engDefaultJson',
            'protected/{:siteName:}/ui/I18n/eng/login.json'                => 'EnLoginJson',
            'protected/{:siteName:}/ui/I18n/eng/signup.json'               => 'EnSignupJson',
            'protected/{:siteName:}/ui/I18n/eng/user.json'                 => 'EnUserJson',
            'protected/{:siteName:}/ui/I18n/fr/default.json'               => 'frDefaultJson',
            'protected/{:siteName:}/ui/I18n/ger/default.json'              => 'gerDefaultJson',
            'protected/{:siteName:}/ui/I18n/spa/default.json'              => 'spaDefaultJson',
            'protected/{:siteName:}/ui/jsControllers/app/helloWorld.js'    => 'helloWorldJs',
            'protected/{:siteName:}/ui/templates/css/app/main.css'         => 'mainCss',
            'protected/{:siteName:}/ui/templates/css/app/top.css'          => 'topCss',
            'protected/{:siteName:}/ui/templates/css/app/bottom.css'       => 'bottomCss',
            'protected/{:siteName:}/ui/templates/css/app/helloWorld.css'   => 'helloWorldCss',
            'protected/{:siteName:}/ui/templates/html/DefaultView.html'    => 'DefaultViewHtml',
            'protected/{:siteName:}/ui/templates/html/error.html'          => 'errorHtml',
            'protected/{:siteName:}/ui/templates/html/top.html'            => 'topHtml',
            'protected/{:siteName:}/ui/templates/html/bottom.html'         => 'bottomHtml',
            'protected/{:siteName:}/ui/templates/html/HelloWorldView.html' => 'HelloWorldViewHtml',
            'protected/{:siteName:}/ui/templates/html-fragment.config'     => 'htmlFragmentConfig',
            'protected/{:siteName:}/ui/templates/html.config'              => 'htmlConfig',
            'protected/{:siteName:}/ui/templates/json/DefaultView.json'    => 'DefaultViewJson',
            'protected/{:siteName:}/ui/templates/json.config'              => 'jsonConfig',
            'protected/{:siteName:}/ui/viewControllers/ViewController.php' => 'ViewControllerPhp',
            'logs/access_log'                                              => 'access_log',
            'logs/error_log'                                               => 'error_log',
            'public/{:siteName:}/index.php'                                => 'indexPhp',
            'public/{:siteName:}/.htaccess'                                => 'siteHtaccess',
            'public/api.{:siteName:}/.htaccess'                            => 'apiHtaccess',
            'public/api.{:siteName:}/index.php'                            => 'apiIndexPhp',
            'public/.gitignore'                                            => 'publicGitIgnore',
            'protected/.gitignore'                                         => 'protectedGitIgnore',
            'protected/{:siteName:}/ui/viewControllers/LoginViewController.php'  => 'LoginViewControllerPhp',
            'protected/{:siteName:}/ui/viewControllers/SignupViewController.php' => 'SignupViewControllerPhp',
            'protected/{:siteName:}/ui/templates/html/login.html'          => 'loginHtml',
            'protected/{:siteName:}/ui/templates/html/signup.html'         => 'signupHtml',
            'bdb'                                                          => 'bdb',
            'protected/{:siteName:}/ui/templateControllers/DefaultErrorTemplateController.php' => 'DefaultErrorTemplateControllerPhp',
            'protected/{:siteName:}/ui/templateControllers/LoginTemplateController.php'        => 'LoginTemplateControllerPhp',
            'protected/{:siteName:}/ui/templateControllers/Secure_imageTemplateController.php' => 'Secure_imageTemplateControllerPhp',
            'protected/{:siteName:}/ui/templateControllers/SignupTemplateController.php'       => 'SignupTemplateControllerPhp',
            'protected/{:siteName:}/ui/templateControllers/UserTemplateController.php'         => 'UserTemplateControllerPhp'
        ];

        private static $_symbolicLinks = [
            'protected/{:siteName:}/app/bootstrap/dev/config.json' => 'protected/{:siteName:}/app/config.json',
            'protected/{:siteName:}/app/bootstrap/dev/.htaccess'   => 'public/.htaccess',
        ];

        private static $_dirs = [
            'protected',
            'protected/sqlDump',
            'protected/{:siteName:}',
            'protected/{:siteName:}/app',
            'protected/{:siteName:}/app/bootstrap',
            'protected/{:siteName:}/app/bootstrap/dev',
            'protected/{:siteName:}/app/bootstrap/setup',
            'protected/{:siteName:}/app/bootstrap/web',
            'protected/{:siteName:}/app/models',
            'protected/{:siteName:}/app/models/aae',
            'protected/{:siteName:}/app/models/aae/prototype',
            'protected/{:siteName:}/app/models/aae/model',
            'protected/{:siteName:}/app/modelsTest',
            'protected/{:siteName:}/app/modelsTest/aae',
            'protected/{:siteName:}/app/modelsTest/aae/prototype',
            'protected/{:siteName:}/app/modelsTest/aae/model',
            'protected/{:siteName:}/resources',
            'protected/{:siteName:}/resources/secureImages',
            'protected/{:siteName:}/ui',
            'protected/{:siteName:}/ui/apiControllers',
            'protected/{:siteName:}/ui/apiControllersTest',
            'protected/{:siteName:}/ui/I18n',
            'protected/{:siteName:}/ui/I18n/eng',
            'protected/{:siteName:}/ui/I18n/fr',
            'protected/{:siteName:}/ui/I18n/ger',
            'protected/{:siteName:}/ui/I18n/spa',
            'protected/{:siteName:}/ui/jsControllers',
            'protected/{:siteName:}/ui/jsControllers/app',
            'protected/{:siteName:}/ui/jsControllers/appTest',
            'protected/{:siteName:}/ui/templateControllers',
            'protected/{:siteName:}/ui/templateControllersTest',
            'protected/{:siteName:}/ui/templates',
            'protected/{:siteName:}/ui/templates/html',
            'protected/{:siteName:}/ui/templates/json',
            'protected/{:siteName:}/ui/templates/css',
            'protected/{:siteName:}/ui/templates/css/app',
            'protected/{:siteName:}/ui/viewControllers',
            'protected/{:siteName:}/ui/viewControllersTest',
            'protected/src', // populate manually or simlink
            'protected/srcCss', // populate manually or simlink
            'protected/srcJs', // populate manually or simlink
            'protected/srcJsTest',
            'protected/srcTest',
            'public',
            'public/{:siteName:}',
            'public/{:siteName:}/_meta',
            'public/{:siteName:}/content',
            'public/{:siteName:}/css',
            'public/{:siteName:}/css/dynamic',
            'public/{:siteName:}/js',
            'public/{:siteName:}/js/dynamic',
            'public/{:siteName:}/svg',
            'public/api.{:siteName:}',
            'logs'

        ];

        private $_dir;
        private $_siteName;
        private $ssh;
        public $localDbPassword;
        public $scriptPassword = '0y0wd4805hqj3';
        public $remoteDbPassword;
        public $userPassword;
		public function __construct($dir, $siteName) {
            $this->_dir = $dir;
            $this->_siteName = $siteName;
            if (!file_exists($this->_dir)) {
                echo "The directory '".$this->_dir." does not exist. Would you like to create it? (y/n)\n\n\tNOT SUPPORTED YET. PLESE ENTER AN EXISTING DIRECTORY!!!!\n\n";
                die();
            }
        }

        public function createFolders() {
            $dbHost = 'localhost';
            $dbUser = $this->_siteName.'User';
            if (strlen($dbUser) > 16) {
                $dbUser = substr($dbUser, 0, 16);
            }
            $webdbHost = 'aae.db';
            $webDbUser = $dbUser;
            $webDbPassword = $this->scriptPassword;

            foreach (self::$_dirs as $dir) {
                $dir = $this->_dir.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;
                $dir = str_replace('{:siteName:}', $this->_siteName, $dir);
                $created = false;
                if (!file_exists($dir)) $created = mkdir($dir);
                if (!$created) echo "---- could not create directory ".$dir."\n";
                else echo "+ created directory ".$dir."\n";
            }

            foreach (self::$_filesNames as $fileName => $templateFileName) {
                $fileName         = $this->_dir.DIRECTORY_SEPARATOR.$fileName;
                $fileName         = str_replace('{:siteName:}', $this->_siteName, $fileName);
                $templateFileName = dirname(__FILE__).DIRECTORY_SEPARATOR.'InitialSiteSetupData'.DIRECTORY_SEPARATOR.$templateFileName.'.txt';

                $content = file_get_contents($templateFileName);

                $content = str_replace('{:siteName:}', $this->_siteName, $content);
                $content = str_replace('{:dbHost:}', $dbHost, $content);
                $content = str_replace('{:dbUser:}', $dbUser, $content);
                $content = str_replace('{:dbPassword:}', $this->scriptPassword, $content);
                $content = str_replace('{:webdbHost:}', $webdbHost, $content);
                $content = str_replace('{:webDbUser:}', $webDbUser, $content);
                $content = str_replace('{:webDbPassword:}', $webDbPassword, $content);

                if (!file_exists($fileName)) {
                    file_put_contents($fileName, $content);
                    echo "+ created file ".$fileName."\n";
                } else echo "---- file ".$fileName." already exists.\n";
            }
            foreach (self::$_symbolicLinks as $target => $link) {
                $target = $this->_dir.DIRECTORY_SEPARATOR.str_replace('{:siteName:}', $this->_siteName, $target);
                $link   = $this->_dir.DIRECTORY_SEPARATOR.str_replace('{:siteName:}', $this->_siteName, $link);
                $result = false;
                if (!file_exists($link)) {
                    $link   = str_replace('{:siteName:}', $this->_siteName, $link);
                    $result = symlink($target, $link);
                }
                if ($result) echo "+ created symlink from $target to $link\n";
                else echo "---- could not create symlink from $target to $link\n";
            }
            return;
        }
        private function sudoFileAppend($fileName, $content) {
            $command = "{$this->userPassword} | echo '$content' | sudo tee -a $fileName";
            system("echo $command");
        }
        public function appendHostsFile() {
            echo "\nApending hosts file:\n";
            $content = "\n\n127.0.0.1 {$this->_siteName}.dev\n127.0.0.1 www.{$this->_siteName}.dev\n";
            $this->sudoFileAppend(HOSTS_FILE_DIR, $content);
            echo "flushing dns cache\n";
            system("dscacheutil -flushcache");
        }
        public function appendVirtualHosts() {
            echo "\nApending virtual hosts file '".VIRTUAL_HOSTS_DIR."':\n";
            $templateFileName = dirname(__FILE__).DIRECTORY_SEPARATOR.'InitialSiteSetupData'.DIRECTORY_SEPARATOR.'virtualHost.txt';
            $hostsString      = sprintf(file_get_contents($templateFileName), $this->_dir, $this->_siteName, SERVER_EMAIL);
            $this->sudoFileAppend(VIRTUAL_HOSTS_DIR, $hostsString);
        }
        public function restartApache() {
            echo "\nRestarting apache\n\n";
            $command = "{$this->userPassword} | sudo apachectl restart";
            system("echo $command");
        }
        public function creatingHardLinks() {
            echo "\nCreating hard links\n\n";
            $hardLinkDirs = [
                'src',
                'srcCss',
                'srcJs'
            ];
            foreach ($hardLinkDirs as $dir) {
                $source = HARD_LINK_SOURCE.DIRECTORY_SEPARATOR.$dir;
                $target = $this->_dir.DIRECTORY_SEPARATOR."protected".DIRECTORY_SEPARATOR.$dir;
                $command = "hln '$source' '$target'";
                rmdir($target);
                system($command);
                echo $command."\n";
            }
        }
        public function localGitInit() {
            $command = "cd '".$this->_dir.DIRECTORY_SEPARATOR."%s'\ngit init\ngit add -A\ngit commit -m 'Initial commit after setup.'";
            system(sprintf($command, "protected"));
            system(sprintf($command, "public"));

            $dynamicResourceFolders = ["js", "css"];
            foreach ($dynamicResourceFolders as $type) {
                $command = "mkdir -p {$this->_dir}/public/%1\$s/dynamic;";
                system(sprintf($command, $type));
            }

            $command = "{$this->userPassword} | sudo -u root -S chmod -R 777 {$this->_dir}/public";
            system("echo $command");
            $command = "{$this->userPassword} | sudo -u root -S chmod +x {$this->_dir}/bdb";
            system("echo $command");
        }
        public function remoteGitInit($sshUser, $sshHost) {
            $this->ssh = new SSHConnection($sshUser, $sshHost);
            $this->ssh->connect();
            $command = "cd /home/%1\$s;
            mkdir .git;
            cd .git;
            git init --bare;
            cd hooks;
            echo 'GIT_WORK_TREE=/home/%1\$s git checkout -f' >> post-receive;
            chmod +x post-receive";

            $this->ssh->exec(sprintf($command, "protected"));
            $this->ssh->exec(sprintf($command, "public"));

            $command = "cd ".$this->_dir.DIRECTORY_SEPARATOR."%1\$s;git remote add web ssh://$sshUser@$sshHost/home/%1\$s/.git";
            system(sprintf($command, "protected"));
            system(sprintf($command, "public"));

            $this->pushToRemote();

            $dynamicResourceFolders = ["js", "css"];
            foreach ($dynamicResourceFolders as $type) {
                $command = "mkdir -p /home/public/%1\$s/dynamic;";
                $this->ssh->exec(sprintf($command, $type));
            }
            $command = "chmod -R 777 /home/public;";
            $this->ssh->exec($command);
        }
        public function localDbBuild() {
            $command = "php ".$this->_dir.DIRECTORY_SEPARATOR."protected/".$this->_siteName."/app/bootstrap/bootstrap.php dev -s-p {$this->localDbPassword}";
            system($command);
        }
        public function remoteDbBuild($sshUser, $sshHost, $remoteDbPassword) {
            if (is_null($this->ssh)) {
                $this->ssh = new SSHConnection($sshUser, $sshHost);
                $this->ssh->connect();
            }
            $command = "php /home/protected/".$this->_siteName."/app/bootstrap/bootstrap.php web -s-p {$this->remoteDbPassword}";
            $this->ssh->exec($command, "protected");
        }
        public function pushToRemote() {
            echo "\nPushing to remote:\n";
            $command = "cd ".$this->_dir.DIRECTORY_SEPARATOR."%1\$s;git push web master";
            system(sprintf($command, "protected"));
            system(sprintf($command, "public"));
        }
        public function getPassword($prompt) {
            $command  = "/usr/bin/env bash -c 'read -s -p \"".addslashes($prompt.":\nEnter password: ")."\" mypassword && echo \$mypassword'";
            $password = rtrim(shell_exec($command));
            echo "\n";
            if (strlen($password) < 1) {
                echo "No password provided";
                die();
            }
            return $password;
        }
        public function createVirtualHost() {
            $this->appendHostsFile();
            $this->appendVirtualHosts();
            $this->restartApache();
        }
    }
    class SSHConnection {
        public function __construct($sshUser, $sshHost) {
            $this->arskeyName    = "id_rsa";
            $this->ssh_auth_user = $sshUser;
            $this->ssh_host      = $sshHost;
            $this->ssh_auth_priv = '~/.ssh/'.$this->arskeyName;
            $this->ssh_auth_pub  = $this->ssh_auth_priv.'.pub';
        }
        // SSH Host
        private $ssh_host;
        // SSH Port
        private $ssh_port = 22;
        // SSH Server Fingerprint
        // private $ssh_server_fp = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        // SSH Username
        private $ssh_auth_user;
        // SSH Public Key File
        private $ssh_auth_pub;
        // SSH Private Key File
        private $ssh_auth_priv;
        // SSH Private Key Passphrase (null == no passphrase)
        private $ssh_auth_pass;
        // SSH Connection
        private $connection;

        public function connect() {
            if (!($this->connection = \ssh2_connect($this->ssh_host, $this->ssh_port))) {
                throw new Exception('Cannot connect to server');
            }
            $fingerprint = \ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
            // if (strcmp($this->ssh_server_fp, $fingerprint) !== 0) {
            //     throw new Exception('Unable to verify server identity!');
            // }
            if (!\ssh2_auth_pubkey_file($this->connection, $this->ssh_auth_user, $this->ssh_auth_pub, $this->ssh_auth_priv, $this->ssh_auth_pass)) {
                throw new Exception('Autentication rejected by server');
            }
        }
        public function exec($cmd) {
            if (!($stream = \ssh2_exec($this->connection, $cmd))) {
                throw new Exception('SSH command failed');
            }
            stream_set_blocking($stream, true);
            $data = "";
            while ($buf = fread($stream, 4096)) {
                $data .= $buf;
            }
            fclose($stream);
            return $data;
        }
        public function disconnect() {
            $this->exec('echo "EXITING" && exit;');
            $this->connection = null;
        }
        public function __destruct() {
            $this->disconnect();
        }
    }
    class Flags {
        public $flags           = [];
        public $recognizedFlags = [];
        public $argValsUsed     = false;

        public function argValSet($flag) {
            $this->argValsUsed = true;
            if (isset($this->recognizedFlags[$flag])) {
                $this->flags[$this->recognizedFlags[$flag]] = true;
            } else {
                throw new \Exception("Flag '$flag' is not recognized", 229162306);
            }
        }
        public function userValSet($flag, $value) {
            $boolVal = ($value == "y") ? true : false;
            $this->flags[$flag] = $boolVal;
        }
        public function is($flag) {
            if (!isset($this->flags[$flag])) return false;
            return $this->flags[$flag];
        }
        public function setUserValWithPropt($flag, $prompt) {
            if (!$this->argValsUsed) {
                $this->userValSet($flag, readline("$prompt:\n"));
            }
        }
        public function registerFlag($short, $long) {
            $this->recognizedFlags[$short] = $long;
        }
        public function __toString() {
            $out = "";
            foreach ($this->recognizedFlags as $short => $long) {
                $value = isset($this->flags[$long]) ? $this->flags[$long] : 0;
                $out .= $long.": ".(int)$value."\n";
            }
            return $out;
        }
    }
    if (!is_null($argv)) {
        $seperatorLine = "##############################################################################\n";
        system('clear');
        echo "$seperatorLine\n";
        echo "\t\t\tServer instance setup\n\n";
        echo "$seperatorLine\n\n";

        $flags = new Flags();
        $flags->registerFlag('d', "createDirectoryStructure");
        $flags->registerFlag('h', "createHardLinks");
        $flags->registerFlag('v', "createVirtualHost");
        $flags->registerFlag('r', "remoteGitInit");
        $flags->registerFlag('b', "initializeLocalDb");
        $flags->registerFlag('w', "initializeRemoteDb");
        $flags->registerFlag('s', "localAndRemotePasswordsAreTheSame");
        $flags->registerFlag('p', "pushToRemote");

        if (isset($argv[1]) && $argv[1][0] == "-") {
            for ($i=1; $i < strlen($argv[1]); $i++) {
                $flags->argValSet($argv[1][$i]);
            }
            unset($argv[1]);
            if (isset($argv[2])) {
                $argv[1] = $argv[2];
                if (isset($argv[3])) {
                    $argv[2] = $argv[3];
                    unset($argv[3]);
                }
            } unset($argv[2]);
        }

        if (isset($argv[1])) {
            $siteName = $argv[1];
        } else {
            $prompt   = "Please enter a site identifier.\n\t(This is the domain name without the domain extensions)";
            $siteName = readline("$prompt:\n");
        }

        echo "Using '$siteName' as site identifier.\n\n";

        if (
            ($flags->argValsUsed) &&
            (!isset($argv[2]))    &&
            (!$flags->is("createDirectoryStructure"))
        ) {
            $dir = getcwd();
        } elseif (isset($argv[2])) {
            if (strlen($argv[2]) < 4) die("Are you sure you provided a directory?");
            $dir = $argv[2];
        } else {
            echo "Server directory setup:\n\n";
            $prompt = "Please enter a new local development source directory (absolute path)";
            $dir    = readline("$prompt:\n");
            $flags->userValSet("createDirectoryStructure", "y");
        }
        echo "Using '$dir' as local source directory.\n\n";

        $sshUser    = "aae_$siteName";
        $sshHost    = "ssh.phx.nearlyfreespeech.net";

        $initalizer = new InitialSiteSetup($dir, $siteName);

        if ($flags->is("createDirectoryStructure") || $flags->is("createVirtualHost")) {
            $initalizer->userPassword = $initalizer->getPassword("Please enter the root passoword");
        }
        $prompt   = "Would you like to create a virtual host for '$siteName'? (y/n)";
        $flags->setUserValWithPropt("createVirtualHost", $prompt);
        $prompt   = "Would you like to initialize the remote server with git? (y/n)";
        $flags->setUserValWithPropt("remoteGitInit", $prompt);
        $prompt   = "Would you like to set up the local DB? (y/n)";
        $flags->setUserValWithPropt("initializeLocalDb", $prompt);
        if ($flags->is("initializeLocalDb")) {
            $prompt          = "Please enter the local database password";
            $localDbPassword = $initalizer->getPassword($prompt);
            $initalizer->localDbPassword = $localDbPassword;
            if ($flags->is("createDirectoryStructure")) {
                $initalizer->scriptPassword = $initalizer->getPassword("Please create a script password for the database");
            }
            $prompt = "Would you like to set up the local DB? (y/n)";
            $flags->setUserValWithPropt("initializeRemoteDb", $prompt);
            if ($flags->is("initializeRemoteDb")) {
                if ($flags->is("localAndRemotePasswordsAreTheSame")) {
                    $remoteDbPassword = $localDbPassword;
                } else {
                    $prompt           = "Please enter the remote database password";
                    $remoteDbPassword = $initalizer->getPassword($prompt);
                }
                $initalizer->remoteDbPassword = $remoteDbPassword;
            }
        }
        if ($flags->is("createDirectoryStructure")) {
            $initalizer->createFolders();

            $prompt = "\n\nWould you like to hard-link the default code base directories located at '".HARD_LINK_SOURCE."'? (y/n)";
            $flags->setUserValWithPropt("createHardLinks", $prompt);

            if ($flags->is("createHardLinks")) {
                $initalizer->creatingHardLinks();
            }
        } else {
            echo "Skipping directory structure setup\n\n";
        }
        echo "\n\n$seperatorLine\n\n";

        if ($flags->is("createVirtualHost"))        $initalizer->createVirtualHost();
        if ($flags->is("createDirectoryStructure")) $initalizer->localGitInit();
        if ($flags->is("remoteGitInit"))            $initalizer->remoteGitInit($sshUser, $sshHost);
        else if ($flags->is("pushToRemote"))        $initalizer->pushToRemote();
        if ($flags->is("initializeLocalDb"))        $initalizer->localDbBuild($localDbPassword);
        if ($flags->is("initializeRemoteDb"))       $initalizer->remoteDbBuild($sshUser, $sshHost, $remoteDbPassword);

        if (
            ($flags->is("remoteGitInit") || $flags->is("pushToRemote")) &&
            $flags->is("initializeLocalDb") &&
            $flags->is("initializeRemoteDb")
        ) {
            while (true) {
                $prompt = "\nWould you like to re-run push to remote and re-initialize the database? (return to continue)";
                $response = readline("$prompt:\n");
                if ($response != "") return;

                $initalizer->pushToRemote();
                $initalizer->localDbBuild($localDbPassword);
                $initalizer->remoteDbBuild($sshUser, $sshHost, $remoteDbPassword);
            }
        }
    }
}