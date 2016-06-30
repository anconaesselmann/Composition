<?php
/**
 *
 */
namespace aae\di {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\di
	 */
	class ConfigurableContainer {
		protected $_services = array(), $_settings = array(), $_configAssoc;

		public function __construct($path, $serializer = null) {
			$file = new \aae\fs\File($path);
			$serializer = new \aae\serialize\Json();
			$this->_serializer = new \aae\serialize\FileSerializer($serializer);
			$this->_configAssoc = $this->_serializer->unserialize($path);
		}
		
		public function setService($serviceName, $defaultServiceInstanceName, $constructorArguments = array()) {
			$this->_services[$serviceName] = new ServiceEntry($constructorArguments, $defaultServiceInstanceName);
		}
		public function setSetting($settingName, $settingDefault = null) {
			$this->_settings[$settingName] = $settingDefault;
		}
		public function getSetting($settingName) {
			if (!array_key_exists($serviceName, $this->_settings)) {
				throw new Exception("The setting was not set.", 227141622);
			}
			if (array_key_exists($settingName, $this->_configAssoc)) {
				return $this->_configAssoc[$settingName];
			} else {
				return $this->_settings[$serviceName];
			}
		}
		public function getService($serviceName) {
			if (!array_key_exists($serviceName, $this->_services)) {
				throw new \Exception("The service '$serviceName' is not defined", 227141445);
			}
			$serviceEntry = $this->_services[$serviceName];

			$factory = new \aae\std\SimpleFactory();
			if (array_key_exists($serviceName, $this->_configAssoc)) {
				$serviceInstanceName = $this->_configAssoc[$serviceName];
			} else {
				$serviceInstanceName = $serviceEntry->getdefaultServiceInstanceName();
			}
			if (method_exists($serviceInstanceName, "__construct")) {
				$instance = $factory->buildWithArgsArray($serviceInstanceName, $serviceEntry->getConstructorArgs());
			} else {
				$instance = $factory->build($serviceInstanceName);
			}
			return $instance;
		}
	}
	Class ServiceEntry {
		public $constructorArguments = "";
		public $defaultServiceInstanceName = "";
		public function __construct($constructorArguments, $defaultServiceInstanceName) {
			$this->constructorArguments = $constructorArguments;
			$this->defaultServiceInstanceName = $defaultServiceInstanceName;
		}
		public function getdefaultServiceInstanceName() {
			return $this->defaultServiceInstanceName;
		}
		public function getConstructorArgs() {
			return $this->constructorArguments;
		}
	}
}