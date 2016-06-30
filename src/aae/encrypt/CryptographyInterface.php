<?php
/**
 *
 */
namespace aae\encrypt {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\security
	 */
	interface CryptographyInterface {
		public function __construct($password = null);
		public function encrypt($decrypted);
		public function decrypt($encrypted);
		public function setPassword($password);
		public function passwordSet();
	}
}