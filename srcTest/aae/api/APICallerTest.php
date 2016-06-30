<?php
namespace aae\api {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class APICallerTest extends \PHPUnit_Framework_TestCase {
		/*public function test_integration_direct_api_call() {
			// Setup
			$expected = array("response" => array("one","two","three"));
			$apiName  = "\\aae\\api\\ControllerActionAPI";
			
			// Testing
			$transmService = new \aae\api\DirectAPICallTransmitter($apiName);
			
			$request 	   = new \aae\api\APIRequest("aae/api/Fu", "ba", array("one", "two", "three"));
			$apiCaller     = new \aae\api\APICaller($transmService);

			$result = $apiCaller->sendRequest($request);

			// Verification
			$this->assertEquals($expected, $result);
		}

		public function test_integration_direct_api_call_exception_logging() {
			// Setup
			$expectedRegEx = '/.*Type:.*0.*Time:.*\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}.*Message:.*\'Exception \d* with message: \'The method .*->.* does not exist.\'/';
			$apiName  = "\\aae\\api\\ControllerActionAPI";
			
			// Testing
			$transmService = new \aae\api\DirectAPICallTransmitter($apiName);
			$logger        = new \aae\log\StringLogger();
			$transmService->setLogger($logger);
			
			$request 	   = new \aae\api\APIRequest("aae/api/Fu", "ba1", array("one", "two", "three"));
			
			$apiCaller     = new \aae\api\APICaller($transmService);
			$callResult = $apiCaller->sendRequest($request);
			$logResult = $logger->getLog();

			// Verification
			$this->assertRegExp($expectedRegEx,$logResult);
		}*/






		public function getService($serviceName) {
			if ($serviceName == "HttpKeyIdtransmissionService") {
				$appKey        = "!o94Jlse*(@)efnGs_8";
				$appId         = "App001";
				
				$serializer    = new \aae\std\JsonSerializer();
				$encrypter     = new \aae\security\MCryptEncrypter($appKey);
				$transmEncoder = new \aae\security\Encoder($serializer, $encrypter);

				$transmitter   = new \aae\connect\CURL("localhost/integration_tests/http_api_call.php");
				$transmService = new \aae\connect\EncodedTransmitter($transmitter, $transmEncoder, $appId);
				
				return $transmService;
			}
		}


		/*public function test_integration_HTTPKeyIdAPI_call() {
			// Setup
			$this->_prepareServer();
			$expected  = array("response" => array("test" => "works"));
			$request   = new \aae\api\APIRequest("aae/api/Fu", "ba", array("one", "two", "three"));
			
			// Testing
			$apiCaller = $apiCaller = new \aae\api\APICaller($this->getService("HttpKeyIdtransmissionService"));
			$result    = $apiCaller->sendRequest($request);

			// Verification
			$this->assertEquals($expected, $result);
		}*/
		
		protected function _prepareServer() {
			$dir = $this->_getLocalhostDir()."integration_tests/http_api_call.php";

			$content = '<?php
namespace aae\api {
	class FuController {
		public function baAction($arg1, $arg2, $arg3) {
			return array("test" => "works");
		}
	}
	class FuContainer {
		public function getService($serviceName) {
			if ($serviceName == "receiver") {
				$appKey        = "!o94Jlse*(@)efnGs_8";
				
				$serializer    = new \aae\std\JsonSerializer();
				$encrypter     = new \aae\security\MCryptEncrypter($appKey);
				$transmEncoder = new \aae\security\Encoder($serializer, $encrypter);
				
				$receiver   = new \aae\connect\REQUEST();
				
				$receiverService = new APICallReceiver($receiver, $transmEncoder);
				return $receiverService;
			}
		}
	}

	require $_SERVER["DOCUMENT_ROOT"]."/../aae_framework/fw_library/aae/autoload/AutoLoader.php";
	$cont = new FuContainer();

	$api = new ControllerActionAPI($cont->getService("receiver"));
}
';
			file_put_contents($dir, $content);
		}

		protected function _getLocalhostDir() {
			$reflectedClass = new \ReflectionObject($this);
			$reflectedClassFileName = $reflectedClass->getFileName();
	        return dirname(dirname(dirname(dirname(dirname($reflectedClassFileName)))))."/staging/";
		}
	}		
}
namespace aae\api {
	class FuController {
		public function baAction($arg1, $arg2, $arg3) {
			return array($arg1, $arg2, $arg3);
		}
	}
}