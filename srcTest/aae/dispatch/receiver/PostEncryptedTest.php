<?php
namespace aae\dispatch\receiver {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class PostEncryptedTest extends \PHPUnit_Framework_TestCase {
		private function _getDecrypter($plain) {
			$decrypter = $this->getMockBuilder("\\aae\\security\\MCryptEncrypter")
					    ->disableOriginalConstructor()
					    ->getMock();
			$decrypter->method('encrypt')
			    ->with($this->isType('string'))
			    ->will($this->returnValue($plain));
			$decrypter->method('decrypt')
			    ->with($this->isType('string'))
			    ->will($this->returnValue($plain));
			$decrypter->method('passwordSet')
			    ->with()
			    ->will($this->returnValue(true));
			return $decrypter;
		}
		public function test___construct() {
			$password = "password1234";
			$decrypter = $this->_getDecrypter('');
			$obj = new PostEncrypted($decrypter);
		}
		public function test_GET() {
			# Given
			$password  = "password1234";
			$decrypter = $this->_getDecrypter('g1');
			$_GET      = ["var1" => $decrypter->encrypt("g1")];
			$obj       = new PostEncrypted($decrypter);

			# When get is called
			$result    = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected  = NULL;
			$this->assertEquals($expected, $result);
		}
		public function test_POST() {
			# Given
			$password  = "password1234";
			$decrypter = $this->_getDecrypter('p1');
			$_POST     = ["var1" => $decrypter->encrypt("p1")];
			$obj       = new PostEncrypted($decrypter);

			# When get is called
			$result    = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected  = "p1";
			$this->assertEquals($expected, $result);
		}
		public function test_POST_with_callback() {
			# Given
			$password  = "password1234";
			$encrypter = $this->_getDecrypter('');

			$decrypter = $this->_getDecrypter('p1');
			$_POST     = ["var1" => $encrypter->encrypt("p1")];
			$obj       = new PostEncrypted($decrypter);
			$obj->setGetPasswordCallback([new PostEncrypted_helper_10_17_2014(), "getPassword"], 1234);

			# When get is called
			$result    = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected  = "p1";
			$this->assertEquals($expected, $result);
		}
		public function test_POST_has_precedence() {
			# Given
			$password  = "password1234";
			$decrypter = $this->_getDecrypter("p1");
			$_POST     = ["var1" => $decrypter->encrypt("p1")];
			$_GET      = ["var1" => "g1"];
			$obj       = new PostEncrypted($decrypter);

			# When get is called
			$result    = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected  = "p1";
			$this->assertEquals($expected, $result);
		}
		public function test_not_FILES() {
			# Given
			$_FILES = ["var1" => "f1"];
			$password = "password1234";
			$decrypter = $this->_getDecrypter("p1");
			$obj = new PostEncrypted($decrypter);

			# When get is called
			$result = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_not_SERVER() {
			# Given
			$_SERVER = ["var1" => "s1"];
			$password = "password1234";
			$decrypter = $this->_getDecrypter("p1");
			$obj = new PostEncrypted($decrypter);

			# When get is called
			$result = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_not_COOKIE() {
			# Given
			$_COOKIE = ["var1" => "s1"];
			$password = "password1234";
			$decrypter = $this->_getDecrypter("p1");
			$obj = new PostEncrypted($decrypter);

			# When get is called
			$result = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_not_SESSION() {
			# Given
			$_SESSION = ["var1" => "s1"];
			$password = "password1234";
			$decrypter = $this->_getDecrypter("p1");
			$obj = new PostEncrypted($decrypter);

			# When get is called
			$result = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_not_ENV() {
			# Given
			$_ENV = ["var1" => "s1"];
			$password = "password1234";
			$decrypter = $this->_getDecrypter("p1");
			$obj = new PostEncrypted($decrypter);

			# When get is called
			$result = $obj["var1"];

			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
		public function test_REQUEST() {
			# Given
			$_REQUEST = ["var1" => "r1"];
			$password = "password1234";
			$decrypter = $this->_getDecrypter("p1");
			$obj = new PostEncrypted($decrypter);

			# When get is called
			$result = $obj["r1"];

			# Then EXPECTED_CONDITIONS
			$expected = null;
			$this->assertEquals($expected, $result);
		}
	}
	/**
	*
	*/
	class PostEncrypted_helper_10_17_2014 {
		public function getPassword($arg) {
			if ($arg == 1234) {
				return "password1234";
			} else return false;
		}
	}
}