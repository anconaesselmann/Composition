<?php
/**
 *
 */
namespace aae\std {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\std
	 */
	class SimpleFactory {
		private $_className = null, $_args = null, $_dependencyResolver;

		public function __construct(\aae\di\DependencyResolverInterface $dependencyResolver = NULL) {
			$this->_dependencyResolver = $dependencyResolver;
		}

		public function build() {
			$this->_args      = func_get_args();
			$this->_className = array_shift($this->_args);
			if ($this->_classIsNotDefined())
				throw new BuildException(sprintf("The class '%s' is not defined.", $this->_className), 211141513);

			return ($this->_constructorArgumentsGiven())
				? $this->_buildWithConstructorArguments()
				: $this->_buildWithoutConstructorArguments();
		}
		private function _constructorArgumentsGiven() {
			return count($this->_args) > 0;
		}

		private function _classIsNotDefined() {
			return !class_exists($this->_className);
		}

		private function _buildWithoutConstructorArguments() {
			$className = $this->_className;
			return new $className();
		}

		private function _buildWithConstructorArguments() {
			$reflect  = new \ReflectionClass($this->_className);
			#if (!method_exists($reflect, "__construct")) {
			#	$this->_buildWithoutConstructorArguments();
			#}
			return $reflect->newInstanceArgs($this->_args);
		}

		public function buildWithArgsArray($className, $constructorArgs) {
			if (!is_string($className) || !is_array($constructorArgs)) {
				throw new \Exception(sprintf("Arguments have to be a string and an array, %s and %s given.", gettype($className), gettype($constructorArgs)), 227141322);
			}
			$arguments = array_merge(array($className), $constructorArgs);
			return call_user_func_array(array($this, "build"), $arguments);
		}

		public function buildWithResolvedDependencies($className, $overrides = array()) {
			$dependencies = $this->_dependencyResolver->resolve($className, "__construct", $overrides);
			array_unshift($dependencies, $className);
			return call_user_func_array(array($this, "build"), $dependencies);
		}
	}
}