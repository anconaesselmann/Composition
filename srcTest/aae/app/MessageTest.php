<?php
namespace aae\app {
    require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
    /**
     * @group database
     */
    class MessageTest extends \aae\unitTesting\DbTestCase {
        public $sut;

        public function setUp() {
            parent::setUp();
            $this->email = $this->getMockBuilder('\aae\message\Email')
                ->disableOriginalConstructor()
                ->getMock();
            $fAPI      = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
            $this->sut = new Message($fAPI, $this->email);
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

        public function test_insertMessage() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $onnectionId = 3;
            $subject     = "aSubject";
            $body        = "aBody";
            $user        = $this->getUser("a.xelesselmann@gmail.com");

            # When insertMessage is called
            $result = $this->sut->insertMessage(
                $user,
                $onnectionId,
                $subject,
                $body
            );

            # Then
            $this->assertEquals(2, $result);
            $this->assertTableHas(
                "messages",
                ["sender_id"        => 2,
                 "connection_id"    => $onnectionId,
                 "messages_subject" => $subject,
                 "messages_body"    => $body,
                 "message_status"   => 0]);
        }

        public function test_getEmailAddressFromConnection() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $connectionId = 3;
            $user         = $this->getUser("a.xelesselmann@gmail.com");
            # When
            $result = $this->sut->getEmailAddressFromConnection($user, $connectionId);

            # Then
            $expected = "axeles.selmann@gmail.com";
            $this->assertEquals($expected, $result);
        }

        public function test_sendEmail() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $body         = "emailBody";
            $subject      = "emailSubject from %s";
            $connectionId = 3;
            $user         = $this->getUser("a.xelesselmann@gmail.com");
            $this->email
                ->expects($this->once())
                ->method('send')
                ->with(
                    $this->equalTo("DO_NOT_REPLY@tesing.com"),
                    $this->equalTo("tom"),
                    $this->equalTo("axeles.selmann@gmail.com"),
                    $this->equalTo("emailSubject from tom"),
                    $this->equalTo("emailBody"))
                ->willReturn(true);

            # When sendEmail is called
            $result = $this->sut->sendEmail(
                $user,
                $connectionId,
                $subject,
                $body,
                "DO_NOT_REPLY@tesing.com"
            );
            #$this->showTable("messages");
            $this->assertTableHas(
                "messages",
                ["sender_id"        => 2,
                 "connection_id"    => $connectionId,
                 "messages_subject" => $subject,
                 "messages_body"    => $body,
                 "message_status"   => 1]);
            $this->assertTrue($result);
        }
        public function test_getMessage() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $user = $this->getUser("a.xelesselmann@gmail.com");

            # When getMessage is called
            $result = $this->sut->getMessage($user, 1);

            # Then
            $expected = [
                "message" => "This is the body.",
                "subject" => "Subject",
                "connection_id" => "3",
                "time_sent" => "2014-11-12 16:32:34",
                "time_read" => ""
            ];
            $this->assertEquals($expected, $result);
        }


    }
}