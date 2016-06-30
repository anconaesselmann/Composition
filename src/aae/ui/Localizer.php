<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class Localizer {
		use \aae\log\LoggableTrait;

		const TIME_SPAN = 1;

		private $_serializer, $_baseDir, $_requestLanguageAbbrev, $_hasDefault = false;
		private static $_files = array();
		private static $_timeLocalizer = null;

		public function __construct(\aae\serialize\FileSerializer $serializer, $baseDir, \aae\log\Loggable $logger = NULL) {
			$this->_serializer = $serializer;
			$this->_baseDir    = $baseDir;
			$this->setLogger($logger);
			if (array_key_exists("lang", $_REQUEST)) $this->_requestLanguageAbbrev = preg_replace("/[^A-Za-z0-9 ]/", '', $_REQUEST["lang"]);
			if (is_null($this->_requestLanguageAbbrev)) $this->_requestLanguageAbbrev = 'eng';
			$this->_hasDefault = $this->_registerFileName("default");
		}

		/**
		 * Get the localized string version of $stringName
		 */
		public function localize($stringName, $fileName, $languageAbbrev = null) {
			$languageAbbrev = (is_null($languageAbbrev)) ? $this->_requestLanguageAbbrev : $languageAbbrev;
			if (is_string($stringName)) return $this->localizeString($stringName, $languageAbbrev, $fileName);
			else return $this->localizeTimeSpan($stringName, $languageAbbrev);
		}
		public function localizeTimeSpan($timeSpan, $lang) {
			if (is_null(self::$_timeLocalizer)) {
				self::$_timeLocalizer = new \aae\ui\TimeLocalizer($lang);
			}
			return self::$_timeLocalizer->localizeTimeSpan($timeSpan);
		}
		public function localizeString($stringName, $languageAbbrev, $fileName) {
			$this->_registerFileName($fileName, $languageAbbrev);
			if (array_key_exists($fileName,   self::$_files) &&
				array_key_exists($stringName, self::$_files[$fileName])
			) {
				$string = self::$_files[$fileName][$stringName];
			} else if (
				array_key_exists("default",   self::$_files) &&
				array_key_exists($stringName, self::$_files["default"])) {
				$string = self::$_files["default"][$stringName];
			} else {
				$string = "UNLOCALIZED_STRING";
				if ($this->hasLogger()) $this->log("No localization for string $stringName in language $languageAbbrev for file $fileName");
			}
			return $string;
		}
		public function localizeArrayElement(&$array, $arrayElmentName, $fileName, $lang) {
			for ($i=0; $i < count($array); $i++) {
				$array[$i][$arrayElmentName] = $this->localize($array[$i][$arrayElmentName], $fileName, $lang);
			}
		}
		/**
		 * Useful when localizing an object that has it's own localization, and
		 * then has to be presented differently depending on the language.
		 * Example: a time span in context: 2 days ago.
		 */
		public function localizeAndDecorateArrayElement(&$array, $arrayElmentName, $decorationStringName, $fileName, $language) {
			$decorationString = $this->localize($decorationStringName, $fileName, $language);
			for ($i=0; $i < count($array); $i++) {
				$array[$i][$arrayElmentName] = sprintf($decorationString, $this->localize($array[$i][$arrayElmentName], $fileName, $language));
			}
		}
		public function localizeAll() {
			$result = [];
			foreach (self::$_files as $file) $result = array_merge($file, $result);
			return $result;
		}
		public function setI18nFileName($controllerName, $languageAbbrev = NULL) {
			$this->_registerFileName($controllerName, $languageAbbrev);
		}
		public function hasDefaultLocalization() {
			return $this->_hasDefault;
		}
		private function _registerFileName($fileName, $languageAbbrev = NULL) {
			if (!array_key_exists($fileName, self::$_files)) {
				if (is_null($languageAbbrev)) $languageAbbrev = $this->_requestLanguageAbbrev;
				$fullDir = $this->_baseDir.DIRECTORY_SEPARATOR."I18n"
										  .DIRECTORY_SEPARATOR.$languageAbbrev
										  .DIRECTORY_SEPARATOR.$fileName;
										  // echo $fullDir;
				try {
					self::$_files[$fileName] = $this->_serializer->unserialize($fullDir);
				} catch (\Exception $e) {
					return false;
					// echo $e->getMessage();
					// TODO: possibly display message if debugging
				}
			}
			return true;
		}
	}
}