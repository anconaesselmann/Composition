<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class MarkdownViewControllerTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$doc = $this->getMockBuilder('\aae\ui\Template')
				->disableOriginalConstructor()
				->getMock();
			$obj = new MarkdownViewController($doc);
		}

	}
}