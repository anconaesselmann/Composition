<?php
/**
 *
 */
namespace aae\api {
    use \aae\db\FunctionAPI as FAPI;
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\api
	 */
	class Runkeeper {
        private $_storageAPI;
        private $_ch;
        private $_email;
        private $_userName;
        private $_password;
        private $_cookieFile;
        private $_currentMonth;
        private $_hasMoreActivities;
        private $_user;
        private $_activities;
        private $_path;
		public function __construct($storageAPI) {
            $this->_storageAPI = $storageAPI;
            $this->_levels = [1., .5, .1, .05, .1];
            $this->_reset();

            $this->_path    = "/Dropbox/WebServer/public/anconaesselmann/content/"; # TODO: don't hard code
        }

        private function _reset() {
            $now                      = new \DateTime();
            $this->_currentMonth      = new \DateTime($now->format('Y-m')."-01");
            $this->_hasMoreActivities = true;
            $this->_activities        = [];
        }
        public function offline($user) {
            $this->_user = $user;
        }
        private function _getUserId() {
            return (is_object($this->_user)) ? $this->_user->getId() : $this->_user;
        }
        public function login($user) {
            $this->_reset();
            $this->_user = $user;
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
            $runkeeperLogin = $this->_storageAPI->get_runkeeper_login($this->_getUserId());
            $this->_storageAPI->setFetchMode(FAPI::RESET);

            $url               = "https://runkeeper.com/login";
            $this->_email      = $runkeeperLogin["user_email"];
            $this->_userName   = $runkeeperLogin["user_name"];
            $this->_password   = $runkeeperLogin["user_password"];
            $this->_cookieFile = '/Dropbox/WebServer/public/anconaesselmann/content/cookies/'.$this->_email.'.txt';

            $fields = array(
                    'email'             => urlencode($this->_email),
                    'password'          => urlencode($this->_password),
                    '_eventName'        => 'submit',
                    'redirectUrl'       => '',
                    'flow'              => '',
                    'failUrl'           => '',
                    'submitLogin'       => 'Log In',
                    'lightBoxLogInForm' => ''
            );

            $z = array(
                'post'       => $fields,
                'cookiefile' => $this->_cookieFile
            );
            $ch =  curl_init();

            $useragent = isset($z['useragent']) ? $z['useragent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2';

            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt( $ch, CURLOPT_POST, isset($z['post']) );

            if( isset($z['post']) ) {
                curl_setopt( $ch, CURLOPT_POST, count($z['post']) );
                $fields_string = '';
                foreach($z['post'] as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                rtrim($fields_string, '&');
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );
            }
            if( isset($z['refer']) )        curl_setopt( $ch, CURLOPT_REFERER, $z['refer'] );

            curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, ( isset($z['timeout']) ? $z['timeout'] : 5 ) );
            curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
            curl_setopt( $ch, CURLOPT_COOKIEJAR,  $this->_cookieFile );
            curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->_cookieFile );

            $result = curl_exec( $ch );
            $this->_ch = $ch;
        }


        public function getActivityList($year, $month) {
            $url             = "https://runkeeper.com/activitiesByDateRange?userName=".$this->_userName."&startDate=$month-01-".(string)$year;
            $result          = $this->_fetch($url);
            $activities      = json_decode($result, true);
            $activityObjects = $this->_convertRunkeeperMonthRecordToActivities($activities);

            return $activityObjects;
        }

        public function getTrackFromGPX($gpx) {
            $gpxParser = new \aae\geo\parsers\GPXParser();
            $track = $gpxParser->parseString($gpx);
            return $track;
        }

        private function _getActivityGPX($activity) {
            $activityId = $activity->getId();

            $url       = "https://runkeeper.com/download/activity?activityId=$activityId&downloadType=gpx";
            $xmlResult = $this->_fetch($url);
            $gpx       = $this->_cleanXml($xmlResult);

            if (strlen($gpx) < 700) return false; // hack to skip non-gps activities
            return $gpx;
        }

        private function _saveGPX($gpx, $activityId) {
            $userId  = $this->_getUserId();
            $gpxPath = $this->_path.DIRECTORY_SEPARATOR."gpx".DIRECTORY_SEPARATOR.$userId.DIRECTORY_SEPARATOR;
            if (!file_exists($gpxPath)) mkdir($gpxPath, 0777, true);

            $gpxFile = fopen($gpxPath."$activityId.gpx", "w");
            fwrite($gpxFile, $gpx);
        }
        private function _saveKML($track, $activityId) {
            $userId  = $this->_getUserId();
            $kmlPath = $this->_path.DIRECTORY_SEPARATOR."kml".DIRECTORY_SEPARATOR.$userId.DIRECTORY_SEPARATOR;
            if (!file_exists($kmlPath)) mkdir($kmlPath, 0777, true);

            $kml    = new \aae\geo\formats\KML($track);
            $kmlFile = fopen($kmlPath."$activityId.kml", "w");
            fwrite($kmlFile, $kml->saveHtml());
        }
        private function _getSVG($track) {
            $projMapper = new \aae\geo\projection\Mercator();
            $cartTree   = $projMapper->map($track, true);
            $svg        = new \aae\svg\Svg(500, 500);
            $img        = new \aae\std\Image($svg, 900, 500);
            $img->strokeColor(0, 0, 255);

            $img->drawToFit($cartTree);
            $result = (string)$img;
            return $result;
        }

        public function getUserIds() {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_NUM_ARRAY);
            $users = $this->_storageAPI->getRunsUserIds();
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            return $users;
        }

        public function loadPreviousActivities($userId = NULL) {
            if (is_null($userId)) $userId  = $this->_getUserId();
            $gpxPath = $this->_path.DIRECTORY_SEPARATOR."gpx".DIRECTORY_SEPARATOR.$userId.DIRECTORY_SEPARATOR;
            $dir = new \DirectoryIterator($gpxPath);

            $geo = new \aae\geo\Calculator();
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot()) {
                    $fileName   = $gpxPath.$fileinfo->getFilename();
                    $gpx        = file_get_contents($fileName);
                    $activityId = $fileinfo->getBasename('.gpx');
                    $track      = $this->getTrackFromGPX($gpx);
                    $time       = $geo::getFirstCoordinate($track)->time;
                    $activity   = new \aae\track\Running($activityId, $time);
                    $activity->addTrack($track);
                    $activity->setUserId($userId);
                    $this->_activities[] = $activity;
                }
            }
        }


        public function loadAllActivities() {
            $gpxPath = $this->_path.DIRECTORY_SEPARATOR."gpx".DIRECTORY_SEPARATOR;
            $dir = new \DirectoryIterator($gpxPath);
            foreach ($dir as $fileinfo) {
                if ($fileinfo->isDot()) continue;
                if ($fileinfo == ".DS_Store") continue;
                $userId = $fileinfo->getFilename();
                $this->loadPreviousActivities($userId);
            }
        }

        public function sortAllActivitiesByDate() {
            usort($this->_activities, function ($a, $b) {
                return $a->getTime() < $b->getTime();
            });
        }

        public function setLevels($levels) {
            $this->_levels = $levels;
        }



        public function getActivity($activity) {
            $gpx = $this->_getActivityGPX($activity);
            if (!$gpx) return false;

            $activityId = $activity->getId();
            $track      = $this->getTrackFromGPX($gpx);

            $activity->addTrack($track);

            $this->_saveGPX($gpx,   $activityId);
            $this->_saveKML($track, $activityId);

            $svg = $this->_getSVG($track);
            return $svg;
        }

        private function _setAccumulators($distance, &$levelAccumulators) {
            $nbrLevels    = count($this->_levels);
            $currentLevel = $nbrLevels;
            for ($i=0; $i < $nbrLevels; $i++) {
                $levelAccumulators[$i] += $distance;
                if ($levelAccumulators[$i] > $this->_levels[$i]) {
                    $currentLevel = $i + 1;
                    for ($j=$i; $j < $nbrLevels; $j++) $levelAccumulators[$j] = 0;
                    return $currentLevel;
                }
            }
            return $currentLevel;
        }

        private function _getActivityName($activity) {
            switch (\aae\std\std::classFromNSClassName(get_class($activity))) {
                case 'Running':
                    return "Running";
                case 'Cycling':
                    return "Cycling";
                case 'Hiking':
                    return "Hiking";
                case 'Walking':
                    return "Walking";
                default:
                    throw new \Exception("Unknown activity: ".get_class($activity), 208161728);
            }
        }

        public function getActivityByLevel($activityId, $level) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $results = $this->_storageAPI->getTrack($activityId, $level);
            $this->_storageAPI->setFetchMode(FAPI::RESET);

            // var_dump($results);
            $points  = [];
            foreach ($results as $result) {
                $points[] = new \aae\math\geospatial\Point($result["latitude"], $result["longitude"], $result["elevation"], $result["time"]);
            }

            $sequence = new \aae\math\geospatial\Sequence($points);
            $track = new \aae\adt\Tree();
            $track->append($sequence);

            $time = \aae\geo\Calculator::getFirstCoordinate($track)->getDateTime();

            $activity = new \aae\track\Running($activityId, $time);
            $activity->addTrack($track);
            $activity->setUserId($this->_getUserId());

            return $activity;
        }

        public function getSVG($activity) {
            $track = $activity->getTrack();

            $projMapper = new \aae\geo\projection\Mercator();


            $svg = $this->_getSVG($track);
            return $svg;
        }

        public function getCombinedSVGFromObject($referencePoint, $maxDistance) {
            return $this->getCombinedSVG($this->_activities, $referencePoint, $maxDistance);
        }

        public function getCombinedSVG($activities, $referencePoint, $maxDistance) {
            $projMapper = new \aae\geo\projection\Mercator();
            $svg        = new \aae\svg\Svg(500, 500, "svg0");
            $img        = new \aae\std\Image($svg, 900, 600);
            $img->strokeColor(0, 0, 255);

            $geo = new \aae\geo\Calculator();

            $selection = [];

            foreach ($activities as $activity) {
                if ($activity instanceof \aae\track\Running) {
                    $track    = $activity->getTrack();
                    if (is_null($track)) continue;

                    $coord    = $geo::getFirstCoordinate($track);
                    $distance = $geo::distanceBetweenCoordinates($coord, $referencePoint);

                    if ($distance < $maxDistance) {
                        $selection[] = $activity;
                    }
                }
            }

            // $color = new \aae\svg\RainbowColor(count($selection));
            // $img->strokeColor($color);

            $colors = [];
            $colors[4] = "currentColor";//new \aae\svg\Color(0,0,255);
            $colors[7] = new \aae\svg\Color(255,0,0);

            foreach ($selection as $activity) {
                $track    = $activity->getTrack();

                $cartTree = $projMapper->map($track, true);

                $parameters = new \aae\draw\Parameters();
                $parameters->set(new \aae\draw\parameters\Group('user'.$activity->getUserId()));
                $parameters->set(new \aae\draw\parameters\Id($activity->getId()));

                $img->strokeColor($colors[$activity->getUserId()]);
                $img->drawToFit($cartTree, $parameters);
            }
            return (string)$img;
        }


        public function getCombinedKML($activities) {
            $kmlParser = new \aae\geo\formats\KML();

            foreach ($activities as $activity) {
                if ($activity instanceof \aae\track\Running) {
                    $track    = $activity->getTrack();
                    if (is_null($track)) continue;
                    $selection[] = $activity;
                }
            }

            foreach ($selection as $activity) {
                $track = $activity->getTrack();
                $time  = \aae\geo\Calculator::getFirstCoordinate($track)->getDateTime()->format('Y-m');
                $time  = implode("\\", explode("-", $time));
                $name  = \aae\geo\Calculator::getFirstCoordinate($track)->getDateTime()->format('d');

                $kmlParser->addTrack($track, "Tracks\\".$time, "Tours\\".$time, $name." (".$activity->getId().")");

            }
            return (string)$kmlParser;
        }

        public function getNextMonth() {
            $year      = $this->_currentMonth->format('Y');
            $month     = $this->_currentMonth->format('M');
            $monthData = $this->getActivityList($year, $month);
            $this->_currentMonth->modify('-1 month');

            if ((int)$this->_currentMonth->format('Y') == 2010) {
                $this->_hasMoreActivities = false;
            }
            return $monthData;
        }
        public function hasMoreActivities() {
            return $this->_hasMoreActivities;
        }
        public function getNewActivityRecords() {
            $lastActivityId = $this->getLastSyncedActivity();

            while ($this->hasMoreActivities()) {
                $monthActivities = $this->getNextMonth();
                foreach ($monthActivities as $activity) {
                    if ($lastActivityId == $activity->getId()) return $this->_activities;
                    $this->_activities[] = $activity;
                }
            }
            return $this->_activities;
        }

        private function _getNewActivityRecords() {
            $lastActivityId  = $this->getLastSyncedActivity();
            $activityRecords = [];

            while ($this->hasMoreActivities()) {
                $monthActivities = $this->getNextMonth();
                foreach ($monthActivities as $activity) {
                    if ($lastActivityId == $activity->getId()) return $activityRecords;
                    $activityRecords[] = $activity;
                }
            }
            return $activityRecords;
        }

        public function syncActivities() {

            $activities = $this->_getNewActivityRecords();

            ini_set('max_execution_time', 300);
            ini_set('memory_limit','5000M');

            usort($activities, function ($a, $b) {
                return $a->getTime() > $b->getTime();
            });

            foreach ($activities as $activity) {
                $gpx = $this->_getActivityGPX($activity);
                if (!$gpx) continue;

                $activityId = $activity->getId();
                $track      = $this->getTrackFromGPX($gpx);
                $activity->addTrack($track);

                // echo $this->_getSVG($activity->getTrack());

                $this->saveActivity($activity);

                $mostRecentActivity = $activity;
                $this->updateMostRecentActivity($mostRecentActivity);
            }
            return $activities;
        }

        public function saveActivity($activity) {
            $activityId        = $activity->getId();
            $geo               = new \aae\geo\Calculator();
            $track             = $activity->getTrack();
            $previousPoint     = NULL;
            $nbrLevels         = count($this->_levels);
            $levelAccumulators = [];
            for ($i=0; $i < $nbrLevels; $i++) $levelAccumulators[] = 0;
            foreach ($track as $sequence) {
                $nbrPoints = count($sequence);
                foreach ($sequence as $point) {
                    $sequenceNbr = $sequence->getSegmentNbr()."\n";
                    $nbrPoints--;
                    if (is_null($previousPoint)) {
                        $previousPoint = $point;
                        $currentLevel  = 1; // TODO: I think these have to change to 1...
                    } else if ($nbrPoints <= 0) {
                        $currentLevel = 1;
                    } else {
                        $distance      = $geo::distanceBetweenCoordinates($previousPoint, $point);
                        $currentLevel  = $this->_setAccumulators($distance, $levelAccumulators);
                        $previousPoint = $point;
                    }

                    $gridId = 1; // TODO: calculate grid id (involves lat, lon and level)

                    $this->_storageAPI->insertTrackPoint(
                        $activity->getUserId(),
                        $activity->getId(),
                        $point->lat,
                        $point->lon,
                        $point->ele,
                        $point->getDateTime(),
                        $this->_getActivityName($activity),
                        $sequenceNbr,
                        $gridId,
                        $currentLevel
                    );
                }
            }
        }

        public function getAllActivitiesByLevel($userId, $level) {
            $activities  = [];
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $activityIdRecords = $this->_storageAPI->getActivityIds($userId);
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            foreach ($activityIdRecords as $activityIdRecord) {
                $activityId   = $activityIdRecord["activity_id"];
                $activity     = $this->getActivityByLevel($activityId, $level);
                $activity->setUserId($userId);
                $activities[] = $activity;
                // return $activities;
            }
            return $activities;
        }


        public function getLastSyncedActivity() {
            return (int)$this->_storageAPI->getMostRecentRunkeeperActivityId($this->_getUserId());
        }
        public function updateMostRecentActivity($activity) {
            return (bool)$this->_storageAPI->updateMostRecentRunkeeperActivityId($this->_getUserId(), $activity->getId());
        }

        private function _cleanXml($result) {
            try {
                $xml    = simplexml_load_string($result);
            } catch (\Exception $e) {
                $matches = array();
                // echo $e->getMessage();

                preg_match('/Opening and ending tag mismatch: (.*) line (\d*)/', $e->getMessage(), $matches);

                $lines = explode("\n", $result);

                $lineNbr = (int)$matches[2] - 1;

                for ($i = $lineNbr - 1; $i < count($lines); $i++) {
                    if ($lines[$i] == $lines[$lineNbr]) {
                        unset($lines[$i]);
                        $result = implode("\n", $lines);
                        break;
                    }
                }
            }
            return $result;
        }

        private function _fetch($url, $z=null ) {
            $useragent = isset($z['useragent']) ? $z['useragent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2';

            curl_setopt( $this->_ch, CURLOPT_URL, $url );
            curl_setopt( $this->_ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $this->_ch, CURLOPT_AUTOREFERER, true );
            curl_setopt( $this->_ch, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt( $this->_ch, CURLOPT_POST, isset($z['post']) );

            if( isset($z['post']) ) {

                curl_setopt( $this->_ch, CURLOPT_POST, count($z['post']) );
                $fields_string = '';
                foreach($z['post'] as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                rtrim($fields_string, '&');
                curl_setopt( $this->_ch, CURLOPT_POSTFIELDS, $fields_string );
            }
            if( isset($z['refer']) )        curl_setopt( $this->_ch, CURLOPT_REFERER, $z['refer'] );

            curl_setopt( $this->_ch, CURLOPT_USERAGENT, $useragent );
            curl_setopt( $this->_ch, CURLOPT_CONNECTTIMEOUT, ( isset($z['timeout']) ? $z['timeout'] : 5 ) );
            curl_setopt( $this->_ch, CURLOPT_COOKIESESSION, true );
            curl_setopt( $this->_ch, CURLOPT_COOKIEJAR,  $this->_cookieFile );
            curl_setopt( $this->_ch, CURLOPT_COOKIEFILE, $this->_cookieFile );

            $result = curl_exec( $this->_ch );
            return $result;
        }

        private function _convertRunkeeperMonthRecordToActivities($monthRecord) {
            $activities = [];
            foreach ($monthRecord as $activity) {
                foreach ($activity as $year => $months) {
                    foreach ($months as $month) {
                        foreach ($month as $activityArray) {
                            $dateTime = \DateTime::createFromFormat("Y M j", $activityArray["year"] ." ".
                                                                             $activityArray["month"]." ".
                                                                             $activityArray["dayOfMonth"]);

                            $type       = $activityArray["mainText"];
                            $activityId = (int)$activityArray["activity_id"];
                            if (strcmp($type, 'Running') === 0) {
                                $activityObj  = new \aae\track\Running($activityId, $dateTime);
                            } else if (strcmp($type, 'Cycling') === 0) {
                                $activityObj  = new \aae\track\Cycling($activityId, $dateTime);
                            } else if (strcmp($type, 'Hiking') === 0) {
                                $activityObj  = new \aae\track\Hiking($activityId, $dateTime);
                            } else if (strcmp($type, 'Walking') === 0) {
                                $activityObj  = new \aae\track\Walking($activityId, $dateTime);
                            } else {
                                $activityObj  = new \aae\track\Activity   ($activityId, $dateTime);
                            }
                            $activityObj->setUserId($this->_getUserId());
                            $activities[] = $activityObj;
                        }
                    }
                }
            }
            usort($activities, function ($a, $b) {
                return $a->getId() < $b->getId();
            });
            return $activities;
        }
	}
}