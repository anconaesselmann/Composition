<?php
namespace aae\db {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	use \aae\db\FunctionAPI as FAPI;
	/**
	 * @group database
	 */
	class FunctionAPITest extends \aae\unitTesting\DbTestCase {
		public $sut;

		public function setUp() {
			parent::setUp();
			$this->sut = new FunctionAPI($this->getDb(), array("dbName" => "tests"));
		}

		public function test_magic_method_call() {
			# When
			$result = $this->sut->createUser("test1", "test2");
			# Then
			$expected = true;
			$this->assertEquals($expected, $result);
		}

		public function testException_function_not_in_db() {
			$expectedCode = 1015141104;

			try {
				$result = $this->sut->aFunction("test");

				$this->fail("Expected Exception with code $expectedCode");
			} catch (StorageAPIException $e) {
				$code = $e->getCode();
				$this->assertEquals($expectedCode, $code);
				return;
			}
		}
		public function test_fetch_stored_procedure_multiple_rows_as_array() {
			$expected = array(
				array(1,'a','abc','value1'),
				array(3,'c','ghi','value1'),
				array(6,'f','pqr','value1')
			);
			$this->sut->setFetchMode(FAPI::FETCH_NUM_ARRAY);
			$result = $this->sut->getUsers("value1");
			$this->assertEquals($expected, $result);

			$this->sut->setFetchMode(FAPI::RESET);
			try {
				$result = $this->sut->getUsers("value1");
			} catch (StorageAPIException $e) {
				return;
			}
			$this->fail("Exception should have been thrown, since the call is a procedure and needs a different fetch mode");
		}
		public function test_fetch_stored_procedure_one_row_as_array() {
			$expected = array(1,'a','abc','value1');
			$this->sut->setFetchMode(FAPI::FETCH_NUM_ARRAY | FAPI::FETCH_ONE_ROW);
			$result = $this->sut->getUsers("value1");
			$this->assertEquals($expected, $result);
		}
		public function test_fetch_stored_procedure_multiple_rows_as_dictionary() {
			$expected = array(
				array('user_id'       => '1',
					  'user_name'     => 'a',
					  'user_password' => 'abc',
					  'data'          => 'value1'
				),
				array('user_id'       => '3',
					  'user_name'     => 'c',
					  'user_password' => 'ghi',
					  'data'          => 'value1'
				),
				array('user_id' => '6',
					  'user_name' => 'f',
					  'user_password' => 'pqr',
					  'data' => 'value1'
				)
			);
			$this->sut->setFetchMode(FAPI::FETCH_ASS_ARRAY);
			$result = $this->sut->getUsers("value1");
			$this->assertEquals($expected, $result);
		}
		public function test_fetch_stored_procedure_one_row_as_dictionary() {
			$expected = array(
			    'user_id'       => '1',
				'user_name'     => 'a',
				'user_password' => 'abc',
				'data'          => 'value1'
			);
			$this->sut->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
			$result = $this->sut->getUsers("value1");
			$this->assertEquals($expected, $result);
		}
		public function test_fetch_stored_procedure_returning_table_with_only_one_column_as_array() {
			$expected = array('a','b','c','d','e','f');
			$this->sut->setFetchMode(FAPI::FETCH_NUM_ARRAY);
			$result = $this->sut->getUsersNames();
			$this->assertEquals($expected, $result);
		}
		public function test_fetch_stored_procedure_returning_table_with_only_one_column_as_dictionary() {
			$expected = array(
				array('user_name' => 'a'),
				array('user_name' => 'b'),
				array('user_name' => 'c'),
				array('user_name' => 'd'),
				array('user_name' => 'e'),
				array('user_name' => 'f')
			);
			$this->sut->setFetchMode(FAPI::FETCH_ASS_ARRAY);
			$result = $this->sut->getUsersNames();
			$this->assertEquals($expected, $result);
		}
		public function test_testing_escaping_of_strings() {
			$result = $this->sut->createUser('a "name"; /* */', "pw");
			$this->assertTableHas(
				"users",
				["user_name" => 'a "name"; /* */',
				 "user_password" => "pw"]
			);
		}
		public function test_test_inserting_NULL() {
			$result = $this->sut->createUser('aName', null);
			$this->assertTableHas(
				"users",
				["user_name" => 'aName',
				 "user_password" => null]
			);
		}
		public function test_test_inserting_false() {
			$result = $this->sut->setSettings(false, true, false);
			$this->assertTableHas(
				"settings",
				["s1" => false,
				 "s2" => true,
				 "s3" => false]
			);
		}
		public function test_addEvalSubstitution() {
			# Given
			$testObject = $this->getMockBuilder('\aae\db\ATestClassForEvalSubstitutionTesting')
			    ->disableOriginalConstructor()
			    ->getMock();
			$testObject->expects($this->exactly(3))
			    ->method('getTheSetting')
			    ->willReturn(true);
			$className = get_class($testObject);
			$callbackName = "getTheSetting";

			# When
			$this->sut->addEvalSubstitution($className, $callbackName);

			$result = $this->sut->setSettings($testObject, $testObject, $testObject);

			# Then
			$this->assertTableHas(
				"settings",
				["s1" => true,
				 "s2" => true,
				 "s3" => true]
			);
		}
		public function testException_addEvalSubstitution_no_substitution_set() {
			$testObject = $this->getMockBuilder('\aae\db\ATestClassForEvalSubstitutionTesting')
			    ->disableOriginalConstructor()
			    ->getMock()
			    ->method('getTheSetting')
			    ->willReturn(true);
			$className = get_class($testObject);
			$callbackName = "getTheSetting";
			$expectedCode = 226151433;
			$expectedMssgRegex = "/FunctionAPI has no eval substitution for an object of type /";
			try {
				$result = $this->sut->setSettings($testObject, $testObject, $testObject);
				$this->fail("Expected \aae\db\StorageAPIException with code $expectedCode containing \"$expectedMssg\"");
			} catch (\aae\db\StorageAPIException $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				$this->assertEquals($expectedCode, $code);
				$this->assertRegExp($expectedMssgRegex, $mssg);
				return;
			}
		}

	}
	class ATestClassForEvalSubstitutionTesting {
		public function getTheSetting() {}
	}
}