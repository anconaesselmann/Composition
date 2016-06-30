<?php
namespace aae\dispatch\receiver {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class PostGetTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new PostGet();
		}
		public function test_GET() {
			# Given 
			$_GET  = ["var1" => "g1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["var1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = "g1";
			$this->assertEquals($expected, $result);
		}
		public function test_POST() {
			# Given 
			$_POST = ["var1" => "p1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["var1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = "p1";
			$this->assertEquals($expected, $result);
		}
		public function test_POST_has_precedence() {
			# Given 
			$_POST = ["var1" => "p1"];
			$_GET  = ["var1" => "g1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["var1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = "p1";
			$this->assertEquals($expected, $result);
		}
		public function test_not_FILES() {
			# Given 
			$_FILES = ["var1" => "f1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["var1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_not_SERVER() {
			# Given 
			$_SERVER = ["var1" => "s1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["var1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_not_COOKIE() {
			# Given 
			$_COOKIE = ["var1" => "s1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["var1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_not_SESSION() {
			# Given 
			$_SESSION = ["var1" => "s1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["var1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_not_ENV() {
			# Given 
			$_ENV = ["var1" => "s1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["var1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_REQUEST() {
			# Given 
			$_REQUEST = ["var1" => "r1"];
			$obj = new PostGet();
		
			# When get is called
			$result = $obj["r1"];
			
			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
	}
}