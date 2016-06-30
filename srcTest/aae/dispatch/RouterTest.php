<?php
namespace aae\dispatch {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class RouterTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$r = new receiver\PostGet();
			$obj = new Router($r);
		}
		public function test_() {
			# Given 
			$ip = "127.0.0.1";
			$_SERVER["REMOTE_ADDR"] = $ip;
			$controller = "c1";
			$action = "a1";
			$args = array("a", "b", "c");
			$_POST = ["controller" => $controller, "action" => $action, "args" => implode("/", $args)];
			$r = new receiver\PostGet();
			$obj = new Router($r);
		
			# When  is called
			$result = $obj->getCall();
			
			# Then
			$expected = new \aae\dispatch\callProtocol\ControllerActionArgs($controller, $action, $args);
			$expected->setIp($ip);
			$this->assertEquals($expected, $result);
		}
		
	}
}