<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	interface APIInterface {
		public function __construct(\aae\api\TransmissionServiceInterface $transmissionService, \aae\log\Loggable $logger);
	}
}