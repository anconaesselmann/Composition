<?php
/**
 *
 */
namespace aae\connect {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\connect
	 */
	class EncodedTransmitter implements \aae\api\TransmissionServiceInterface {
		use \aae\util\LoggableTrait;

		private $_senderId;

		public function __construct(\aae\connect\TransmitterInterface $transmitter, \aae\security\Encoder $encoder, $senderId = null) {
			$this->_encoder     = $encoder;
			$this->_transmitter = $transmitter;
			$this->_senderId    = $senderId;
		}
		public function transmit($message) {
			$encodedMessage = $this->_encoder->encode($message);
			$transmPackage  = new \aae\connect\TransmissionPackage($encodedMessage, $this->_senderId);
			$response = $this->_transmitter->transmit($transmPackage->getParameters());
			return $this->_encoder->decode($response);
		}

		public function request(){}
	}
}