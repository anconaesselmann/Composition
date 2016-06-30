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
	class User extends \aae\db\Persistable implements UserInterface {
		private $_cost, $_storageAPI, $_remoteAddr;

		protected $image, $userName, $userId = 0, $email;

		const COOKIE_LOGIN = 2;

		/**
		 * @param \aae\db\StorageAPI $storageAPI An API that maps application function calls to storage function calls
		 * @param \aae\app\Session   $session    [description]
		 * @param array         	 An associative array with options.
		 *                          	   cost: determines computational effort for password hashes.
		 */
		public function __construct(FAPI $storageAPI, \aae\app\Session $session = NULL, $options = NULL, \aae\app\Image $image = NULL) {
			parent::__construct($storageAPI);
			$this->_cost       = (is_array($options) && array_key_exists("cost", $options) && is_int($options["cost"])) ? $options["cost"] : 10;
			$this->_storageAPI = $storageAPI;
			$this->_remoteAddr = (array_key_exists('REMOTE_ADDR', $_SERVER)) ? $_SERVER['REMOTE_ADDR'] : 0;
			$this->_session    = $session;
			$this->_restoreFromSession();
			$this->image = $image;
		}

		public function toArray() {
		    return [
		        "userId"    => $this->userId,
		        "email"     => $this->getEmail(),
		        "userName"  => $this->userName,
		        "image"     => $this->getImage(),
		        "promotional" => true
		    ];
		}

		public function getImage() {
			if (!is_null($this->image)) {
				return $this->image->getUrl($this->userId);
			}
			return NULL;
		}

		/**
		 * creates a new user and returns the verification code
		 * @param  string $userName
		 * @param  string $password  The plaint-ext password
		 * @param  string $userEmail
		 * @return string The verification code required by verify()
		 */
		public function createUser($userName, $password, $userEmail) {
			$hash = $this->createPWHash($password);
			$code = $this->getCode(252); // change this once I fix the DB
			$this->_storageAPI->createUser($userName, $hash, $userEmail, $code, $this->_remoteAddr);
			return $code;
		}
		public function updateUser($userId, $userName, $password, $userEmail) {
			throw new \Exception("Not implemented", 1);

		}

		/**
		 * Verify the email address the user provided during registration
		 *
		 * @param  string $email User's email given during registration
		 * @param  string $code  Code given to user during registration
		 * @return bool
		 */
		public function verify($email, $code) {
			return (bool)$this->_storageAPI->verifyEmail($email, $code);
		}

		/**
		 * Login works for accounts with a verified email address
		 * @param  string  $email     Verified email
		 * @param  string  $password  Plain-text password
		 * @param  int 	   $LoginType Integer value signifying browser or application logins
		 * @return boolean True when login was successful
		 */
		public function login($email, $password, $LoginType = 1) {
			// $passwordHash = $this->createPWHash($password);
			$hash    = $this->_storageAPI->getPasswordHash($email);
			$success = $this->verifyPWHash($password, $hash);
			$userId  = (int)$this->_storageAPI->logLogin($email, $success, $LoginType, $this->_remoteAddr);
			if ($success) {
				$this->_loginSessionManagement($email, $userId);
				$this->userName = $this->_storageAPI->getUserName($userId);
			}
			return $userId;
		}

		/**
		 * If A cookie with valid persistent login credentials exists, log log in with the credentials
		 * @return bool True if login was successful, otherwise return false.
		 */
		public function cookieLogin() {
			$cookie   = $this->_session->getLoginCookie();
			$noErrors = $this->_validateCookie($cookie);
			if ($noErrors) {
				$this->email = $cookie["email"];
				$hashedNewCookieCode = $this->setLoginCookie($cookie["deviceName"]);
				if (!($hashedNewCookieCode !== false)) throw new \Exception("An error occurred when setting a new login cookie, after a valid login cookie was presented.", 1030140012);
				$userId = $this->_storageAPI->logLogin($cookie["email"], true, self::COOKIE_LOGIN, $this->_remoteAddr);
				$this->_loginSessionManagement($cookie["email"], $userId);
				$this->userName = $this->_storageAPI->getUserName($userId);
				return true;
			} else if (is_array($cookie) && array_key_exists("email", $cookie) && !is_null($cookie["email"]) && $cookie["email"] != '') {
				# Previously used login cookie presented. Possible attempt at stealing login credentials!
				$this->_storageAPI->logLogin($cookie["email"], 0, self::COOKIE_LOGIN, $this->_remoteAddr);
				$this->_storageAPI->remove_all_persistent_logins($cookie["email"]);
				# TODO: Catch all tampering exceptions and for this one warn the user that something foul might be going on.
				$this->_session->unsetLoggedIn();
				throw new LoginException("Invalid login cookie presented. Possible attempt at stealing persistent login credentials.", 1030141258);
			}
			return false;
		}
		/**
		 * Create a persistent credential login cookie.
		 * User has to be logged in.
		 * @param string $cookieUserEmail Users email
		 * @param string $deviceName      A user defined name to identify the device with the credentials.
		 */
		public function setLoginCookie($deviceName) {
			if (is_null($this->getEmail())) throw new \Exception("Trying to create login cookie without being logged in.", 1029141812);
			$plainNewCokieCode   = $this->getCode(128);
			$this->_session->setLoginCookie($this->getEmail(), $deviceName, $plainNewCokieCode);
			$hashedNewCookieCode = $this->createPWHash($plainNewCokieCode);
			#var_dump($this->getEmail());
			$noErrors = (bool)$this->_storageAPI->set_persistent_login($hashedNewCookieCode, $this->getEmail(), $deviceName, $this->_session->getUserAgent());
			return ($noErrors) ? $hashedNewCookieCode : false;
		}
		public function unsetLoginCookie($deviceName) {
			if (!$this->_session->isLoggedIn()) throw new \Exception("Trying to unset a login cookie without being logged in.", 1030141121);
			return (int)$this->_storageAPI->unset_persistent_login($this->getEmail(), $deviceName);
		}

		/**
		 * When logged in returns all active persistent logins
		 * @return array array of device names and browser info
		 */
		public function getAllPersistentLogins() {
			if (!$this->_session->isLoggedIn()) throw new \Exception("Trying to access persistent logins without being logged in.", 1030141030);
			$this->_storageAPI->setFetchMode(FAPI::FETCH_NUM_ARRAY);
			$result = $this->_storageAPI->get_all_persistent_logins($this->getEmail());
			$this->_storageAPI->setFetchMode(FAPI::RESET);
			return $result;
		}

		/**
		 * Destroys the session and removes the device as a persistent login
		 */
		public function logout() {
			$cookie = $this->_session->getLoginCookie();
			if (array_key_exists("deviceName", $cookie)) {
				$this->unsetLoginCookie($cookie["deviceName"]);
			}
			return $this->_session->unsetLoggedIn();
		}

		public function updateUserDetails($firstName, $lastName, $phoneNbr, $address, $city, $zip, $state, $country) {
			if (!$this->_session->isLoggedIn()) throw new \Exception("Has to be logged in to update.", 1207141819);
			return (bool)$this->_storageAPI->updateUserDetails($this->userId, $firstName, $lastName, $phoneNbr, $address, $city, $zip, $state, $country);
		}
		public function getUserDetails() {
			if (!$this->_session->isLoggedIn()) throw new \Exception("Has to be logged in to retrieve user details.", 1208141305);
			$this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
			$result = $this->_storageAPI->getUserDetails($this->userId);
			$this->_storageAPI->setFetchMode(FAPI::RESET);
			return $result;
		}

		public function isLoggedIn() {
			return $this->_session->isLoggedIn();
		}

		public function getEmail() {
			return $this->email;
		}
		public function getId() {
			return $this->userId;
		}
		/**
		 * Generates code required by resetPassword()
		 * @param  string $email A registered email
		 * @return string/false A code accepted by resetPassword() or false
		 */
		public function requestPasswordResetCode($email) {
			$resetCode = $this->getCode(252); // change this once I fix the DB
			$success   = (bool)$this->_storageAPI->requestPasswordReset($email, $resetCode, $this->_remoteAddr);
			return ($success) ? $resetCode : false;
		}

		/**
		 * Reset a password with a reset code generated by requestPasswordResetCode()
		 * @param  string $email       A registered email
		 * @param  string $newPassword The new password in plain-text
		 * @param  string $resetCode   Code generated with requestPasswordResetCode
		 * @return boolean True if reset was successful
		 */
		public function resetPassword($email, $newPassword, $resetCode) {
			$passwordHash = $this->createPWHash($newPassword);
			return (bool)$this->_storageAPI->resetPassword($email, $passwordHash, $resetCode);
		}

		private function _loginSessionManagement($email, $userId) {
			$this->_session["aae_app_User"] = ["email" => $email, "userId" => $userId];
			$this->_restoreFromSession();
			$this->_session->setLoggedIn($email);
		}
		protected function _validCookie($cookie) {
			return (
				is_array($cookie) &&
				array_key_exists("email",      $cookie) &&
				array_key_exists("code",       $cookie) &&
				array_key_exists("deviceName", $cookie) &&
				!is_null($cookie["email"])      &&
				!is_null($cookie["code"])       &&
				!is_null($cookie["deviceName"]) &&
				$cookie["email"]      != ''     &&
				$cookie["code"]       != ''     &&
				$cookie["deviceName"] != ''
			);
		}
		private function _validateCookie($cookie) {
			if (!$this->_validCookie($cookie)) return false;
			$hashedFromDb = $this->_storageAPI->get_persistent_login($cookie["deviceName"], $cookie["email"]);
			return $valid = (bool)$this->verifyPWHash($cookie["code"], $hashedFromDb);
		}
		private function _restoreFromSession() {
			$this->email = $this->_session["aae_app_User"]["email"];
			$this->userId    = $this->_session["aae_app_User"]["userId"];
		}
		public function getCode($length) {
			$code = base64_encode(openssl_random_pseudo_bytes(3 * ($length >> 2)));
			$code = str_replace("+", "-", $code);
			$code = str_replace("/", "_", $code);
			return $code;
		}
		public function createPWHash($password) {
			return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->_cost]);
		}
		public function verifyPWHash($password, $hash) {
			return password_verify($password, $hash);
		}
	}
}