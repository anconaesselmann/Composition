<?php
namespace aae\dispatch {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class CallDispatcherTest extends \PHPUnit_Framework_TestCase {
		public function test_() {
			/*empty for now*/
		}
		/*public function test_integration_direct() {

			$xpctdTransmission = array("response" => array("test" => "works", "1 2 3" => "one two three"), "errorCode" => null);
			$controllerName    = "fu";
			$actionName        = "ba";
			$args              = array("one", "two", "three");
			$className         = "REPLACE";
			$caller            = new \aae\dispatch\caller\Direct($className); 
			$logger            = new \aae\log\StringLogger();
			$logger->debug();
			$caller->setLogger($logger);
			$serializer        = new \aae\serialize\Json();
			$call              = new \aae\dispatch\callProtocol\ControllerActionArgs($controllerName, $actionName, $args);
			$call->setPublicKey("aw09i0f04w00()094f93#*\$DG");
			$dispatcher        = new \aae\dispatch\CallDispatcher($caller, $serializer);

			$transmissionRslt  = $dispatcher->dispatch($call);

			$this->assertEquals($xpctdTransmission, $transmissionRslt);
		}*/

		protected function _prepareServerUnencrypted() {
			$dir = $this->_getLocalhostDir()."integration_tests/http_api_call.php";

			$content = '<?php
namespace {
	class FuController {
		public function baAction($arg1, $arg2, $arg3) {
			return array("test" => "works", "1 2 3" => "$arg1 $arg2 $arg3");
		}
	}
}
namespace aae\api {
	require $_SERVER["DOCUMENT_ROOT"]."/../aae_framework/fw_library/aae/autoload/AutoLoader.php";
	

	$serializer = new \aae\serialize\Json();
	$encrypter  = null;#new \aae\encrypt\PlainText();
	$router     = new \aae\dispatch\Router($serializer, $encrypter);
	$logger     = new \aae\log\StringLogger();
	$logger->debug();

    $factory    = new \aae\std\SimpleFactory();
    $instance   = $factory->build("\aae\dispatch\ControllerActionAPI", $router, $serializer, $encrypter, $logger);
    
    echo $instance->run();

    #echo $logger->getLog();
}
';
			file_put_contents($dir, $content);
		}
		/*public function test_integration_curl() {
			$this->_prepareServerUnencrypted();

			$xpctdTransmission = array("response" => array("test" => "works", "1 2 3" => "one two three"), "errorCode" => null);
			$controllerName    = "fu";
			$actionName        = "ba";
			$args              = array("one", "two", "three");
			$url               = "localhost/integration_tests/http_api_call.php";
			$caller            = new \aae\dispatch\caller\Curl($url); // Object
			$serializer        = new \aae\serialize\Json();
			$encrypter         = null;//new \aae\encrypt\PlainText();
			$call              = new \aae\dispatch\callProtocol\ControllerActionArgs($controllerName, $actionName, $args);
			$call->setPublicKey("aw09i0f04w00()094f93#*\$DG");
			$dispatcher        = new \aae\dispatch\CallDispatcher($caller, $serializer, $encrypter);

			$transmissionRslt  = $serializer->unserialize($dispatcher->dispatch($call));

			#print_r($transmissionRslt);
			$this->assertEquals($xpctdTransmission, $transmissionRslt);
		}*/



		protected function _prepareServerEncrypted() {
			$dir = $this->_getLocalhostDir()."integration_tests/http_api_call.php";

			$content = '<?php
namespace {
	class FuController {
		public function baAction($arg1, $arg2, $arg3) {
			return array("test" => "works", "1 2 3" => "$arg1 $arg2 $arg3");
		}
	}
}
namespace aae\api {
	require $_SERVER["DOCUMENT_ROOT"]."/../aae_framework/fw_library/aae/autoload/AutoLoader.php";
	
	#print_r($_REQUEST);

	$serializer = new \aae\serialize\Json();
	$encrypter  = new \aae\encrypt\MCrypt("9843feud$%3sfg");
	$router     = new \aae\dispatch\Router($serializer, $encrypter);
	$logger     = new \aae\log\StringLogger();
	$logger->debug();

    $factory    = new \aae\std\SimpleFactory();
    $instance   = $factory->build("\aae\dispatch\ControllerActionAPI", $router, $serializer, $encrypter, $logger);
    
    echo $instance->run();

    echo $logger->getLog();
}
';
			file_put_contents($dir, $content);
		}
		/*public function test_integration_curl_encrypted() {
			$this->_prepareServerEncrypted();

			$xpctdTransmission = array("response" => array("test" => "works", "1 2 3" => "one two three"), "errorCode" => null);
			$controllerName    = "fu";
			$actionName        = "ba";
			$args              = array("one", "two", "three");
			$url               = "localhost/integration_tests/http_api_call.php";
			$caller            = new \aae\dispatch\caller\Curl($url);
			$logger            = new \aae\log\StringLogger();
			$logger->debug();
			$caller->setLogger($logger);
			$serializer        = new \aae\serialize\Json();
			$encrypter         = new \aae\encrypt\MCrypt("9843feud$%3sfg");
			$call              = new \aae\dispatch\callProtocol\ControllerActionArgs($controllerName, $actionName, $args);
			$call->setPublicKey("aw09i0f04w00()094f93#*\$DG");
			$dispatcher        = new \aae\dispatch\CallDispatcher($caller, $serializer, $encrypter);

			$transmissionRslt  = $dispatcher->dispatch($call);

			print($logger->getLog());
			$this->assertEquals($xpctdTransmission, $transmissionRslt);
		}*/


		protected function _getLocalhostDir() {
			$reflectedClass = new \ReflectionObject($this);
			$reflectedClassFileName = $reflectedClass->getFileName();
	        return dirname(dirname(dirname(dirname(dirname($reflectedClassFileName)))))."/staging/";
		}
	}		
}
namespace {
	class FuController {
		public function baAction($arg1, $arg2, $arg3) {
			return array("test" => "works", "1 2 3" => "$arg1 $arg2 $arg3");
		}
	}
}