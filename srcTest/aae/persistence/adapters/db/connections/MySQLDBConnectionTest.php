<?php
// namespace aae\persistence\adapters\db\connections {
// 	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
// 	/**
// 	 * @group database
// 	 */
// 	class MySQLDBConnectionTest extends \aae\unitTesting\DBTestCase {
// 		public function test___construct() {
// 			$obj = new MySQLDBConnection();
// 		}

// 		public function test_getConnection_exception() {
// 			// Setup
// 			$obj = new MySQLDBConnection();
// 			$dbConfig = array('host' => '', 'user' => '', 'password' => '', 'dbName' => '', 'port' => null, 'socket' => null);

// 			// Testing
// 			try {
// 				$result = $obj->getConnection($dbConfig);
// 			} catch (\Exception $e) {
// 				$this->assertEquals(211140819, $e->getCode());
// 				return;
// 			}
// 			$this->fail("An exception should have been thrown, since invalid db connection information was given.");
// 		}

// 		public function test_setLogger_with_logger_intance() {
// 			// Setup
// 			$obj = new MySQLDBConnection();
// 			$loggerInstance = $this->getMock('\aae\log\Loggable');

// 			// Testing
// 			$obj->setLogger($loggerInstance);
// 		}

// 		public function test_setLogger_with_logger_class_name() {
// 			// Setup
// 			$obj = new MySQLDBConnection();
// 			$loggerClassName = '\aae\persistence\adapters\db\connections\LoggerStubb';

// 			// Testing
// 			$obj->setLogger($loggerClassName);
// 		}

// 		public function test_setLogger_exception_with_undefined_logger_class_name() {
// 			// Setup
// 			$obj = new MySQLDBConnection();
// 			$loggerClassName = '\aae\persistence\adapters\db\connections\UndefinedClassName';

// 			// Testing
// 			try {
// 				$obj->setLogger($loggerClassName);
// 			} catch (\Exception $e) {
// 				$this->assertEquals(211140943, $e->getCode());
// 				return;
// 			}
// 			$this->fail("An Exception should have been thrown, since the logger '$loggerClassName' is not defined.");
// 		}

// 		public function test_setLogger_instance_exception() {
// 			// Setup
// 			$obj = new MySQLDBConnection();
// 			$loggerInstance = new \stdClass();

// 			// Testing
// 			try {
// 				$obj->setLogger($loggerInstance);
// 			} catch (\Exception $e) {
// 				$this->assertEquals(211140900, $e->getCode());
// 				return;
// 			}
// 			$this->fail("An Exception should have been thrown, since loggerInstance does not implement '\aae\log\Loggable'");
// 		}

// 		public function test_getConnection_error_logging() {
// 			// Setup
// 			$obj = new MySQLDBConnection();
// 			$dbConfig = array('host' => '', 'user' => '', 'password' => '', 'dbName' => '', 'port' => null, 'socket' => null);
// 			$mockLogger = $this->getMock('\\aae\\log\\Loggable');
// 			$mockLogger->expects($this->atLeastOnce())->method('log')->with($this->isType('string'));

// 			$obj->setLogger($mockLogger);

// 			// Testing
// 			try {
// 				$result = $obj->getConnection($dbConfig);
// 			} catch (\Exception $e) {}
// 		}

// 	}

// 	class LoggerStubb implements \aae\log\Loggable {
// 		public function log($message, $eventType) {}
// 		public function setLogFile($logFileDir) {}
// 	}
// }