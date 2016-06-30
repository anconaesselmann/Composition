<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	/**
	 * @group database
	 */
	class ConnectionTest extends \aae\unitTesting\DbTestCase {
		use \aae\unitTesting\TestFilesTrait;

		public $sut;

		public $detailsB = [
                "user_a_id"       => "7",
                "user_b_id"       => "2",
                "status"          => "0",
                "alias"           => "tom",
                "can_be_messaged" => "0",
                "show_real_name"  => "0",
                "show_user_name"  => "0",
                "show_alias"      => "1",
                "show_email"      => "1",
                "show_phone"      => "0",
                "show_address"    => "0",
                "user_name"       => "axel1",
				"user_email"      => "a.xelesselmann@gmail.com",
				"connection_id"   => "3"
		];
		public $detailsB_display = [
			"name"            => "tom",
			"connection_id"   => 3,
			"email"           => "a.xelesselmann@gmail.com",
			"can_be_messaged" => false

		];
		public $detailsA = [
                "user_a_id"       => "7",
                "user_b_id"       => "2",
                "status"          => "0",
                "alias"           => "tim",
                "can_be_messaged" => "1",
                "show_real_name"  => "1",
                "show_user_name"  => "0",
                "show_alias"      => "0",
                "show_email"      => "0",
                "show_phone"      => "0",
                "show_address"    => "0",
                "user_name"       => "axel6",
				"user_email"      => "axeles.selmann@gmail.com",
				"connection_id"   => "3"
		];
		public $detailsA_display = [
			"name"            => "axel6",
			"connection_id"   => "3",
			"email"           => "",
			"can_be_messaged" => true
		];

		protected function _getConnection() {
			$fAPI = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
			return new Connection($fAPI);
		}
		protected function _getUser($email, $plainCode = "", $codeHash = "") {
			$user = $this->getMockBuilder('\aae\app\User')
				->disableOriginalConstructor()
				->getMock();
			$user->method('getCode')
             	 ->willReturn($plainCode);
			$user->method('createPWHash')
             	 ->willReturn($codeHash);
			$user->method('getEmail')
             	->willReturn($email);
            return $user;
		}
		public function setUp() {
			parent::setUp();
			$this->sut = $this->_getConnection();
		}

		public function test_initiateConnection() {
			# Given
			$this->runTestSqlFile("populateConnections.sql");
			$plainCode = "1234Code";
			$codeHash = "hash1234";
			$user = $this->_getUser("axel.esselmann@gmail.com", $plainCode, $codeHash);
			$connection = $this->_getConnection();

			# When initiateConnection is called
			$result = $connection->initiateConnection($user, json_encode($this->detailsB));

			# Then
			$expected = ["connectionId" => 1, "plainCode" => $plainCode];
			$this->assertEquals($expected, $result);

			#$this->showTable("initiated_connections");
			$this->assertTableHas(
				"initiated_connections",
				["init_connection_id" => $result["connectionId"],
				 "initiator_id"     => 5,
				 "init_code"        => $codeHash]);
		}

		public function provider_initiateConnection_invalid_input() {
			return array(
				array("b", 1113142150),
				array("{}", 1113142151),
				array('{"show_real_name":false,"show_user_name":false,"show_alias":false}', 1113142152),
				array('{"show_alias":true}', 1113142153),
				array('{"show_alias":true,"alias":""}', 1113142153)
			);
		}

		/**
		 * @dataProvider provider_initiateConnection_invalid_input
		 */
		public function test_initiateConnection_connectionDetails_not_Json($details, $expected) {
			# Given
			$user   = $this->_getUser("a.xelesselmann@gmail.com", true);

			# When
			try {
				$result = $this->sut->initiateConnection($user, $details);
			} catch (\aae\std\ValidationException $e) {
				$code = $e->getCode();
				$this->assertEquals($expected, $code);
				return;
			}
			$this->fail("initiateConnection needs connection details in json.");
		}

		public function get_connection_for_reciprocate() {
			$this->plainCode = "1234Code";
			$this->codeHash = "hash1234";
			$this->connectionDetails = '{"can_be_messaged":false,"show_real_name":false,"show_alias":true,"show_phone":false,"show_email":true,"alias":"","show_user_name":false}';
			$this->runTestSqlFile("populateConnections.sql");
			$this->query("INSERT INTO tests.initiated_connections VALUES(NULL, 1, \"{$this->codeHash}\", NULL, \"\", NULL)");
			$this->query("INSERT INTO tests.initiated_connections VALUES(NULL, 7, \"{$this->codeHash}\", NULL, \"{\\\"can_be_messaged\\\":true}\", NULL)");
			$this->query("INSERT INTO tests.initiated_connections VALUES(NULL, 5, \"{$this->codeHash}\", NULL, \"\", NULL)");
			$this->user = $this->_getUser("a.xelesselmann@gmail.com");
            $this->user->expects($this->once())
                 ->method('verifyPWHash')
                 ->with($this->equalTo($this->plainCode),$this->equalTo($this->codeHash))
                 ->willReturn(true);
			$this->connectionId = 2;
			$connection = $this->_getConnection();
			return $connection;
		}
		public function test_reciprocateConnection() {
			# Given
			$connection = $this->get_connection_for_reciprocate();

			# When ceciprocateConnection is called
			$result = $connection->reciprocateConnection(
				$this->user,
				$this->connectionId,
				$this->plainCode,
				$this->connectionDetails
			);

			# Then
			#$this->showTable("initiated_connections");
			#$this->showTable("connections");
			#$this->showTable("connection_details");
			$this->assertTableHas(
				"initiated_connections",
				["init_connection_id" => 2,
				 "initiator_id"       => 7,
				 "init_code"          => $this->codeHash,
				 "connection_id"      => 3]);
			$this->assertTableHas(
				"connections",
				["connection_id" => 3,
				 "user_a_id"     => 7,
				 "user_b_id"     => 2,
				 "status"        => 0]);
			$this->assertTableHas(
				"connection_details",
				["connection_id" => 3,
				 "user_id"       => 2,
				 "show_email"    => true]);
			$this->assertTableHas(
				"connection_details",
				["connection_id"   => 3,
				 "user_id"         => 7,
				 "can_be_messaged" => true]);
			$this->assertEquals(3, $result);
		}

		public function test_reciprocateConnection_returns_false_and_does_not_update_connection_for_codes_that_have_been_used() {
			# Given
			$plainCode = "1234Code";
			$codeHash = "hash1234";
			$connectionDetails = "";
			$this->runTestSqlFile("populateConnections.sql");
			$this->query("INSERT INTO tests.initiated_connections VALUES(NULL, 7, \"$codeHash\", 2, \"\", NULL)");
			$user = $this->_getUser("a.xelesselmann@gmail.com");
            $user->expects($this->once())
                 ->method('verifyPWHash')
                 ->with($this->equalTo($plainCode),$this->equalTo($codeHash))
                 ->willReturn(true);
			$connectionId = 1;
			$connection = $this->_getConnection();

			# When ceciprocateConnection is called
			$result = $connection->reciprocateConnection($user, $connectionId, $plainCode, $connectionDetails);

			# Then
			#$this->showTable("initiated_connections");
			#$this->showTable("connections");
			$this->assertTableHasNot(
				"initiated_connections",
				["init_connection_id" => 1,
				 "initiator_id"       => 7,
				 "init_code"          => $codeHash,
				 "connection_id"      => 3]);
			$this->assertTableHasNot(
				"connections",
				["connection_id" => 3,
				 "user_a_id"     => 7,
				 "user_b_id"     => 2,
				 "status"        => 0]);
			$this->assertFalse($result);
		}

		public function test_deleteInitiatedConnection() {
			# Given
			$codeHash = "hash1234";
			$this->runTestSqlFile("populateConnections.sql");
			$this->query("INSERT INTO tests.initiated_connections VALUES(NULL, 7, \"$codeHash\", NULL, \"\", NULL)");
			$user = $this->_getUser("axeles.selmann@gmail.com");

			$connection = $this->_getConnection();

			# When
			$result = $connection->deleteInitiatedConnection($user);

			# Then
			$this->assertTableHasNot(
				"initiated_connections",
				["init_connection_id" => 1,
				 "initiator_id"     => 7]);
			$this->assertTrue($result);
		}

		protected function _prepareDbForDetailsRetrieval() {
			$this->runTestSqlFile("populateConnections.sql");
			$this->query(
			   "INSERT INTO tests.connections        VALUES(NULL,7,2,0,\"2014-11-12 16:32:34\");
			    INSERT INTO tests.connection_details VALUES(3,2,\"tom\",0,0,0,1,1,0,0);
			    INSERT INTO tests.connection_details VALUES(3,7,\"tim\",1,1,0,0,0,0,0)"
			);
		}
		public function test_getOwnDetails_user_a() {
			$userA        = $this->_getUser("axeles.selmann@gmail.com");
			$connectionId = 3;
			$this->_prepareDbForDetailsRetrieval();

			# When
			$result = $this->sut->getOwnDetails($userA, $connectionId);
			# Then
			$this->assertAssocContainsAssoc($result, $this->detailsA);
		}
		public function test_getOwnDetails_user_b() {
			$userB        = $this->_getUser("a.xelesselmann@gmail.com");
			$connectionId = 3;
			$this->_prepareDbForDetailsRetrieval();

			# When
			$result = $this->sut->getOwnDetails($userB, $connectionId);
			# Then
			$this->assertAssocContainsAssoc($result, $this->detailsB);
		}
		public function test_getOtherDetails_user_a() {
			$userA        = $this->_getUser("axeles.selmann@gmail.com");
			$connectionId = 3;
			$this->_prepareDbForDetailsRetrieval();

			# When
			$result = $this->sut->getOtherDetails($userA, $connectionId);
			# Then
			$this->assertAssocContainsAssoc($result, $this->detailsB_display);
		}
		public function test_getOtherDetails_user_b() {
			$userB        = $this->_getUser("a.xelesselmann@gmail.com");
			$connectionId = 3;
			$this->_prepareDbForDetailsRetrieval();

			# When
			$result = $this->sut->getOtherDetails($userB, $connectionId);
			# Then
			$this->assertAssocContainsAssoc($result, $this->detailsA_display);
		}
		public function test_getOwnDisplayNameForConnection_display_alias() {
			$this->runTestSqlFile("populateConnections.sql");
			$user   = $this->_getUser("a.xelesselmann@gmail.com");
			$connectionId = 3;
			$this->query(
			   "INSERT INTO tests.connections        VALUES(3,9,2,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.connection_details VALUES(3,2,\"tom\",0,0,0,1,0,0,0);"
			);
			# When
			$result = $this->sut->getOwnDisplayNameForConnection($user, $connectionId);

			# Then
			$expected = "tom";
			$this->assertEquals($expected, $result);
		}
		public function test_getOwnDisplayNameForConnection_display_userName_second_named() {
			$this->runTestSqlFile("populateConnections.sql");
			$user   = $this->_getUser("a.xelesselmann@gmail.com");
			$connectionId = 3;
			$this->query(
			   "INSERT INTO tests.connections        VALUES(3,9,2,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.connection_details VALUES(3,2,\"tom\",0,0,1,0,0,0,0);
				INSERT INTO tests.connection_details VALUES(3,9,\"tim\",0,0,1,0,0,0,0);"
			);
			# When
			$result = $this->sut->getOwnDisplayNameForConnection($user, $connectionId);

			# Then
			$expected = "axel1";
			$this->assertEquals($expected, $result);
		}
		public function test_getOwnDisplayNameForConnection_display_userName_first_named() {
			$this->runTestSqlFile("populateConnections.sql");
			$user   = $this->_getUser("axelesse.lmann@gmail.com");
			$connectionId = 3;
			$this->query(
			   "INSERT INTO tests.connections        VALUES(3,9,2,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.connection_details VALUES(3,2,\"tom\",0,0,1,0,0,0,0);
				INSERT INTO tests.connection_details VALUES(3,9,\"tim\",0,0,1,0,0,0,0);"
			);
			# When
			$result = $this->sut->getOwnDisplayNameForConnection($user, $connectionId);

			# Then
			$expected = "axel8";
			$this->assertEquals($expected, $result);
		}
		public function test_getOwnDisplayNameForConnection_display_real_name() {
			$this->runTestSqlFile("populateConnections.sql");
			$user   = $this->_getUser("a.xelesselmann@gmail.com");
			$connectionId = 3;
			$this->query(
			   "INSERT INTO tests.connections        VALUES(3,9,2,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.connection_details VALUES(3,2,\"tom\",0,1,0,0,0,0,0);"
			);
			#$this->showTable("connection_details");
			# When
			$result = $this->sut->getOwnDisplayNameForConnection($user, $connectionId);

			# Then
			$expected = "REAL NAME PLACEHOLDER";
			$this->assertEquals($expected, $result);
		}
		public function test_getAll() {
			# Given
			$this->runTestSqlFile("populateConnections.sql");
			$user   = $this->_getUser("axeles.selmann@gmail.com");
			$this->query(
			   "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.connection_details VALUES(3,2,\"conn0\",0,0,0,1,0,0,0);
				INSERT INTO tests.connection_details VALUES(3,7,\"conn0a\",0,0,0,1,0,0,0);
				INSERT INTO tests.connections        VALUES(4,9,7,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.connection_details VALUES(4,9,\"\",0,0,1,0,0,0,0);
				INSERT INTO tests.connection_details VALUES(4,7,\"\",0,0,1,0,0,0,0);
				INSERT INTO tests.connections        VALUES(5,7,5,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.connection_details VALUES(5,5,\"axel\",0,1,0,0,1,0,0);
				INSERT INTO tests.connection_details VALUES(5,7,\"other\",0,1,0,0,1,0,0);"
			);

			# When
			$result = $this->sut->getAll($user);

			# Then
			$this->assertEquals(3, count($result));
			$conn0 = [
				"name"          => "conn0",
				"connection_id" => "3",
				"email"         => ""
			];
			$this->assertAssocContainsAssoc($result[0], $conn0);
			$conn1 = [
				"name"          => "axel8",
				"connection_id" => "4",
				"email"         => ""
			];
			$this->assertAssocContainsAssoc($result[1], $conn1);
			$conn2 = [
				"email"         => "axel.esselmann@gmail.com",
				"connection_id" => "5"
			];
			$this->assertAssocContainsAssoc($result[2], $conn2);
		}
		public function test_checkInitResponse_has_been_accepted_and_returns_connection_id() {
			# Given
			$codeHash = "hash1234";
			$this->runTestSqlFile("populateConnections.sql");
			$user   = $this->_getUser("axeles.selmann@gmail.com");
			$this->query(
			   "INSERT INTO tests.connections           VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.initiated_connections VALUES(99, 7, \"{$codeHash}\", 3, \"{\\\"can_be_messaged\\\":true}\", NULL)");

			# When
			$result = $this->sut->checkInitResponse($user, 99);

			# Then
			$this->assertEquals(3, $result);
			$this->assertTableHasNot(
				"initiated_connections",
				["init_connection_id" => 99]);
		}
		public function test_checkInitResponse_has_not_been_accepted_and_returns_0() {
			# Given
			$codeHash = "hash1234";
			$this->runTestSqlFile("populateConnections.sql");
			$user   = $this->_getUser("axeles.selmann@gmail.com");
			$this->query(
			   "INSERT INTO tests.connections           VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.initiated_connections VALUES(99, 7, \"{$codeHash}\", NULL, \"{\\\"can_be_messaged\\\":true}\", NULL)");

			# When
			$result = $this->sut->checkInitResponse($user, 99);

			# Then
			$this->assertEquals(0, $result);
			$this->assertTableHas(
				"initiated_connections",
				["init_connection_id" => 99]);
		}
		public function test_delete_connection() {
			# Given
			$this->runTestSqlFile("populateConnections.sql");
			$user = $this->_getUser("axeles.selmann@gmail.com");
			$this->query(
			   "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
				INSERT INTO tests.connection_details VALUES(3,2,\"conn0\",0,0,0,1,0,0,0);
				INSERT INTO tests.connection_details VALUES(3,7,\"conn0a\",0,0,0,1,0,0,0);"
			);

			# When
			$result = $this->sut->deleteConnection($user, 3);

			#$this->showTable("deleted_connections");
			#$this->showTable("deleted_connection_details");
			# Then
			$this->assertEquals(1, $result);
			$this->assertTableHasNot(
				"connections",
				["connection_id" => 3]);
			$this->assertTableHasNot(
				"connection_details",
				["connection_id" => 3]);
			$this->assertTableHas(
				"deleted_connections",
				["connection_id" => 3]);
			$this->assertTableHas(
				"deleted_connection_details",
				["connection_id" => 3]);
		}
	}
}