<?php
/**
 *
 */
namespace aae\persistence {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\persistence
	 */
	class DataPersister {
		use \aae\abstr\Configurable;

		public $adapterFactory = null;

		public function __construct($configFileDir, $authenticationObj) {
			$this->initConfigurable($configFileDir);
			$this->adapterFactory = new AdapterFactory();
		}

		private function _initializeWithArray($configArray) {
			if (!array_key_exists("persistence", $configArray)) {
				throw new \Exception("Configuration data has to include a 'persistence' array.", 209141706);
			}
			$this->configs = $configArray;
		}
	
		/**
		 * __functionDescription__
		 * @param __type__ __parameterDescription__
		 */
		public function persist($data) {
			$dataType = get_class($data);
			$adapterSettings = null;

			if (!array_key_exists($dataType, $this->configs['persistence'])) {
				throw new \Exception("No adapter specified in configurations for data of type '$dataType'", 209141719);
			}
			$adapterName = $this->configs['persistence'][$dataType];

			if (is_array($adapterName)) {
				$adapterName = $this->configs['persistence'][$dataType]['adapter'];

				if (is_array($adapterName)) {
					$adapterName = $this->configs['persistence'][$dataType]['adapter']['class'];
					if (array_key_exists("settings", $this->configs['persistence'][$dataType]['adapter'])) {
						$adapterSettings = $this->configs['persistence'][$dataType]['adapter']['settings'];
					}
				}
			}

			$adapter = $this->adapterFactory->build($adapterName);

			if (!$adapter instanceof \aae\persistence\AdapterInterface) {
				throw new \Exception("The adapter for '$dataType' does not implement the interface '\\aae\\persistence\\AdapterInterface'", 209141836);
			}
			
			$result = $adapter->persist($data, $adapterSettings);
			return $result;
		}
	}
}