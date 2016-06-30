<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class Application {
		use \aae\log\LoggableTrait;

		public function __construct($api, $serializer, $errorViewController, $timeZone) {
			$this->_api = $api;
			$this->_serializer = $serializer;
			$this->_errorViewController = $errorViewController;
			date_default_timezone_set($timeZone);
		}
		public function run() {
		    $response = $this->_serializer->unserialize($this->_api->run());
		    echo $response["response"];
		}
	}
}