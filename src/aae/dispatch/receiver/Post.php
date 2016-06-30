<?php
/**
 *
 */
namespace aae\dispatch\receiver {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch\receiver
	 */
	class Post extends ReiceiverAbstract {
		public function __construct() {
			$this->_container = $_POST;
		}
	}
}