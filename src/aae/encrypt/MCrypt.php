<?php
/**
 *
 */
namespace aae\encrypt {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\encrypt
	 */
	class MCrypt implements \aae\encrypt\CryptographyInterface {
		public function __construct($password = NULL, $salt="1I]uSk'V/iO'WtNFn") {
        	// if ($this->_noMCrypt())
			$this->setPassword($password);
			$this->_salt     = $salt;
		}
		public function encrypt($decrypted) {
			if (!is_string($decrypted)) throw new \Exception("MCrypt only accepts strings", 219140853);
			srand();
			$key       = hash('SHA256', $this->_salt.$this->_password, true);
			try {
				$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
			} catch (\Exception $e) {
				throw new \Exception("mycrytp extension is not installed", 217142155);
			}
			$iv_base64 = rtrim(base64_encode($iv), '=');
			if (strlen($iv_base64) != 22) {
				throw new \Exception("Encryption failed.", 218141254);
			}
			$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted.md5($decrypted), MCRYPT_MODE_CBC, $iv));
        	return $iv_base64.$encrypted;
		}
		public function decrypt($encrypted) {
			if (!is_string($encrypted)) throw new \Exception("MCrypt only accepts strings", 219140853);
			$key       = hash('SHA256', $this->_salt.$this->_password, true);
			$iv_base64 = substr($encrypted, 0, 22);
			$iv        = base64_decode($iv_base64 . '==');
			if (strlen($iv) != 16) throw new \Exception("Decryption failed.", 218141302);
			$encrypted = substr($encrypted, 22);
			try {
				$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
			} catch (\Exception $e) {
				throw new \Exception("mycrytp extension is not installed", 217142155);
			}
			$hash      = substr($decrypted, -32);
			$decrypted = substr($decrypted,   0, -32);
			if (md5($decrypted) != $hash) throw new \Exception("Decryption failed.", 218141302);
			return $decrypted;
		}
		public function setPassword($password) {
			$this->_password = $password;
		}
		public function passwordSet() {
			return (!is_null($this->_password));
		}
	}
}