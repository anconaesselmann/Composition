<?php
/**
 *
 */
namespace aae\dispatch {
	use \aae\dispatch\Router                as Router;
	use \aae\serialize\SerializerInterface  as SerializerInterface;
	use \aae\encrypt\CryptographyInterface  as CryptographyInterface;
	use \aae\log\Loggable                   as Loggable;
	use \aae\di\DependencyResolverInterface as DependencyResolverInterface;

	use \aae\ui\Markdown as Markdown;
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch
	 *
	 * Exposes all public methods on all classes as the API.
	 */
	class Api /*implements \aae\dispatch\ApiInterface */{
		use \aae\log\LoggableTrait;

		protected $_controllerAppend = "", $_actionAppend = "", $_controller;

		private $_router, $_dependencyResolver, $_serializer = null, $_encrypter = null, $_response = null, $_templateDependencyName = null;

		public $headers = null;

		public function __construct(
			Router $router,
			DependencyResolverInterface $dependencyResolver,
			SerializerInterface $serializer,
			CryptographyInterface $encrypter = null,
			Loggable $logger                 = null)
		{
			$this->setLogger($logger);
			$this->_router     = $router;
			$this->_dependencyResolver = $dependencyResolver;
			$this->_serializer = $serializer;
			$this->_encrypter  = $encrypter;

			set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
			    if (0 === error_reporting()) return false;
			    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
			});
		}

		public function setControllerEnding($string) {
			$this->_controllerAppend = $string;
		}

		public function run() {
			$apiCallResult = array('response' => false, 'errorCode' => 0);
			try {
				$apiRequest                 = $this->_router->getCall();
				$response                   = $this->_getApplicationResponse($apiRequest);
				$apiCallResult['response']  = $response;
				$apiCallResult['errorCode'] = $this->_controller->getErrorCode();
			} catch (\Exception $e) {
				$apiCallResult["errorCode"] = $e->getCode();
				$apiCallResult["errorMessage"] = $e->getMessage();
				$this->_logError($e);
				// TODO who provides the default error? also remove from applicaiton!
				try {
					$apiRequest = new \aae\api\APIRequest('\aae\ui\DefaultError', '', [$e->getMessage(), $e->getCode()]);
					$response   = $this->_getApplicationResponse($apiRequest);
				} catch (\Exception $e) {
					 $response = "View controller error ".$e->getCode()." with message: ".$e->getMessage();
				}

				$apiCallResult['response']  = $response;
			}
			$response = $this->_serializer->serialize($apiCallResult);
			if ($this->_encrypter) {
				$response = $this->_encrypter->encrypt($response);
			}
			if (!is_null($this->headers)) {
				$this->headers->send();
			}
			return $response;
		}

		protected function _getApplicationResponse($apiRequest) {
			$actionName     = $apiRequest->getAction();
			$controller     = $this->_getController($apiRequest->getController());
			$actionReturn   = $this->_callControllerAction($controller, $actionName, $apiRequest->getArgs());
			if ($actionReturn === NULL) {
				# The action did not explicitly return anything
				# TODO: this is going to be changed once controllers do not interact directly with templates any more
				$actionReturn  = call_user_func(array($controller,"getView"));
			}
			return $actionReturn;
		}
		protected function _getActionDependencies($controller, $action) {
			$dependencyResolver = new \aae\di\ArgNameDependencyResolver($this->_router->getRequestArray());
			return $dependencyResolver->resolveAllowNoMatch(get_class($controller), $action);
		}
		protected function _logError($e) {
			if ($this->_logger) $this->_logger->logError($e);
		}
		protected function _logDebug($message) {
			if ($this->_logger) $this->_logger->logDebug($message);
		}
		protected function _getControllerName($controllerName) {
			return $controllerName.$this->_controllerAppend;
		}
		protected function _getTemplateControllerName($controllerName) {
			return $controllerName."TemplateController";
		}
		protected function _getFactory() {
			return new \aae\std\SimpleFactory($this->_dependencyResolver);
		}
		protected function _injectAdditionalDependencies($controller) {
			if (method_exists($controller, "dependencies")) {
				$dependencies = $this->_dependencyResolver->resolve(get_class($controller), "dependencies");
				call_user_func_array(array($controller, "dependencies"), $dependencies);
			}
		}
		protected function _getController($controllerName) {
			$controllerFactory = $this->_getFactory();
			$substituteDependencyNames = (is_null($this->_templateDependencyName)) ? [] : ["template" => $this->_templateDependencyName];
			try {
				$controller = $controllerFactory->buildWithResolvedDependencies(
					$this->_getControllerName($controllerName),
					$substituteDependencyNames
				);
			} catch (\aae\di\ClassNotDefinedException $e) {
				// TODO: Allow settings to disallow automatic template controller loading when no specific view controller is present
				try {
					$controller = $controllerFactory->buildWithResolvedDependencies(
						$this->_getTemplateControllerName($controllerName),
						$substituteDependencyNames
					);
				}
				catch (\aae\di\ClassNotDefinedException $e){
					$dynamicresolver = new DynamicResolver();
					$dynamicControllerName = $dynamicresolver->getControllerName($controllerName);
					$controller = $controllerFactory->buildWithResolvedDependencies(
						$dynamicControllerName,
						$substituteDependencyNames
					);
					$dynamicresolver->resolve($controller);
				}
			}
			$this->_injectAdditionalDependencies($controller);
			$this->_controller = $controller;
			return $controller;
		}
		public function setTemplateDependencyName($dependcyName) {
			$this->_templateDependencyName = $dependcyName;
		}
		protected function _getFullActionName($controller, $action) {
			$actionEndings = $controller->getActionEndings();
			if (is_array($actionEndings)) {
				foreach ($actionEndings as $ending) {
					$fullActionName = $action.$ending;
					if (method_exists($controller, $fullActionName)) return $fullActionName;
				}
				throw new \Exception("The method ".get_class($controller)."->$fullActionName does not exist.", 214141546);
			} else {
				return $action.$actionEndings;
			}
		}
		protected function _callControllerAction($controller, $action, $args) {
			if (is_null($action) || strlen($action) < 1) $action = "default";

			$fullActionName = $this->_getFullActionName($controller, $action);
			$actionArgs     = $this->_getActionDependencies($controller, $fullActionName);
			# arguments in URI part have precedence over query string variables
			for ($i=0; $i < count($args); $i++) {
				if (!array_key_exists($i, $actionArgs) ||
					(is_null($actionArgs[$i]) && !is_null($args[$i])))
				{
					$actionArgs[$i] = $args[$i];
				}
			}
			$controllerReturn = (count($actionArgs) > 0)
				? call_user_func_array(array($controller, $action), $actionArgs)
				: call_user_func(      array($controller, $action));
			if (property_exists($controller, "headers")) {
				$this->headers = $controller->headers;
			}
			return $controllerReturn;
		}
	}

	class DynamicResolutionException extends \Exception {}
	class DynamicResolver {
		private $_directories = ["anconaesselmann/content", "anconaesselmann/content/projects"];
		private $_extension = ".md";
		private $_validDir = false;

		public function getControllerName() {
			$args = func_get_args();
			$fileName = \aae\std\std::classFromNSClassName(strtolower($args[0])).$this->_extension;
			foreach ($this->_directories as $dir) {
				$fullDir = $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$fileName;
				if (file_exists($fullDir)) {
					$this->_validDir = $fullDir;
		            return "aae\ui\MarkdownViewController";;
				}
			}
			throw new DynamicResolutionException("Could not resolve dynamically", 627161632);
		}
		public function resolve($controller) {
			$controller->markdownFileName = $this->_validDir;
			$controller->markdownSettings = Markdown::ALLOW_HTML | Markdown::DISPLAY_ATTRIBUTION;
			return $controller;
		}
	}
}