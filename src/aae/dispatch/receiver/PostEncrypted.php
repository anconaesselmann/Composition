<?php
/**
 *
 */
namespace aae\dispatch\receiver {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch\receiver
	 */
	class PostEncrypted  extends Post {
		private $_encrypter, $_getPasswordCallback = NULL, $_getPasswordcallbackArgs;

		public function __construct($encrypter, $getPasswordCallback = NULL) {
			parent::__construct();
			$this->_encrypter = $encrypter;
			if (!is_null($getPasswordCallback)) $this->setGetPasswordCallback($getPasswordCallback);
		}
		public function setGetPasswordCallback($getPasswordCallback, $getPasswordcallbackArgs = array()) {
			$this->_getPasswordCallback = $getPasswordCallback;
			if (!is_array($getPasswordcallbackArgs)) {
				$getPasswordcallbackArgs = array($getPasswordcallbackArgs);
			}
			$this->setGetPasswordCallbackArgs($getPasswordcallbackArgs);
		}
		public function setGetPasswordCallbackArgs($getPasswordcallbackArgs = array()) {
			$this->_getPasswordcallbackArgs = $getPasswordcallbackArgs;
		}

		public function offsetGet($offset) {
			if (!$this->_encrypter->passwordSet()) {
				if (is_null($this->_getPasswordCallback)) throw new \Exception("No password set for ".get_class($this->_encrypter)." and no callback provided.", 1017141520);
				$password = call_user_func_array($this->_getPasswordCallback, $this->_getPasswordcallbackArgs);
				$this->_encrypter->setPassword($password);
			}
			return isset($this->_container[$offset]) ? $this->_encrypter->decrypt($this->_container[$offset]) : null;
		}
	}
}