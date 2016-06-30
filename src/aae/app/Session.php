<?php
/**
 *
 */
namespace aae\app {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app
	 */
	class Session implements \ArrayAccess {
		public function __construct() {
			session_start();
			if (!array_key_exists("aae_app_Session", $_SESSION)) $_SESSION["aae_app_Session"] = [];
		}
		public function setLoggedIn($identifier) {
			$this["login_identifier"] = $identifier;
		}
		public function unsetLoggedIn() {
			$this->offsetUnset("login_identifier");
			if(isset($_COOKIE['aae_app_Session_persistent_login'])) {
				$time = time() - 3600;
				setcookie("aae_app_Session_persistent_login[email]",      NULL, $time, "/");
				setcookie("aae_app_Session_persistent_login[deviceName]", NULL, $time, "/");
				setcookie("aae_app_Session_persistent_login[code]",       NULL, $time, "/");
				setcookie("aae_app_Session_persistent_login",             NULL, $time, "/");
				unset($_COOKIE["aae_app_Session_persistent_login"]);
				return true;
			}
			session_destroy();
			return false;
		}
		public function isLoggedIn() {
			return ($this->offsetExists("login_identifier")) ? true : false;
		}
		public function setCookie($key, $value, $time = NULL, $path = "/") {
			setcookie($key, $value, $time, "/");
		}
		public function setLoginCookie($cokieUserEmail, $deviceName, $plainTextCode) {
			$time = time() + 3600 * 24 * 30 * 3;
			setcookie("aae_app_Session_persistent_login[email]",      $cokieUserEmail, $time, "/");
			setcookie("aae_app_Session_persistent_login[deviceName]", $deviceName,     $time, "/");
			setcookie("aae_app_Session_persistent_login[code]",       $plainTextCode,  $time, "/");
		}
		public function getLoginCookie() {
			$result = [];
			if (array_key_exists("aae_app_Session_persistent_login", $_COOKIE)) {
				$result["code"]       = $_COOKIE["aae_app_Session_persistent_login"]["code"];
				$result["email"]      = $_COOKIE["aae_app_Session_persistent_login"]["email"];
				$result["deviceName"] = $_COOKIE["aae_app_Session_persistent_login"]["deviceName"];
			}
			return $result;
		}
		public function getUserAgent() {
			$userAgent = preg_replace('/[^a-zA-Z0-9\s]/', '', $_SERVER['HTTP_USER_AGENT']);
			return $userAgent;
		}
		public function offsetSet($offset, $value) {
			if (is_null($offset)) throw new \Exception("Provide an offset to save to the session.", 1029141512);
			$_SESSION["aae_app_Session"][$offset] = $value;
		}
		public function offsetExists($offset) {
			return isset($_SESSION["aae_app_Session"][$offset]);
		}
		public function offsetUnset($offset) {
			unset($_SESSION["aae_app_Session"][$offset]);
		}
		public function offsetGet($offset) {
			return isset($_SESSION["aae_app_Session"][$offset]) ? $_SESSION["aae_app_Session"][$offset] : null;
		}
	}
}