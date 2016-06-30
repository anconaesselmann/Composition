<?php
namespace aae\di {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ArgNameDependencyResolverTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new ArgNameDependencyResolver(array());
		}

		public function test_resolveAllowNoMatch_no_Matches() {
			# Given a resolution strategy with no matches
			$resolutionStrategy = array();
			$obj = new ArgNameDependencyResolver($resolutionStrategy);

			# When resolveAllowNull is called
			$result = $obj->resolveAllowNoMatch("\aae\di\MockObject_10_16_2014_a", "aFunctionWithDependencies", false);

			# Then
			$expected = array(false, false, false);
			$this->assertEquals($expected, $result);
		}

		public function test_resolveAllowNoMatch_all_matches() {
			# Given a resolution strategy with no matches
			$resolutionStrategy = array("dep3" => true, "dep1" => "test", "dep2" => 55);
			$obj = new ArgNameDependencyResolver($resolutionStrategy);

			# When resolveAllowNull is called
			$result = $obj->resolveAllowNoMatch("\aae\di\MockObject_10_16_2014_a", "aFunctionWithDependencies");

			# Then
			$expected = array("test", 55, true);
			$this->assertEquals($expected, $result);
		}

		public function test_resolve_for_constructor() {
			# Given a resolution strategy with no matches
			$resolutionStrategy = array("dep3" => true, "dep1" => "test", "dep2" => 55);
			$obj = new ArgNameDependencyResolver($resolutionStrategy);

			# When resolveAllowNull is called
			$result = $obj->resolve("\aae\di\MockObject_10_16_2014_a", "__construct");

			# Then
			$expected = array("test", 55, true);
			$this->assertEquals($expected, $result);
		}

		public function test_resolveAllowNoMatch_dic_test() {
			# Given a resolution strategy with no matches
			$dicMock = $this->getMockBuilder('\aae\std\DIFactory')
                     		->disableOriginalConstructor()
                     		->getMock();
			$dicMock->expects($this->any())
             		->method('build')
             		->will($this->returnArgument(0));

			$obj = new ArgNameDependencyResolver($dicMock);

			# When resolveAllowNull is called
			$result = $obj->resolveAllowNoMatch("\aae\di\MockObject_10_16_2014_a", "aFunctionWithDependencies");

			# Then
			$expected = array("dep1", "dep2", "dep3");
			$this->assertEquals($expected, $result);
		}
		public function testException_resolve_without_all_dependencies() {
			$expectedCode = 1016141128;
			$resolutionStrategy = array("dep3" => true, "dep1" => "test");
			$obj = new ArgNameDependencyResolver($resolutionStrategy);

			try {
				$obj->resolve("\aae\di\MockObject_10_16_2014_a", "aFunctionWithDependencies");

				$this->fail("Expected ServiceNotFoundException with code $expectedCode.");
			} catch (ServiceNotFoundException $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				$this->assertEquals($expectedCode, $code);
				return;
			}
		}

		public function testException_ReflectionExceptions_get_conveted() {
			$expectedCode = 1211141019;
			$resolutionStrategy = array("dep3" => true, "dep1" => "test", "dep2" => 55);
			$obj = new ArgNameDependencyResolver($resolutionStrategy);

			try {
				$result = $obj->resolve("\aae\di\MockObject_10_16_2014_a_does_not_exist", "__construct");
			} catch (\Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				$this->assertEquals($expectedCode, $code);
				return;
			}
			$this->fail("Expected \Exception with code $expectedCode");
		}
	}



	class MockObject_10_16_2014_a {
		public $dependencies = array();

		public function __construct($dep1, $dep2, $dep3) {
			$this->aFunctionWithDependencies($dep1, $dep2, $dep3);
		}

		public function aFunctionWithDependencies($dep1, $dep2, $dep3) {
			$this->dependencies[] = $dep1;
			$this->dependencies[] = $dep2;
			$this->dependencies[] = $dep3;
		}
	}
}