<?php
/**
 *
 */
namespace aae\serialize {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\serialize
	 */
	class AssocXml {
		protected $_rootName, $_rowTagName;
		protected $_attributes = array();
		protected $_groupedNodes = array();
		protected $_columnAttribute = array();

		public function __construct($rootName, $rowTagName) {
			$this->_rootName = $rootName;
			$this->_rowTagName = $rowTagName;
		}

		protected function _addTag($dom, $element, $tag, $tagName) {
			if (array_key_exists($tagName, $this->_groupedNodes)) {
				$this->_addToGroupNode($dom, $element, $tag, $tagName);
			} else {
				$element->appendChild($tag);
			}
		}

		protected function _addToGroupNode($dom, $element, $tag, $tagName) {
			$actualParentNodeName = $this->_groupedNodes[$tagName][1];
			#$parentNode = $element->parentNode;
			$parent = $element;
			$parentNodeName = $parent->tagName;
			if ($actualParentNodeName == $parentNodeName) {
				$groupNodeName = $this->_groupedNodes[$tagName][0];
				$nodeList = $parent->getElementsByTagName($groupNodeName);
				if ($nodeList->length < 1) {
					$group = $dom->createElement($groupNodeName);
					$parent->appendChild($group);
				} else {
					$group = $nodeList->item(0);
				}
				$group->appendChild($tag);
			}
		}

		public function groupNodes($groupNodeName, $nodeNameArray, $parentNodeName = null) {
			if (is_null($parentNodeName)) {
				$parentNodeName = $this->_rowTagName;
			}
			foreach ($nodeNameArray as $nodeName) {
				$this->_groupedNodes[$nodeName] = array($groupNodeName, $parentNodeName);
			}
			return;
		}
		
		public function serialize($arrayOfAssocs) {
			$dom = new \DOMDocument();
			$root = $dom->createElement($this->_rootName);
			$dom->appendChild($root);
			foreach ($arrayOfAssocs as $row) {
				$this->_parseRow($dom, $root, $row);
			}
			return $dom->saveXML();
		}

		protected function _parseRow($dom, $root, $row) {
			$element = $dom->createElement($this->_rowTagName);
			$root->appendChild($element);
			foreach ($row as $tagName => $value) {
				$this->_parseColumn($dom, $element, $tagName, $value);
			}
		}

		protected function _parseColumn($dom, $element, $tagName, $value) {
			$result = null;
			if ($this->_isAttribute($tagName)) {
				$this->_addAttribute($element, $tagName, $dom, $value);
			} else {
				$tag = $dom->createElement($tagName, $value);
				$this->_addTag($dom, $element, $tag, $tagName);
			}
			return $result;
		}

		protected function _isAttribute($tagName) {
			$isAttribute = false;
			foreach ($this->_attributes as $key => $value) {
				if ($key == $tagName or $value == $tagName) {
					$isAttribute = true;
				}
			}
			foreach ($this->_columnAttribute as $key => $value) {
				if ($key == $tagName or $value == $tagName) {
					$isAttribute = true;
				}
			}
			if ($isAttribute) {
				#echo "$tagName is an attribute";
			}
			return $isAttribute;
		}

		protected function _addAttribute($element, $attributeName, $dom, $value) {
			if (array_key_exists($attributeName, $this->_columnAttribute)) {
				$attributeName = $this->_columnAttribute[$attributeName];
			}
			if (!array_key_exists($attributeName, $this->_attributes)) {
				return;
			}
			$attributeNodeName = $this->_attributes[$attributeName];

			$nodeList = $element->getElementsByTagName(			$attributeName);
			if ($nodeList->length < 1) {
				$node = $dom->createElement(					$attributeNodeName);
				$element->appendChild($node);
			} else {
				$node = $nodeList->item(0);
			}
			$attribute = $dom->createAttribute($attributeName);
			$attribute->value = $value;
			$node->appendChild($attribute);
		}
	
		public function colIsAttribute($attributeNodeName, $attributeName, $columnName = null) {
			$this->_attributes[$attributeName] = $attributeNodeName;
			if (!is_null($columnName)) {
				$this->_columnAttribute[$columnName] = $attributeName;
			}
		}
	}
}