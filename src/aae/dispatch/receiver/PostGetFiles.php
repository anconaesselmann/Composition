<?php
/**
 *
 */
namespace aae\dispatch\receiver {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch\receiver
	 */
	class PostGetFiles extends ReiceiverAbstract {
        public function __construct() {
            $this->_container = array_merge($_GET, $_POST, $_FILES);
        }
	}
}