<?php
namespace aae\di {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ConfigurableContainerTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;

		/*public function test___construct() {
			try {
				$obj = new ConfigurableContainer("");
			} catch (\Exception $e) {
				var_dump($e->getMessage());
				$this->assertEquals(213141057, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been thrown, since the constructor takes a path to a file as argument.");
		}*/

		public function test_getService_get_default() {
			// Setup
			$path = $this->getTestDataPath()."/config1.json";
			$serviceName = "wrong_service";
			$obj = new ConfigurableContainer($path);
			$constructorArguments = array("one", "two", "three");
			$defaultServiceInstanceName = "aae\di\\FuAltService";
		
			// Testing
			$obj->setService($serviceName, $defaultServiceInstanceName, $constructorArguments);
			$service = $obj->getService($serviceName);
			$result = get_class($service);

			// Verification
			$this->assertEquals($defaultServiceInstanceName, $result);
		}
		public function test_getService_from_config() {
			// Setup
			$path = $this->getTestDataPath()."/config1.json";
			$serviceName = "fu_service";
			$serviceClassName = "aae\\di\\FuService";
			$obj = new ConfigurableContainer($path);
			$constructorArguments = array("one", "two", "three");
			$defaultServiceInstanceName = "aae\di\\FuAltService";
		
			// Testing
			$obj->setService($serviceName, $defaultServiceInstanceName, $constructorArguments);
			$service = $obj->getService($serviceName);
			$result = get_class($service);

			// Verification
			$this->assertEquals($serviceClassName, $result);
		}
		public function test_constructor_args_passing() {
			// Setup
			$path = $this->getTestDataPath()."/config1.json";
			$serviceName = "fu_service";
			$obj = new ConfigurableContainer($path);
			$constructorArguments = array("one", "two", "three");
			$defaultServiceInstanceName = "aae\di\\FuAltService";
		
			// Testing
			$obj->setService($serviceName, $defaultServiceInstanceName, $constructorArguments);
			$service = $obj->getService($serviceName);
			$result = $service->ba();

			// Verification
			$this->assertEquals($constructorArguments, $result);
		}
		
	}

	class FuService {
		public function __construct($arg1, $arg2, $arg3) {
			$this->args = array($arg1, $arg2, $arg3);
		}
		public function ba() {
			return $this->args;
		}
	}
	class FuAltService {
		public function __construct($arg1, $arg2, $arg3) {
			$this->args = array($arg1, $arg2, $arg3);
		}
	}
}