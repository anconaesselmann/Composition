<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ViewControllerTest extends \PHPUnit_Framework_TestCase {

		public $sut;

		public function setUp() {
		    parent::setUp();
		    $doc = $this->getMockBuilder('\aae\ui\Template')
				->disableOriginalConstructor()
				->getMock();
		    $this->sut = new Mocke_ViewController_12_06_14($doc);
		}

		public function test_callable() {
			# When
			$result = $this->sut->callable();

			# Then
			$this->assertTrue($result);
		}
	}
	class Mocke_ViewController_12_06_14 extends ViewController{
		public function callableAction() {
			return true;
		}
	}
}