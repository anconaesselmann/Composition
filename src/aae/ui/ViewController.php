<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class ViewController implements \aae\ui\ViewControllerInterface {
		public $template;
		protected $_errorCode = 0;
		protected $_errorMessage;

		public function __construct(
			\aae\ui\TemplateInterface $template,
			\aae\ui\Localizer $localizer = null
		) {
			$this->template = $template;
			$this->_localizer = $localizer;
			if (!is_null($this->_localizer)) {
				$this->_localizer->setI18nFileName($this->_getControllerName());
				$this->template->setLocalizer($localizer);
                if ($this->_localizer->hasDefaultLocalization()) $this->template["pageTitle"] = $this->localize("defaultPageTitle");
			}
			$this->constructCall();
		}

		public function setErrorCode($errorCode) {
			$this->_errorCode = $errorCode;
		}
		public function setErrorMessage($errorMessage) {
			$this->_errorMessage = $errorMessage;
		}
		public function getErrorCode() {
			return $this->_errorCode;
		}
		public function getErrorMessage() {
			return $this->_errorMessage;
		}
		public function setError($errorMessage, $errorCode) {
			$this->setErrorMessage($errorMessage);
			$this->setErrorCode($errorCode);
			return false;
		}

		public function __call($functionName, $args) {
			$actionName = $functionName."Action";
			if (!method_exists($this, $actionName)) throw new \Exception(get_class($this)." does not have the function $functionName", 1206140141);
			return call_user_func_array(array($this, $actionName), $args);
		}
		public function useTemplate($templateName) {
			$this->template->load($templateName);
		}
		public function internalRedirect($actionName, $args = []) {
			$argsString = "/".implode("/", $args);
			$controller = $this->_getControllerName();
			$action     = strstr($actionName, "Action", true);
			$location   = (strlen($action) > 0) ? "/$controller/".$action : $location = "/$controller";
			$locationWithArgs = $location.$argsString;
			header(header("Location: $locationWithArgs", 301));
		}
		public function loadTemplate($templateName) {
			$this->template->load($templateName);
		}
		public function getHtml() {

			return $this->template->__toString();
		}
		public function getView() {
			$this->lastCall();
			return $this->getHtml();
		}
		public function localize($stringName, $language = null) {
			$fileName = $this->_getControllerName();
			return $this->_localizer->localize($stringName, $fileName, $language);
		}
		public function localizeArrayElement(&$array, $arrayElmentName, $language = null) {
			$fileName = $this->_getControllerName();
			$this->_localizer->localizeArrayElement($array, $arrayElmentName, $fileName, $language);
		}
		public function localizeAndDecorateArrayElement(&$array, $arrayElmentName, $decorationStringName, $language = null) {
			$fileName = $this->_getControllerName();
			$this->_localizer->localizeAndDecorateArrayElement($array, $arrayElmentName, $decorationStringName, $fileName, $language);
		}
		public function getActionEndings() {
			return "Action";
		}
		/**
		 * If html items have a template variable that is numbered cuntinuously, setMenuItem clears all but the $n
		 * @param str $selectorName template variable (has to be numbered continuously)
		 * @param int $n            the variable to be set as active
		 * @param int $count        the number of template variables associated with $selectorName
		 */
		public function cssClassToggle($selectorName, $n, $count) {
		    for ($i=0; $i < $count; $i++) {
		        if ($i !== $n) $this->template[$selectorName.$i] = "";
		    }
		    $this->template[$selectorName.$n] = " active";
		}
		public function lastCall() {
			# gets called before serializing of template. Overwrite to execute something on every action call
		}
		public function constructCall() {
			# gets called after the constructor is called. Overwrite to extend the constructor.
		}
		private function _getControllerName() {
			$class = get_class($this);
			$cn = strstr($class, "ViewController", true);
			if (strlen($cn) < 1) {
				$cn = strstr($class, "ApiController", true);
				if (strlen($cn) < 1) {
					$cn = strstr($class, "TemplateController", true);
					if (strlen($cn) < 1 && ($class != "ViewController" && $class != "TemplateController")) throw new \Exception("Controller ".$class." does not end in 'ViewController' or 'TemplateController'", 204151652);
				}
			}
			return strtolower($cn);
		}
		public function get_all_localized_stringsAction() {
			return $this->_localizer->localizeAll();
		}
	}
}