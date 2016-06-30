<?php
/**
 *
 */
namespace aae\security {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\security
	 */
	interface CryptographyInterface {
		public function __construct($password);
		public function encrypt($decrypted);
		public function decrypt($encrypted);
	}
}