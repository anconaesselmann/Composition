<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class MarkdownViewController extends ViewController {
		public $templateName     = "MarkdownView";
		public $idName           = "main";
		public $markdownSettings = false;
		public $markdownFileName = "";

		public function defaultAction() {
			$mdFileName = $this->markdownFileName;
			if ($mdFileName[0] != DIRECTORY_SEPARATOR) $mdFileName = $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.$mdFileName;
			$md          = new Markdown($this->markdownSettings);
   			$htmlContent = $md->fileGetContentsAsHtml($mdFileName);

			$this->loadTemplate($this->templateName);
			$this->template["main"] = new \aae\ui\encoding\IsHtml($htmlContent);
		}
	}
}