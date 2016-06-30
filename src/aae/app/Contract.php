<?php
/**
 *
 */
namespace aae\app {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app
	 */
	class Contract implements ContractInterface {
		private $_storageAPI;

		public function __construct(\aae\db\FunctionAPI $storageAPI) {
			$this->_storageAPI = $storageAPI;
		}
		public function getInfo($contractId, $requestorEmail) {
			return $this->_storageAPI->getInfo($contractId, $requestorEmail);
		}
		public function initiateContract($user) {
			$plainCode  = $user->getCode(20);
			$codeHash   = $user->createPWHash($plainCode);
			$userEmail  = $user->getEmail();
			$contractId = (int)$this->_storageAPI->initiateContract($userEmail, $codeHash);
			return ["contractId" => $contractId, "plainCode" => $plainCode];
		}

		public function reciprocateContract($user, $contractId, $plainCode) {
			$userEmail = $user->getEmail();
			$codeHash  = $this->_storageAPI->getInitiatedContractCode($contractId);
			$validCode = $user->verifyPWHash($plainCode, $codeHash);
			if ($validCode) {
				$success = (bool)$this->_storageAPI->reciprocateContract($userEmail, $contractId, $plainCode);
				if ($success > 0) return true;
			}
			return false;
		}

		public function deleteInitiatedContract($user) {
			return (bool)$this->_storageAPI->deleteInitiatedContract($user->getEmail());
		}
	}
}