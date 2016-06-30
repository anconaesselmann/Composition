<?php
namespace aae\autoload {
	// add library to watched folders
	#$libraryDir = dirname(__FILE__)."/../../../../../src/";
	$libraryDir = substr(__FILE__, 0, strrpos(__FILE__, 'aae/autoload/AutoLoader.php'));
	if (is_dir($libraryDir)) {
		Autoloader::addDir($libraryDir);
	}
	
	/**
	 * AutoLoader is a PSR-0 compliant autoloader.
	 * AutoLoader registers directories from which files should be auto-loaded.
	 * AutoLoader can not be instantiated, it instantiates itself the first time
	 * a static call is made to AutoLoader::addDir().
	 * For an example look at the documentation for addDir().
	 *
	 * @package aae\std
	 */
	class AutoLoader {
		/**
		 * Holds the directories as strings passed with AutoLoader::addDir()
		 * that should be included in the autoload function
		 * @var string[]
		 */
		private static $_dirs = array();
		/**
		 * Is set to true once _autoload() has been added to spl_autoload
		 * @var boolean
		 */
		private static $_spl_autoload_registered = false;

		/**
		 * AutoLoader can not be instantiated.
		 */
		private function __construct() {/* intentionally left blank */}
		/**
		 * Add directories that should be added to spl_autoload_register().
		 * ATTENTION: The default behavior of AutoLoader is to convert the
		 * name-space-structure of a class to a matching directory-structure.
		 * 		Example:
		 * 			AutoLoader::addDir('/auto/load/root/');
		 *    		$obj = new \name\spaced\Object();
		 *
		 * 			Autoloader will look for a file called Object.php in /auto/load/root/name/spaced/
		 *
		 * THE FOLLOWING IS NOT IMPLEMENTED YET!!!!
		 * To look for a file in /auto/load/root/ set $namespaced = false
		 *
		 * @param string $dir a string that holds a directory name
		 */
		public static function addDir($dir, $namespaced = true) {
			if (substr($dir, 0, 1) != "/") {
				$docRoot = $_SERVER["DOCUMENT_ROOT"];
				if (substr($docRoot, -1) != "/") {
					$docRoot .= "/";
				}
				$dir = $docRoot."../".$dir;
			}
			if (substr($dir, -1) !== '/') $dir .= '/';
			self::_init();
			self::$_dirs[$dir] = $namespaced;
		}
		private static function _init() {
			if (!self::$_spl_autoload_registered) {
				spl_autoload_register(array(new AutoLoader(), '_autoload'));
				self::$_spl_autoload_registered = true;
			}
		}
		private static function _autoload($class) {
			$parts = explode('\\', $class);
			foreach (self::$_dirs as $d => $namespaced) {
				if ($namespaced) {
					$file = $d.implode('/', $parts).'.php';
					if (file_exists($file)) {
						require $file;
						return true;
					}
				} else { // add directory without converting namespace elements to directories
					$file = $d.$parts[count($parts)-1].'.php';
					if (file_exists($file)) {
						require $file;
						return true;
					}
				}

			}
			return false;
		}
	}
}