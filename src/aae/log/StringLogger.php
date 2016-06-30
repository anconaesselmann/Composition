<?php
/**
 *
 */
namespace aae\log {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\log
	 */
	class StringLogger extends \aae\log\AbstractLogger  implements \aae\log\Loggable {
		private $_result = "";


		public function log($message, $eventType = ScreenLogger::NORMAL) {
			$timeString = $this->_getTimeString();
			$this->_result .= $this->_getString($message, $timeString, $eventType);
		}
		public function getLog() {
			return $this->_result;
		}
	}
}