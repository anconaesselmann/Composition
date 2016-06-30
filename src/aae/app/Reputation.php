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
	class Reputation {
		private $_storageAPI;

		public function __construct(\aae\db\FunctionAPI $storageAPI) {
			$this->_storageAPI = $storageAPI;
		}
		public function getRep($userEmail) {
			return (int)$this->_storageAPI->getRep($userEmail);
		}

		/**
		 * Calculates reputation for all uncounted reputation events and updates
		 * the reputation of the user identified by $userEmail
		 * 
		 * @param  string $userEmail User Email
		 * @return int               The new reputation of that user.
		 */
		public function updateAndGetRep($userEmail) {
			$this->_storageAPI->setFetchMode(FAPI::FETCH_NUM_ARRAY);
			$repEvents = $this->_storageAPI->getNewReputationEvents($userEmail);
			$newRepPoints = 0;
			foreach ($repEvents as $repEvent) {
				$newRepPoints += $this->_getRepForEvent($repEvent);
			}
			$this->_storageAPI->setFetchMode(FAPI::RESET);
			return (int)$this->_storageAPI->updateRep($newRepPoints);
		}

		protected function _getRepForEvent($repEvent) {
			//var_dump($repEvent);
			return 5;
		}
	
		public function registerReputationEvent($userEmail, $eventType, $benefactorId) {
			return $this->_storageAPI->registerReputationEvent($userEmail, $eventType, $benefactorId);
		}
	}
}
