<?php
namespace aae\serialize {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class AssocXmlTest extends \PHPUnit_Framework_TestCase {
		/*public function test___construct() {
			$obj = new AssocXml("", "");
		}*/
		public function provider_serialize() {
			return array(
				array(
					array(array("title" => "title1"))
					, "root", "row", "<?xml version=\"1.0\"?>
<root><row><title>title1</title></row></root>
"
				),
				array(
					array(array("title" => "title1"), array("title" => "title2"))
					, "root", "row", "<?xml version=\"1.0\"?>
<root><row><title>title1</title></row><row><title>title2</title></row></root>
"
				),
				array(
					array(array("title" => "title1", "date" => "date1", "pages" => "pages1"), array("title" => "title2", "date" => "date2", "pages" => "pages2"))
					, "root", "row", "<?xml version=\"1.0\"?>
<root><row><title>title1</title><date>date1</date><pages>pages1</pages></row><row><title>title2</title><date>date2</date><pages>pages2</pages></row></root>
"
				)
			);
		}
		
		/**
		 * @dataProvider provider_serialize
		 */
		public function test_serialize($assoc, $rootName, $rowTagName, $expected) {
			# Given a flat associative array
			$obj = new AssocXml($rootName, $rowTagName);
		
			# When serialize is called
			$result = $obj->serialize($assoc);
			
			# Then an XML string with $root as the root and $rowName as each row element is returned
			$this->assertEquals($expected, $result);
		}

		public function provider_serializeWithAttributes() {
			return array(
				array(
					array(array("AttributeNodeName" => null, "attributeName" => "source"))
					, "AttributeNodeName", "attributeName", null, "<?xml version=\"1.0\"?>
<root><row><AttributeNodeName attributeName=\"source\"/></row></root>
"
				),
				array(
					array(array("attributeName" => "source"))
					, "AttributeNodeName", "attributeName", null, "<?xml version=\"1.0\"?>
<root><row><AttributeNodeName attributeName=\"source\"/></row></root>
"
				),
				array(
					array(array("attributeName" => "source"))
					, "AttributeNodeName", "attributeName", null, "<?xml version=\"1.0\"?>
<root><row><AttributeNodeName attributeName=\"source\"/></row></root>
"
				),
				array(
					array(array("columnName" => "source"))
					, "AttributeNodeName", "attributeName", "columnName", "<?xml version=\"1.0\"?>
<root><row><AttributeNodeName attributeName=\"source\"/></row></root>
"
				)
			);
		}
		
		/**
		 * @dataProvider provider_serializeWithAttributes
		 */
		public function test_serializeWithAttributes($assoc, $nodeName, $attributeName, $columnName, $expected) {
			# Given a mapping for an attribute
			$rootName = "root";
			$rootTagName = "row";
			$obj = new AssocXml($rootName, $rootTagName);
			$obj->colIsAttribute($nodeName, $attributeName, $columnName);
		
			# When serialize is called
			$result = $obj->serialize($assoc);
			
			# Then the attribute $attributeName is added to the node $nodeName
			$this->assertEquals($expected, $result);
		}

		public function provider_serializeGroupNodes() {
			return array(
				array(
					array(array("title" => "title1", "date" => "date1", "pages" => "pages1"))
					, "group", array("date", "pages"), null, "<?xml version=\"1.0\"?>
<root><row><title>title1</title><group><date>date1</date><pages>pages1</pages></group></row></root>
"
				),
				array(
					array(array("title" => "title1", "date" => "date1", "pages" => "pages1"))
					, "group", array("title", "pages"), "row", "<?xml version=\"1.0\"?>
<root><row><group><title>title1</title><pages>pages1</pages></group><date>date1</date></row></root>
"
				)
			);
		}
		
		/**
		 * @dataProvider provider_serializeGroupNodes
		 */
		public function test_serializeGroupNodes($assoc, $groupNodeName, $nodeNameArray, $parentNodeName, $expected) {
			# Given a parent node name, a group node name, and the name of nodes to be grouped
			$rootName = "root";
			$rootTagName = "row";
			$obj = new AssocXml($rootName, $rootTagName);
			$obj->groupNodes($groupNodeName, $nodeNameArray, $parentNodeName);
			# When serialize is called
			$result = $obj->serialize($assoc);
			
			# Then the nodes to be grouped will be grouped inside the group node, which is appended to the parent node
			$this->assertEquals($expected, $result);
		}

		/*public function test_implementation_testing() {
			# Given 
			$csvString = "Title,todo,effects,src
Build a pipeline,\"Find a financier
Get something passed with the word oil or gas in it (either win the proposition, or win the solution)\",\"Gain votes if from red state
Looses votes if from blue state\",file://images/sandpiper.jpg
Build a wall between the us and Mexico,find a financier,\"Looses votes if from states with 20+ percent Mexicans
Gain votes if from red state
Looses votes if from blue state\",file://images/sandpiper.jpg
Pass anti gun legislation,find a financier,\"Gain votes if from blue state
Looses votes if from red state\",file://images/sandpiper.jpg
Pass pro gun legislation,find a financier,\"Gain votes if from red state
Looses votes if from blue state\",file://images/sandpiper.jpg
Pass health care reform,find a financier,\"Gain votes if from blue state
Looses votes if from red state\",file://images/sandpiper.jpg
Pass entitlement reform,find a financier,\"Gain votes if from red state
Looses votes if from blue state\",file://images/sandpiper.jpg
Return to Gold Standard,find a financier,\"Gain votes if from red state
Looses votes if from blue state\",file://images/sandpiper.jpg
Pass Gay marriage constitutional amendment,find a financier,\"if passed after anti gay marriage amendment this one overturns the other
Gain votes if from blue state
Looses votes if from red state\",file://images/sandpiper.jpg
Pass anti Gay marriage constitutional amendment,find a financier,\"if passed after pro gay marriage amendment this one overturns the other
Gain votes if from red state
Looses votes if from blue state\",file://images/sandpiper.jpg";
	
			#$csvString = "image\nfile://images/sandpiper.jpg";

			$csv = new Csv();
		
			# When test is called
			$assoc = $csv->unserialize($csvString);
			var_dump($assoc);

			$rootName = "root";
			$rootTagName = "row";
			$obj = new AssocXml($rootName, $rootTagName);
			$obj->colIsAttribute("img", "src");
			$result = $obj->serialize($assoc);


			# Then 
			$expected = "";
			$this->assertEquals($expected, $result);
		}*/


	}
}