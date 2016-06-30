<?php
/**
 * Image uploads an image file and creates a database entry.
 */
namespace aae\app {
    use \aae\db\FunctionAPI as FAPI;
	/**
	 * @author Axel Ancona Esselmann <axel@anconaesselmann.com>
	 * @package aae\app
	 */
	class Image {
        protected $_storageAPI, $_location, $_sizeLimit, $_lastUploadId;

        public function __construct(FAPI $storageAPI, $location) {
            $this->_storageAPI    = $storageAPI;
            $this->setLocation($location);
            $this->_sizeLimit     = 500000;
            $this->_maxResolution = null;
            $this->_lastUploadId = null;
        }
        public function setSizeLimit($sizeLimit) {
            $iniMax = (int)strstr(ini_get('upload_max_filesize'), "M", true) * 1024 * 1024;
            if ($iniMax < $sizeLimit) throw new \Exception("php ini max for file upload is ".$iniMax." bytes (".($iniMax / 1024 / 1024)."MB), trying to set a max image size of ".$sizeLimit." bytes(".($sizeLimit / 1024 / 1024)." MB)", 313151902);
            $this->_sizeLimit = $sizeLimit;
        }
        public function setLocation($location) {
            $this->_location = $location;
        }
        public function setMaxResolution($maxResolution) {
            $this->_maxResolution = $maxResolution;
        }
        public function setOptions($options) {
            if (array_key_exists("location", $options))      $this->setLocation($options["location"]);
            if (array_key_exists("sizeLimit", $options))     $this->setSizeLimit($options["sizeLimit"]);
            if (array_key_exists("maxResolution", $options)) $this->setMaxResolution($options["maxResolution"]);
        }
        public function getLocation() {
            return $this->_location;
        }
        public function upload($fileToUpload, $user, $fileName = null) {
            $targetDir     = $this->_location.DIRECTORY_SEPARATOR;
            $imageFileType = strtolower(pathinfo($fileToUpload["name"],PATHINFO_EXTENSION));
            if ($imageFileType == "jpeg") $imageFileType = "jpg";
            $id = $this->_createDbEntry($user, $fileName);
            if (is_null($fileName)) {
                $fileName = $id.".".$imageFileType;
            }
            $targetFile    = (string)(\aae\fs\Path::resolve($targetDir . $fileName));
            if (is_null($fileToUpload))                    throw new \Exception("No image uploaded.",    1206141823);
            if(!@getimagesize($fileToUpload["tmp_name"]))   throw new \Exception("File is not an image.", 1206141824);
            if ($fileToUpload["size"] > $this->_sizeLimit) throw new \Exception("File is too large.",    1206141825);
            $this->_changeResolution($fileToUpload);
            if ($imageFileType != "jpg"  &&
                $imageFileType != "jpeg")                   throw new \Exception("Only JPG, JPEG files are allowed. You submitted a file of type $imageFileType.", 120614187);
            if (!move_uploaded_file($fileToUpload["tmp_name"], $targetFile)) throw new \Exception("There was an error uploading file '$targetFile'", 1206141828);
            $this->_lastUploadId = $id;
            return $fileName;
        }
        public function getLastUploadId() {
            return $this->_lastUploadId;
        }
        public function updateNameDescription($fileName, $imageName, $imageDescription) {
            return $this->_storageAPI->updateImageByFileName($fileName, $imageName, $imageDescription);
        }
        public function getImageByFileName($fileName) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
            $result = $this->_storageAPI->getImageByFileName($fileName);
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            return $result;
        }
        // TODO: temporary, only takes jpg files
        public function delete($user, $imageId) {
            $result = (int)$this->_storageAPI->deleteImage($user, $imageId);
            if ($result > 0) {
                $dir = (string)(\aae\fs\Path::resolve($this->_location.DIRECTORY_SEPARATOR.$imageId.".jpg"));
                if (unlink($dir)) return true;
            } else throw new \Exception("Image could not be deleted", 319152154);
        }
        public function _createDbEntry($user, $fileName = null) {
            if (is_null($fileName)) $fileName = "";
            return $this->_storageAPI->createImage($user, $fileName);
        }
        public function getFullPath($fileName = null) {
            // str_replace(array("/", "\\", DIRECTORY_SEPARATOR), '', $fileName)[0]; // make sure that relative directories can not be accessed
            $completeFileName = (string)(\aae\fs\Path::resolve($this->_location.DIRECTORY_SEPARATOR.$fileName));
            if (!is_file($completeFileName)) throw new \Exception("Image '$fileName' does not exist", 1205142029);
            return $completeFileName;
        }
        private function _changeResolution($fileToUpload) {
            if ($this->_maxResolution > 0) {
                $fileName   = $fileToUpload["tmp_name"];
                $imateResizer = new \aae\app\ImageResizer();
                $imateResizer->resize($fileName, $fileName, $this->_maxResolution);
            }
        }
	}
}