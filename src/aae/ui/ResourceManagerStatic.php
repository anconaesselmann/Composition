<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class ResourceManagerStatic implements ResourceManagerInterface {
		protected $_baseDir = "", $_modRewriteDocumentRoot = "", $_jsBaseDirs = [], $_cssBaseDirs = [];

		protected $_linkedFiles = array();
		protected $_links = array();

		public function __construct() {

		}


		public function addResource($dependency) {
			$resources = null;
			if (is_array($dependency) && array_key_exists("link", $dependency)) {
				$link = $dependency["link"];
				if (array_key_exists("resources", $dependency)) {
					$resources = $dependency["resources"];
				}
			} else if (is_string($dependency)) {
				$link = $dependency;
			} else {
				throw new \Exception("Invalid Resource declaration", 509141209);
			}
			$this->addLink($link, $resources);
		}

		public function addLink($link) {
			$this->_links[] = $link;
		}

		public function getLinks() {
			return $this->_links;
		}

		public function getHtmlLink() {
			$links = $this->getLinks();
			$result = "";
			foreach ($links as $link) {
				if (pathinfo($link, PATHINFO_EXTENSION) == "js") {
					$result .= '<script type="text/javascript" src="'.$link.'"></script>';
				} else if (pathinfo($link, PATHINFO_EXTENSION) == "css") {
					$result .= '<link rel="stylesheet" type="text/css" href="'.$link.'" />';
				}
				$result .= "\n";
			}
			return $result;
		}

		public function setBaseDir($baseDir) {
			$this->_baseDir = $baseDir;
		}
		public function addJsBaseDir($jsBaseDir) {
			$this->_jsBaseDirs[] = $jsBaseDir;
		}
		public function addCssBaseDir($cssBaseDir) {
			$this->_cssBaseDirs[] = $cssBaseDir;
		}
		public function setModRewriteDocumentRoot($modRewriteDocumentRoot) {
			$this->_modRewriteDocumentRoot = $modRewriteDocumentRoot;
		}
	}
}