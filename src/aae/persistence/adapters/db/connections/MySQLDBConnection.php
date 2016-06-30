<?php
// /**
//  *
//  */
// namespace aae\persistence\adapters\db\connections {
// 	/**
// 	 * @author Axel Ancona Esselmann
// 	 * @package aae\persistence\adapters\db\connections
// 	 */
// 	class MySQLDBConnection implements \aae\persistence\adapters\db\connections\DBConnectionInterface {
// 		private $_logger = null;

// 		private $_dbPointer = null;

// 		private $_currentConfig = null;

// 		public function getConnection($dbConfig) {
// 			if ($this->_connectionIsStale()) {
// 				@$this->_dbPointer = new \MySQLi($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['dbName'], $dbConfig['port'], $dbConfig['socket']);
// 				if ($this->_dbPointer->connect_errno) {
// 					$this->_loggConnectionError();
// 					throw new \Exception("Could not connect to database.", 211140819);
// 				}
// 			}
// 			return $this->_dbPointer;
// 		}

// 		public function setLogger($loggerInstance) {
// 			if (is_string($loggerInstance)) {
// 				$loggerInstance = $this->_getLoggerInstance($loggerInstance);
// 			}
// 			if (!$loggerInstance instanceof \aae\util\Loggable) {
// 				throw new \Exception("Loggers have to implement the '\aae\util\Loggable' interface", 211140900);
// 			}
// 			$this->_logger = $loggerInstance;
// 		}

// 		private function _connectionIsStale() {
// 			return $this->_dbPointer === null || $dbConfig != $this->_currentConfig;
// 		}

// 		private function _getConnectionErrorString() {
// 			return sprintf("MySQLi error %s: %s",
// 				$this->_dbPointer->connect_errno,
// 				$this->_dbPointer->connect_error);
// 		}

// 		private function _loggConnectionError() {
// 			if ($this->_logger !== null) {
// 				$errorMessage = $this->_getConnectionErrorString();
// 				$this->_logger->log($errorMessage);
// 			}
// 		}

// 		private function _getLoggerInstance($loggerClassName) {
// 			if (!class_exists($loggerClassName)) {
// 				throw new \Exception("The logger '$loggerClassName' is not defined.", 211140943);
// 			}
// 			return new $loggerClassName();
// 		}
// 	}
// }