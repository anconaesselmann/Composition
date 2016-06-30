<?php
/**
 *
 */
namespace aae\ui {
	use \aae\autoload\AutoLoader;
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	if (!function_exists('getallheaders')) {
	    function getallheaders() {
	        $headers = '';
	        foreach ($_SERVER as $name => $value) {
	           if (substr($name, 0, 5) == 'HTTP_') {
	               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
	            }
	        }
	        return $headers;
	    }
	}
	class ApplicationIndex {
		private $_contentResolutionHashMap = ["application/json" => "_serveJSON", "text/html-fragment" => "_serveHTMLFragment"];
		public function __construct($appName) {
			$contentType = $this->_getContentType();
			if ($contentType) {
				if (array_key_exists($contentType, $this->_contentResolutionHashMap)) {
					$functionName = $this->_contentResolutionHashMap[$contentType];
					$this->$functionName($appName);
					return;
				}
			}
			$this->_serveHTML($appName);
		}
		private function _getContentType() {
			// TODO: This is here because I can't send the Content-Type header from swift POST.
			// Swift will sent Post-COntent-Type instead.
			// This is a hacky workaround and should be fixed as soon as possible
			$allHeaders = getallheaders();
			if (array_key_exists('Post-Content-Type', $allHeaders)) {
				return $allHeaders['Post-Content-Type'];
			}
			if (array_key_exists("CONTENT_TYPE", $_SERVER)) {
				if (strlen($_SERVER["CONTENT_TYPE"]) > 0) {
					return $_SERVER["CONTENT_TYPE"];
				}
			}
			return false;
		}
		private function _serveHTML($appName) {
			$container  = $this->_getContainer($appName);
			$serializer = $container->build("serializer");
			$api        = $container->build("api");
			$errorVC    = $container->build("errorViewController");
			$timeZone   = $container->get("timeZone");
			$app        = new \aae\ui\Application($api, $serializer, $errorVC, $timeZone);
		    $app->run();
		}
		private function _serveHTMLFragment($appName) {
			try {
				$container  = $this->_getContainer($appName);
				$serializer = $container->build("serializer");
				$api        = $container->build("htmlFragmentApi");
				$result     = $api->run();
			} catch (\Exception $e) {
				echo '<div class="errorFragment">errorCode:'.$e->getCode()." ".$e->getMessage().'</div>';
				return;
			}
			if (is_null($api->headers)) {
				header('Content-Type: text/html');
			}
			echo $serializer->unserialize($result)["response"];
		}
		private function _serveJSON($appName) {
			try {
				$container  = $this->_getContainer($appName);
				$serializer = $container->build("serializer");
				$api        = $container->build("apiApi");
				$result     = $api->run();
			} catch (\Exception $e) {
				$result = "{\"response\":false,\"errorCode\":1030142017}";
			}
			if (is_null($api->headers)) {
				header('Content-Type: application/json');
			}
			echo $result;
		}
		private function _getContainer($appName) {
			$appDir            = "protected/{$appName}/app/";
			$apiContrDir       = "protected/{$appName}/ui/apiControllers";
			$viewContrDir      = "protected/{$appName}/ui/viewControllers";
			$modelDir          = "protected/{$appName}/app/models";
			$templateConfigDir = "protected/{$appName}/ui/templateControllers";
			$appConfigDir      = "$appDir/config.json";

            // echo $appDir;

			AutoLoader::addDir($appDir);
			AutoLoader::addDir($templateConfigDir);
			AutoLoader::addDir($apiContrDir);
			AutoLoader::addDir($viewContrDir);
			AutoLoader::addDir($modelDir);

			$fs         = new \aae\serialize\FileSerializer(new \aae\serialize\Json());
			$assoc      = $fs->unserialize($appConfigDir);
			$container  = new \aae\std\DIFactory($assoc);
			return $container;
		}
	}
}