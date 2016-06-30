<?php
/**
 *
 */
namespace aae\unitTesting {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\unitTesting
	 */
	class DbTestCaseException extends \Exception {}
	class DbTestCase extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;
		//use \aae\util\DbCLRunner;

		private $_dbRunner;
		protected $db;



		public function setUp() {
			$testDbConfigDir = dirname(__FILE__).DIRECTORY_SEPARATOR.basename(__FILE__, '.php')."Data".DIRECTORY_SEPARATOR.'config.cnf';
			//var_dump($testDbConfigDir);
			$this->_dbRunner = new \aae\util\DbCLRunner($testDbConfigDir);
			//$this->_dbRunner->showErrors();
			if (file_exists($this->getClassDataPath('setup.json'))) {
				$json  = $this->getClassDataContent('setup.json');
				$assoc = json_decode($json, true);
				foreach ($assoc as $className) {
					$classPath = $this->getClassPath($className);
					$setupFile = dirname($classPath).DIRECTORY_SEPARATOR.basename($classPath, '.php')."Data".DIRECTORY_SEPARATOR."tables.sql";
					if (file_exists($setupFile)) $this->runSqlFile($setupFile);
					$setupFile = dirname($classPath).DIRECTORY_SEPARATOR.basename($classPath, '.php')."Data".DIRECTORY_SEPARATOR."functions.sql";
					if (file_exists($setupFile)) $this->runSqlFile($setupFile);
					$setupFile = dirname($classPath).DIRECTORY_SEPARATOR.basename($classPath, '.php')."Data".DIRECTORY_SEPARATOR."triggers.sql";
					if (file_exists($setupFile)) $this->runSqlFile($setupFile);
					$setupFile = dirname($classPath).DIRECTORY_SEPARATOR.basename($classPath, '.php')."Data".DIRECTORY_SEPARATOR."setup.sql";
					if (file_exists($setupFile)) $this->runSqlFile($setupFile);
				}
			}
			if (file_exists($this->getTestDataPath('tables.sql'))) $this->runTestSqlFile('tables.sql');
			if (file_exists($this->getTestDataPath('functions.sql'))) $this->runTestSqlFile('functions.sql');
			if (file_exists($this->getTestDataPath('triggers.sql'))) $this->runTestSqlFile('triggers.sql');
			if (file_exists($this->getTestDataPath('setup.sql'))) $this->runTestSqlFile('setup.sql');
			if (file_exists($this->getClassDataPath('tables.sql'))) $this->runClassSqlFile('tables.sql');
			if (file_exists($this->getClassDataPath('functions.sql'))) $this->runClassSqlFile('functions.sql');
			if (file_exists($this->getClassDataPath('triggers.sql'))) $this->runClassSqlFile('triggers.sql');
			if (file_exists($this->getClassDataPath('setup.sql'))) $this->runClassSqlFile('setup.sql');
			$this->db = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
		}
		public function getErrorLog() {
			return "ERROR LOG:\n".implode("\n", $this->_dbRunner->execLog);
		}
		public function runClassSqlFile($fileName) {
			$scriptPath = $this->getClassDataPath($fileName);
			$this->runSqlFile($scriptPath);
		}
		public function runTestSqlFile($fileName) {
			$scriptPath = $this->getTestDataPath($fileName);
			$this->runSqlFile($scriptPath);
		}

		public function tearDown() {
			$dbName = $this->_dbRunner->getDbName();
			$conn   = $this->getDb();
			$result = $conn->query("DROP DATABASE IF EXISTS $dbName;");
		}
		public function getDb() {
			return $this->_dbRunner->getDb();
		}

		protected function getUser($email, $loggedIn = true) {
		    $user = $this->getMockBuilder('\aae\app\User')
		        ->disableOriginalConstructor()
		        ->getMock();
		    $user->method('isLoggedIn')
		        ->willReturn($loggedIn);
		    $user->method('getEmail')
		        ->willReturn($email);
		    $result = $this->query("CALL tests.getUserByEmail(\"$email\")");
		    $userId = (int)$result->fetchObject()->user_id;
		    $user->method('getId')
		        ->willReturn($userId);
		    $this->db->addEvalSubstitution(get_class($user), "getId");
		    return $user;
		}
		public function insertTestRows() {
		    $newTableValues = $this->newTableValues;
		    foreach ($newTableValues as $key => $value) {
		        if (is_string($value)) $newTableValues[$key]    = "'$value'";
		        else if (is_null($value)) $newTableValues[$key] = 'NULL';
		    }
		    $keys         = array_keys($newTableValues);
		    $values       = array_values($newTableValues);
		    $inserts      = array_fill(0, count($keys), "%s");
		    $insertString = implode(", ", $inserts);
		    $queryString  = "INSERT INTO tests.".$this->tableName." ($insertString) VALUES($insertString)";
		    $merged       = array_merge([$queryString], $keys, $values);
		    $queryStringComplete = call_user_func_array("sprintf", $merged);

		    $this->query($queryStringComplete);
		}

		public function snakeArrayToCamelArray($assoc) {
		    $camelArray = [];
		    foreach ($assoc as $snakeKey => $value) {
		        $camelKey = \aae\std\std::snakeToCamel($snakeKey);
		        $camelArray[$camelKey] = $value;
		    }
		    return $camelArray;
		}
		public function snakeArrayToCamelArrayLcFirst($assoc) {
		    $camelArray = [];
		    foreach ($assoc as $snakeKey => $value) {
		        $camelKey = lcfirst(\aae\std\std::snakeToCamel($snakeKey));
		        $camelArray[$camelKey] = $value;
		    }
		    return $camelArray;
		}
		public function assertGettersReturn($obj, $assoc) {
		    $camelArray = $this->snakeArrayToCamelArray($assoc);
		    foreach ($camelArray as $name => $value) {
		        $getterName = "get$name";
		        $result = $obj->$getterName();
		        $this->assertEquals($value, $result);
		    }
		}

		/**
		 * Returns an MySQL Time stamp with an offset of $seconds.
		 *
		 * @param  int $seconds Positive values for times in the future, negative values for times in the past
		 * @return string       Time stamp
		 */
		protected function getTimeStamp($seconds = 0) {
			$interval   = new \DateInterval('P0DT'.abs($seconds).'S');
			$time       = new \DateTime();
			if ($seconds < 0) {
				$time->sub($interval);
			} else if ($seconds > 0) {
				$time->add($interval);
			}
			$timeString = $time->format('Y-m-d H:i:s');
			return $timeString;
		}

		public function assertTableHas($table, $values) {
			$condition    = $this->_tableHas($table, $values);
			$dbName       = $this->_dbRunner->getDbName();
			$valuesString = $this->_getValuesString($values);
			$message = "Failed asserting that $table in database $dbName has one or more rows with values: $valuesString";
			self::assertThat($condition, self::isTrue(), $message);
		}
		public function assertTableHasNot($table, $values) {
			$condition    = $this->_tableHas($table, $values);
			$dbName       = $this->_dbRunner->getDbName();
			$valuesString = $this->_getValuesString($values);
			$message = "Failed asserting that $table in database $dbName does not have a row with values: $valuesString";
			self::assertThat($condition, self::isFalse(), $message);
		}
		public function assertUserDefinedVariableEquals($expected, $userDefinedVariable) {
			$dbName  = $this->_dbRunner->getDbName();
			$query   = "SELECT @$userDefinedVariable";
			$result  = ($this->query($query)->fetchColumn());
			$message = "Failed asserting that the user-defined-variable '$userDefinedVariable' in database $dbName has the value '$expected'";
			$this->assertEquals($expected, $result);
		}
		public function getClassPath($className) {
			$reflector = new \ReflectionClass($className);
			return $reflector->getFileName();
		}
		public function showTable($tableName) {
			$out = "\nTable $tableName:\n";
			$query = "SELECT * FROM {$this->_dbRunner->getDbName()}.$tableName";
			try {
				$results = $this->query($query);
			} catch (\Exception $e) {
				throw new DbTestCaseException("showTable encountered a database error with message:\n".$e->getMessage(), 999999999);
			}

			$results->setFetchMode(\PDO::FETCH_NUM);
			$row = $results->fetch();
			while ($row) {
				for ($i=0; $i < count($row); $i++) {
					if (is_null($row[$i])) $row[$i] = "NULL";
				}
				$out .= implode("\t", $row)."\n";
				$row = $results->fetch();
			}
			echo $out;
		}
		public function query($queryString) {
			return $this->_dbRunner->query($queryString);
		}
        public function runSql($sqlStatements) {
            return $this->_dbRunner->runSql($sqlStatements);
        }
        public function runSqlFile($fileName) {
            return $this->_dbRunner->runSqlFile($fileName);
        }

		private function _tableHas($table, $values) {
			$dbName       = $this->_dbRunner->getDbName();
			$valuesString = $this->_getValuesString($values);
			$query    = "SELECT * FROM $dbName.$table WHERE ".$valuesString;
			//echo "\n".$query."\n";
			try {
				return (bool)($this->query($query)->rowCount());
			} catch (\Exception $e) {
				throw new DbTestCaseException("hasTable encountered a database error with message:\n".$e->getMessage(), 999999999);
			}
		}
		private function _getValuesString($values) {
			$colNames  = array_keys($values);
			$colValues = array_values($values);
			$queryCond = array();
			for ($i=0; $i < count($colValues); $i++) {
				if (is_string($colValues[$i]))     $colValue = $this->getDb()->quote($colValues[$i]);
				else if ($colValues[$i] === true)  $colValue = "TRUE";
				else if ($colValues[$i] === false) $colValue = "FALSE";
				else $colValue = $colValues[$i];

				if (is_null($colValue)) {
					$queryCond[] = $colNames[$i]." IS NULL";
				} else {
					$queryCond[] = $colNames[$i]." = ".$colValue;
				}
			}
			return implode(" and ", $queryCond);
		}
	}
}