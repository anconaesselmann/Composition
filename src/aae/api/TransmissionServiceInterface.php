<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	interface TransmissionServiceInterface {
		public function transmit($response);
		public function request();
	}
}