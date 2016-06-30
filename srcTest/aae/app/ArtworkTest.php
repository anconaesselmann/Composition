<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	/*class ArtworkTest extends \aae\unitTesting\DbTestCase {
        use \aae\unitTesting\TestFilesTrait;
        public $sut;

        public function setUp() {
            parent::setUp();
            $this->fAPI = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
            $this->sut = new Artwork($this->fAPI);
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
        public function test_setArtwork() {
            # Given
            $this->query("INSERT INTO tests.images VALUES(NULL, 1, \"\", NULL)");
            # When
            $result = $this->sut->setArtwork(1, "test", "test", NULL);
            $this->showTable("artwork");

            # Then
            $expected = "?";
            $this->assertEquals($expected, $result);
        }
	}*/
}