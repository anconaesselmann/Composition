<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	interface TemplateInterface {
		public function load($templateName = null);
        public function loadFromFile($path);
        public function setLocalizer($localizer);
        public function getBaseDir();
        public function arraySet($array);
	}
}