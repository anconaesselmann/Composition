<?php
/**
 *
 */
namespace aae\dispatch {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch
	 */
	class MessageDispatcher {
		private $_caller, $_receiver;
		public function __construct(\aae\dispatch\caller\CallerInterface $caller, \aae\dispatch\receiver\ReceiverInterface $receiver) {
			$this->_caller = $caller;
			$this->_receiver = $receiver;
		}
		public function dispatch(/*\aae\dispatch\TransmissionPackage */$transmissionPackage) {
			#print_r($transmissionPackage);
			$transmissionResult = $this->_caller->transmit(array("transmissionString" => $transmissionPackage));
			print_r($transmissionResult);
		}
	}
}