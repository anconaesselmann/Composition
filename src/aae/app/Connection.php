<?php
/**
 *
 */
namespace aae\app {
	use \aae\db\FunctionAPI as FAPI;
	use \aae\std\std as std;
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app
	 */
	class Connection implements ConnectionInterface {
		private $_storageAPI;

		public function __construct(\aae\db\FunctionAPI $storageAPI) {
			$this->_storageAPI = $storageAPI;
		}
		public function initiateConnection($user, $connectionDetails) {
			$this->_validateConnectionDetails($connectionDetails);
			$plainCode    = $user->getCode(20);
			$codeHash     = $user->createPWHash($plainCode);
			$userEmail    = $user->getEmail();
			$connectionId = (int)$this->_storageAPI->initiateConnection($userEmail, $codeHash, $connectionDetails);
			return ["connectionId" => $connectionId, "plainCode" => $plainCode];
		}

		public function reciprocateConnection($user, $initConnectionId, $plainCode, $connectionDetails) {
			$userEmail = $user->getEmail();
			$codeHash  = $this->_storageAPI->getInitiatedConnectionCode($initConnectionId);
			$validCode = $user->verifyPWHash($plainCode, $codeHash);
			if ($validCode) {
				$connectionId = $this->_storageAPI->reciprocateConnection($userEmail, $initConnectionId, $plainCode);
				$initConnDet  = $this->_storageAPI->getInitiatorConnectionDetails($initConnectionId);
				if ($connectionId > 0 && strlen($initConnDet) > 0) {
					$initDetInserted  = (bool)$this->_insertConnectionDetails($user, $connectionId, $initConnDet, true);
					$recipDetInserted = (bool)$this->_insertConnectionDetails($user, $connectionId, $connectionDetails);
					if ($initDetInserted && $recipDetInserted) {
						return $connectionId;
					}
				}
			}
			return false;
		}

		public function deleteInitiatedConnection($user) {
			return (bool)$this->_storageAPI->deleteInitiatedConnection($user->getEmail());
		}

		public function getOwnDetails($user, $connectionId) {
			$this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
			$connection = $this->_storageAPI->getOwnDetails($user->getEmail(), $connectionId);
			$this->_storageAPI->setFetchMode(FAPI::RESET);
			return $connection;
		}
		public function getOtherDetails($user, $connectionId) {
			$this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
			$connection = $this->_storageAPI->getOtherDetails($user->getEmail(), $connectionId);
			$this->_storageAPI->setFetchMode(FAPI::RESET);
			return $this->_getFormattedConnection($connection);
		}
		public function getOwnDisplayNameForConnection($user, $connectionId) {
			return $this->_storageAPI->getOwnDisplayNameForConnection($user->getEmail(), $connectionId);
		}
		public function getAll($user) {
			$this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
			$connections = $this->_storageAPI->getAllConnections($user->getEmail());
			$this->_storageAPI->setFetchMode(FAPI::RESET);
			return $this->_getFormattedConnections($connections);
		}
		public function checkInitResponse($user, $initConnectionId) {
			return (bool)$this->_storageAPI->checkInitResponse($user->getEmail(), $initConnectionId);
		}
		public function deleteConnection($user, $connectionId) {
			return (bool)$this->_storageAPI->deleteConnection($user->getEmail(), $connectionId);
		}

		protected function _getFormattedConnections(&$connections) {
			$result = [];
			for ($i=0; $i < count($connections); $i++) {
				$result[] = $this->_getFormattedConnection($connections[$i]);
			}
			return $result;
		}

		protected function _getFormattedConnection(&$connection) {
			$result = [];
			$name = "CORRUPTED_DATA";
			if ((bool)$connection["show_alias"]) {
				$name = $connection["alias"];
			} else if ((bool)$connection["show_user_name"]) {
				$name = $connection["user_name"];
			} else if ((bool)$connection["show_real_name"]) {
				$name = $connection["user_name"]; // TODO: THIS IS PLACEHOLDER TEXT
			} else throw new \Exception("Error retrieving user data", 205151131);
			$result["name"]            = $name;
			$result["email"]           = ((bool)$connection["show_email"]) ? $connection["user_email"] : "";
			$result["connection_id"]   = (int)$connection["connection_id"];
			$result["can_be_messaged"] = (bool)$connection["can_be_messaged"];
			return $result;
		}

		protected function _validateConnectionDetails($connectionDetails) {
			$assoc = json_decode($connectionDetails, true);
			if (!is_array($assoc)) throw new \aae\std\ValidationException("Connection details not in JSON form", 1113142150);
			if (!(
				array_key_exists("show_real_name", $assoc) ||
				array_key_exists("show_user_name", $assoc) ||
				array_key_exists("show_alias", $assoc)
			)) throw new \aae\std\ValidationException("Either show_real_name, show_user_name or show_alias has to exist in connection details", 1113142151);
			if (
				!(
					(array_key_exists("show_real_name", $assoc) && (bool)$assoc["show_real_name"] === true) ||
					(array_key_exists("show_user_name", $assoc) && (bool)$assoc["show_user_name"] === true) ||
					(array_key_exists("show_alias", $assoc)     && (bool)$assoc["show_alias"]     === true)
				)
			) throw new \aae\std\ValidationException("Either show_real_name, show_user_name or show_alias must be true", 1113142152);
			if (
				array_key_exists("show_alias", $assoc) &&
				(bool)$assoc["show_alias"] === true    &&
				(!array_key_exists("alias", $assoc)    ||
				strlen($assoc["alias"]) < 1)
			) throw new \aae\std\ValidationException("When using show_alias, alias must be given", 1113142153);
			return true;
		}

		protected function _insertConnectionDetails($user, $initConnectionId, $connectionDetails, $dataFromInitiator = false) {
			$assoc = json_decode($connectionDetails);
			$details = [
				"alias"           => "",
				"can_be_messaged" => false,
				"show_real_name"  => false,
				"show_user_name"  => false,
				"show_alias"      => false,
				"show_email"      => false,
				"show_phone"      => false,
				"show_address"    => false,
			];
			foreach ($assoc as $key => $value) {
				$details[$key] = $value;
			}
			$success = (bool)$this->_storageAPI->insertConnectionDetails(
				(int)$dataFromInitiator,
				$initConnectionId,
				$user->getEmail(),
				$details["alias"],
				(int)$details["can_be_messaged"],
				(int)$details["show_real_name"],
				(int)$details["show_user_name"],
				(int)$details["show_alias"],
				(int)$details["show_email"],
				(int)$details["show_phone"],
				(int)$details["show_address"]
			);
			return $success;
		}
	}
}