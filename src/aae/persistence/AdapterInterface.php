<?php
/**
 *
 */
namespace aae\persistence {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\persistence
	 */
	interface AdapterInterface {
		public function persist($data, $settings);
	}
}