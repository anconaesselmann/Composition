<?php
namespace aae\serialize {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class abstractSvTest extends \PHPUnit_Framework_TestCase {
		public function test___constructCsv() {
			$obj = new Csv();
		}

		protected function _getDataset($seperator) {
			return array(
				array("col1\nval1", array(array("col1" => "val1"))),
				array("col1\n\"val1\nline2\"", array(array("col1" => "val1\nline2"))),
				array("col1{$seperator}col2{$seperator}col3\nval1{$seperator}\"val2\nline2\"{$seperator}val3", array(array("col1" => "val1", "col2" => "val2\nline2", "col3" => "val3"))),
				array("col1{$seperator}col2{$seperator}col3\n\"val1\nline2\"{$seperator}val2{$seperator}val3", array(array("col1" => "val1\nline2", "col2" => "val2", "col3" => "val3"))),
				array("col1{$seperator}col2{$seperator}col3\n\"val1\nline2\nline3\nline4\"{$seperator}val2{$seperator}val3", array(array("col1" => "val1\nline2\nline3\nline4", "col2" => "val2", "col3" => "val3"))),
				
				array("col1\nval\\$seperator with seperator", array(array("col1" => "val$seperator with seperator"))),
				array("col1{$seperator}col2{$seperator}col3\n1a{$seperator}1b{$seperator}1c", array(array("col1" => "1a", "col2" => "1b", "col3" => "1c"))),
				array("col1{$seperator}col2{$seperator}col3\n1a{$seperator}1b{$seperator}1c\n2a{$seperator}2b{$seperator}2c\n3a{$seperator}3b{$seperator}3c\n4a{$seperator}4b{$seperator}4c", array(array("col1" => "1a", "col2" => "1b", "col3" => "1c"),array("col1" => "2a", "col2" => "2b", "col3" => "2c"),array("col1" => "3a", "col2" => "3b", "col3" => "3c"),array("col1" => "4a", "col2" => "4b", "col3" => "4c"))),
			);
		}

		public function provider_unserializeCsv() {
			return $this->_getDataset(",");
		}
		
		/**
		 * @dataProvider provider_unserializeCsv
		 */
		public function test_unserializeCsv($csvString, $expected) {
			# Given a comma separated value string
			$obj = new Csv();
		
			# When unserialize is called
			$result = $obj->unserialize($csvString);
			
			# Then an two- dimensional array mirroring the csv is returned
			$this->assertEquals($expected, $result);
		}

		public function test___constructTsv() {
			$obj = new Tsv();
		}

		public function provider_unserializeTsv() {
			return $this->_getDataset("\t");
		}
		
		/**
		 * @dataProvider provider_unserializeTsv
		 */
		public function test_unserializeTsv($csvString, $expected) {
			# Given a comma separated value string
			$obj = new Tsv();
		
			# When unserialize is called
			$result = $obj->unserialize($csvString);
			
			# Then an two- dimensional array mirroring the csv is returned
			$this->assertEquals($expected, $result);
		}		
	}
}