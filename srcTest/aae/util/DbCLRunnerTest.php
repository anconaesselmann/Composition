<?php
namespace aae\util {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
    class DbCLRunnerTest extends \aae\unitTesting\DbTestCase {
		public $sut;

        public function setUp() {
            parent::setUp();
            $this->sut = new DbCLRunner($this->getTestDataPath('config.cnf'));
        }

        public function test_runSql() {
            # When runSql is called
            $result = $this->sut->runSql(
               'CREATE TABLE tests.works(id INT);
                INSERT INTO tests.works VALUES(5);'
            );

            # Then
            $this->assertTableHas("works",["id"=>5]);
        }

        public function test_runSqlFile() {
            # When runSql is runSqlFile
            $result = $this->sut->runSqlFile($this->getTestDataPath('setupTest.sql'));

            # Then
            $this->assertTableHas("works",["id"=>5]);
        }

        public function test_query() {
            # Given
            $this->sut->runSqlFile($this->getTestDataPath('setupTest.sql'));

            # When runSql is called
            $this->sut->query("INSERT INTO tests.works VALUES(99)");
            # Then
            $this->assertTableHas("works",["id"=>99]);
        }

        public function test_getLoginFromConfig() {
            # Given
            $dir = $this->getTestDataPath("c.cnf");

            # When
            $result = $this->sut->getLoginFromConfig($dir);

            # Then
            $expected = [
                "userName" => "aUser",
                "dbName"   => "aDB",
                "password" => "aPW",
                "host"     => "aHost"
            ];
            $this->assertEquals($expected, $result);
        }


	}
}