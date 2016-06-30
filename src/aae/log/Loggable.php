<?php
/**
 *
 */
namespace aae\log {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\log
	 */
	interface Loggable {
		public function log($message, $eventType);
	}
}