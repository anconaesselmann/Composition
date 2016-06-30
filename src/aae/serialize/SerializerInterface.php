<?php
/**
 *
 */
namespace aae\serialize {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\std
	 */
	interface SerializerInterface {
		public function serialize($instance);
		public function unserialize($string);
	}
}