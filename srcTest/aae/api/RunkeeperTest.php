<?php
namespace aae\api {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class RunkeeperTest extends \aae\unitTesting\DbTestCase {

        public function test___construct() {
            // $this->setUp();
            $fAPI = new \aae\db\FunctionAPI($this->getDb(), array("dbName" => "tests"));
			$obj  = new Runkeeper($fAPI);

            $levels = [
                0 => 1.,
                1 => .1,
                2 => .02
            ];
            $obj->setLevels($levels);

            // echo $this->getErrorLog();

            $activity = new \aae\track\Running(12345, new \DateTime);
            $track = $obj->getTrackFromGPX($this->getTestDataContent("12345.gpx"));
            $activity->setUserId(4);
            $activity->addTrack($track);

            $obj->saveActivity($activity, $levels);

            $activity = $obj->getActivityByLevel(12345, 3);

            $svg = $obj->getSVG($activity);

            echo $svg;

            $activities = $obj->getAllActivitiesByLevel(4, 2);
            var_dump($activities);

            // $this->showTable("tracks");
            // $this->showTable("points");
            // $this->showTable("track_points");
            // $this->showTable("grids");
            //
            $this->showTable("activities");
            $this->showTable("sequences");
            $this->getErrorLog();
		}


        protected function _getMockMonth() {
            $result = [
               "activities" => [
                    2016 => [
                       "Jan" => [
                            0 => [
                               "month"         => "Jan",
                               "distance"      => "2.56",
                               "dayOfMonth"    => "31",
                               "year"          => "2016",
                               "activity_id"   => 727691593,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "23:53"
                            ],
                            1 => [
                               "month"         => "Jan",
                               "distance"      => "6.04",
                               "dayOfMonth"    => "30",
                               "year"          => "2016",
                               "activity_id"   => 726987009,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "1:08:21"
                            ],
                            2 => [
                               "month"         => "Jan",
                               "distance"      => "5.60",
                               "dayOfMonth"    => "29",
                               "year"          => "2016",
                               "activity_id"   => 726418341,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "50:59"
                            ],
                            3 => [
                               "month"         => "Jan",
                               "distance"      => "3.04",
                               "dayOfMonth"    => "29",
                               "year"          => "2016",
                               "activity_id"   => 726363038,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "24:21"
                            ],
                            4 => [
                               "month"         => "Jan",
                               "distance"      => "2.83",
                               "dayOfMonth"    => "28",
                               "year"          => "2016",
                               "activity_id"   => 726112289,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "32:19"
                            ],
                            5 => [
                               "month"         => "Jan",
                               "distance"      => "0.85",
                               "dayOfMonth"    => "23",
                               "year"          => "2016",
                               "activity_id"   => 723502514,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "9:24"
                            ],
                            6 => [
                               "month"         => "Jan",
                               "distance"      => "1.42",
                               "dayOfMonth"    => "13",
                               "year"          => "2016",
                               "activity_id"   => 718658318,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "11:37"
                            ],
                            7 => [
                               "month"         => "Jan",
                               "distance"      => "14.37",
                               "dayOfMonth"    => "11",
                               "year"          => "2016",
                               "activity_id"   => 717557862,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "2:25:07"
                            ],
                            8 => [
                               "month"         => "Jan",
                               "distance"      => "9.02",
                               "dayOfMonth"    => "10",
                               "year"          => "2016",
                               "activity_id"   => 717276105,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "1:28:54"
                            ],
                            9 => [
                               "month"         => "Jan",
                               "distance"      => "15.65",
                               "dayOfMonth"    => "9",
                               "year"          => "2016",
                               "activity_id"   => 716717482,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "2:27:20"
                            ],
                            10 => [
                               "month"         => "Jan",
                               "distance"      => "7.27",
                               "dayOfMonth"    => "8",
                               "year"          => "2016",
                               "activity_id"   => 716172155,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "1:04:43"
                            ],
                            11 => [
                               "month"         => "Jan",
                               "distance"      => "13.59",
                               "dayOfMonth"    => "7",
                               "year"          => "2016",
                               "activity_id"   => 715719424,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "2:11:33"
                            ],
                                12 => [
                               "month"         => "Jan",
                               "distance"      => "9.25",
                               "dayOfMonth"    => "6",
                               "year"          => "2016",
                               "activity_id"   => 715247594,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "1:24:53"
                            ],
                            13 => [
                               "month"         => "Jan",
                               "distance"      => "7.64",
                               "dayOfMonth"    => "5",
                               "year"          => "2016",
                               "activity_id"   => 714607237,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "1:04:37"
                            ],
                            14 => [
                               "month"         => "Jan",
                               "distance"      => "5.96",
                               "dayOfMonth"    => "3",
                               "year"          => "2016",
                               "activity_id"   => 713666325,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "47:38"
                            ],
                            15 => [
                               "month"         => "Jan",
                               "distance"      => "6.87",
                               "dayOfMonth"    => "2",
                               "year"          => "2016",
                               "activity_id"   => 713204326,
                               "distanceUnits" => "mi",
                               "mainText"      => "Running",
                               "monthNum"      => "01",
                               "type"          => "CARDIO",
                               "live"          => false,
                               "username"      => "DudeOnRock",
                               "elapsedTime"   => "2:42:41"
                            ]
                        ]
                    ]
                ]
            ];
            return $result;
        }

	}
}