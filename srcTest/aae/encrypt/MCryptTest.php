<?php
namespace {
	$mockPlain = "NONE";
	const MCRYPT_RIJNDAEL_128 = 1;
	const MCRYPT_MODE_CBC = 1;
	const MCRYPT_RAND = 1;
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
}
namespace aae\encrypt {
	function mcrypt_create_iv() {return "1234567890123456";}
	function mcrypt_get_iv_size() {}
	function mcrypt_encrypt($algorythm, $key, $decrypted, $mode, $iv) {
		global $mockPlain;
		$mockPlain = $decrypted;
	}
	function mcrypt_decrypt($cipher, $key, $data, $mode, $iv) {
		global $mockPlain;
		return $mockPlain;
	}
}
namespace aae\encrypt {
	class MCryptTest extends \PHPUnit_Framework_TestCase {
		// Integration testing
		public function test_encrypt_decrypt_integration_with_correct_password() {
			// Setup
			global $mockPlain;
			$encrypter = new MCrypt("1234Password");
			$decrypter = new MCrypt("1234Password");

			$decrypted = "TestString";

			// Testing
			$encodeResult = $encrypter->encrypt($decrypted);
			$decodeResult = $decrypter->decrypt($encodeResult);

			// Verification
			$this->assertEquals($decrypted, $decodeResult);
		}

		public function test_encrypt_decrypt_integration_with_wrong_password() {
			// Setup
			global $mockPlain;
			$encrypter = new MCrypt("1234Password");
			$decrypter = new MCrypt("234Password");

			$decrypted = "TestString";

			// Testing
			$encodeResult = $encrypter->encrypt($decrypted);
			$mockPlain = "wrongString";
			try {
				$decodeResult = $decrypter->decrypt($encodeResult);
			} catch (\Exception $e) {
				// Verification
				$this->assertEquals(218141302, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been thrown, since the passwords were not identical.");
		}
		public function test___construct() {
			$obj = new MCrypt("");
		}
		public function test_encrypt_exception_argument_not_a_string() {
			// Setup
			$obj = new MCrypt("password");
			$decrypted = new \stdClass();
			// Testing
			try {
				$result = $obj->encrypt($decrypted);
			} catch (\Exception $e) {
				// Verification
				$this->assertEquals(219140853, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been thrown since only strings can be encrypted.");
		}

		public function test_decrypt_exception_argument_not_a_string() {
			// Setup
			$obj = new MCrypt("password");
			$encrypted = new \stdClass();
			// Testing
			try {
				$result = $obj->decrypt($encrypted);
			} catch (\Exception $e) {
				// Verification
				$this->assertEquals(219140853, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been thrown since only strings can be encrypted.");
		}


	}
	class MCryptTestGlobals	{
		public static $PLAIN;
	}
}