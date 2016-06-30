<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	interface APICallerInterface {
		public function sendRequest(\aae\api\APIRequest $apiRequest);
	}
}