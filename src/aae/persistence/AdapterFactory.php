<?php
/**
 *
 */
namespace aae\persistence {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\persistence
	 */
	class AdapterFactory {
		
	
		/**
		 * __functionDescription__
		 * @param __type__ __parameterDescription__
		 */
		public function build($adapterName) {
			$adapter = new $adapterName();
			return $adapter;
		}
	}
}