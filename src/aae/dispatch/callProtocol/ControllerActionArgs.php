<?php
/**
 *
 */
namespace aae\dispatch\callProtocol {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch\callProtocol
	 */
	class ControllerActionArgs implements \aae\dispatch\callProtocol\CallInterface, \JsonSerializable {
		protected $_controller, $_action, $_args, $_ip = null, $_publicKey = null;

		public function __construct($controller, $action, $args, $ip = NULL) {
			$this->setController($controller);
			$this->_action = $action;
			$this->_args = $args;
		}
		public function setController($controller) {
			$controller = ucfirst($controller);
			$this->_controller = str_replace("/", "\\", $controller);
			if (substr($this->_controller, 0,1) != "\\") {
				$this->_controller = "\\".$this->_controller;
			}
		}
		public function getController() {
			return $this->_controller;
		}
		public function getAction() {
			return $this->_action;
		}
		public function getArgs() {
			return $this->_args;
		}
		public function setIp($ip) {
			$this->_ip = $ip;
		}		
		public function getIp() {
			return $this->_ip;
		}
		public function setPublicKey($publicKey) {
			$this->_publicKey = $publicKey;
		}		
		public function getPublicKey() {
			return $this->_publicKey;
		}
		public function jsonSerialize() {
			$array = array("controller" => $this->_controller, "action" => $this->_action, "args" => $this->_args);
			if (!is_null($this->_ip)) {
				$array["ip"] = $this->_ip;
			}
			if (!is_null($this->_publicKey)) {
				$array["publicKey"] = $this->_publicKey;
			}
			return $array;
		}
	}
}