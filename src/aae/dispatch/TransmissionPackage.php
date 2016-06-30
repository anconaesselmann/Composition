<?php
/**
 *
 */
namespace aae\dispatch {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch
	 */
	class TransmissionPackage implements \JsonSerializable {
		private $_transmissionString, $_errorCode = null;
		public function __construct($transmissionString = null) {
			$this->setTransmissionString($transmissionString);
		}
		public function getTransmissionString() {
			return $this->_transmissionString;
		}
		public function setTransmissionString($transmissionString) {
			$this->_transmissionString = $transmissionString;
		}
		public function getErrorCode() {
			return $this->_errorCode;
		}
		public function setErrorCode() {
			$this->_errorCode = $errorCode;
		}

		public function jsonSerialize() {
			return array("transmissionString" => $this->_transmissionString, "errorCode" => $this->_errorCode);
		}
	}
}