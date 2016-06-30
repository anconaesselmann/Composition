<?php
namespace aae\persistence\adapters\db {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class DBConfigTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$configArray = array();
			$obj = new DBConfig($configArray);
		}
		
	}
}