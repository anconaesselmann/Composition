<?php
namespace aae\dispatch {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ApiTest extends \PHPUnit_Framework_TestCase {
		public $sut;

		public function setUp() {
		    parent::setUp();
		    $this->router = $this->getMockBuilder('\aae\dispatch\Router')
				->disableOriginalConstructor()
				->getMock();
			$this->dr = $this->getMockBuilder('\aae\di\DependencyResolverInterface')
				->disableOriginalConstructor()
				->getMock();
			$this->serial = new \aae\serialize\Json();
			$this->sut = new Api($this->router, $this->dr, $this->serial);
		}

		public function test_call_Action() {
			# Given
			$this->router->expects($this->once())
			    ->method('getCall')
			    ->willReturn(new \aae\api\APIRequest(
			    	"Mock_12_06_14_3_28",
			    	"callable",
			    	null
			    )
			);
			$this->dr->expects($this->once())
			    ->method("resolve")
			    ->willReturn([]);
			$this->sut->setControllerEnding("ViewController");

			# When call_Action is called
			$result = $this->sut->run();

			# Then
			$this->assertEquals('{"response":true,"errorCode":0}', $result);
		}

		public function test_notCallable() {
			# Given
			$this->router->expects($this->atLeastOnce())
			    ->method('getCall')
			    ->willReturn(new \aae\api\APIRequest(
			    	"Mock_12_06_14_3_28",
			    	"notCallable",
			    	null
			    )
			);
			$this->dr->expects($this->atLeastOnce())
			    ->method("resolve")
			    ->willReturn([]);
			$this->sut->setControllerEnding("ViewController");

			# When call_Action is called
			$result = $this->sut->run();

			# Then
			$this->assertRegExp('/1109141941/', $result);
		}
	}
}
namespace {
	class Mock_12_06_14_3_28ViewController extends \aae\ui\ViewController {
		public function __construct() {}
		public function callableAction() {
			return true;
		}
		public function notCallable() {
			return true;
		}
	}
}