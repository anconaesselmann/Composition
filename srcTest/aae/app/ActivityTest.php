<?php
namespace aae\app {
    require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
    /**
     * @group database
     */
    class ActivityTest extends \aae\unitTesting\DbTestCase {
        use \aae\unitTesting\TestFilesTrait;

        public $sut;

        public function setUp() {
            parent::setUp();
            $fAPI = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
            $this->sut = new Activity($fAPI);
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
        public function test_trigger_connection_insert() {
            # Given
            $this->runTestSqlFile("populate.sql");

            # When
            $this->query(
               "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
                INSERT INTO tests.connection_details VALUES(3,2,\"alias_2\",0,0,0,1,0,0,0);
                INSERT INTO tests.connection_details VALUES(3,7,\"alias_7\",0,0,0,1,0,0,0);"
            );

            # Then
            $this->assertTableHas(
                "connections_activity",[
                    "user_id" => 7,
                    "action"  => 1
                ]);
            $this->assertTableHas(
                "connections_activity",[
                    "user_id" => 2,
                    "action"  => 2
                ]);
        }
        /*public function test_getConnectionsActivity_reciprocated() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $user = $this->_getUser("a.xelesselmann@gmail.com");

            $this->query(
               "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
                INSERT INTO tests.connection_details VALUES(3,2,\"alias_2\",0,0,0,1,0,0,0);
                INSERT INTO tests.connection_details VALUES(3,7,\"alias_7\",0,0,0,1,0,0,0);"
            );
            # When
            $result = $this->sut->getConnectionsActivity($user);

            # Then
            $this->assertAssocContainsAssoc(
                $result[0],
                [
                    "activity_id" =>2,
                    "category"    => "connections",
                    "action"      => "reciprocated",
                    "actor"       => "alias_7"
                ]
            );
        }
        public function test_getConnectionsActivity_initiated() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $user = $this->_getUser("axeles.selmann@gmail.com");

            $this->query(
               "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
                INSERT INTO tests.connection_details VALUES(3,2,\"alias_2\",0,0,0,1,0,0,0);
                INSERT INTO tests.connection_details VALUES(3,7,\"alias_7\",0,0,0,1,0,0,0);"
            );
            # When
            $result = $this->sut->getConnectionsActivity($user);

            # Then
            $this->assertAssocContainsAssoc(
                $result[0],
                [
                    "activity_id" =>1,
                    "category"    => "connections",
                    "action"      => "initiated",
                    "actor"       => "alias_2"
                ]
            );
        }*/

        public function test_trigger_messages_insert() {
            # Given
            $this->runTestSqlFile("populate.sql");

            # When
            $this->query(
               "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
                INSERT INTO tests.connection_details VALUES(3,2,\"alias_2\",0,0,0,1,0,0,0);
                INSERT INTO tests.connection_details VALUES(3,7,\"alias_7\",0,0,0,1,0,0,0);
                INSERT INTO tests.messages VALUES(NULL, 7, 3, \"subject1\", \"body1\", 0, NOW(), NOW(), NOW());"
            );

            # Then
            $this->assertTableHas(
                "messages_activity",[
                    "user_id" => 7,
                    "action"  => 3
                ]);
            $this->assertTableHas(
                "messages_activity",[
                    "user_id" => 2,
                    "action"  => 4
                ]);
        }
        /*public function test_getMessagesActivity_received() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $user = $this->_getUser("a.xelesselmann@gmail.com");

            $this->query(
               "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
                INSERT INTO tests.connection_details VALUES(3,2,\"alias_2\",0,0,0,1,0,0,0);
                INSERT INTO tests.connection_details VALUES(3,7,\"alias_7\",0,0,0,1,0,0,0);
                INSERT INTO tests.messages VALUES(NULL, 7, 3, \"subject1\", \"body1\", 0, NOW(), NOW(), NOW());"
            );
            # When
            $result = $this->sut->getMessagesActivity($user);

            # Then
            $this->assertAssocContainsAssoc(
                $result[0],
                [
                    "activity_id" =>2,
                    "category"    => "messages",
                    "action"      => "received",
                    "actor"       => "alias_7"
                ]
            );
        }
        public function test_getMessagesActivity_sent() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $user = $this->_getUser("axeles.selmann@gmail.com");

            $this->query(
               "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2014-11-12 16:32:34\");
                INSERT INTO tests.connection_details VALUES(3,2,\"alias_2\",0,0,0,1,0,0,0);
                INSERT INTO tests.connection_details VALUES(3,7,\"alias_7\",0,0,0,1,0,0,0);
                INSERT INTO tests.messages VALUES(NULL, 7, 3, \"subject1\", \"body1\", 0, NOW(), NOW(), NOW());"
            );
            # When
            $result = $this->sut->getMessagesActivity($user);

            # Then
            $this->assertAssocContainsAssoc(
                $result[0],
                [
                    "activity_id" =>1,
                    "category"    => "messages",
                    "action"      => "sent",
                    "actor"       => "alias_2"
                ]
            );
        }*/

        public function test_getActivity_initiated_sent() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $user = $this->_getUser("axeles.selmann@gmail.com");
            $time = '"2011-11-1 11:11:11"';

            $this->query(
               "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2010-10-10 10:10:10\");
                INSERT INTO tests.connection_details VALUES(3,2,\"alias_2\",0,0,0,1,0,0,0);
                INSERT INTO tests.connection_details VALUES(3,7,\"alias_7\",0,0,0,1,0,0,0);
                INSERT INTO tests.messages VALUES(NULL, 7, 3, \"subject1\", \"body1\", 0, $time, $time, NULL);"
            );

            # When
            $result = $this->sut->getActivity($user);

            # Then
            $this->assertAssocContainsAssoc(
                $result[0],
                [
                    "activity_id" =>1,
                    "category"    => "messages",
                    "action"      => "sent",
                    "actor"       => "alias_2",
                    "category_id" => "1"
                ]
            );
            $this->assertAssocContainsAssoc(
                $result[1],
                [
                    "activity_id" => 1,
                    "category"    => "connections",
                    "action"      => "initiated",
                    "actor"       => "alias_2",
                    "category_id" => "3"
                ]
            );
        }
        public function test_getActivity_reciprocated_received() {
            # Given
            $this->runTestSqlFile("populate.sql");
            $user = $this->_getUser("a.xelesselmann@gmail.com");
            $time = '"2011-11-1 11:11:11"';

            $this->query(
               "INSERT INTO tests.connections        VALUES(3,7,2,0,\"2010-10-10 10:10:10\");
                INSERT INTO tests.connection_details VALUES(3,2,\"alias_2\",0,0,0,1,0,0,0);
                INSERT INTO tests.connection_details VALUES(3,7,\"alias_7\",0,0,0,1,0,0,0);
                INSERT INTO tests.messages VALUES(NULL, 7, 3, \"subject1\", \"body1\", 0, $time, $time, NULL);"
            );
            # When
            $result = $this->sut->getActivity($user);

            # Then
            $this->assertAssocContainsAssoc(
                $result[0],
                [
                    "activity_id" =>2,
                    "category"    => "messages",
                    "action"      => "received",
                    "actor"       => "alias_7",
                    "category_id" => "1"
                ]
            );
            $this->assertAssocContainsAssoc(
                $result[1],
                [
                    "activity_id" => 2,
                    "category"    => "connections",
                    "action"      => "reciprocated",
                    "actor"       => "alias_7",
                    "category_id" => "3"
                ]
            );
        }

	}
}