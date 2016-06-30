<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	/*class MetadataTest extends \aae\unitTesting\DbTestCase {
        use \aae\unitTesting\TestFilesTrait;

        public $sut;

        public function setUp() {
            parent::setUp();
            $this->fAPI = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
            $this->sut  = new Metadata($this->fAPI, "test");
        }

        protected function getUser($email, $loggedIn = true) {
            $user = $this->getMockBuilder('\aae\app\User')
                ->disableOriginalConstructor()
                ->getMock();
            $user->method('isLoggedIn')
                ->willReturn($loggedIn);
            $user->method('getEmail')
                ->willReturn($email);
            $result = $this->query("CALL tests.getUserByEmail(\"$email\")");
            $userId = (int)$result->fetchObject()->user_id;
            $user->method('getId')
                ->willReturn($userId);
            $this->fAPI->addEvalSubstitution(get_class($user), "getId");
            return $user;
        }

        public function test_create_table() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            # When
            $this->sut->createMetadataTable();

            try {
                $this->query("INSERT INTO tests.test_metadata VALUES(NULL, 1, \"other\", NULL, NULL);");
            } catch (\PDOException $e) {
                $this->fail("Table was not created");
            }
        }
        public function test_new() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $other = "bla";

            #
            $this->sut->createMetadataTable();
            $metaId = $this->sut->create($user, $other);
            $result = $this->sut->get($user, $metaId);

            # Then
            $this->assertEquals("bla", $result["other"]);
        }
        public function test_new_withou_prior_table_creation() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $other = "bla";

            #
            $metaId = $this->sut->create($user, $other);
            $result = $this->sut->get($user, $metaId);

            # Then
            $this->assertEquals("bla", $result["other"]);
        }

	}*/
}