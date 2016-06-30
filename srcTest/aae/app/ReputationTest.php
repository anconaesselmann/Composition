<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ReputationTest extends \aae\unitTesting\DbTestCase {
		protected function _getRep() {
			$pdo  = $this->getDb();
			$fAPI = new \aae\db\FunctionAPI($pdo, array("dbName" => "tests"));
			return new Reputation($fAPI);
		}


		public function test___construct() {
			$obj = $this->_getRep();
		}

		public function test_getRep() {
			# Given 
			$this->runTestSqlFile("populateReputations.sql");
			$userEmail = "axel.esselmann@gmail.com";
			$obj = $this->_getRep();
		
			# When 
			$result = $obj->getRep($userEmail);
			
			# Then 
			$expected = 100;
			$this->assertEquals($expected, $result);
		}

		public function test_updateAndGetRep() {
			# Given 
			$this->runTestSqlFile("populateReputations.sql");
			$userEmail = "axel.esselmann@gmail.com";
			$obj = $this->_getRep();

			$this->assertTableHas("reputation_events", ["beneficiary_id"=>5, "last_counted"=>null]);
		
			# When 
			$result = $obj->updateAndGetRep($userEmail);
			
			# Then 
			$expected = 115;
			$this->assertEquals($expected, $result);
			$this->assertTableHasNot("reputation_events", ["beneficiary_id"=>5, "last_counted"=>null]);
		}

		public function test_registerReputationEvent() {
			# Given 
			$this->runTestSqlFile("populateReputations.sql");
			$userEmail = "axel.esselmann@gmail.com";
			$eventType = 123;
			$benefactorId = 1;
			$obj = $this->_getRep();
		
			# When 
			$result = $obj->registerReputationEvent($userEmail, $eventType, $benefactorId);
			
			# Then 
			$expected = true;
			$this->assertEquals($expected, $result);
		}
		
	}
}