<?php
/**
 *
 */
namespace aae\log {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\log
	 */
	abstract class AbstractLogger {
		const NORMAL = 0, DEBUG = 1, FATAL = 2;
		private $_debug = false;
		protected $_newLine = "\n";
		protected function _getTimeString() {
			date_default_timezone_set('America/Los_Angeles');
			return date('m/d/Y h:i:s a', time());
		}
		abstract public function log($message, $eventType = ScreenLogger::NORMAL);
		public function debug($bool = true) {
			$this->_debug = (bool)$bool;
		}
		protected function _getString($message, $timeString, $eventType) {
			if (!is_string($message)) {
				$message = serialize($message);
			}
			return "Type: {$eventType}, Time: {$timeString}, Message: '{$message}'\n";
		}
		public function logDebug($message) {
			if ($this->_debug) {
				$this->log($message, AbstractLogger::DEBUG);
			}
		}
		public function logError(\Exception $e) {
			$errorMessage = get_class($e)." {$e->getCode()} with message: '{$e->getMessage()}'";
			if ($this->_debug) {
				$errorMessage .= $this->_newLine.$this->_newLine."Exception stack:".$this->_newLine;
				$trace = $e->getTrace();
				for ($i=0; $i < count($trace); $i++) { 
					if (isset($trace[$i]["file"])) {
						$file = " in '".$trace[$i]["file"]."' on line ".$trace[$i]["line"];
					} else {
						$file = "";
					}
					$class = "";
					$type = "";
					if (isset($trace[$i]["class"])) {
						$class = $trace[$i]["class"];
						$type = $trace[$i]["type"];
					}
					if ($class != "PHPUnit_Framework_TestCase" && $class != "PHPUnit_Framework_TestSuite" && $class != "PHPUnit_Framework_TestResult" && $class != "PHPUnit_TextUI_TestRunner" && $class != "PHPUnit_TextUI_Command") {
						$errorMessage .= $this->_newLine.$class.$type.$trace[$i]["function"].$file;
					}
				}
			}
			$this->log($errorMessage, AbstractLogger::FATAL);
		}
	}
}