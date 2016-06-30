<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	interface TemplateBuilderInterface {
		
		public function getHtmlTemplate($templateName);
		public function getDomTemplate($templateName);
	}
}