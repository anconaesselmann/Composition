<?php
namespace aae\adt {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TreeTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Tree();
		}

		public function test_append() {
			# Given a string and an empty tree
			$child = "test";
			$obj = new Tree();
		
			# When the string is appended to the tree
			$obj->append($child);
			$result = array_values($obj->nodes());
	
			# Then the nodes for the tree are an array with one element, which is the string
			$expected = array($child);
			$this->assertEquals($expected, $result);
		}

		public function test_getNodeByName() {
			# Given a tree with a named node
			$child = "test";
			$nodeName = "name1";
			$obj = new Tree();
		
			# When getNodeByName is called
			$obj->append($child, $nodeName);
			$result = $obj->getNodeByName($nodeName);
			
			# Then the node is returned
			$expected = $child;
			$this->assertEquals($expected, $result);
		}

		public function test_getNode() {
			# Given a tree with at least one node
			$child = "test";
			$nodeNbr = 0;
			$obj = new Tree();
		
			# When getNode is called
			$obj->append($child);
			$result = $obj->getNode($nodeNbr);
			
			# Then the node is returned
			$expected = $child;
			$this->assertEquals($expected, $result);
		}

		public function test_getParent() {
			# Given a string and an empty tree
			$pName = "p";
			$p = new Tree($pName);
			$c = new Tree("c");
			$c->append($text);
			$p->append($c);
		
			# When the string is appended to the tree
			$result = $c->getParent()->nodeName;
	
			# Then the nodes for the tree are an array with one element, which is the string
			$this->assertEquals($pName, $result);
		}
		
	}
}