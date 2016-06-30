<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class AuthenticatedViewControllerTest extends \PHPUnit_Framework_TestCase {
		public function setUp() {
			$this->_doc = $this->getMockBuilder('\aae\ui\Template')
				->disableOriginalConstructor()
				->getMock();
			$this->_session = $this->getMockBuilder('\aae\app\Session')
				->disableOriginalConstructor()
				->getMock();
			$this->_user = $this->getMockBuilder('\aae\app\User')
				->disableOriginalConstructor()
				->getMock();
		}

		protected function _getInstance() {
			$obj = new AuthenticatedViewController($this->_doc, $this->_user, $this->_session, null);
			return $obj;
		}

		public function test___construct() {
			$obj = $this->_getInstance();
		}

		public function test_test_session_acces() {
			# Given
			$obj = $this->_getInstance();

			# When
			$result = get_class($obj->session);

			# Then
			$this->assertRegExp('/^Mock_Session/', $result);
		}

		public function test_AuthenticatedAction_logged_in() {
			# Given
			$sut = new DerivingClassAuthenticatedViewController($this->_doc, $this->_user, $this->_session, null);
			$this->_user->method('isLoggedIn')
    			        ->willReturn(true);
			# When AuthenticatedAction is called
			$result = $sut->fu(1,2);

			# Then
			$expected = "fuAuthenticatedAction called with 1, 2";
			$this->assertEquals($expected, $result);
		}
		public function test_AuthenticatedAction_logged_in_no_args() {
			# Given
			$sut = new DerivingClassAuthenticatedViewController($this->_doc, $this->_user, $this->_session, null);
			$this->_user->method('isLoggedIn')
    			        ->willReturn(true);
			# When AuthenticatedAction is called
			$result = $sut->ba();

			# Then
			$expected = "baAction called";
			$this->assertEquals($expected, $result);
		}
		public function test_Action_logged_in_no_args() {
			# Given
			$sut = new DerivingClassAuthenticatedViewController($this->_doc, $this->_user, $this->_session, null);
			$this->_user->method('isLoggedIn')
    			        ->willReturn(true);
			# When AuthenticatedAction is called
			$result = $sut->fuba();

			# Then
			$expected = "fubaAction called";
			$this->assertEquals($expected, $result);
		}
		public function test_Action_does_not_exist() {
			# Given
			$sut = new DerivingClassAuthenticatedViewController($this->_doc, $this->_user, $this->_session, null);
			$this->_user->method('isLoggedIn')
    			        ->willReturn(true);
			# When AuthenticatedAction is called
			try {
				$sut->doesNotExist();
			} catch (\Exception $e) {
				$this->assertEquals(1206140141, $e->getCode());
				return;
			}
			$this->fail("The function does not exist and calling it should have raised an exception.");
		}

		public function test_AuthenticatedAction_not_logged_in() {
			# Given
			$sut = new DerivingClassAuthenticatedViewController($this->_doc, $this->_user, $this->_session, null);

			# When AuthenticatedAction is called
			try {
				$sut->fu(1,2);
			} catch (AuthenticationException $e) {
				$this->assertEquals(1107141659, $e->getCode());
				return;
			}
			$this->fail("Not logged in. Call to fu should have raised an exception.");
		}

		public function test_AuthenticatedAction_not_logged_in_but_alternative_Action_exists_and_is_called() {
			# Given
			$sut = new DerivingClassAuthenticatedViewController($this->_doc, $this->_user, $this->_session, null);
			$this->_user->method('isLoggedIn')
    			        ->willReturn(false);
			# When AuthenticatedAction is called
			$result = $sut->ba();

			# Then
			$expected = "baAction called not logged in";
			$this->assertEquals($expected, $result);
		}
	}

	class DerivingClassAuthenticatedViewController extends AuthenticatedViewController {
		public function fuAuthenticatedAction($arg1, $arg2) {
			return "fuAuthenticatedAction called with ".$arg1.", ".$arg2;
		}
		public function baAuthenticatedAction() {
			return "baAction called";
		}

		public function baAction() {
			return "baAction called not logged in";
		}
		public function fubaAction() {
			return "fubaAction called";
		}
	}
}