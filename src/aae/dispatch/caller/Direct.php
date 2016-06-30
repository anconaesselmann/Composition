<?php
/**
 *
 */
namespace aae\dispatch\caller {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch\caller
	 */
	class Direct implements \aae\dispatch\caller\CallerInterface {
		use \aae\log\LoggableTrait;

		public function __construct($controllerActionApi, $serializer) {
			$this->_controllerActionApi = $controllerActionApi;
			$this->_serializer = $serializer;
		}

		public function transmit($params) {
	        return $this->_serializer->unserialize($this->_controllerActionApi->run());
		}
	}
}