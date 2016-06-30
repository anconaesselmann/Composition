<?php
/*
 * mostly unit tested
 */
namespace aae\std {
	/**
	 * Std is a collection of static functions.
	 *
	 * When auto loading is turned on, any of these functions can be easily
	 * accessed by calling std::functionName() without having to include a file manually.
	 *
	 *
	 * @package aae\std
	 */
	class std extends \aae\abstr\FunctionCollection {
		private static $_previousErrorHandler;

/* STRING MANIPULATION */

		protected static function startsWith($haystack, $needle) {
    		return $needle === "" || strpos($haystack, $needle) === 0;
		}
		protected static function endsWith($haystack, $needle) {
    		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
		}

		/**
		 * Returns part of $haystack string starting after the last
		 * occurrence of $needle until the end of the string.
		 *
		 * @param  string  $haystack:      The input string
		 * @param  string  $needle:        a substring of $haystack
		 * @param  boolean $before_needle: If TRUE, strrstr() returns the part of the haystack
		 *                                 from the beginning until the last occurrence of the
		 *                                 needle (excluding the needle).
		 * @return string                  Returns the portion of string, or FALSE if needle is not found.
		 */
		protected static function strrstr($haystack, $needle, $before_needle = false) {
			$result = false;
			$pos = strrpos($haystack, $needle);
			if ($pos !== false) {
				if (!$before_needle) {
					$pos += strlen($needle);
					$result = substr($haystack, $pos);
				} else {
					$result = substr($haystack, 0, $pos);
				}
			}
			return $result;
		}

		protected static function strstrNoNeedle($haystack, $needle, $before_needle = false) {
			$result = false;
			$includingNeedle = strstr($haystack, $needle, $before_needle);
			if ($includingNeedle !== false) {
				$needleLength = strlen($needle);
				if (!$before_needle) {
					$result = substr($includingNeedle, $needleLength);
				} else {
					$result = $includingNeedle;
				}
			}
			return $result;
		}

		/**
		 * Returns true if $needle1 occurs before $needle2 in $haystack.
		 *
		 * When $processBackToFront is true, returns true when $needle1 occurs
		 * before $needle2 starting from the end of $haystack, moving to the front.
		 *
		 * @param  string  $haystack           the string to be processed
		 * @param  string  $needle1            the string to occur first
		 * @param  string  $needle2            the string to occur next
		 * @param  boolean $processBackToFront true for backwards processing
		 * @return boolean                     true if $needle1 occurs before $needle2
		 */
		protected static function strBeforeStr($haystack, $needle1, $needle2, $processBackToFront = false) {
			$result = false;
			if (!$processBackToFront) {
				$posN1 = strpos($haystack, $needle1);
				$posN2 = strpos($haystack, $needle2);
				if ($posN1 !== false && $posN2 !== false) {
					$result = ($posN1 < $posN2) ? true : false;
				}
			} else {
				$posN1 = strrpos($haystack, $needle1);
				$posN2 = strrpos($haystack, $needle2);
				if ($posN1 !== false && $posN2 !== false) {
					$result = ($posN1 > $posN2) ? true : false;
				}
			}
			return $result;
		}

		/**
		 * Returns true if the string $needles or a string in the
		 * array $needles is found in $haystack, after position $offset
		 *
		 * @param  string  $haystack the input string
		 * @param  mixed   $needles  a string or array of strings to be matched
		 * @param  integer $offset   the string position in $haystack after which the search is conducted
		 * @return boolean           true if $needles is in $haystack, false if not
		 */
		protected static function isInStr($haystack, $needles, $offset=0) {
			if (!is_array($needles)) $needles = array($needles);
			foreach($needles as $needle) {
				if (strpos($haystack, $needle, $offset) !== false) {
					return true;
				}
			}
			return false;
		}

		/** TODO: either integrate reverse or create a reverse version

		 * Returns $nbr of characters, beginning with the character at $strPos.
		 * Updates $strPos to be the character after the last returned.
		 *
		 * @param  string $haystack: The string to be processed
		 * @param  int 	  $nbr:		 The number of characters to be returned
		 * @param  int    $strPos:   A reference to the current position in the string.
		 *                           his function updates $strPos.
		 *
		 * @return string            The string beginning at $strPos with string length of $nbr.
		 */
		public static function strNbrChar($haystack, $nbr, &$strPos) {
			if ($strPos >= strlen($haystack)) {
				$strPos = strlen($haystack);
				return false;
			}
			$result = substr($haystack, $strPos, $nbr);
			$strPos +=$nbr;
			$strLen = strlen($haystack);
			if ($strPos > $strLen) {
				$strPos = $strLen;
			}
			return $result;
		}

		/**
		 * Checks if starting at $strPos the next characters match $needle exactly.
		 * If they do, $strPos will point at the first character AFTER $needle.
		 *
		 * @param  string $haystack: The string to be processed
		 * @param  string $needle:   The string that needs to match
		 * @param  int    $strPos:   A reference to the current position in the string.
		 * 							 This function updates $strPos.
		 *
		 * @return boolean         	 True if the characters at $strPos matched $needle
		 */
		public static function strIsStr($haystack, $needle, &$strPos = 0) {
			if ($strPos >= strlen($haystack)) {
				$strPos = strlen($haystack);
				return false;
			}
			$hayChunk = substr($haystack, $strPos, strlen($needle));
			if ($hayChunk == $needle) {
				$strPos += strlen($needle);
				return true;
			} else return false;
		}

		/**
		 * Returns a string that has has everything replaced between the first occurrence of needle 1 and needle 2.
		 *
		 * @param  string $haystack 		A string that has both needles in it
		 * @param  string $needle1  		A string or character that marks the beginning of what is to be removed
		 * @param  string $needle2  		A string or character that marks the end of what is to be removed
		 * @param  boolean $removeNeedles 	true removes the needles, false preserves the needles
		 * @return mixed          			The string without what the text between the needles or
		 *                               	false if either of the needles is not part of the string
		 */
		protected static function strReplaceBetween($haystack, $needle1, $needle2, $replacement, $removeNeedles = true) {
			$needle1Pos = strpos($haystack, $needle1);
			$needle2Pos = strpos($haystack, $needle2, $needle1Pos + strlen($needle1));
			if ($needle1Pos === false || $needle2Pos === false) {
				$result = false;
			} else {
				if ($removeNeedles === false) {
					$needle1Pos += strlen($needle1);
					$needle2Pos -= strlen($needle2);
				}
				$result = substr($haystack, 0, $needle1Pos);
				$result .= $replacement;
				$result .= substr($haystack, $needle2Pos + strlen($needle2));
			}
			return $result;
		}

		/**
		 * Returns the characters starting with $strPos until a character sequence
		 * matches $until or the end of the string is reached.
		 *
		 * $until will not be returned as part of the result.
		 * $strPos will reference the first character after the occurrence of $until in $haystack
		 *
		 * @param  string $haystack: The string to be processed
		 * @param  string $until:    The sequence of characters to be matched.
		 * @param  int    $strPos:   A reference to the current position in the string.
		 * 							 This function updates $strPos.
		 *
		 * @return string            The substring starting with $strPos until $until or the end of the string.
		 */
		public static function strUntil($haystack, $until, &$strPos = 0) {
			if ($strPos >= strlen($haystack)) {
				$strPos = strlen($haystack);
				return false;
			}
			$untilPos = strpos($haystack, $until, $strPos);
			if ($untilPos === false) $untilPos = strlen($haystack);
			$nbr = $untilPos - $strPos;
			$result = substr($haystack, $strPos, $nbr);
			$strPos += $nbr + strlen($until);

			$strLen = strlen($haystack);
			if ($strPos > $strLen) {
				$strPos = $strLen;
			}
			return $result;
		}

		/**
		 * Behaves like strUntil, but will return false if a character sequence matches
		 * an element in $ifNots first.
		 *
		 * If false is returned, $strPos is not advanced.
		 *
		 * @param  string $haystack: The string to be processed
		 * @param  string $until:    The sequence of characters to be matched.
		 * @param  mixed  $ifNots:   A string or array of string that will abort the function i
		 *                           f it occurs before $until
		 * @param  int    $strPos:   A reference to the current position in the string.
		 * 							 This function updates $strPos.
		 *
		 * @return mixed             Same as strUntil or false
		 */
		public static function strUntilIfNot($haystack, $until, $ifNots, &$strPos) {
			if ($strPos >= strlen($haystack)) {
				$strPos = strlen($haystack);
				return false;
			}
			if (!is_array($ifNots)) {
				$ifNots = array($ifNots);
			}
			$ifNotPos = strlen($haystack);
			foreach ($ifNots as $ifNot) {
				$tempIfNotPos = strpos($haystack, $ifNot, $strPos);
				if ($tempIfNotPos < $ifNotPos) $ifNotPos = $tempIfNotPos;
			}

			$untilPos = strpos($haystack, $until, $strPos);

			if ($untilPos === false) $untilPos = strlen($haystack);
			if ($ifNotPos === false) $ifNotPos = strlen($haystack);

			if ($ifNotPos <= $untilPos && $untilPos !== strlen($haystack)) return false;
			$nbrChar = $untilPos - $strPos;
			$result = static::strNbrChar($haystack, $nbrChar, $strPos);

			$strPos += strlen($until); // advance strPos until after the delimiter until
			$strLen = strlen($haystack);
			if ($strPos > $strLen) {
				$strPos = $strLen;
			}

			if (strlen($result) === 0) $result = false; // empty string returns false but advances past $until
			return $result;
		}

/* DIRECTORY-STRING MANIPULATION */

		/**
		 * Returns true if $folderStr the name of a valid folder.
		 *
		 * (assumes that the string passed to the function is properly formatted.
		 * Folder names that include periods (.) can cause false negatives)
		 *
		 * @param  string $folderStr the string to be tested
		 * @return boolean            true if $folderStr is the name of a folder
		 */
		protected static function strIsFolder($folderStr) {
			$result = false;
			if (strpos($folderStr, '/') !== false) {
				$noPeriodsBeforeSlashes = !static::strBeforeStr($folderStr, '.', '/', true);
				$result = $noPeriodsBeforeSlashes;
			} else {
				if (strpos($folderStr, '.') === false) {
					$result = true;
				}
			}
			return $result;
		}

		/**
		 * Returns true if $dirStr the name of a valid file.
		 *
		 * (assumes that the string passed to the function is properly formatted.
		 * Folder names that include periods (.) can cause false positives)
		 *
		 * @param  string $dirStr the string to be tested
		 * @return boolean            true if $dirStr is the name of a file
		 */
		protected static function strIsFile($dirStr) {
			$result = false;
			if (strpos($dirStr, '/') !== false) {
				$periodBeforeSlashes = static::strBeforeStr($dirStr, '.', '/', true);
				$result = $periodBeforeSlashes;
			} else {
				if (strpos($dirStr, '.') !== false) {
					$result = true;
				}
			}
			return $result;
		}

		/**
		 * Returns only the folder of a file-system path.
		 *
		 * For properly formated file names, the folder is returned.
		 * For properly formated folder names, $dir is returned.
		 *
		 * Has the same limitations as strIsFile and strIsFolder.
		 *
		 * @param  string $dir the string to be processed
		 * @return string      The folder of $dir
		 */
		protected static function strGetFolder($dir) {
			$result = false;
			if (static::strIsFile($dir)) {
				if (strpos($dir, '/') !== false) {
					$folderParts = explode('/',$dir);
					if (count($folderParts) > 1) {
						array_pop($folderParts);
					}
					$result = implode('/', $folderParts);
				} else {
					$result = '/';
				}
			} else if (static::strIsFolder($dir)) {
				$result = $dir;
			}
			if ($result == '') {
				$result = '/';
			} else if ($result && substr($result, -1) != '/') {
				$result = $result.'/';
			}
			return $result;
		}

		/**
		 * Returns only the file name when passed a properly formated path to a file.
		 *
		 * Returns false when a folder name is passed.
		 *
		 * @param  string $dir a directory in string form
		 * @return mixed      the file name of a properly formated path of false.
		 */
		protected static function strGetFile($dir) {
			$result = false;
			if (static::strIsFile($dir)) {
				if (strpos($dir, '/') !== false) {
					$result = static::strrstr($dir, '/');
				} else {
					$result = $dir;
				}
			}
			return $result;
		}


		protected static function strDirEqual($dir1, $dir2) {
			$dir1 = static::strStandadizePath($dir1);
			$dir2 = static::strStandadizePath($dir2);
			return ($dir1 == $dir2);
		}

		protected static function strPathHasParent($strDir, $strDirParent) {
			$strDir 	  = static::strStandadizePath($strDir);
			$strDirParent = static::strStandadizePath($strDirParent);

			$strDir = static::strGetFolder($strDir);

			return static::strIsStr($strDir, $strDirParent);
		}

		protected static function strPathIsInFolder($strDir, $strDirParent) {
			$strDir = static::strGetFolder($strDir);
			$strPos = 0;
			$result = false;
			do {
				$tempResult = false;
				$tempFolder = substr($strDir, $strPos);
				$strPos = strpos($strDir, '/', $strPos + 1);

				$tempResult = static::strPathHasParent($tempFolder, $strDirParent);
				if ($tempResult) {
					$result = true;
					break;
				}
			} while ($strPos !== false);
			return $result;
		}

		protected static function strStandadizePath($path, $stripSurroundingSlashes = false) {
			if ($stripSurroundingSlashes) {
				$length = strlen($path);
				$startPos = 0;
				if (strlen($path) < 1) {
					return false;
				}
				if ($path[0] == '/') {
					$startPos = 1;
				}
				if (static::strIsFolder($path)) {
					if ($path[strlen($path) - 1] == '/') {
						$length--;
					}
				}
				$length -= $startPos;
				$path = substr($path, $startPos, $length);
			} else {
				if ($path[0] != '/') {
					$path = '/'.$path;
				}
				if (static::strIsFolder($path)) {
					if ($path[strlen($path) - 1] != '/') {
						$path .= '/';
					}
				}
			}
			return $path;
		}

		protected static function strRelativePath($path, $referenceDir) {
			$path 		  = static::strStandadizePath($path);
			$referenceDir = static::strStandadizePath($referenceDir);

			$relativePath 	= static::strstrNoNeedle($path, $referenceDir);
			if (!$relativePath) {
				$relativePath = substr($path, 1, strlen($path) - 1);
			}
			return $relativePath;
		}

		protected static function strAbsolutePath($path, $referenceDir) {
			$referenceDir = static::strStandadizePath($referenceDir);

			$relativePath = static::strRelativePath($path, $referenceDir);
			return $referenceDir.$relativePath;
		}

		/**
		 * Returns the topmost folder name
		 *
		 * @param  string $dir a directory
		 * @return string the topmost folder name
		 */
		protected static function getTopmostFolderName($dir, $directorySeperator = '/') {
			$result = static::strrstr($dir, $directorySeperator);
			if ($result === false) {
				$result = $dir;
			}
			return $result;
		}


/* NAMESPACE MANIPULATION */

		/**
		 * Returns the complete name-space without the class name of a name-spaced class
		 * @param  string $className A name-spaced class name
		 * @return string            The name-space of the class
		 *
		 * (Unit tested) TODO: unit test Exception and move text to ERROR class
		 */
		protected static function namespaceFromNSClassName($className) {
			if (!is_string($className)) throw new \InvalidArgumentException('Arguments passed to '.__FUNCTION__.' have to be of type string.');
			return static::strrstr($className, '\\', true);
		}

		/**
		 * Returns only the class name from a fully name-spaced class name
		 *
		 * @param  string $className fully name-spaced class name
		 * @return string            class name without name-space components
		 */
		protected static function classFromNSClassName($className) {
			if (is_object($className)) $className = get_class($className);
			$result = static::strrstr($className, '\\');
			if ($result === false) {
				$result = $className;
			}
			//echo "\nclassName: $className, result: $result\n";
			return $result;
		}



		/**
		 * Returns an element of a name-spaced identifier.
		 *
		 * @param  string $namespacedId: The name of an
		 * @param  int $pos:       An integer larger or smaller than 0
		 * @throws Exception If pos is not an integer or === 0
		 * @return (string|false)  The desired name-space component or
		 *                         false if the identifier contained fewer components than abs($pos)
		 */
		protected static function namespaceComponent($namespacedId, $pos) {
			if (!is_int($pos)) {
				throw new \InvalidArgumentException(sprintf(constant\Error::WRONG_TYPE, 'pos', 'integer'));
			} else if ($pos === 0) {
				throw new \InvalidArgumentException(sprintf(constant\Error::INVALID_POSITION));
			}
			$result = false;
			$parts = explode('\\', $namespacedId);
			if (strlen($parts[0]) < 1) array_shift($parts);
			$maxLength = count($parts);
			if ($maxLength < 1) {
				$result = false;
			} else if ($maxLength < abs($pos)) {
				$result = false;
			} else {
				if ($pos > 0) {
					$result = $parts[$pos - 1];
				} else {
					$result = $parts[$maxLength + $pos];
				}
			}
			return $result;
		}

		/**
		 * Returns how many levels of name-spacing a class name has.
		 *
		 * For classes in the global name-space the function return 0.
		 * 		Example:
		 * 			Std::namespaceDepth('MyClass') == 0
		 * 			Std::namespaceDepth('\MyClass') == 0
		 * 			Std::namespaceDepth('\name\spaced\MyClass') == 2
		 * @param  string $className A name-spaced class-identifier
		 * @return int            The number of name-spacing-levels of $className
		 */
		protected static function namespaceDepth($className) {
			$parts = explode('\\', $className);
			if (strlen($parts[0]) < 1) unset($parts[0]);
			return count($parts) - 1;
		}

		/**
		 * Returns true if a class name is name-spaced.
		 *
		 * @param  string  $className The name of the class to be tested
		 * @return boolean            true if the class name is name-spaced, false if not.
		 */
		protected static function isNamespaced($className) {
			$result = strpos($className, '\\');
			if ($result !== false) {
				$result = true;
			}
			return $result;
		}

		/**
		 * Resolves a relative name-space, just like relative paths are resolved by the server.
		 *
		 * @param  string $relativeNamespace a string containing a relative name-space
		 * @return string                    fully resolved name-space
		 */
		protected static function resolveRelativeNamespace($relativeNamespace) {
			$count = 1;
			$result = $relativeNamespace;
			while ($count > 0) {
				$count = 0;
				$result = preg_replace('/\w+(\\\.\.\\\)/', '', $result, 1, $count); //TODO: this also matches \.a\
			}
			if (substr($relativeNamespace, 0, 4) == '\\..\\' || substr($relativeNamespace, 0, 3) == '..\\') {
				throw new \Exception(sprintf(constant\Error::COULD_NOT_RESOLVE_NAMESPACE, $result));
			}
			return $result;
		}

		/**
		 * Converts a name-space-string to a directory-path-string.
		 *
		 * @param  string $namespaceString A string containing a name-space
		 * @return string                  A directory path in string form
		 */
		protected static function namespaceToDir($namespaceString) {
			$parts = explode('\\', $namespaceString);
			$dir = implode('/', $parts);
			return $dir;
		}

		protected static function snakeToCamel($snake, $isVar = false) {
			$camel = implode('',array_map('ucfirst',explode('_',$snake)));
			if ($isVar) $camel = lcfirst($camel);
			return $camel;
		}

/* Array manipulation */

		/**
		 * [implodeAssArray description]
		 * @param  [type] $glue   [description]
		 * @param  [type] $format example: `%1\$s` = '%1\$s'
		 * @param  [type] $pieces [description]
		 * @return [type]         [description]
		 */
		protected static function implodeAssArray($glue, $format, $pieces) {
			$result = "";

			$i = 0;
			foreach ($pieces as $key => $value) {
				if ($i > 0) {
					$result .= $glue;
				}
				$result .= sprintf($format, $key, $value);
				$i++;
			}
			return $result;
		}

		protected static function implodeAssArrayDBSave($link, $glue, $format, $pieces) {
			$result = "";

			$i = 0;
			foreach ($pieces as $key => $value) {
				if ($i > 0) {
					$result .= $glue;
				}
				$result .= sprintf($format, mysqli_real_escape_string($link, $key), mysqli_real_escape_string($link, $value));
				$i++;
			}
			return $result;
		}


/* Uncategorized */

		/**
		 * Clean comments of JSON content and decode it with json_decode().
		 * Works like the original php json_decode() function with the same parameters
		 *
		 * Taken from user 1franck on php.net
		 *
		 * @param   string  $json    The json string being decoded
		 * @param   boolean $assoc   When TRUE, returned objects will be converted into associative arrays.
		 * @param   integer $depth   User specified recursion depth. (>=5.3)
		 * @param   integer $options Bit-mask of JSON decode options. (>=5.4)
		 * @return  string
		 */
		protected static function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {
		    // search and remove comments like /* */ and //
		    $jsonString = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);

		    if(version_compare(phpversion(), '5.4.0', '>=')) {
		        $json = json_decode($jsonString, $assoc, $depth, $options);
		    }
		    // @codeCoverageIgnoreStart
		    else if(version_compare(phpversion(), '5.3.0', '>=')) {
		        $json = json_decode($jsonString, $assoc, $depth);
		    } else {
		        $json = json_decode($jsonString, $assoc);
		    }
		    // @codeCoverageIgnoreEnd
		    if ($json === NULL) {
		    	//print_r(debug_backtrace()[0]);
		    	throw new \Exception(sprintf(constant\ERROR::INVALID_JSON, $jsonString));
		    }
		    return $json;
		}

		/**
		 * Call an application's static setter with arguments from a JSON file
		 * located at $dir.
		 *
		 * @param  mixed  $className  An instance of the application or the name of the application as a string
		 * @param  string $dir        The full path to the JSON file
		 * @param  string $setterName The name of the static setter to be called
		 */
		protected static function staticInitFromJSON($className, $dir, $setterName) {
			if (file_exists($dir)) {
				$fileString = file_get_contents($dir);
				$fileJSON = std::json_clean_decode($fileString, true);
				$noErrors = true;
				static::turnOnErrorExceptions();
				foreach ($fileJSON as $constName => $value) {
					try {
						$className::$setterName($constName, $value);
					} catch (\ErrorException $e) {
						static::turnOffErrorExceptions();
						if (is_object($className)) $className = get_class($className);
						throw new \Exception(sprintf(constant\Error::STATIC_FUNCTION_DOES_NOT_EXIST, $className, $setterName));
					}
				}
				static::turnOffErrorExceptions();

			} else throw new \Exception(sprintf(constant\Error::FILE_NOT_FOUND, $dir));
		}

		/**
		 * Turns php's warnings and notices into ErrorExceptions
		 * @return callable the error handler that was active beforehand
		 */
		protected static function turnOnErrorExceptions() {
			static::$_previousErrorHandler = set_error_handler(
				function ($errno, $errstr, $errfile, $errline, array $errcontext) {
					throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
				}
			);
			return static::$_previousErrorHandler;
		}

		/**
		 * Restores the error handler before ErrorExceptions were turned on
		 * @return boolean true if a previous error handler was restored, false if not
		 */
		protected static function turnOffErrorExceptions() {
			if (isset(static::$_previousErrorHandler)) {
				set_error_handler(static::$_previousErrorHandler);
				return true;
			} else return false;
		}

		/**
		 * Returns an array of all class names $object derives from, including $object's class name.
		 *
		 * $object can be a fully name-spaced class identifying-string or an object instance
		 *
		 *
		 * @param  mixed $object a class instance or a string with that holds a class name
		 * @return array        an array of strings
		 */
		protected static function getClassPedegree($object) {
			$pedegree = array();
			if (is_string($object)) {
				$pedegree[] = $object;
				$class = new \ReflectionClass($object);
				while ($class = $class->getParentClass()) {
				    $pedegree[] = $class->getName();
				}
				return array_reverse($pedegree);
			} else {
				$class = get_class($object);
			    do {
					$pedegree[] = $class;
				} while (($class = get_parent_class($class)) !== false);
				return array_reverse($pedegree);
			}
		}

		/**
		 * Returns the type (for scalar and array arguments) or class name of $value
		 * @param  mixed $value Object instance or scalar value to be classified
		 * @return string        The type of $value
		 */
		protected static function getType($value) {
			if ($value === NULL) {
				$result = 'NULL';
			} elseif (is_array($value)) {
				$result = 'array';
			} elseif (is_scalar($value)) {
				$scalarType = gettype($value);
				$result = $scalarType;
			} else {
				$valueClassType = get_class($value);
				$result = $valueClassType;
			}
			return $result;
		}

		/**
		 * Returns an instance of a class identified by $className.
		 *
		 * Arguments passed in the $args array will be passed as individual
		 * arguments to the constructor of the class.
		 *
		 * @param  string $className: The name of the class to be instantiated
		 * @param  array $args:       The arguments to be passed individually to the constructor
		 * @return mixed           	  An instance of the class identified by $className.
		 */
		protected static function getInstanceWithArgs($className, $args = array()) {
			if (!static::isNamespaced($className)) throw new \Exception(sprintf(static::WARNING_CLASS_NAME_NOT_NAMESPACED, $className));
			if(count($args) < 1)
				$obj = new $className;
			else {
				$r = new \ReflectionClass($className);
				$obj = $r->newInstanceArgs($args);
			}
			return $obj;
		}



		protected static function callFunctionWithArguments($class, $function, $arguments) {
			$result = false;
			if (method_exists($class, $function)) {
				if (!is_array($arguments)) {
					$arguments = array($arguments);
				}
				$result = call_user_func_array(array($class, $function), $arguments);
			}
			return $result;
		}

		/**
		 *
		 *
		 * Taken from php.net
		 *
		 * @param  [type]  $bytes     [description]
		 * @param  integer $precision [description]
		 * @return [type]			  [description]
		 */
		protected static function formatBytes($bytes, $precision = 2) {
			$units = array('B', 'KB', 'MB', 'GB', 'TB');

			$bytes = max($bytes, 0);
			$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
			$pow = min($pow, count($units) - 1);
			$bytes /= pow(1024, $pow);

			return round($bytes, $precision) . ' ' . $units[$pow];
		}

		protected static function isSSL() {
			return ($_SERVER['SERVER_PORT'] == 443);
		}

		protected static function modRewriteIsOn() {
			$moduleExists = false;
			if (function_exists('apache_get_modules')) {
				$modules = apache_get_modules();
				$moduleExists = in_array('mod_rewrite', $modules);
			}
			if (getenv('HTTP_MOD_REWRITE')=='On') {
				$environMentVariableSet = true;
			} else {
				$environMentVariableSet = false;
			}

			$result = $moduleExists && $environMentVariableSet;
			return $result;
		}










// NOT UNIT TESTED!!!!!
		// modified from user "fivedigit" on stackexchange
		protected static function curlGetJSON($url, $postArray = array()) {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			if (defined('aae\framework\FW_CURL_VERIFY_PEER') &&
				!\aae\framework\FW_CURL_VERIFY_PEER) {
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			}
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_SSLVERSION,3);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_USERAGENT, "cURL call");
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        	curl_setopt($ch, CURLOPT_TIMEOUT       , 120);
        	curl_setopt($ch, CURLOPT_MAXREDIRS     , 10);


        	if (count($postArray) > 0) {
        		$postString = http_build_query($postArray);
        		curl_setopt($ch,CURLOPT_POST, count($postArray));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $postString);
        	}

			$data = curl_exec($ch);
			$resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($resultCode == 200) {
				$result = static::json_clean_decode($data);
				if ($result === NULL) throw new \Exception(sprintf(\aae\fw\constant\ERROR::INVALID_JSON,$data));
				return $result;
			} else {
				throw new \Exception(sprintf(\aae\fw\constant\ERROR::CURL_REQUEST_FAILED, $resultCode));
			}
		}


		static function execInBackground($cmd, $output = NULL, $errorOutput = NULL) {
		    if (substr(php_uname(), 0, 7) == "Windows"){
		        pclose(popen("start /B ". $cmd, "r"));
		    }
		    else {
				if ($output === NULL) {
					$output = '/dev/null';
				}
				if ($errorOutput === NULL) {
					$errorOutput = '/dev/null';
				}
				exec($cmd . " > $output 2> $errorOutput &");
		    }
		}
/* Query String manipulation */

		/**
		 * Test if a query variable is set.
		 *
		 * @param  string $var: The name of the query variable
		 * @return bool      true if isset, false if not
		 */
/*		protected static function issetQueryVar($var) {
			return (array_key_exists($var, $_GET)) ? true : false;
		}*/
		/**
		 * Unset a variable from the query string and return the new query string
		 *
		 * @param  string $var The name of the query variable
		 * @return string      The new query string
		 */
/*		protected static function unsetQueryVar($var) {
			if (array_key_exists($var, $_GET)) unset($_GET[$var]);
			return static::queryString();
		}*/
		/**
		 * Get the complete current query string.
		 *
		 * Preserves the current query string while adding query variables to the
		 * output by passing a key-value pair or an associative array with key-value pairs.
		 * @param  string|strin[] $var: either the name of the query variable or
		 *                              an array with key-value pairs for query variables.
		 * @param  string|NULL $value The value of the variable, or null when an
		 *                            array is passed for var
		 * @return string        The complete query string, including all key-value additions, including ?
		 */
/*		protected static function queryString($var = NULL, $value = NULL) {
			$query = $_GET;
			$patterns = array('/%2F/', '/%2B/', '/%3A/', '/%3B/', '/%2C/', '/%5C/');
			$replacements = array('/', '+', ':', ';', ',', '\\');
			if (is_array($var)) {
				foreach ($var as $qVar => $qValue) {
					$query[$qVar] = $qValue;
				}
			} else if ($var !== NULL || $value !== NULL) $query[$var] = $value;
			$result = (count($query) > 0) ? '?'.http_build_query($query) : '';

			$result = preg_replace($patterns, $replacements, $result);
			return $result;
		}*/
/*		protected static function queryStringWithPath($var = NULL, $value = NULL) {
			$fileParts = explode('/', $_SERVER['SCRIPT_NAME']);
			$fileName = $fileParts[count($fileParts)-1];
			return ($fileName == 'index.php') 	? dirname($_SERVER['PHP_SELF']).static::queryString($var, $value)
												: $_SERVER['PHP_SELF'].static::queryString($var, $value);
		}*/

		/**
		 * Set a variable to the query string.
		 *
		 * Use this when BUILDING a query string from across the application.
		 * For adding different query variables to links use Std::queryString().
		 *
		 * @param string $var   The name of the query variable
		 * @param string $value The value of the query variable
		 * @return string The new query string.
		 */
/*		protected static function setQueryVar($var, $value) {
			$_GET[($var)] = urlencode($value);
			return static::queryString();
		}*/

	}
}






