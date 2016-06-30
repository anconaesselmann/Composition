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
	class ApiIndex {
		public function __construct($appName) {
			try {
				$appName = substr($appName, 4);

				$appDir        = "protected/{$appName}/app/";
				$viewContrDir  = "protected/{$appName}/ui/apiControllers";
				$templateConfigDir = "protected/{$appName}/ui/templateControllers";
				$appConfigDir  = "$appDir/config.json";

				AutoLoader::addDir($appDir);
				AutoLoader::addDir($templateConfigDir);
				AutoLoader::addDir($viewContrDir);

				$fs         = new \aae\serialize\FileSerializer(new \aae\serialize\Json());
				$assoc      = $fs->unserialize($appConfigDir);
				$container  = new \aae\std\DIFactory($assoc);

				$serializer = $container->build("serializer");
				$api        = $container->build("apiApi");

				$result = $api->run();
			} catch (\Exception $e) {
				$result = "{\"response\":false,\"errorCode\":1030142017}";
			}
			if (is_null($api->headers)) {
				header("Access-Control-Allow-Headers: Content-Type");
				header("Access-Control-Allow-Origin: http://".str_replace("api.", "", $_SERVER["SERVER_NAME"]));
				header("Access-Control-Allow-Credentials: true");
				header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
				header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
				header('Content-Type: application/json');
			}
			echo $result;
		}
	}
}