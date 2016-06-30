<?php
/**
 *
 */
namespace aae\api {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 *
	 * Exposes all public methods on all classes as the API.
	 */
	class API implements \aae\api\APIInterface {
		use \aae\log\LoggableTrait;

		protected $_controllerAppend = "", $_actionAppend = "";


		public function __construct(\aae\api\TransmissionServiceInterface $transmissionService, \aae\log\Loggable $logger = null) {
			$this->setLogger($logger);
			$apiCallResult = array('response' => null);
			try {
				$apiRequest = $transmissionService->request();
				$apiCallResult['response'] = $this->_getApplicationResponse($apiRequest);
			} catch (\Exception $e) {
				$apiCallResult["errorNbr"] = $e->getCode();
				$this->logError($e);
			}
			$transmissionService->transmit($apiCallResult);
		}

		protected function _makeApplicationCall($controller, $action, $args) {
			$controllerName = get_class($controller);
			$result = null;
			if (method_exists($controller, $action)) {
				$result = call_user_func_array(array($controller, $action), $args);
				if ($result === 1) {
					//$result = call_user_func(array($controller, "getView"));
					$result = "Worked";
				}
			} else {
				$controllerName = get_class($controller);
				throw new \Exception("The method $controllerName->$action does not exist.", 214141546);
			}
			return $result;
		}
		
		protected function _getApplicationResponse($apiRequest) {
			$contFactory    = new \aae\std\SimpleFactory();
			$controllerName =  $apiRequest->getController().$this->_controllerAppend;
			$controller     =  $contFactory->build($controllerName);
			$action         =  $apiRequest->getAction().$this->_actionAppend;
			$args           =  $apiRequest->getArgs();

			return $this->_makeApplicationCall($controller, $action, $args);
		}
	}
}