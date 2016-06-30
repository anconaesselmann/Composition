<?php
namespace aae\util {
	/**
	 *
	 * @package aae\util
	 */
	class SimplePhpClass {
		public function __construct ()
		{

		}
		public	function 	__toString() {
			return $this->formatOutput();
		}
		public function func1() {
			$this->_paused = 0;
			$this->_time = microtime(true);
		}
		public function func2() {
			if ($this->_paused === 0) {
				$this->_paused = microtime(true);
			}
		}
	}
}
?>