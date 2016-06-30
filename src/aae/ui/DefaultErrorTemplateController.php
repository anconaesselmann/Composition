<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class DefaultErrorTemplateController extends \aae\ui\ViewController {
		public function defaultAction($errorMessage, $errorNbr) {
			return false;
		}
	}
}