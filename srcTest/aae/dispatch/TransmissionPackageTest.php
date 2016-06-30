<?php
namespace aae\dispatch {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TransmissionPackageTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new TransmissionPackage();
		}
		
	}
}