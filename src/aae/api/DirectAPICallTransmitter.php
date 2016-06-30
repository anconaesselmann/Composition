<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	class DirectAPICallTransmitter implements \aae\api\TransmissionServiceInterface {
		use \aae\log\LoggableTrait;

		private $_apiClassName;

		public function __construct($apiClassName) {
			$this->_apiClassName = $apiClassName;
		}
		public function transmit($message, $id = null) {
			$factory     = new \aae\std\SimpleFactory();

			$transmissionHelper = new \aae\api\DirectAPICallTransmissionHelper();
			$transmissionHelper->transmit($message);
			
			$apiInstance = $factory->build(
				$this->_apiClassName,
				$transmissionHelper, 
				$this->_logger);
			return $transmissionHelper->request();
		}
		public function request(){}
	}

	class DirectAPICallTransmissionHelper implements \aae\api\TransmissionServiceInterface {
		private $_result;

		public function transmit($message, $id = null) {
			$this->_result = $message;
			return $this->_result;
		}
		public function request() {
			return $this->_result;
		}
	}
}