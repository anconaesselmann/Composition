<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	class APIRequest implements \JsonSerializable {
		protected $_controller;
		protected $_action;
		protected $_args;

		public function __construct($controller, $action, $args) {
			$this->setController($controller);
			$this->_action = $action;
			$this->_args = $args;
		}
		public function setController($controller) {
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
		public function jsonSerialize() {
			return array("controller" => $this->_controller, "action" => $this->_action, "args" => $this->_args);
		}
	}
}