<?php
namespace aae\app {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class SignupTest extends \aae\unitTesting\DbTestCase {
        use \aae\unitTesting\TestFilesTrait;
		public $sut;

        public function setUp() {
            parent::setUp();
            $this->email = $this->getMockBuilder('\aae\message\Email')
                ->disableOriginalConstructor()
                ->getMock();
            $serializer     = new \aae\serialize\FileSerializer(new \aae\serialize\Json());
            $this->localize = new \aae\ui\Localizer($serializer, $this->getTestDataPath());
            $this->sut      = new Signup($this->email, "www.aae.dev", "DNR@aae.dev", "aae", $this->localize);

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

        public function test_create_and_sendRegistrationEmail() {
            # Given
            $toEmail  = "a.xelesselmann@gmail.com";
            $user     = $this->_getUser($toEmail);
            $code     = "123code";
            $lang     = "eng";
            $userName = "axel";
            $userPw   = "123pw";
            $user->method('create')
                ->willReturn($code);
            $this->email->expects($this->once())
                ->method('send')
                ->with(
                    $this->equalTo("DNR@aae.dev"),
                    $this->equalTo("aae"),
                    $this->equalTo($toEmail),
                    $this->equalTo("aae email verification"),
                    $this->equalTo("Please <a href=\"www.aae.dev/signup/confirm?e=a.xelesselmann%40gmail.com&c=$code\">confirm your email address</a> to complete the registration process.")
                )
                ->willReturn(true);

            # When
            $result = $this->sut->submit($user, $userName, $toEmail, $userPw, $lang);

            # Then
            $this->assertTrue($result);
        }

        public function test_confirm() {
            # Given
            $toEmail = "a.xelesselmann@gmail.com";
            $user    = $this->_getUser($toEmail);
            $code    = "123code";
            $user->method('verify')
                ->with(
                    $this->equalTo($toEmail),
                    $this->equalTo($code)
                )
                ->willReturn(true);

            # When
            $result = $this->sut->confirm($user, $code, $toEmail);

            # Then
            $this->assertTrue($result);
        }

	}
}