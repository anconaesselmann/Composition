<?php
// /**
//  *
//  */
// namespace aae\persistence\adapters\db {

// 	/**
// 	 * @author Axel Ancona Esselmann
// 	 * @package aae\persistence\adapters\db
// 	 */
// 	use \aae\persistence\adapters\db\connections\DBConnectionInterface as DBConnectionInterface;
// 	use \aae\persistence\adapters\db\connections\MySQlDBConnection as MySQlDBConnection;

// 	class DataToMySQL  implements \aae\persistence\AdapterInterface {
// 		private $dbConnection = null;

// 		public function __construct() {
// 			$this->setDbConnection(new MySQlDBConnection());
// 		}

// 		public function setDbConnection(DBConnectionInterface $connectionInstance) {
// 			$this->dbConnection = $connectionInstance;
// 		}


// 		public function persist($data, $settings) {
// 			$this->_validateSettings($settings);

// 			$data   = $this->_convertFromDataFieldsToColumns($data, $settings);

// 			$stmt   = $this->_getPreparedPersistStatement($data, $settings);
// 			$this->_bindParameters($stmt, $data);
// 			$result = $stmt->execute();
// 			$stmt->close();

// 			return $result;
// 		}


// 		/*public function persist($data, $settings) {
// 			$this->_validateSettings($settings);
// 			$config = $this->_getConfigAssoc($settings);

// 			$dbConnection = $this->_getDbConnection($settings);
// 			$dbName = $config['dbName'];
// 			$tableName = $this->_getTableNames($settings);

// 			$data = $this->_convertFromDataFieldsToColumns($data, $settings);

// 			$columnNames = "";
// 			$values = "";
// 			foreach (get_object_vars($data) as $varName => $value) {
// 				if (strlen($columnNames) > 0) {
// 					$columnNames .= ", ";
// 					$values      .= ", ";
// 				}
// 				$columnNames     .= $varName;

// 				if (!is_int  ($value) &&
// 					!is_float($value) &&
// 					!is_null ($value) &&
// 					!is_bool ($value))
// 				{
// 					$value = "\"".strval($value)."\"";
// 				}

// 				$values .= $value;
// 			}
// 			$queryString = "
// 				INSERT INTO $dbName.$tableName ($columnNames)
// 					VALUES ($values)
// 					";
// 			print($queryString);
// 			$result = $dbConnection->query($queryString);

// 			if (!$result) {
// 				throw new \Exception("Error Processing Request", 1);

// 			}

// 			return $result;
// 		}*/


// 		public function retrieve($data, $settings) {
// 			$this->_validateSettings($settings);
// 			$config = $this->_getConfigAssoc($settings);
// 			$dbName = $config['dbName'];
// 			$tableName = $this->_getTableNames($settings);

// 			$dbConnection = $this->_getDbConnection($settings);

// 			$queryString = "SELECT * FROM $dbName.$tableName";
// 			$result = $dbConnection->query($queryString);
// 			if ($result) {
// 				$assoc = $result->fetch_assoc();
// 				$matchingAssoc = $this->_convertFromColumnsToDataFields($assoc, $settings);
// 				$data = $this->_updateData($data, $matchingAssoc);
// 				return $data;
// 			} else {
// 				return false;
// 			}
// 		}

// 		private function _updateData($data, $matchingAssoc) {
// 			foreach ($matchingAssoc as $key => $value) {
// 				if (property_exists($data, $key)) {
// 					$data->$key = $value;
// 				}
// 			}
// 			return $data;
// 		}

// 		private function _convertFromColumnsToDataFields($assoc, $settings) {
// 			$tableName = $settings['tableName'];

// 			$matchingAssoc = array();
// 			if (is_array($tableName)) {
// 				foreach ($settings['tableName'] as $table => $columns) {
// 					$tableName = $table; // TODO: This does not deal with multiple tables
// 					foreach ($columns as $columnName => $fieldName) {
// 						$matchingAssoc[$fieldName] = $assoc[$columnName];
// 					}
// 				}
// 			} else {
// 				$matchingAssoc = $assoc;
// 			}
// 			return $matchingAssoc;
// 		}

// 		private function _getColumnNames($data) {
// 			return array_keys(get_object_vars($data));
// 		}

// 		private function _getBindingParams($data) {
// 			$bindingTypes = "";
// 			$bindingArray = array("");
// 			foreach (get_object_vars($data) as $varName => $value) {
// 				$bindingArray[] = &$data->$varName;
// 				$bindingTypes .= "s";
// 			}
// 			$bindingArray[0] = $bindingTypes;
// 			return $bindingArray;
// 		}

// 		private function _getPreparedValues($data) {
// 			$result = array();
// 			$result = array_fill(0, count(get_object_vars($data)), "?");
// 			return $result;
// 		}

// 		private function _getPersistQueryString($data, $settings) {
// 			$config = $this->_getConfigAssoc($settings);
// 			$dbName = $config['dbName'];
// 			$tableName = $this->_getTableNames($settings);
// 			return sprintf("INSERT INTO %s.%s (%s) VALUES (%s) ",
// 				$dbName,
// 				$tableName,
// 				implode(", ", $this->_getColumnNames($data)),
// 				implode(", ", $this->_getPreparedValues($data)));
// 		}

// 		private function _bindParameters($stmt, $data) {
// 			call_user_func_array(array($stmt, 'bind_param'), $this->_getBindingParams($data));
// 		}

// 		private function _getPreparedPersistStatement($data, $settings) {
// 			$db          = $this->_getDbConnection($settings);
// 			$queryString = $this->_getPersistQueryString($data, $settings);
// 			return $db->prepare($queryString);
// 		}

// 		private function _getConfigAssoc($source) {
// 			if (is_string($source['dbConfig'])) {
// 				$config = $this->_getJSONFromDir($source['dbConfig']);
// 			} else {
// 				$config = $source['dbConfig'];
// 			}
// 			return $config;
// 		}

// 		private function _convertFromDataFieldsToColumns($data, $settings) {
// 			$tableName = $settings['tableName'];
// 			if (is_array($tableName)) {
// 				foreach ($settings['tableName'] as $table => $columns) {
// 					$tableName = $table; // TODO: This does not deal with multiple tables
// 					$temp = new \stdClass();
// 					foreach ($columns as $columnName => $fieldName) {
// 						$temp->$columnName = $data->$fieldName;
// 					}
// 					$data = $temp;
// 				}
// 			}
// 			return $data;
// 		}

// 		private function _getTableNames($settings) {
// 			if (is_array($settings['tableName'])) {
// 				foreach ($settings['tableName'] as $table => $columns) {
// 					$tableName = $table;
// 				}
// 			} else {
// 				$tableName = $settings['tableName'];
// 			}

// 			return $tableName;
// 		}

// 		private function _getDbConnection($settings) {
// 			$dbConfig = new \aae\persistence\adapters\db\DBConfig($settings['dbConfig']);
// 			$dbConnection = $this->dbConnection->getConnection($dbConfig);
// 			if (array_key_exists('logger', $settings)) {
// 				$dbConnection->setLogger($settings['logger']); // TODO: UNTESTED!!!
// 			}
// 			return $dbConnection;
// 		}








// 		private function _getJSONFromDir($dir) {
// 			$fileContent = file_get_contents($dir);
// 			$fileContent = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $fileContent);
// 			$json = json_decode($fileContent, true);
// 			return $json;
// 		}

// 		private function _validateSettings($settings) {
// 			if (!is_array($settings))                      throw new \Exception("Settings arguments have to be arrays"   , 210142006);
// 			if (!array_key_exists('tableName', $settings)) throw new \Exception("Settings have to have a 'tableName' key", 210142010);
// 			if (!array_key_exists('dbConfig' , $settings)) throw new \Exception("Settings have to have a 'dbConfig' key" , 210142015);

// 			$this->_validateDBCOnfig($settings['dbConfig']);
// 		}

// 		private function _validateDBCOnfig($dbConfig) {
// 			if (!is_array($dbConfig)) {
// 				if (is_string($dbConfig)) {
// 					if (!file_exists($dbConfig)) {
// 						throw new \Exception("The dbConfig file at '".$dbConfig."' does not exist.", 210142017);
// 					} else {
// 						// TODO: here the file is parsed just to determine validity, meaning it will have to be parsed a second time.
// 						$dbConfig = $this->_getJSONFromDir($dbConfig);
// 						if (!$dbConfig) throw new \Exception("The configuration file '".$dbConfig."' contains invalid JSON.", 210142136);
// 					}
// 				} else {
// 					throw new \Exception("The settings value for dbConfig either has to be a valid path or an associative array.", 210142034);
// 				}
// 			} else {
// 				$dbConfig = $dbConfig;
// 			}
// 			$this->_validateDBConfigAssoc($dbConfig);
// 		}

// 		private function _validateDBConfigAssoc($dbConfig) {
// 			$requiredArrayKeys = array(
// 				'dbName'   => 210142043,
// 				'password' => 210142058,
// 				'user'     => 210142102,
// 				'host'     => 210142104);
// 			foreach ($requiredArrayKeys as $arrayKey => $errorCode) {
// 				if (!array_key_exists($arrayKey, $dbConfig)) {
// 					throw new \Exception("The dbConfig has to have a '$arrayKey' key.", $errorCode);
// 				}
// 			}
// 		}

// 	}
// }