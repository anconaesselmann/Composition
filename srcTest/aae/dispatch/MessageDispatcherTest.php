<?php
namespace aae\dispatch {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class MessageDispatcherTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$caller = $this->getMockBuilder('\aae\dispatch\caller\CallerInterface')
				->disableOriginalConstructor()
				->getMock();
			$receiver = $this->getMockBuilder('\aae\dispatch\receiver\ReceiverInterface')
				->disableOriginalConstructor()
				->getMock();
			$obj = new MessageDispatcher($caller, $receiver);
		}
		
	}
}