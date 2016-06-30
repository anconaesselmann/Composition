<?php
namespace {
	global $password;
	function mcrypt_create_iv(){}
	function mcrypt_get_iv_size(){}
	function mcrypt_encrypt($var){
		global $password;
		$password = $var;
	}
	function mcrypt_decrypt($var){
		global $password;
		echo "password: ".$password;
		return $password;
	}
}
namespace aae\security {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';

	class MCryptEncrypterTest extends \PHPUnit_Framework_TestCase {
		// Integration testing
		public function test_encrypt_decrypt_integration_with_correct_password() {
			$encrypter = new MCryptEncrypter("1234Password");
			$decrypter = new MCryptEncrypter("1234Password");

			$decrypted = "TestString";

			// Testing
			$encodeResult = $encrypter->encrypt($decrypted);

			$decodeResult = $decrypter->decrypt($encodeResult);

			// Verification
			$this->assertEquals($decrypted, $decodeResult);
		}

		public function test_encrypt_decrypt_integration_with_wrong_password() {
			// Setup
			$encrypter = new MCryptEncrypter("1234Password");
			$decrypter = new MCryptEncrypter("234Password");

			$decrypted = "TestString";

			// Testing
			$encodeResult = $encrypter->encrypt($decrypted);

			try {
				$decodeResult = $decrypter->decrypt($encodeResult);
			} catch (\Exception $e) {
				// Verification
				$this->assertEquals(218141302, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been thrown, since the passwords were not identical.");
		}

		// Unit testing
		public function test___construct() {
			$obj = new MCryptEncrypter("");
		}
		public function test_encrypt_exception_argument_not_a_string() {
			// Setup
			$obj = new MCryptEncrypter("password");
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
			$obj = new MCryptEncrypter("password");
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
}