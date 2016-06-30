<?php
/**
 *
 */
namespace aae\dispatch\receiver {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch\receiver
	 */
	interface ReceiverInterface extends \arrayaccess {
		public function get($varName = false);
	}
}