<?php
/**
 *
 */
namespace aae\dispatch {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch
	 */
	class CallDispatcher {
		private $_caller, $_serializer, $_encrypter;

		public function __construct(\aae\dispatch\caller\CallerInterface $caller, \aae\serialize\SerializerInterface $serializer, \aae\encrypt\CryptographyInterface $encrypter = null) {
			$this->_caller     = $caller;
			$this->_serializer = $serializer;
			$this->_encrypter  = $encrypter;
		}

		public function dispatch(\aae\dispatch\callProtocol\ControllerActionArgs $call) {
			if (!is_null($this->_encrypter)) {
				$transmissionPackage = $this->_getEncodedTransmissionPackage($call);
			} else {
				$transmissionPackage = $this->_getUnencodedTransmissionPackage($call);
			}
			$transmissionResult = $this->_caller->transmit($transmissionPackage);
			if (!is_null($this->_encrypter)) {
				$decrypted    = $this->_encrypter->decrypt($transmissionResult);
				$unserialized = $this->_serializer->unserialize($decrypted);
				$transmissionResult = $unserialized;
			}
			return $transmissionResult;
		}

		private function _getEncodedTransmissionPackage($call) {
			$transmissionPackage = array();
			$serialized = $this->_serializer->serialize($call);
			$transmissionPackage["transmissionString"] = $this->_encrypter->encrypt($serialized);
			$transmissionPackage["publicKey"] = $call->getPublicKey();
			return $transmissionPackage;
		}
		
		private function _getUnencodedTransmissionPackage($call) {
			$transmissionPackage = array();
			$args = $call->jsonSerialize();
			$transmissionPackage["controller"] = $call->getController();
			$transmissionPackage["action"] = $call->getAction();
			$transmissionPackage["args"] = $this->_serializer->serialize($call->getArgs());
			return $transmissionPackage;
		}
	}
}