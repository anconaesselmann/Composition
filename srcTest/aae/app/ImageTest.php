<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ImageTest extends \aae\unitTesting\DbTestCase {
        use \aae\unitTesting\TestFilesTrait;
        public $sut;

        public function setUp() {
            parent::setUp();
            $this->fAPI = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
            $location = $this->getTestDataPath();
            $headers = $this->getMockBuilder('\aae\dispatch\Headers')
                ->disableOriginalConstructor()
                ->getMock();
            $this->sut = new Image($this->fAPI , $location, $headers);
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
        public function test__createDbEntry() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");

            # When
            $result = $this->sut->_createDbEntry($user);

            # Then
            $expected = 1;
            // $this->showTable("images");
            $this->assertEquals($expected, $result);
        }
	}
}