<?php
/**
 *
 */
namespace aae\connect {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\connect
	 */
	class TransmissionPackage {
		private $_id, $_transmissionString;

		public function __construct($transmissionString, $id = null) {
			$this->_transmissionString = $transmissionString;
			$this->_id = $id;
		}
		public function getId() {
			return $this->_id;
		}
		public function setId($id) {
			$this->_id = $id;
		}
		public function getTransmissionString() {
			return $this->_transmissionString;
		}
		public function getParameters() {
			return array("id" => $this->_id, "transmissionString" => $this->_transmissionString);
		}
	}
}