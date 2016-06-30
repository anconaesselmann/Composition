<?php
/**
 *
 */
namespace aae\connect {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\connect
	 */
	class REQUEST implements TransmitterInterface {
		public function transmit($params) {
			$result = array();
			foreach ($params as $value) {
				$result[$value] = $_REQUEST[$value];
			}
	        return $result;
		}
	}
}