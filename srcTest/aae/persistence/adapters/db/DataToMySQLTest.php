<?php
// namespace aae\persistence\adapters\db {
// 	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
// 	/**
// 	 * @group database
// 	 */
// 	class DataToMySQLTest extends \aae\unitTesting\DBTestCase {
// 		protected function _getInstance() {
// 			$obj = new DataToMySQL();
// 			$mockDBConnection = $this->getMock('\\aae\\persistence\\adapters\\db\\connections\\MySQlDBConnection');
// 			$mockDBConnection->expects($this->any())->method('getConnection')->will($this->returnValue($this->dbPointer));

// 			$obj->setDbConnection($mockDBConnection);
// 			return $obj;
// 		}

// 		public function test___construct() {
// 			$obj = $this->_getInstance();
// 		}

// 		/**
// 		 * @dataProvider provider_persist_exception_testing
// 		 */
// 		public function test_persist_exception_testing($settings, $errorCode, $errorMessage) {
// 			// Setup
// 			$obj = $this->_getInstance();
// 			$data = new \stdClass();

// 			// Testing
// 			try {
// 				$result = $obj->persist($data, $settings);
// 			} catch (\Exception $e) {
// 				$this->assertEquals($errorCode, $e->getCode());
// 				return;
// 			}
// 			$this->fail($errorMessage);
// 		}


// 		public function provider_persist_exception_testing() {
// 			return array(
// 				array(
// 					null,
// 					210142006, "An exception should have been thrown, since the settings argument has to be an array"
// 				),
// 				array(
// 					array(),
// 					210142010, "An exception should have been thrown, since the settings argument has to have a tableName key"
// 				),
// 				array(
// 					array(
// 						'tableName' => 'TableA'),
// 					210142015, "An exception should have been thrown, since the settings argument has to have a dbConfig key"
// 				),
// 				array(
// 					array(
// 						'tableName' => 'TableA',
// 						'dbConfig'  => 'someString'),
// 					210142017, "An exception should have been thrown, since the settings argument's dbConfig value has to be a valid path if it is a string"
// 				),
// 				array(
// 					array(
// 						'tableName' => 'TableA',
// 						'dbConfig'  => null),
// 					210142034, "An exception should have been thrown, since the settings argument's dbConfig value has to be an array if it is not a file dir"
// 				),
// 				array(
// 					array(
// 						'tableName' => 'TableA',
// 						'dbConfig'  => array(
// 							'password' => null,
// 							'user'     => null,
// 							'host'     => null
// 					)),
// 					210142043, "An exception should have been thrown, since the settings argument's dbConfig has to have a dbName key"
// 				),
// 				array(
// 					array(
// 						'tableName' => 'TableA',
// 						'dbConfig'  => array(
// 							'dbName'   => null,
// 							'user'     => null,
// 							'host'     => null
// 						)),
// 					210142058, "An exception should have been thrown, since the settings argument's dbConfig has to have a password key"
// 				),
// 				array(
// 					array(
// 						'tableName' => 'TableA',
// 						'dbConfig'  => array(
// 							'dbName'   => null,
// 							'password' => null,
// 							'host'     => null
// 						)),
// 					210142102, "An exception should have been thrown, since the settings argument's dbConfig has to have a user key"
// 				),
// 				array(
// 					array(
// 						'tableName' => 'TableA',
// 						'dbConfig'  => array(
// 							'dbName'   => null,
// 							'password' => null,
// 							'user'     => null
// 						)),
// 					210142104, "An exception should have been thrown, since the settings argument's dbConfig has to have a host key"
// 				),

// 				array(
// 					array(
// 						'tableName' => 'TableA',
// 						'dbConfig'  => dirname(__FILE__)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data/corruptedDbConfig.ini"
// 					),
// 					210142136, "An exception should have been thrown, since the dbConfig.ini at the given path is corrupted"
// 				),
// 			);
// 	    }

// 	    private function _getDataObject() {
// 	    	$emptyData = new \stdClass();
// 			return $emptyData;
// 	    }

// 		public function test_persist_and_retrieve_with_tableName_and_data_fileds_as_dbColumns() {
// 			// Setup
// 			$obj = $this->_getInstance();
// 			$data = $this->_getDataObject();
// 			$emptyData = $this->_getDataObject();
// 			$dbConfigDir = dirname(__FILE__)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data/dbConfig.ini";

// 			$columnName1 = "tableA_item_id";
// 			$columnName2 = "tableA_item_name";
// 			$columnName3 = "tableA_item_text";
// 			$value1 = 5;
// 			$value2 = "testItem";
// 			$value3 = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";

// 			$data->$columnName1 = $value1;
// 			$data->$columnName2 = $value2;
// 			$data->$columnName3 = $value3;

// 	    	$emptyData->$columnName1 = null;
// 			$emptyData->$columnName2 = null;
// 			$emptyData->$columnName3 = null;

// 			$settings = array(
// 				'tableName' => 'TableA',
// 				'dbConfig'  => $dbConfigDir
// 			);

// 			// Testing
// 			$persistSuccess = $obj->persist ($data, $settings);
// 			$retrieveResult = $obj->retrieve($emptyData, $settings);

// 			// Verification
// 			$this->assertEquals(true, $persistSuccess);
// 			$this->assertEquals($data, $retrieveResult);
// 		}

// 		/*public function test_persist_and_retrieve_update_with_tableName_and_data_fileds_as_dbColumns() {
// 			// Setup
// 			$obj = $this->_getInstance();
// 			$data = $this->_getDataObject();
// 			$emptyData = $this->_getDataObject();
// 			$dbConfigDir = dirname(__FILE__)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data/dbConfig.ini";

// 			$columnName1 = "tableA_item_id";
// 			$columnName2 = "tableA_item_name";
// 			$columnName3 = "tableA_item_text";
// 			$value1 = 5;
// 			$value2 = "testItem";
// 			$value3 = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
// 			$value2b = "updatedName";
// 			$value3b = "Updated text";
// 			$data->$columnName1 = $value1;
// 			$data->$columnName2 = $value2;
// 			$data->$columnName3 = $value3;
// 			$retrieveExpected = array(
// 				$columnName1 => $value1,
// 				$columnName2 => $value2b,
// 				$columnName3 => $value3b);
// 			$settings = array(
// 				'tableName' => 'TableA',
// 				'dbConfig'  => $dbConfigDir
// 			);

// 			// Testing
// 			$obj->persist ($data, $settings);
// 			$data->$columnName2 =
// 			$persistSuccess = $obj->persist ($data, $settings);
// 			$retrieveResult = $obj->retrieve($emptyData, $settings);

// 			// Verification
// 			$this->assertEquals(true, $persistSuccess);
// 			$this->assertEquals($retrieveExpected, $retrieveResult);
// 		}*/

// 		public function test_persist_and_retrieve_with_tableName_and_data_fileds_different_from_dbColumns() {
// 			// Setup
// 			$obj = $this->_getInstance();
// 			$data = $this->_getDataObject();
// 			$emptyData = $this->_getDataObject();
// 			$dbConfigDir = dirname(__FILE__)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data/dbConfig.ini";

// 			$columnName1 = "tableA_item_id";
// 			$columnName2 = "tableA_item_name";
// 			$columnName3 = "tableA_item_text";
// 			$field1 = "a";
// 			$field2 = "b";
// 			$field3 = "c";
// 			$value1 = 5;
// 			$value2 = "testItem";
// 			$value3 = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";

// 			$data->$field1 = $value1;
// 			$data->$field2 = $value2;
// 			$data->$field3 = $value3;

// 			$emptyData->$field1 = null;
// 			$emptyData->$field2 = null;
// 			$emptyData->$field3 = null;

// 			$settings = array(
// 				'tableName'  => array(
// 					'TableA' => array(
// 						$columnName1 => $field1,
// 						$columnName2 => $field2,
// 						$columnName3 => $field3,
// 					),
// 				),
// 				'dbConfig' => $dbConfigDir
// 			);

// 			// Testing
// 			$persistSuccess = $obj->persist ($data, $settings);
// 			$retrieveResult = $obj->retrieve($emptyData, $settings);

// 			// Verification
// 			$this->assertEquals(true , $persistSuccess);
// 			$this->assertEquals($data, $retrieveResult);
// 		}









// 		public function test_sql_injection() {
// 			// Setup
// 			$obj = $this->_getInstance();
// 			$data = $this->_getDataObject();
// 			$emptyData = $this->_getDataObject();
// 			$injectionData = $this->_getDataObject();
// 			$dbConfigDir = dirname(__FILE__)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data/dbConfig.ini";

// 			$columnName1 = "tableA_item_id";
// 			$columnName2 = "tableA_item_name";
// 			$columnName3 = "tableA_item_text";
// 			$value1 = 5;
// 			$value2 = "testItem";
// 			$value3 = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";

// 			$data->$columnName1 = $value1;
// 			$data->$columnName2 = $value2;
// 			$data->$columnName3 = $value3;


// 			$emptyData->$columnName2 = null;


// 			$dropTableCommand = " ON DUPLICATE KEY UPDATE tableA_item_name=\"SQL INJECTED!!!\", tableA_item_text=\"SQL INJECTED!!!\"";

// 			$injectionData->$columnName1 = 5;
// 			$injectionData->$columnName2 = "val2\", \"val3\") $dropTableCommand;#";
// 			$injectionData->$columnName3 = "a2";

// 			$settings = array(
// 				'tableName' => 'TableA',
// 				'dbConfig'  => $dbConfigDir
// 			);

// 			// Testing
// 			$persistSuccess = $obj->persist ($data, $settings);
// 			$persistSuccess = $obj->persist ($injectionData, $settings);
// 			$retrieveResult = $obj->retrieve($emptyData, $settings);

// 			if ($emptyData->tableA_item_name == 'SQL INJECTED!!!') {
// 				$this->fail("Vulnerable to SQL Injection");
// 			}
// 		}

// 	}
// }