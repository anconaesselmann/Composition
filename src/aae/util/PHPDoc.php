<?php
namespace aae\util {
	/**
	 *
	 * @package aae\util
	 */
	class PHPDoc {
		public $command = "";

		public function __construct($codeBaseDir, $documentationDir) {
			$this->command = "phpdoc -d ".$codeBaseDir." -t ".$documentationDir;//" 2>/dev/null &";
		}
		public function generate() {
			\aae\std\std::execInBackground($this->command, getTempDir().'/output.txt', getTempDir().'/error.txt');
		}
	}
}