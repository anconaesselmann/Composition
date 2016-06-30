<?php
/**
 *
 */
namespace aae\encrypt {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\security
	 */
	class PlainText implements \aae\encrypt\CryptographyInterface {
		public function __construct($password = null) {}
		public function encrypt($decrypted) {
			return $decrypted;
		}
		public function decrypt($encrypted) {
			return $encrypted;
		}		
		public function setPassword($password) {}
		public function passwordSet() {
			return true;
		}
	}
}