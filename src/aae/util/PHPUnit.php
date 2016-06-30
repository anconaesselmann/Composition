<?php
namespace aae\util {
	/**
	 *
	 * @package aae\util
	 */
	class PHPUnit {
		public $fw_dir = "";
		public $codeCoverageCommand = "";
		public $output = '';

		public function __construct() {
			$this->fw_dir = CODE_BASE_DIR.SUB_CODE_BASE_DIR;
			$this->codeCoverageCommand = "--coverage-html ".CODE_COVERAGE_DIR." ";
		}

		public function generatingCodeCoverageReport() {
			//echo "Tests successful, generating code coverage report:\r";
			//$sound = "afplay /System/Library/Sounds/Ping.aiff";
			//
			//
			$command = "phpunit --colors ".$this->codeCoverageCommand.$this->fw_dir;
			\aae\std\std::execInBackground($command, getTempDir().'/output.txt', getTempDir().'/error.txt');
			//\aae\std\std::execInBackground($sound, getTempDir().'/output.txt', getTempDir().'/error.txt');
		}
		public function unitTest() {
			echo "Testing in progress\x1b[5m...\x1b[25m\r";
			exec("phpunit --colors ".$this->fw_dir, $this->output, $return_var);
			//var_dump($this->output);
			return $return_var;
		}
	}
}