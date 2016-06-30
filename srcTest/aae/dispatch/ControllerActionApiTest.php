<?php
namespace aae\dispatch {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ControllerActionApiTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$router = $this->getMockBuilder('\aae\dispatch\Router')
				->disableOriginalConstructor()
				->getMock();
			$dr = $this->getMockBuilder('\aae\di\DependencyResolverInterface')
				->disableOriginalConstructor()
				->getMock();
			$serial = $this->getMockBuilder('\aae\serialize\SerializerInterface')
				->disableOriginalConstructor()
				->getMock();
			$obj = new ControllerActionApi($router, $dr, $serial);
		}
		
	}
}