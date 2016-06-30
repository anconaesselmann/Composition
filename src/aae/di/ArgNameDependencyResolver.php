<?php
/**
 *
 */
namespace aae\di {
	class ServiceNotFoundException extends \Exception {}
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\di
	 */
	class ArgNameDependencyResolver implements DependencyResolverInterface {
		private $_rs;

		public function __construct($resolutionStrategy) {
			$this->_rs = $resolutionStrategy;
		}

		/**
		 * Resolves dependencies and for services not found resolves to NULL
		 */
		public function resolveAllowNoMatch($class, $method, $noMatch = NULL) {
			return $this->_resolve($class, $method, [], $noMatch, false);
		}
		public function resolve($class, $method, $overrides = array()) {
			return $this->_resolve($class, $method, $overrides, NULL, true);
		}

		protected function _resolve($class, $method, $overrides = array(), $noMatch = NULL, $throwException = false) {
			if (!is_string($class) && !is_null($class)) throw new \Exception("The class argument has to be of type string. Type '".get_class($class)."' given", 1109141901);
			$args     = array();
			if (!class_exists($class)) throw new \aae\di\ClassNotDefinedException("Class $class not defined", 1211141019);
			try {
				$contRefl = new \ReflectionMethod($class, $method);
			} catch (\ReflectionException $e) {
				throw new \Exception($e->getMessage(), 1109141941);
			}
			$params   = $contRefl->getParameters();
			foreach ($params as $param) {
			    $paramName = $param->getName();
			    foreach ($overrides as $oldParamName => $overRiddenParamName) {
			    	if ($paramName == $oldParamName) $paramName = $overRiddenParamName;
			    }
			    try {$paramVal = $this->_getService($paramName);}
			    catch (ServiceNotFoundException $e) {
			    	if ($throwException) throw $e;
			    	$paramVal = $noMatch;
			    }
			    $args[]        = $paramVal;
			}
			return $args;
		}

		private function _getService($serviceName, $className = NULL) {
			if (is_array($this->_rs)) {
				if (!array_key_exists($serviceName, $this->_rs)) throw new ServiceNotFoundException ("Could not locate service $serviceName", 1016141128);
				return $this->_rs[$serviceName];
			} else {
				return $this->_rs->build($serviceName);
			}
		}
	}
}