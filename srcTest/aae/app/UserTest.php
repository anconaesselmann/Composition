<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	/**
	 * @group database
	 */
	class UserTest extends \aae\unitTesting\DbTestCase {

		private $_pdo     = NULL;
		private $_session = NULL;

		protected function _getUser() {
			$pdo        = $this->getDb();
			$this->_pdo = $pdo;
			$fAPI	    = new \aae\db\FunctionAPI($pdo, array("dbName" => "tests"));
			$session = $this->getMockBuilder('\aae\app\Session')
				->disableOriginalConstructor()
				->getMock();
			$this->_session = $session;
			/*$this->_session->method('getUserAgent')
						   ->will($this->onConsecutiveCalls("phpUnit browser", "NEW BROWSER AGENT"));*/
			return new User($fAPI, $session, array("cost" => 4));
		}



// Implementation testing


		public function test_createUser() {
			$user = $this->_getUser();
			$code = $user->create("axel", "1234password", "axel.esselmann@gmail");
			$this->assertTableHas("users", ["user_id"=>1]);
			$this->assertTableHas("user_details", ["user_id"=>1]);
		}

		public function test_verifyUser() {
			# Given
			$email  = "axel.esselmann@gmail";
			$user   = $this->_getUser();
			$code   = $user->create("axel", "1234password", $email);

			$this->assertTableHas("reset_codes", ["reset_code" => $code]);
			# When
			$result = $user->verify($email, $code);

			# Then
			$this->assertTableHasNot("reset_codes", ["reset_code" => $code]);
			$this->assertTableHas("users", ["user_id"=>1,"user_status"=>1]);
		}

		public function test_login_fails_for_unregistered_user() {
			# Given non-existant login credentials
			$email    = "axel.esselmann@gmail";
			$password = "1234password";
			$user     = $this->_getUser();

			# When login is called
			$result   = (bool)$user->login($email, $password);

			# Then login returns false
			$this->assertFalse($result);
			//$this->assertFalse($user->isLoggedIn());
			$this->assertTableHasNot("logins", ["login_success"=>1]);
		}

		public function test_login_fails_user_didnt_verify() {
			# Given correct login credentials
			$email    = "axel.esselmann@gmail";
			$password = "1234password";
			$userName = "axel";
			$user     = $this->_getUser();
			$code     = $user->create($userName, $password, $email);

			# When login is called
			$result   = (bool)$user->login($email, $password);

			# Then login returns true
			$this->assertFalse($result);
			//$this->assertFalse($user->isLoggedIn());
			$this->assertTableHasNot("logins", ["login_success"=>1]);
		}

		public function test_login_fails_wrong_password() {
			# Given login credentials
			$email    = "axel.esselmann@gmail";
			$password = "1234password";
			$userName = "axel";
			$user     = $this->_getUser();
			$code     = $user->create($userName, $password, $email);
			$user->verify($email, $code);

			# When login is called with a wrong password
			$result   = (bool)$user->login($email, "wrongPassword");

			# Then login returns false
			$this->assertFalse($result);
			//$this->assertFalse($user->isLoggedIn());
			$this->assertTableHas("logins", ["login_success"=>0]);
		}

		public function test_login_success() {
			# Given correct login credentials
			$this->runSql('SELECT createUser("axel0",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axelesselmann@gmail", "1234code", "127.0.0.1");');
			$email    = "axel.esselmann@gmail";
			$password = "1234password";
			$userName = "axel";
			$user     = $this->_getUser();
			$code     = $user->create($userName, $password, $email);
			$user->verify($email, $code);

			# When login is called
			$userId   = $user->login($email, $password);

			# Then login returns true
			$this->assertEquals(2, $userId);
			//$this->assertTrue($user->isLoggedIn());
			$this->assertTableHas("logins", ["login_success"=>1]);
		}

		public function test_loginTimeout_pass_after_successful_login() {
			$email    = "axelesse.lmann@gmail";
			$password = "1234password";
			$user     = $this->_getUser();

			// Preparing database:
			// 		A failed login attempt
			// 		followed by a successful login attempt
			$this->runTestSqlFile("populateUsers.sql");
			$this->runSql(
			   'INSERT INTO logins
					VALUES(NULL, 9, "'.$this->getTimeStamp(-2).'", TRUE,  1, NULL);
				INSERT INTO logins
					VALUES(NULL, 9, "'.$this->getTimeStamp(-3).'", FALSE, 1, NULL);'
			);

			$result = (bool)$user->login($email, $password);
			$this->assertTrue($result);
		}

		public function test_loginTimeout_pass_5_seconds_after_failed_login() {
			$email    = "axelesse.lmann@gmail";
			$password = "1234password";
			$user     = $this->_getUser();

			// Preparing database:
			// 		A failed login attempt 5 seconds in the past
			$this->runTestSqlFile("populateUsers.sql");
			$this->runSql(
			   'INSERT INTO logins
					VALUES(NULL, 9, "'.$this->getTimeStamp(-6).'", FALSE, 1, NULL);'
			);

			$result = (bool)$user->login($email, $password);
			$this->assertTrue($result);
		}

		public function test_loginTimeout_deny_5_sec_after_fail() {
			$email    = "axelesse.lmann@gmail";
			$password = "1234password";
			$user     = $this->_getUser();

			// Preparing database:
			// 		A failed login attempt 3 seconds in the past
			$this->runTestSqlFile("populateUsers.sql");
			$this->runSql(
			   'INSERT INTO logins
					VALUES(NULL, 9, "'.$this->getTimeStamp(-3).'", FALSE, 1, NULL);
			');
			$result = (bool)$user->login($email, $password);
			$this->assertFalse($result);
		}

		public function test_changePassword_success() {
			$email        = "axelesse.lmann@gmail";
			$password     = "1234password";
			$newPassword  = "newPassword1234";
			$user         = $this->_getUser();

			$this->runTestSqlFile("populateUsers.sql");

			$resetCode = $user->requestPasswordResetCode($email);
			$this->assertTableHas("reset_codes", ["user_id" => 9, "reset_code" => $resetCode]);

			$resetSuccess = $user->resetPassword($email, $newPassword, $resetCode);
			$this->assertTrue($resetSuccess);
			$this->assertTableHasNot("reset_codes", ["user_id" => 9]);

			$loginSuccess = (bool)$user->login($email, $newPassword);
			$this->assertTrue($loginSuccess);

			$this->assertTableHas("old_passwords", ["user_id" => 9]);
		}

		public function test_changePassword_fail_wrong_code() {
			$email        = "axelesse.lmann@gmail";
			$password     = "1234password";
			$newPassword  = "newPassword1234";
			$user         = $this->_getUser();

			$this->runTestSqlFile("populateUsers.sql");

			$resetCode    = $user->requestPasswordResetCode($email);
			$resetSuccess = $user->resetPassword($email, $newPassword, "wrongCode");

			$this->assertFalse($resetSuccess);
			$this->assertTableHas("reset_codes", ["user_id" => 9]);

			$loginSuccess = (bool)$user->login($email, $newPassword);
			$this->assertFalse($loginSuccess);

			$this->assertTableHasNot("old_passwords", ["user_id" => 9]);
		}

		public function test_changePassword_fail_code_used_twice() {
			$email        = "axelesse.lmann@gmail";
			$password     = "1234password";
			$newPassword1 = "newPassword1234";
			$newPassword2 = "newPassword5678";
			$user         = $this->_getUser();

			$this->runTestSqlFile("populateUsers.sql");

			$resetCode    = $user->requestPasswordResetCode($email);
			$this->assertTableHas("reset_codes", ["user_id" => 9, "reset_code" => $resetCode]);

			$resetSuccess = $user->resetPassword($email, $newPassword1, $resetCode);
			$this->assertTrue($resetSuccess);
			$this->assertTableHasNot("reset_codes", ["user_id" => 9]);

			$resetSuccess = $user->resetPassword($email, $newPassword2, $resetCode);
			$this->assertFalse($resetSuccess);

			$loginSuccess = (bool)$user->login($email, $newPassword2);
			$this->assertFalse($loginSuccess);
		}

		public function test_changePassword_failed_code_expired() {
			$email       = "axelesse.lmann@gmail";
			$password    = "1234password";
			$newPassword = "newPassword1234";
			$user        = $this->_getUser();

			$this->runTestSqlFile("populateUsers.sql");
			$resetCode   = $user->requestPasswordResetCode($email);
			$this->runSql('
				UPDATE reset_codes SET reset_time = "'.$this->getTimeStamp(-910).'" WHERE user_id = 9;
			');

			$resetSuccess = $user->resetPassword($email, $newPassword, $resetCode);
			$this->assertFalse($resetSuccess);
			$this->assertTableHasNot("reset_codes", ["user_id" => 9]);

			$loginSuccess = (bool)$user->login($email, $newPassword);
			$this->assertFalse($loginSuccess);
		}

		private function _prepareMockSession($email, $userId, $loggedIn = false) {
			$this->_session->method('getUserAgent')
						   ->willReturn("phpUnit browser");
			$this->_session->method('isLoggedIn')
             	           ->willReturn($loggedIn);
            $this->_session->method('offsetGet')
             	           ->willReturn(["email" => $email, "userId" => $userId]);
		}

		public function test_setLoginCookie() {
			# Given
			$email      = "axelesse.lmann@gmail";
			$id         = 9;
			$deviceName = "axels-macbook";
			$user       = $this->_getUser();
			$userAgent  = "phpUnit browser";
			$this->_prepareMockSession($email, $id, true);
			$this->runTestSqlFile("populateUsers.sql");

			# When
			$user->login($email, "1234password");
			$result = $user->setLogincookie($deviceName);

			# Then
			$this->assertTableHas(
				"persistent_logins",
				["series_id"    => 1,
				 "device_name"  => $deviceName,
				 "browser_info" => $userAgent]);
			$this->assertTrue((bool)$result);
		}

		public function test_update_with_setLoginCookie() {
			# Given
			$email      = "axelesse.lmann@gmail";
			$deviceName = "axels-macbook";
			$user       = $this->_getUser();
			$this->_session->method('getUserAgent')
						   ->will($this->onConsecutiveCalls("phpUnit browser", "NEW BROWSER AGENT"));
			$this->_session->method('isLoggedIn')
             	           ->willReturn(true);
            $this->_prepareMockSession($email, 9, false);
			$this->runTestSqlFile("populateUsers.sql");

			# When
			$user->login($email, "1234password");
			$result1 = $user->setLogincookie($deviceName);
            $result2 = $user->setLogincookie($deviceName);

			# Then
			$this->assertTableHas(
				"persistent_logins",
				["series_id"    => 1,
				 "device_name"  => $deviceName,
				 "browser_info" => "NEW BROWSER AGENT"]);
		}

		public function test_cookieLogin_no_persistent_login_defined_1() {
			# Given
			$user = $this->_getUser();
			$this->runTestSqlFile("populateUsers.sql");
			$this->_session->method('isLoggedIn')
             	           ->willReturn(true);
            $this->_session->method('getLoginCookie')
             	           ->willReturn(null);
			# When
			$result = $user->cookieLogin();

			# Then
			$this->assertFalse($result);
		}
		public function test_cookieLogin_no_persistent_login_defined_2() {
			# Given
			$user = $this->_getUser();
			$this->runTestSqlFile("populateUsers.sql");
			$this->_session->method('isLoggedIn')
             	           ->willReturn(true);
            $this->_session->method('getLoginCookie')
             	           ->willReturn(
								["email"      => null,
								 "deviceName" => null,
								 "code"       => null]);
			# When
			$result = $user->cookieLogin();

			# Then
			$this->assertFalse($result);
		}
		public function test_cookieLogin_no_persistent_login_defined_3() {
			# Given
			$user = $this->_getUser();
			$this->runTestSqlFile("populateUsers.sql");
			$this->_session->method('isLoggedIn')
             	           ->willReturn(true);
            $this->_session->method('getLoginCookie')
             	           ->willReturn(
								["email"      => '',
								 "deviceName" => '',
								 "code"       => '']);
			# When
			$result = $user->cookieLogin();

			# Then
			$this->assertFalse($result);
		}
		public function test_cookieLogin_outdated_code() {
			# Given
			$email      = "axelesse.lmann@gmail";
			$deviceName = "axels-macbook";
			$user       = $this->_getUser();
			$this->runTestSqlFile("populateUsers.sql");
			$this->_session->method('isLoggedIn')
             	           ->willReturn(true);
			$this->runTestSqlFile("populatePersistentLogins.sql");
			$this->_session->method('getLoginCookie')
             	           ->willReturn(
								["email"      => $email,
								 "deviceName" => $deviceName,
								 "code"       => "WRONG CODE!!!!"]);
            $expectedCode = 1030141258;
			# When
			try {
				$result = $user->cookieLogin();
				$this->fail("Expected LoginException with code $expectedCode.");
			} catch (LoginException $e) {
				# Then
				$code = $e->getCode();
				$this->assertEquals($expectedCode, $code);
				$this->assertTableHas(
					"logins",
					["login_success" => 0,
					 "login_type"    => 2]);
				$this->assertTableHasNot(
					"persistent_logins",
					["user_id"    => 9]);
				return;
			}
		}
		public function test_cookieLogin() {
			# Given
			$email      = "axelesse.lmann@gmail";
			$id         = 9;
			$deviceName = "axels-macbook";
			$user       = $this->_getUser();
			$this->_prepareMockSession($email, $id, true);
			$this->runTestSqlFile("populateUsers.sql");

			$pCode    = "12345abc";
            $hashCode = password_hash($pCode, PASSWORD_BCRYPT, ['cost' => 4]);
            $this->runSql("INSERT INTO tests.persistent_logins VALUES(NULL, 9, \"$deviceName\", \"phpUnit browser\", \"$hashCode\")");
			$this->_session->method('getLoginCookie')
             	           ->willReturn(
								["email"      => $email,
								 "deviceName" => $deviceName,
								 "code"       => $pCode]);
            $this->_session->expects($this->atLeastOnce())
                ->method('offsetSet')
                ->with($this->equalTo("aae_app_User"), $this->equalTo(["email" => $email, "userId" => 9]));

			# When
			$user->login($email, "1234password");
			$result = $user->cookieLogin();

			# Then
			#$this->showTable("persistent_logins");
			#$this->showTable("logins");

			$this->assertTrue($result);
			$this->assertTableHas(
				"logins",
				["login_success" => 1,
				 "login_type"    => 2]);

			# When
			$result = $user->unsetLoginCookie($deviceName);

			# Then
			$this->assertEquals(1, $result);
			$this->assertTableHasNot(
				"persistent_logins",
				["device_name" => $deviceName]);
		}
		public function test_getAllPersistentLogins() {
			# Given
			$email = "axelesse.lmann@gmail";
			$id    = 9;
			$user  = $this->_getUser();
			$this->_prepareMockSession($email, $id, true);
			$this->runTestSqlFile("populateUsers.sql");
			$this->runTestSqlFile("populatePersistentLogins.sql");

			# When
			$user->login($email, "1234password");
			$result = $user->getAllPersistentLogins();

			# Then
			$expected = ["deviceName444", "phpUnit browser5"];
			$this->assertEquals($expected, $result[3]);
		}

		public function test_updateUser_all_fields_previously_null() {
			# Given
			$email = "axelesse.lmann@gmail";
			$id    = 9;
			$user  = $this->_getUser();
			$this->_prepareMockSession($email, $id, true);
			$this->runTestSqlFile("populateUsers.sql");

			$firstName = "axel";
			$lastName  = "ancona esselmann";
			$phoneNbr  = "(626) 344-9785";
			$address   = "1390 34th ave";
			$city      = "SF";
			$zip       = "94122";
			$state     = "CA";
			$country   = "USA";

			# When
			$user->login($email, "1234password");
			$result = $user->updateUserDetails(
				$firstName,
				$lastName,
				$phoneNbr,
				$address,
				$city,
				$zip,
				$state,
				$country
			);

			# Then
			$this->assertTableHas(
				"user_details",
				[
					"user_id"    => 9,
					"first_name" => $firstName,
					"last_name"  => $lastName,
					"phone_nbr"  => $phoneNbr,
					"address"    => $address,
					"city"       => $city,
					"zip"        => $zip,
					"country"    => $country
				]);
			$this->assertTrue($result);
		}
		public function test_updateUserDetails_only_when_not_null() {
			# Given
			$email = "axelesse.lmann@gmail";
			$id    = 9;
			$user  = $this->_getUser();
			$this->_prepareMockSession($email, $id, true);
			$this->runTestSqlFile("populateUsers.sql");

			$firstName = "axel";
			$lastName  = "ancona esselmann";
			$phoneNbr  = "(626) 344-9785";
			$address   = "1390 34th ave";
			$city      = "SF";
			$zip       = "94122";
			$state     = "CA";
			$country   = "USA";

			# When
			$user->login($email, "1234password");
			$result = $user->updateUserDetails(
				$firstName,
				$lastName,
				$phoneNbr,
				$address,
				$city,
				$zip,
				$state,
				$country
			);
			$result = $user->updateUserDetails(
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL
			);

			# Then
			$this->assertTableHas(
				"user_details",
				[
					"user_id"    => 9,
					"first_name" => $firstName,
					"last_name"  => $lastName,
					"phone_nbr"  => $phoneNbr,
					"address"    => $address,
					"city"       => $city,
					"zip"        => $zip,
					"country"    => $country
				]);
			$this->assertFalse($result);
		}
		public function test_updateUserDetails_all_fields_previously_not_null() {
			# Given
			$email = "axelesse.lmann@gmail";
			$id    = 9;
			$user  = $this->_getUser();
			$this->_prepareMockSession($email, $id, true);
			$this->runTestSqlFile("populateUsers.sql");

			$firstName = "axel";
			$lastName  = "ancona esselmann";
			$phoneNbr  = "(626) 344-9785";
			$address   = "1390 34th ave";
			$city      = "SF";
			$zip       = "94122";
			$state     = "CA";
			$country   = "USA";

			# When
			$user->login($email, "1234password");
			$result = $user->updateUserDetails(
				"aPrevValue",
				"aPrevValue",
				"aPrevValue",
				"aPrevValue",
				"aPrevValue",
				"aPrevValue",
				"aPrevValue",
				"aPrevValue"
			);
			$result = $user->updateUserDetails(
				$firstName,
				$lastName,
				$phoneNbr,
				$address,
				$city,
				$zip,
				$state,
				$country
			);

			# Then
			$this->assertTableHas(
				"user_details",
				[
					"user_id"    => 9,
					"first_name" => $firstName,
					"last_name"  => $lastName,
					"phone_nbr"  => $phoneNbr,
					"address"    => $address,
					"city"       => $city,
					"zip"        => $zip,
					"country"    => $country
				]);
			$this->assertTrue($result);
		}
		public function test_getUserDetails() {
			# Given
			$email = "axelesse.lmann@gmail";
			$id    = 9;
			$user  = $this->_getUser();
			$this->_prepareMockSession($email, $id, true);
			$this->runTestSqlFile("populateUsers.sql");

			$firstName = "axel";
			$lastName  = "ancona esselmann";
			$phoneNbr  = "(626) 344-9785";
			$address   = "1390 34th ave";
			$city      = "SF";
			$zip       = "94122";
			$state     = "CA";
			$country   = "USA";

			$user->login($email, "1234password");
			$result = $user->updateUserDetails(
				$firstName,
				$lastName,
				$phoneNbr,
				$address,
				$city,
				$zip,
				$state,
				$country
			);

			# When
			$result = $user->getUserDetails();

			# Then
			$expected = [
				"first_name" => $firstName,
				"last_name"  => $lastName,
				"phone_nbr"  => $phoneNbr,
				"address"    => $address,
				"city"       => $city,
				"zip"        => $zip,
				"state"      => $state,
				"country"    => $country
			];
			$this->assertEquals($expected, $result);
		}
	}
}