<?php
namespace aae\util {
	/**
	 *
	 * @package aae\util
	 */
	class Timer {
		public function __construct($start = false) {
			$this->_paused = 0;
			$this->_time = 0;
			if ($start) $this->start();
		}
		public function __toString() {
			return $this->formatOutput();
		}
		public function start() {
			$this->_paused = 0;
			$this->_time = microtime(true);
		}
		public function pause() {
			if ($this->_paused === 0) {
				$this->_paused = microtime(true);
			}
		}
		public function unpause() {
			if ($this->_paused !== 0) {
				$this->_time += microtime(true) - $this->_paused;
				$this->_paused = 0;
			}
		}
		public function get($decimals = 5) {
			if ($this->_time !== 0)
				if ($this->_paused == 0) return round(microtime(true) - $this->_time, $decimals);
				else return round($this->_paused - $this->_time, $decimals);
			else return 0;
		}
		public function formatOutput() {
			$time = $this->get();
			$s = floor($time);
			$ms = ($time - $s) * 1000;
			if ($s > 0) return 's:'.$s.' ms:'.$ms;
			else return 'ms: '.$ms;

		}
		private $_time;
		private $_paused;
	}
}
?>