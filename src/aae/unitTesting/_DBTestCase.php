<?php
/**
 *
 */
namespace aae\unitTesting {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\unitTesting
	 */
	class DBTestCase extends \PHPUnit_Framework_TestCase {
		
	/*
	
CREATE DATABASE phpUnit_db;
CREATE USER 'phpUnit'@'localhost' 
    IDENTIFIED BY '0y0&h85';
GRANT CREATE, CREATE TEMPORARY TABLES, DROP, DELETE, SELECT, INSERT, UPDATE 
    ON phpUnit_db.* 
    TO 'phpUnit'@'localhost';
flush privileges;

	 */


		public $dbPointer = null;

		protected function _createFromDBSchema($dbSchemaDir = null, $dbConfigDir = null) {
			$reflectedClass = new \ReflectionObject($this);
			$reflectedClassFileName = $reflectedClass->getFileName();
			if ($dbSchemaDir === null) {
				$dbSchemaDir = dirname($reflectedClassFileName)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data/testSchema.sql";
				if (!file_exists($dbSchemaDir)) {
					throw new \Exception("A valid path name for a db schema has to be provided", 1);
				}
			}
			if ($dbConfigDir === null) {
				$dbConfigDir = dirname(__FILE__)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data/dbConfig.ini";
				if (!file_exists($dbConfigDir)) {
					$dbConfigDir = array(
						'host' => 'localhost',
						'user' => 'phpUnit',
						'password' => '0y0&h85',
						'dbName' => 'phpUnit_db');
				}
			}
			
			$dbConfig = new \aae\persistence\adapters\db\DBConfig($dbConfigDir);
			
			$this->dbPointer = new \MySQLi($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['dbName']);
			$schema = file_get_contents($dbSchemaDir);
			$queries = explode(';', $schema);
			foreach ($queries as $query) {
				$query = trim($query);
				if (strlen($query) > 0) {
					$result = $this->dbPointer->query($query);
				}
			}
		}

		public function setup() {
			$this->_createFromDBSchema();
		}

		public function tearDown() {
			if ($this->dbPointer) {
				$this->dbPointer->close();
			}
		}
	}
}