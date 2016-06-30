<?php
/**
 *
 */
namespace aae\log {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	trait LoggableTrait {
		private $_logger = null;


		public function setLogger(\aae\log\Loggable $logger = null) {
			$this->_logger = $logger;
		}
		public function getLogger() {
			return $this->_logger;
		}
		public function log($message, $eventType = AbstractLogger::NORMAL) {
			$this->_logger->log($message, $eventType);
		}
		public function hasLogger() {
			return (is_null($this->_logger)) ? false : true;
		}
		public function logError(\Exception $e) {
			$this->_logger->logError($e);
		}
	}
}