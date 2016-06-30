<?php
/**
 *
 */
namespace aae\dispatch\caller {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch\caller
	 */
	interface CallerInterface {
		public function transmit($params);
	}
}