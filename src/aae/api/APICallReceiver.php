<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	class APICallReceiver implements \aae\api\TransmissionServiceInterface {
		use \aae\util\LoggableTrait;

		private $_encoder, $_transmitter;

		public function __construct(\aae\connect\TransmitterInterface $transmitter, \aae\security\Encoder $encoder, $senderId = null) {
			$this->_encoder     = $encoder;
			$this->_transmitter = $transmitter;
			#$this->_senderId    = $senderId;
		}

		public function transmit($message, $id = null) {
			$encoded = $this->_encoder->encode($message);
			echo $encoded;
		}
		public function request() {
			$params       = $this->_transmitter->transmit(array("id", "transmissionString"));
			$transmString = $params["transmissionString"];
			$request      = $this->_encoder->decode($transmString);
			$apiRequest   = new \aae\api\APIRequest(
				$request["controller"],
				$request["action"],
				$request["args"]);
			return $apiRequest;
		}
	}
}