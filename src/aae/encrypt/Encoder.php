<?php
/**
 *
 */
namespace aae\encrypt {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\connect
	 */
	class Encoder {
		private $_serializer, $_encrypter;
		public function __construct(\aae\serialize\SerializerInterface $serializer, \aae\encrypt\CryptographyInterface $encrypter) {
			$this->_encrypter  = $encrypter;
			$this->_serializer = $serializer;
		}
		public function encode($package) {
			$serializedPackage = $this->_serializer->serialize($package);
			$encyptedPackage   = $this->_encrypter->encrypt($serializedPackage);
			return $encyptedPackage;
		}
		/**
		 * Returns the unserialized, decrypted $encodedPackage
		 * @param  string $encodedPackage A serialized and encrypted package
		 * @return primitive              The unserialized and decrypted $encodedPackage
		 */
		public function decode($encodedPackage) {
			$serializedPackage = $this->_encrypter->decrypt($encodedPackage);
			return $this->_serializer->unserialize($serializedPackage);
		}
	}
}