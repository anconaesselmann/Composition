<?php
/**
 *
 */
namespace aae\security {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\security
	 */
	class PlainText implements \aae\security\CryptographyInterface {
		public function __construct($password = null) {}
		public function encrypt($decrypted) {
			return $decrypted;
		}
		public function decrypt($encrypted) {
			return $encrypted;
		}
	}
}