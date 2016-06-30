<?php
/**
 *
 */
namespace aae\dispatch {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch
	 */
	class ControllerActionApi extends \aae\dispatch\Api {
		protected $_controllerAppend = "Controller", $_actionAppend = "Action";
		/*public function __construct(\aae\dispatch\Router $router, \aae\serialize\SerializerInterface $serializer, \aae\encrypt\CryptographyInterface $encrypter = null, \aae\log\Loggable $logger = null) {
			parent::__construct($router, $serializer, $encrypter, $logger);
		}*/
	}
}