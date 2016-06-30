<?php
namespace aae\util {
	/**
	 *
	 * @package aae\util
	 */
	class SimpleTemplateController {
		public function __construct ()
		{

		}
		public	function 	__toString() {
			return $this->formatOutput();
		}
		public function func1Action() {
			$this->_paused = 0;
			$this->_time = microtime(true);
		}
		public function func2Action() {
			if ($this->_paused === 0) {
				$this->_paused = microtime(true);
			}
		}
	}
}
?>