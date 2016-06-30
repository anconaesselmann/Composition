<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	class APICaller implements \aae\api\APICallerInterface {
		private $_transmissionService;

		public function __construct(\aae\api\TransmissionServiceInterface $transmissionService) {
			$this->_transmissionService = $transmissionService;
		}

		public function sendRequest(\aae\api\APIRequest $apiRequest, $appId = null) {
			return $this->_transmissionService->transmit($apiRequest, $appId);
		}
	}
}