<?php
/**
 *
 */
namespace aae\dispatch {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch
	 */
	class DirectRouter extends \aae\dispatch\Router {
		public function __construct(receiver\ReceiverInterface $receiver, $encrypter = null, $params = array()) {
			parent::__construct($receiver, $encrypter);
			$this->_source = $params;
		}
	}
}