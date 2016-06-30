<?php
/**
 *
 */
namespace aae\cms {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\cms
	 */
	class ControllerFactory extends \aae\std\DIFactory {
		protected $_namespace, $_parentId;
		public function __construct($configuration, $namespace, $parentId = Null) {
			parent::__construct($configuration);
			$this->_namespace = $namespace;
			$this->_parentId = $parentId;
		}

		public function build($instanceName) {

		}
	}
}