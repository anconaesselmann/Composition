<?php
namespace aae\abstr {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ImplementingConfigurable  {
		use \aae\abstr\Configurable;
		public function __construct($configFileDir) {
			$this->initConfigurable($configFileDir);
		}
		protected function _initializeWithArray($configArray) {

		}
	}

	class ConfigurableTest extends \PHPUnit_Framework_TestCase {


		public function test___construct_invalid_configFile_location() {
			$configFileDir = null;
			$authenticationObj = null;
			try {
				$obj = new ImplementingConfigurable($configFileDir);
			} catch (\Exception $e) {
				$this->assertEquals(209141613, $e->getCode());
				return;
			}
			$this->fail("An exception should have been raised, because an invalid path was provided to the constructor.");
		}

		public function test___construct_with_invalid_config_file() {
			$configFileDir = dirname(__FILE__)."/ConfigurableTestData/invalid.json";
			$authenticationObj = null;
			try {
				$obj = new ImplementingConfigurable($configFileDir);
			} catch (\Exception $e) {
				$this->assertEquals(209141605, $e->getCode());
				return;
			}
			$this->fail("An exception should have been raised, because the config file contained invalid json.");
		}
		
	}
}