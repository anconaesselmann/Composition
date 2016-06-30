<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	/**
	 * @group database
	 */
	class ContractTest extends \aae\unitTesting\DbTestCase {
		protected function _getContract() {
			$fAPI = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
			return new Contract($fAPI);
		}

		/*public function test___construct() {
			$obj = $this->_getContract();
		}*/

		/*public function testException___getInfo_sharerId_could_not_be_retrieved() {
			$expectedCode = 'ERROR';
			$expectedMssgRegex = "/sharerId could not be retrieved/";

			$contract = $this->_getContract();
			try {
				$contract->getInfo(1234,1234);
				$this->fail("Expected \PDOException with code $expectedCode containing \"$expectedMssg\"");
			} catch (\PDOException $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				$this->assertEquals($expectedCode, $code);
				$this->assertRegExp($expectedMssgRegex, $mssg);
				return;
			}
		}*/

		public function test_getInfo_contractor_is_sharerId() {
			# Given
			$this->runTestSqlFile("populateContracts.sql");
			$contract = $this->_getContract();

			# When
			$result = $contract->getInfo(1,"axelesselmann@gmail.com");

			# Then
			$this->assertUserDefinedVariableEquals(2, '_sharerId');
			$this->assertEquals("some_info", $result);
		}
		public function test_getInfo_contractee_is_sharerId() {
			# Given
			$this->runTestSqlFile("populateContracts.sql");
			$contract = $this->_getContract();

			# When
			$result = $contract->getInfo(2,"ax.elesselmann@gmail.com");

			# Then
			$this->assertUserDefinedVariableEquals(4, '_sharerId');
			$this->assertEquals("some_other_info", $result);
		}

		public function test_initiateContract() {
			# Given
			$this->runTestSqlFile("populateContracts.sql");
			$user = $this->getMockBuilder('\aae\app\User')
				->disableOriginalConstructor()
				->getMock();
			$plainCode = "1234Code";
			$user->method('getCode')
             	 ->willReturn($plainCode);
            $codeHash = "hash1234";
			$user->method('createPWHash')
             	 ->willReturn($codeHash);
			$user->method('getEmail')
             	 ->willReturn("axel.esselmann@gmail.com");
			$contract = $this->_getContract();

			# When initiateContract is called
			$result = $contract->initiateContract($user);

			# Then
			$expected = ["contractId" => 1, "plainCode" => $plainCode];
			$this->assertEquals($expected, $result);

			#$this->showTable("initiated_contracts");
			$this->assertTableHas(
				"initiated_contracts",
				["init_contract_id" => $result["contractId"],
				 "initiator_id"     => 5,
				 "init_code"        => $codeHash]);
		}

		public function test_reciprocateContract() {
			# Given
			$plainCode = "1234Code";
			$codeHash = "hash1234";
			$this->runTestSqlFile("populateContracts.sql");
			$this->query("INSERT INTO tests.initiated_contracts VALUES(NULL, 7, \"$codeHash\", NULL, NULL)");
			$user = $this->getMockBuilder('\aae\app\User')
				->disableOriginalConstructor()
				->getMock();
			$user->method('getEmail')
             	 ->willReturn("a.xelesselmann@gmail.com");
            $user->expects($this->once())
                 ->method('verifyPWHash')
                 ->with($this->equalTo($plainCode),$this->equalTo($codeHash))
                 ->willReturn(true);
			$contractId = 1;
			$contract = $this->_getContract();

			# When ceciprocateContract is called
			$result = $contract->reciprocateContract($user, $contractId, $plainCode);

			# Then
			#$this->showTable("initiated_contracts");
			#$this->showTable("contracts");
			$this->assertTableHas(
				"initiated_contracts",
				["init_contract_id" => 1,
				 "initiator_id"     => 7,
				 "init_code"        => $codeHash,
				 "contract_id"      => 3]);
			$this->assertTableHas(
				"contracts",
				["contract_id"   => 3,
				 "contractor_id" => 7,
				 "contractee_id" => 2]);
			$this->assertTrue($result);
		}

		public function test_reciprocateContract_returns_false_and_does_not_update_contract_for_codes_that_have_been_used() {
			# Given
			$plainCode = "1234Code";
			$codeHash = "hash1234";
			$this->runTestSqlFile("populateContracts.sql");
			$this->query("INSERT INTO tests.initiated_contracts VALUES(NULL, 7, \"$codeHash\", 2, NULL)");
			$user = $this->getMockBuilder('\aae\app\User')
				->disableOriginalConstructor()
				->getMock();
			$user->method('getEmail')
             	 ->willReturn("a.xelesselmann@gmail.com");
            $user->expects($this->once())
                 ->method('verifyPWHash')
                 ->with($this->equalTo($plainCode),$this->equalTo($codeHash))
                 ->willReturn(true);
			$contractId = 1;
			$contract = $this->_getContract();

			# When ceciprocateContract is called
			$result = $contract->reciprocateContract($user, $contractId, $plainCode);

			# Then
			#$this->showTable("initiated_contracts");
			#$this->showTable("contracts");
			$this->assertTableHasNot(
				"initiated_contracts",
				["init_contract_id" => 1,
				 "initiator_id"     => 7,
				 "init_code"        => $codeHash,
				 "contract_id"      => 3]);
			$this->assertTableHasNot(
				"contracts",
				["contract_id"   => 3,
				 "contractor_id" => 7,
				 "contractee_id" => 2]);
			$this->assertFalse($result);
		}

		public function test_deleteInitiatedContract() {
			# Given
			$codeHash = "hash1234";
			$this->runTestSqlFile("populateContracts.sql");
			$this->query("INSERT INTO tests.initiated_contracts VALUES(NULL, 7, \"$codeHash\", NULL, NULL)");
			$user = $this->getMockBuilder('\aae\app\User')
				->disableOriginalConstructor()
				->getMock();
			$user->method('getEmail')
             	 ->willReturn("axeles.selmann@gmail.com");

			$contract = $this->_getContract();

			# When
			$result = $contract->deleteInitiatedContract($user);

			# Then
			$this->assertTableHasNot(
				"initiated_contracts",
				["init_contract_id" => 1,
				 "initiator_id"     => 7]);
			$this->assertTrue($result);
		}
	}
}