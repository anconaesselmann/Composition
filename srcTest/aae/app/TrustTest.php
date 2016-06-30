<?php
namespace aae\app {
    require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
    class TrustTest extends \aae\unitTesting\DbTestCase {
        use \aae\unitTesting\TestFilesTrait;
        public $sut;

        public function setUp() {
            parent::setUp();
            $fAPI      = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
            $pvc       = new DummyPVC();
            $this->sut = new Trust($fAPI, $pvc);
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
            return $user;
        }

        public function test_addTrustPointsToConnection() {
            # Given
            $connectionId = 4;
            $points = 5;
            $user = $this->getUser("axelesselmann@gmail.com");
            $this->query("INSERT INTO tests.connections VALUES(4, 1, 9, 0, NULL);");

            # When addTrustPointsToConnection is called
            $result = $this->sut->addTrustPointsToConnection($user, $connectionId, $points);

            # Then the total points are the number of points that where added
            $this->assertEquals($points, $result);
            $this->assertTableHas(
                "connection_trust",
                ["connection_id" => 4,
                "giver_id" => 1,
                "points_invested" => $points]
            );

            # When addTrustPointsToConnection is called again
            $result = $this->sut->addTrustPointsToConnection($user, $connectionId, $points);

            # Then total points are the previous total plus the new points
            $this->assertEquals($points * 2, $result);
                        $this->assertTableHas(
                "connection_trust",
                ["connection_id" => 4,
                "giver_id" => 1,
                "points_invested" => $points * 2]
            );
        }

        public function test_subtractTrustPointsToConnection() {
            # Given
            $connectionId = 4;
            $points = 5;
            $user = $this->getUser("axelesselmann@gmail.com");

            $this->query("INSERT INTO tests.connections VALUES(4, 1, 9, 0, NULL);");

            # When subtractTrustPointsToConnection is called
            $result = $this->sut->subtractTrustPointsToConnection($user, $connectionId, $points);

            # Then the total points are the number of points that where added
            $this->assertEquals(0, $result);
            $this->assertTableHas(
                "connection_trust",
                ["connection_id" => 4,
                "giver_id" => 1,
                "points_invested" => 0]
            );

            # When subtractTrustPointsToConnection is called again
            $this->sut->addTrustPointsToConnection($user, $connectionId, 99);
            $result = $this->sut->subtractTrustPointsToConnection($user, $connectionId, $points);

            # Then total points are the previous total plus the new points
            $this->assertEquals(99 - $points, $result);
            $this->assertTableHas(
                "connection_trust",
                ["connection_id" => 4,
                "giver_id" => 1,
                "points_invested" => 99 - $points]
            );
        }

        public function test_getUserIds() {
            # When getUserIds is called
            $result = $this->sut->getUserIds();

            # Then
            $expected = [
                "1", "2", "11", "3", "4", "5", "6", "7", "8", "9", "10"
            ];
            $this->assertEquals($result, $expected);
        }

        public function provider_updateAll() {
            $userIds = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11"];
            $defaultPassFail = true;
            return array(
                array(
                    $userIds,
                    $this->getTestDataContent("updateDataProviderSql/1.sql"),
                    "2015-01-29 16:00:01",
                    eval("return ".$this->getTestDataContent("updateDataProviderSql/1.array").";"),
                    $defaultPassFail
                ),
                array(
                    $userIds,
                    $this->getTestDataContent("updateDataProviderSql/2.sql"),
                    "2015-01-29 16:03:20",
                    eval("return ".$this->getTestDataContent("updateDataProviderSql/2.array").";"),
                    $defaultPassFail
                )
            );
        }

        /**
         * @dataProvider provider_updateAll
         */
        public function test_updateAll($userIds, $query, $nowTimestamp, $tableAssertions, $passFail) {
            $this->query($query);
            # Get db into unit-testable state
            $this->query("
                UPDATE tests.trust_settings   SET last_calculated = \"2014-01-29 16:00:00\";
                UPDATE tests.connection_trust SET last_calculated = \"2015-01-29 16:00:00\";
                UPDATE tests.user_trust_time  SET last_calculated = \"2015-01-29 16:00:00\";"
            );

            # When
            $result = $this->sut->updateAll($userIds, $nowTimestamp);

            // $this->showTable("connection_trust");
            // $this->showTable("user_trust_time");

            # Then
            $this->assertEquals($passFail, $result);

            foreach ($tableAssertions as $table) {
                // var_dump($table);
                $this->assertTableHas($table[0], $table[1]);
            }
        }

        public function test_prepareTrustScoreUpdate_notAllowed() {
            # When
            $result = $this->sut->updateAll([], "2015-01-29 16:10:00");
            $this->assertFalse($result);
        }
        public function test_getConnectionTrustScore() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $this->query("
                INSERT INTO tests.connections VALUES(1, 1, 2, 0, NULL);
                INSERT INTO tests.user_trust_time VALUES(2,123,456,0,0,789,\"2015-01-29 16:00:00\");
            ");
            $connectionId = 1;

            # When
            $result = $this->sut->getConnectionTrustScore($user, $connectionId);

            # Then
            $expected = [
                "trust_time_final" => 789,
                "trust_time_given" => 123,
                "trust_time_gotten" => 456
            ];
            $this->assertEquals($expected, $result);
        }

    }

    class DummyPVC implements trust\PointValueCalculatorInterface {
        public function getPointValue($personalScore, $pointsInvested) {
            return 1;
        }
    }
}