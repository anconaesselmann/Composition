<?php
/** 
 * This file uses the following Classes that are declared in other files:
 * 		\aae\fs\File
 *
 *
 * For license information view the license file that is part of this distribution.
 * 
 */
namespace aae\ui {
	/**
	 * Document provides convenience functions for DOM manipulation. Instances of
	 * Document serve as views for ViewController. The inheriting class Template
	 * provides additional functionality geared towards building a Document instance
	 * from separate modules.
	 * 
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class Document {
		protected $_pageViewDOM = null;
		protected $_formatOutput = true;
		protected $_resourceManager = null;

		/**
		 * Can be initialized with HTML content. If no content is provided,
		 * the instance can be initialized with loadHTML() or loadFromFile().
		 * @param string $content a string with valid HTML
		 */
		public function __construct($content = null, \aae\ui\ResourceManagerInterface $resourceManager = null) {
			if (!is_null($content)) {
				$this->loadHTML($content);
			}
			if (!is_null($resourceManager)) {
				$this->_resourceManager = $resourceManager;
			}
		}

		/**
		 * load needs to be implemented by classes inheriting from Document, if
		 * they allow the file to be build after instantiation from a configuration
		 * file.
		 *
		 * For an example look at Template->load()
		 */
		public function load() { /* intentionally left blank */ }

		/**
		 * Loads from HTML from string.
		 * 
		 * @param  string $content HTML string
		 */
		public function loadHTML($content) {
			$this->_pageViewDOM = new \DOMDocument("1.0", "utf-8");
			$content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
			$this->_pageViewDOM->loadHTML($content);
			$this->_pageViewDOM->formatOutput = $this->_formatOutput;
		}

		/**
		 * Initialize Document with an instance of DOMDocument
		 * 
		 * @param  DOMDocument $dom
		 */
		public function loadDOM (\DOMDocument $dom) {
			$this->_pageViewDOM = $dom;
		}

		/**
		 * Loads from HTML file
		 * @param  string $path A path in string form
		 * @throws \Exception If the path does not exist
		 */
		public function loadFromFile($path) {
			$path = new \aae\fs\File($path);
			$this->loadHTML(file_get_contents($path));
		}

		/**
		 * Determines if the the string representation of Document is formated
		 * for human readability.
		 * 
		 * @param boolean $val
		 */
		public function setFormatOutput($val = true) {
			$this->_formatOutput = (bool)$val;
		}

		/**
		 * Inserts HTML into a tag with a CSS id of $idName. Content already present
		 * is either replaced or appended, depending on $repaceContent.
		 * 
		 * @param  string  $xmlString      a string containing valid HTML
		 * @param  string  $idName         the CSS id of the target tag
		 * @param  boolean $replaceContent replace or append content
		 * @throws \Exception If $idName does not exist in Document
		 */
		public function insertHtmlAtId($xmlString, $idName, $replaceContent = true) {
			$targetNode = $this->_pageViewDOM->getElementById($idName);
			if (is_null($targetNode)) 
				throw new \Exception("The id $idName does not exist", 221141010);

			$this->_removeExistingContent($targetNode, $replaceContent);
			$this->_insertHtml($targetNode, $xmlString);
		}

		protected function _insertHtml($targetNode, $xmlString) {
			$tempDoc = new \DOMDocument("1.0", "utf-8");
			$xmlString = mb_convert_encoding($xmlString, 'HTML-ENTITIES', "UTF-8");
			$tempDoc->loadHTML(strval($xmlString));
			$newElements = $tempDoc->getElementsByTagName("body")->item(0);
			if (!is_null($newElements)) {
				foreach($newElements->childNodes as $node){
					$node =  $this->_pageViewDOM->importNode($node, true);
					$targetNode->appendChild($node);
				}
			} else {
				$frag = $this->_pageViewDOM->createDocumentFragment();
				$frag->appendXML($xmlString);
				$targetNode->appendChild($frag);
			}
		}

		/**
		 * Inserts text into a tag with a CSS id of $idName. Content already present
		 * is either replaced or appended, depending on $repaceContent.
		 * 
		 * @param  string  $string         a text
		 * @param  string  $idName         the CSS id of the target tag
		 * @param  boolean $replaceContent replace or append content
		 * @throws \Exception If $idName does not exist in Document
		 */
		public function insertTextAtId($string, $idName, $replaceContent = true ) {
			$targetNode = $this->_pageViewDOM->getElementById($idName);			
			if (is_null($targetNode)) 
				throw new \Exception("The id $idName does not exist", 221141010);
			$this->_removeExistingContent($targetNode, $replaceContent);
			$node =  $this->_pageViewDOM->createTextNode($string);
			$targetNode->appendChild($node);
		}

		/**
		 * Inserts text into a tags with CSS class $cssClassName. Content already 
		 * present is either replaced or appended, depending on $repaceContent.
		 * 
		 * @param  string  $string         a text
		 * @param  string  $cssClassName   the CSS class name of the target tags
		 * @param  boolean $replaceContent replace or append content
		 * @throws \Exception If $cssClassName does not exist in Document
		 */
		public function insertTextAtClass($string, $cssClassName, $replaceContent = true ) {
			$nodes = $this->getClassNodes($cssClassName);

			foreach ($nodes as $targetNode) {
				$this->_removeExistingContent($targetNode, $replaceContent);
				$node =  $this->_pageViewDOM->createTextNode($string);
				$targetNode->appendChild($node);
			}
		}

		public function insertHtmlAtClass($xmlString, $cssClassName, $replaceContent = true ) {
			$nodes = $this->getClassNodes($cssClassName);

			foreach ($nodes as $targetNode) {
				$this->_removeExistingContent($targetNode, $replaceContent);
				$this->_insertHtml($targetNode, $xmlString);
			}
		}

		public function insertDomElementAtClass($domElement, $cssClassName, $repaceContent = true) {
			$nodes = $this->getClassNodes($cssClassName);
			foreach ($nodes as $targetNode) {
				$this->_removeExistingContent($targetNode, $replaceContent);
				$targetNode->appendChild($domElement);
			}
		}

		public function setClassAttribute($attributeName, $attributeValue, $cssClassName) {
			$nodes = $this->getClassNodes($cssClassName);

			foreach ($nodes as $targetNode) {
				$targetNode->setAttribute($attributeName, $attributeValue);
			}
		}

		protected function _removeExistingContent($targetNode, $replaceContent) {
			if ($replaceContent) {
				while ($targetNode->hasChildNodes()) {
				    $targetNode->removeChild($targetNode->firstChild);
				}
			}
		}

		public function getClassNodes($cssClassName) {
			$finder = new \DomXPath($this->_pageViewDOM);
			$nodes = $finder->query("//*[contains(@class, '$cssClassName')]");
			if ($nodes->length < 1) 
				throw new \Exception("The class name $cssClassName does not exist", 221141011);
			return $nodes;
		}

		/**
		 * Access to the DOMDocument instance
		 * 
		 * @return DOMDocument The Document as a DOMDocument
		 */
		public function getDom() {
			return $this->_pageViewDOM;
		}

		public function getHtml() {
			if (!is_null($this->_resourceManager)) {
				$resourceLinks = $this->_resourceManager->getLinks();
				if (count($resourceLinks) > 0) {
					$node = $this->_pageViewDOM->getElementsByTagName('head')->item(0);
					foreach ($resourceLinks as $link) {
						$frag = $this->_pageViewDOM->createDocumentFragment();
						if (pathinfo($link, PATHINFO_EXTENSION) == "js") {
							$frag->appendXML("\n<script type=\"text/javascript\" src=\"$link\"></script>");
						} else if (pathinfo($link, PATHINFO_EXTENSION) == "css") {
							$frag->appendXML("\n<link rel=\"stylesheet\" type=\"text/css\" href=\"$link\" />");
						}
						$node->appendChild($frag);
					}
				}
			}
			return $this->_pageViewDOM->saveHTML();
		}

		/**
		 * The formating of the output can be formated for human readability
		 * by calling setFormatOutput().
		 * 
		 * @return string The the HTML output.
		 */
		public function __toString() {
			return $this->getHtml();
		}

		public function addResource($resourceDir) {
			$this->_resourceManager->addResource($resourceDir);
		}
		public function addLink($link) {
			$this->_resourceManager->addLink($link);
		}
	}
}