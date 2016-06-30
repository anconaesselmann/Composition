<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class SecureImageTest  extends \aae\unitTesting\DbTestCase {
        public $sut;

        public function setUp() {
            parent::setUp();
            $fAPI      = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
            $location = $this->getTestDataPath();
            $headers = $this->getMockBuilder('\aae\dispatch\Headers')
                ->disableOriginalConstructor()
                ->getMock();
            $this->sut = new SecureImage($fAPI, $location, $headers);
        }
        protected function getUser($email, $loggedIn = true) {
            $user = $this->getMockBuilder('\aae\app\User')
                ->disableOriginalConstructor()
                ->getMock();
            $user->method('isLoggedIn')
                ->willReturn($loggedIn);
            $user->method('getEmail')
                ->willReturn($email);
            return $user;
        }

        /**
         * runInSeparateProcess
        */
        public function test_get() {
            # Given
            $fileName = "aFolder/image1.jpg";

            # When get is called
            ob_start();
            $this->sut->get($fileName);
            $result = ob_get_clean();

            # Then EXPECTED_CONDITIONS
            $expected = 'File read';
            $this->assertEquals($expected, $result);
        }

	}
}