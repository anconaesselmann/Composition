<?php
/**
 * This file uses the following Classes that are declared in other files:
 * 		\aae\ui\Document
 *
 *
 * For license information view the license file that is part of this distribution.
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class _old_Template extends \aae\ui\Document {
		private $_assoc, $_baseDir = "", $_modRewriteDocumentRoot = "", $_jsBaseDir = "", $_cssBaseDir = "";

		/**
		 *
		 * Template is a Document that can build itself from multiple template
		 * source files. An associative array passed in the constructor dictates
		 * the structure of the final HTML DOM. The document structure is not flat,
		 * elements can have child elements listed in an array with a "children"
		 * key. Those elements are inserted into their respective parent tags
		 * by CSS id. The parent's CSS id's are located in each child's "id" key.
		 * The template files for each element to be loaded should be provided
		 * with a "template" key.
		 *
		 * Unless a "base_dir" is present ( see load() ), paths cant be relative.
		 * Paths start one directory below the document root.
		 *
		 *
		 * The document.html file has a div with a "top" id
		 * and a div with a "main" id.
		 * <body>
		 *   <div id="top"></div>
		 *   <div id="top"></div>
		 * </body>
		 *
		 * Both, top.html and main.html have valid HTML inside <body> tags.
		 *
		 * {
		 *   "template": "/protected/templates/document.html",
		 *   "children":
		 *     [
		 *       {
		 *         "div": "top",
		 *         "template": "/protected/templates/top.html"
		 *       },
		 *       {
		 *         "div": "main",
		 *         "template": "/protected/templates/main.html"
		 *       }
		 *     ]
		 *   }
		 * }
		 *
		 * (For readability the associative array is displayed in JSON)
		 *
		 * The example above builds the following document structure:
		 *
		 * <body>
		 *   <div id="top">
		 *     <div>Content of child nodes of <body> tag in top.html</div>
		 *   </div>
		 *   <div id="top">
		 *     <div>Content of child nodes of <body> tag in main.html</div>
		 *   </div>
		 * </body>
		 *
		 *
		 * @param array  $assoc template configuration
		 * @param boolean $load true build the template during instantiation
		 */
		public function __construct($assoc = null, \aae\ui\ResourceManagerInterface $resourceManager = null, $load = true) {
			$this->_assoc = $assoc;
			parent::__construct(null, $resourceManager);
			if ($load) $this->load();
		}

		/**
		 *
		 * The loading of Template from a configuration file can be separated from
		 * instantiation. To take advantage of this feature, pass false as a
		 * second argument to the Template constructor.
		 *
		 * If load() is used, a specific template in the assoc array can be used
		 * to load Template. The structure of the assoc passed to the constructor
		 * should then be as follows:
		 *
		 * {
		 *    "theNameOfTheTemplate": {
		 *      ...
		 *    }
		 * }
		 *
		 * (For readability the associative array is displayed in JSON)
		 *
		 * @param  string $templateName the key name of the template to be loaded
		 */
		public function load($templateName = null) {
			if (is_array($this->_assoc)) {
				$templateConfig = $this->_getTemplateConfig($templateName);
				$this->_setConfigConstants($this->_assoc);
				$this->_buildTemplate($templateConfig);
				$this->_insertResources($templateName);
				$this->_insertChildren($templateConfig);
			}
		}
		public function getBaseDir() {
			return $this->_baseDir;
		}

		private function _insertResources($templateName) {
			if (!is_null($templateName)) {
				$viewPos = strpos($templateName, "View");
				if ($viewPos) {
					$jsDependencyName = substr($templateName, 0, $viewPos)."JS";
					$this->_addResource($jsDependencyName);
					$cssDependencyName = substr($templateName, 0, $viewPos)."CSS";
					$this->_addResource($cssDependencyName);
				}
			}
		}
		private function _addResource($dependencyName) {
			if (array_key_exists($dependencyName, $this->_assoc)) {
				foreach ($this->_assoc[$dependencyName] as $dependency) {
					var_dump($dependency);
					$this->_resourceManager->addResource($dependency);
				}
			}
		}

		private function _getTemplateConfig($templateName) {
			if (!is_null($templateName)) {
				if (!array_key_exists($templateName, $this->_assoc)) {
					throw new \Exception("No entry for template $templateName in the template configuration file.", 225141923);
				}
				return $this->_assoc[$templateName];
			} else {
				return $this->_assoc;
			}
		}

		private function _setConfigConstants($assoc) {
			if (array_key_exists("base_dir", $assoc)) {
				$this->_baseDir = $assoc["base_dir"];
			}
			if (array_key_exists("mod_rewrite_document_root", $assoc)) {
				$this->_modRewriteDocumentRoot = $assoc["mod_rewrite_document_root"];
			}
			if (array_key_exists("js_base_dir", $assoc)) {
				$this->_jsBaseDir = $assoc["js_base_dir"];
			}
			if (array_key_exists("css_base_dir", $assoc)) {
				$this->_cssBaseDir = $assoc["css_base_dir"];
			}

			if (!is_null($this->_resourceManager)) {
				$this->_resourceManager->setBaseDir($this->_baseDir);
				$this->_resourceManager->setJsBaseDir($this->_jsBaseDir);
				$this->_resourceManager->setCssBaseDir($this->_cssBaseDir);
				$this->_resourceManager->setModRewriteDocumentRoot($this->_modRewriteDocumentRoot);
			}
		}

		private function _buildTemplate($assoc) {
			if (array_key_exists("template", $assoc)) {
				$templateDir = $assoc["template"];
				$templateDir = $this->_buildPath($templateDir);
				$this->loadFromFile($templateDir);
			} else if (array_key_exists("view", $assoc)) {
				$templateConfig = $assoc["view"];
				$this->load($templateConfig);
			}
		}

		private function _buildPath($path) {
			if ($path[0] != "/") {
				$path = $this->_baseDir.$path;
			} else {
				$path = substr($path, 1);
			}
			return $path;
		}

		private function _insertChildren($templateConfig) {
			if (array_key_exists("children", $templateConfig)) {
				foreach ($templateConfig["children"] as $child) {
					$childTemplate     = new _old_Template($this->_assoc, $this->_resourceManager, false);
					if (array_key_exists("view", $child)) {
						$childTemplate->load($child["view"]);
						$childDiv          = $child["id"];
						$this->insertHtmlAtId($childTemplate->_pageViewDOM->saveHTML(), $childDiv, false);
					} else if (array_key_exists("template", $child)) {
						$child["base_dir"] = $this->_baseDir;
						$childTemplate     = new _old_Template($child, null);
						$childDiv          = $child["id"];
						$this->insertHtmlAtId($childTemplate, $childDiv, false);
					}
				}
			}
		}
	}
}