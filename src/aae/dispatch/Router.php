<?php
/**
 *
 */
namespace aae\dispatch {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch
	 */
	class Router {
		private $_receiver;

		public function __construct(receiver\ReceiverInterface $receiver, $encrypter = null) {
			$this->_receiver = $receiver;
		}
		public function route() {
			/*if (!is_null($this->_dispatcher)) {
				$call = $this->getCall();
				$this->_dispatcher($call);
			}*/
		}
		public function getCall() {
			$args = null;

			$controllerName = (isset($this->_receiver["controller"])) ? $this->_receiver["controller"] : null;
			$actionName     = (isset($this->_receiver["action"]))     ? $this->_receiver["action"]     : null;
			if (isset($this->_receiver["args"])) {
				if (strlen($this->_receiver["args"]) === 0) {
					$args = [];
				} else {
					# all elements after the action that are seperated by / are
					# turned into variables.
					$args = explode("/", $this->_receiver["args"]);
					for ($i=0; $i < count($args); $i++) {
						if (strlen($args[$i]) === 0) $args[$i] = NULL;
					}
					if (count($args) > 0 && is_null($args[count($args) - 1])) {
						array_pop($args);
					}
				}
			}
			$call = new \aae\dispatch\callProtocol\ControllerActionArgs($controllerName, $actionName, $args);
			if (isset($_SERVER["REMOTE_ADDR"])) {
				$call->setIp($_SERVER["REMOTE_ADDR"]);
			}
			return $call;
		}
		public function makeCall() {

		}
		public function getRequestArray() {
			return $this->_receiver->getRequestArray();
		}
	}
}