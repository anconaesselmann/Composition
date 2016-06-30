<?php
/**
 *
 */
namespace aae\ui {
	use \aae\std\std as std;
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class AuthenticatedViewController extends ViewController {
		protected $_user, $_session, $_signInRedirectURL;

		public function __construct(
			\aae\ui\TemplateInterface $template,
			\aae\app\User     $user,
			\aae\app\Session  $session,
			\aae\ui\Localizer $localizer = null,
			$signInRedirectURL = null)
		{
			parent::__construct($template, $localizer);
			$this->_user    = $user;
			$this->_session = $session;
			$this->_signInRedirectURL = $signInRedirectURL;
		}

		public function __get($propertyName) {
			switch ($propertyName) {
				case 'session':   return $this->_session;
				case 'user':      return $this->_user;
				case 'localizer': return $this->_localizer;

				default: throw new \Exception("Property $propertyName is not accessible.", 1030141209);
			}
		}

		public function __set($propertyName, $value) {
			switch ($propertyName) {
				case 'session':
				case 'user':
					throw new \Exception("Property $propertyName has to be set during instantiation.", 1030141210);

				default: return $this->$propertyName = $value;
			}
		}

		public function __call($functionName, $args) {
			$authenticatedFunctionName = $functionName."AuthenticatedAction";
			if (method_exists($this, $authenticatedFunctionName)) {
				if (!$this->_user->isLoggedIn()) {
					$unauthenticatedFunctionName = $functionName."Action";
					if (!method_exists($this, $unauthenticatedFunctionName)) {
						if (is_null($this->_signInRedirectURL)) throw new AuthenticationException("Trying to perform action that requires being logged in.", 1107141659);
						$from = "/".$this->getUrlControllerName(get_class($this))."/".$functionName."/".implode("/", $args);
						$from = urlencode($from);
						header("HTTP/1.1 401 Unauthorized");
						header ("Location: {$this->_signInRedirectURL}?redirectFrom=$from");
					}
					return parent::__call($functionName, $args);
				}
				if (count($args) > 0) return call_user_func_array(array($this, $authenticatedFunctionName), $args);
				else                  return call_user_func(      array($this, $authenticatedFunctionName));
			}
			return parent::__call($functionName, $args);
		}

		public function getActionEndings() {
			return array("Action", "AuthenticatedAction");
		}

		private function getUrlControllerName($controllerName) {
			return strtolower(strstr($controllerName, 'TemplateController', true));
		}
	}
}