<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class DefaultErrorViewController extends \aae\ui\ViewController {
		public function defaultAction($errorMessage, $errorNbr) {
			$this->useTemplate("ErrorView");
            $this->template["errorMessage"] = $errorMessage;
		}
	}
}