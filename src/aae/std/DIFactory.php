<?php
/**
 *
 */
namespace aae\std {
	class DIFactoryException extends \Exception {}
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\std
	 */
	class DI_Exception extends \aae\std\BuildException {}
	class DIFactory {
		protected $_assoc;
		protected $_staticInstances = array();

		protected $_parentAssocName = null;
		protected $_actualClassName = null;
		protected $_factory;

		public function __construct($configuration) {
			$this->_assoc   = $configuration;
			$this->_factory = new \aae\std\SimpleFactory();
		}
		public function get($varName) {
			return $this->_assoc[$varName];
		}
		public function build($instanceName) {
			if (!is_null($this->_parentAssocName)) {
				$this->_actualClassName = $instanceName;
				$instanceName = $this->_parentAssocName;
			}
			if (!array_key_exists($instanceName, $this->_assoc)) {
				try {
					$prototypeClassName = "\\aae\\prototype\\".ucfirst($instanceName);
					return $this->_getNonStaticInstance($prototypeClassName, []);
				} catch (DI_Exception $e) {
					throw new DIFactoryException("Dependency \"$instanceName\" could not be created, no entry found in the container configuration file. For prototyping, create a class '$prototypeClassName'", 1016141133);
				}
			}

			$depConfig = $this->_assoc[$instanceName];
			if (is_array($depConfig) && array_key_exists("class", $depConfig)) {
				$className = $this->_getClassName($depConfig);
				$args = $this->_getArguments($depConfig);
				if (array_key_exists("static", $depConfig)) {
					$result = $this->_getStaticInstance($className, $args);
				} else {
					$result = $this->_getNonStaticInstance($className, $args);
				}
				$this->_callSetters($result, $depConfig);
				if (array_key_exists("evaluate", $depConfig)) {
					$result = $this->_getEvaluation($result, $depConfig);
				}
			} else {
				$result = $depConfig;
			}
			return $result;
		}

		public function extend($parentInstanceName) {
			if (is_null($this->_parentAssocName)) {
				$this->_parentAssocName = $parentInstanceName;
			}
			return $this;
		}

		private function _getEvaluation($instance, $depConfig) {
			$evalFunctionName = $this->_getEvalFunctionName($depConfig);
			$args = $this->_getEvalFunctionArgArray($depConfig);
			return call_user_func_array(array($instance, $evalFunctionName), $args);
		}
		private function _getEvalFunctionArgArray($depConfig) {
			if (is_string($depConfig["evaluate"])) return array();
			else return $this->_getSetterArgArray($depConfig["evaluate"]);
		}
		private function _getEvalFunctionName($depConfig) {
			if (is_string($depConfig["evaluate"])) {
				$evalFunctionName = $depConfig["evaluate"];
			} else {
				reset($depConfig["evaluate"]);
				$evalFunctionName = key($depConfig["evaluate"]);
			}
			return $evalFunctionName;
		}

		private function _callSetters($instance, $depConfig) {
			if (array_key_exists("setters", $depConfig)) {
				foreach ($depConfig["setters"] as $setter) {
					if (is_string($setter)) {
						$instance->$setter();
					} else {
						$setterName = $this->_getSetterName($setter);
						$args = $this->_getSetterArgArray($setter);
						call_user_func_array(array($instance, $setterName), $args);
					}
				}
			}
		}
		private function _getSetterName($setter) {
			reset($setter);
			return key($setter);
		}
		private function _getSetterArgArray($setter) {
			reset($setter);
			$setterName = key($setter);
			$args = $setter[$setterName];
			if (!is_array($args)) {
				$args = array($args);
			}
			if (array_key_exists("dep", $args)) {
				$args = array($this->_resolveDependency($args));
			} else {
				for ($i=0; $i < count($args); $i++) {
					$args[$i] = $this->_resolveDependency($args[$i]);
				}
			}
			return $args;
		}

		private function _getClassName($depConfig) {
			if (!is_null($this->_parentAssocName)) {
				$className = $this->_actualClassName;
			} else {
				$className = $depConfig["class"];
			}
			return implode("\\", explode("/", $className));
		}

		private function _getNonStaticInstance($className, $args) {
			try {
				$instance = $this->_factory->buildWithArgsArray($className, $args);
			} catch (\aae\std\BuildException $e) {
				throw new DI_Exception("Dependency $className could not created.", 1017141654);
			}

			return $instance;
		}

		private function _getStaticInstance($className, $args) {
			if (array_key_exists($className, $this->_staticInstances)) {
				$instance = $this->_staticInstances[$className];
			} else {
				$instance = $this->_getNonStaticInstance($className, $args);
				$this->_staticInstances[$className] = $instance;
			}
			return $instance;
		}

		private function _getArguments($depConfig) {
			if (! array_key_exists("args", $depConfig)) return array();
			$args = $depConfig["args"];
			for ($i=0; $i < count($args); $i++) {
				$args[$i] = $this->_resolveDependency($args[$i]);
			}
			return $args;
		}

		private function _resolveDependency($arg) {
			if (is_array($arg) && array_key_exists("dep", $arg)) {
				$arg = $this->build($arg["dep"]);
			}
			return $arg;
		}
	}
}