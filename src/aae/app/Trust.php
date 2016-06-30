<?php
/**
 *
 */
namespace aae\app {
    use \aae\db\FunctionAPI as FAPI;
    /**
     * @author Axel Ancona Esselmann
     * @package aae\app
     */
    class Trust {
        private $_pvc = NULL;

        public function __construct(\aae\db\FunctionAPI $storageAPI, trust\PointValueCalculatorInterface $pvc) {
            $this->_storageAPI = $storageAPI;
            $this->_pvc = $pvc;
        }
        public function addTrustPointsToConnection($user, $connectionId, $points) {
            $userId = $user->getId();
            $totalPoints = (int)$this->_storageAPI->addTrustPointsToConnection($userId, $connectionId, $points);
            return $totalPoints;
        }
        public function subtractTrustPointsToConnection($user, $connectionId, $points) {
            $userId = $user->getId();
            $totalPoints = (int)$this->_storageAPI->subtractTrustPointsToConnection($userId, $connectionId, $points);
            return $totalPoints;
        }

        public function getUserIds() {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_NUM_ARRAY);
            $userIds = $this->_storageAPI->getUserIds();
            $this->_storageAPI->resetFetchMode();
            return $userIds;
        }

        public function updateAll($userIds, $nowTimestamp) {
            $canUpdate = (bool)$this->_storageAPI->prepareTrustScoreUpdate();
            if (!$canUpdate) return false;
            foreach ($userIds as $userId) {
                $personalScore = (int)$this->_storageAPI->getPersonalScore($userId);
                $this->_storageAPI->setFetchMode(FAPI::FETCH_NUM_ARRAY);
                $connectionIds = $this->_storageAPI->getAllConnectionIds($userId);
                $this->_storageAPI->resetFetchMode();
                foreach ($connectionIds as $connectionId) {
                    $pointsInvested = (int)$this->_storageAPI->getNbrPointsInvested($userId, $connectionId);
                    if ($pointsInvested > 0) {
                        $pointValue   = $this->_pvc->getPointValue($personalScore, $pointsInvested);
                        $maxTimeGiven = $pointsInvested * $pointValue;
                        $timeGiven    = $this->_storageAPI->updateConnectionTrustTime($userId, $connectionId, $nowTimestamp, $maxTimeGiven);
                        // echo "\npointsInvested: $pointsInvested\n";
                        // echo "PointValue: $pointValue\n";
                        // echo "MaxTimeGiven: $maxTimeGiven\n";
                        // echo "Time given: $timeGiven\n";
                    }
                }
            }
            $this->_storageAPI->finalizeTrustScoreUpdate();
            return true;
        }

        public function getConnectionTrustScore($user, $connectionId) {
            $userId = $user->getId();
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
            $trustScore = $this->_storageAPI->getConnectionTrustScore($userId, $connectionId);
            $this->_storageAPI->resetFetchMode();
            return $trustScore;
        }
    }
}