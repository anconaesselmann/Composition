<?php
/**
 *
 */
namespace aae\serialize {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\serialize
	 */
	class FileSerializer implements \aae\serialize\SerializerInterface {
		private $_serializer;

		public function __construct(\aae\serialize\SerializerInterface $serializer) {
			$this->_serializer = $serializer;
		}
		/**
		 * Unserializes a the file at $fileDir. If no file extension is given,
		 * and only matching file with a file extension exists, that file gets unserialized.
		 */
		public function unserialize($fileDir) {
			$fileDir = (string)(\aae\fs\Path::resolve($fileDir));
			// If fileDir does not exist, check if there is a file with
			// name fileDir plus extension and use that instead
			if (!file_exists($fileDir)) {
				$files = glob ($fileDir.".*");
				switch (count($files)) {
					case 1:
						$fileDir = $files[0];
						break;
					case 0:
						throw new \Exception("No file of name $fileDir",      1015141705);
					default:
						throw new \Exception("File name $fileDir ambiguous.", 1015141700);
				}
			}

			$fileDir    = new \aae\fs\File($fileDir);
			$serialized = file_get_contents($fileDir);
			return $this->_serializer->unserialize($serialized);
		}
		public function serialize($instance, $fileDir = null) {
			$serialized = $this->_serializer->serialize($instance);
			$result = file_put_contents($fileDir, $serialized);
			if ($result === false) {
				throw new \Exception("An error occured writing to file $fileDir", 225141505);
			}
		}
		public function fileExists($fileDir) {
			if (!file_exists($fileDir)) {
				$files = glob ($fileDir.".*");
				switch (count($files)) {
					case 1:
						return true;
						break;
					case 0:
						return false;
					default:
						return false;
				}
			}
			return true;
		}

	}
}