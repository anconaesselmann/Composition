<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 *
	 * Exposes all public methods ending in "Action" in classes ending in "Controller" as API.
	 */
	class ControllerActionAPI extends \aae\api\API {
		
		protected $_controllerAppend = "Controller", $_actionAppend = "Action";
		public function __construct(\aae\api\TransmissionServiceInterface $transmissionService, \aae\log\Loggable $logger = null) {
			parent::__construct($transmissionService, $logger);
			print_r($_REQUEST);
		}
	}
}