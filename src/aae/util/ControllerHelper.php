<?php
/**
 *
 */
namespace aae\util {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\util
	 */
	class ControllerHelper extends PhpClassHelper {
        private $_actionEndings = ["AuthenticatedAction", "Action"];
        private $_defaultLanguage = "eng";
        private $_serializer = NULL;

        public function __construct(\aae\serialize\FileSerializer $serializer) {
            $this->_serializer = $serializer;
        }
        public function getActions() {
            $result    = [];
            $functions = $this->getFunctions();
            foreach ($this->_actionEndings as $ending) {
                $actions = [];
                foreach ($functions as $key => $value) {
                    if (strstr($value, $ending, true)) {
                        $actions[] = strstr($value, $ending, true);
                        unset($functions[$key]);
                    }
                }
                $result[$ending] = $actions;
            }
            return $result;
        }
        public function getLanguageFileNames() {
            $languagesDir   = dirname(dirname($this->_fileName)).DIRECTORY_SEPARATOR."I18n".DIRECTORY_SEPARATOR;
            $controllerName = $this->_getControllerName();
            $languages      = $this->getLanguages();
            foreach ($languages as $language) {
                $folders[] = $languagesDir.$language.DIRECTORY_SEPARATOR.$controllerName.".json";
            }
            return $folders;
        }
        public function getLanguages() {
            $languagesDir = dirname(dirname($this->_fileName)).DIRECTORY_SEPARATOR."I18n".DIRECTORY_SEPARATOR;
            $all          = scandir($languagesDir);
            $languages    = [];
            foreach ($all as $item) {
                if ($item == "." or $item == ".." or $item == ".DS_Store") continue;
                $languages[] = $item;
            }
            return $languages;
        }
        public function getLocalizedVarNames() {
            $all = $this->_getAll();
            return array_keys($all);
        }
        public function getLanguagesVarValues() {
            $languages = $this->getLanguages();
            $getLocalizedVarNames = $this->getLocalizedVarNames();
            $result = [];
            foreach ($languages as $language) {
                $values = [];
                $languageValues = $this->_getAll($language);
                foreach ($getLocalizedVarNames as $localizedVarName) {
                    $localizedValue = "";
                    if (!is_null($languageValues) && array_key_exists($localizedVarName, $languageValues)) {
                        $localizedValue = $languageValues[$localizedVarName];
                    }
                    $values[$localizedVarName] = $localizedValue;
                }
                $result[$language] = $values;
            }
            return $result;
        }
        public function writeToLanguage($languageName, $languageValuesArray) {
            $fileName = dirname(dirname($this->_fileName)).DIRECTORY_SEPARATOR."I18n".DIRECTORY_SEPARATOR.$languageName.DIRECTORY_SEPARATOR.$this->_getControllerName().".json";
            file_put_contents($fileName, json_encode($languageValuesArray, JSON_PRETTY_PRINT));
        }

        public function _getAll($languageName = NULL) {
            if (is_null($languageName)) $languageName = $this->_defaultLanguage;
            $fileName = dirname(dirname($this->_fileName)).DIRECTORY_SEPARATOR."I18n".DIRECTORY_SEPARATOR.$languageName.DIRECTORY_SEPARATOR.$this->_getControllerName().".json";
            if (!file_exists($fileName)) {
                $file = fopen($fileName, "w");
                fclose($file);
            }
            $allVars = json_decode(file_get_contents($fileName), true);
            return $allVars;
        }


        private function _getControllerName() {
            return basename(strtolower(strstr($this->_fileName, "TemplateController", true)));
        }

        public function getConfigurations() {
            $fileNames = scandir($this->_fileName);
            $configurations = [];
            foreach ($fileNames as $file) {
                $firstChar = substr($file, 0, 1);
                $tempDir = $this->_fileName.DIRECTORY_SEPARATOR.$file;
                if ($firstChar != '.' && $firstChar != '_' && is_dir($tempDir)) {
                    $configurationFileName = $tempDir.DIRECTORY_SEPARATOR."config.json";
                    if (file_exists($configurationFileName)) {
                        $configuration["fileName"] = $configurationFileName;
                        $configuration["name"] = $file;
                        $configuration["content"] = $this->_serializer->unserialize($configurationFileName);
                        $configurations[] = $configuration;
                    }
                }
            }
            return $configurations;
        }
        public function getMasterConfiguration() {
            $fileName = $this->_fileName.DIRECTORY_SEPARATOR."config.json";
            $content = $this->_serializer->unserialize($fileName);
            $dependencies = [];
            foreach ($content as $masterEntry) {
                $dep = reset($masterEntry);
                $depName = key($masterEntry);
                if (is_array($dep) && array_key_exists("class", $dep)) {
                    $depClassName = $dep["class"];
                } else $depClassName = "";
                $dependencies[$depName][$depClassName][] = new \aae\di\Dependency($depName, $masterEntry, false);
            }
            return $dependencies;
        }
        public function writeConfiguration($environment, $content) {
            $fileName = $this->_fileName.DIRECTORY_SEPARATOR.$environment.DIRECTORY_SEPARATOR."config.json";
            file_put_contents($fileName, json_encode($content, JSON_PRETTY_PRINT));
        }
        public function toggleDependency($environment, $depName) {
            $configurations = $this->getConfigurations();
            foreach ($configurations as $configuration) {
                if ($configuration["name"] == $environment) {
                    $content = $configuration["content"];
                    if (array_key_exists($depName, $content)) {
                        unset($content[$depName]);
                        $this->writeConfiguration($environment, $content);
                        return;
                    } else {
                        $fromMaster = reset($this->getMasterConfiguration()[$depName]);
                        $fromMaster = json_decode(reset($fromMaster)->json(), true);
                        $content[$depName] = $fromMaster;
                        $this->writeConfiguration($environment, $content);
                    }
                }
            }
            return;
        }
        public function setDependency($environment, $depName, $depJson) {
            $configurations = $this->getConfigurations();
            foreach ($configurations as $configuration) {
                if ($configuration["name"] == $environment) {
                    $content = $configuration["content"];
                    $array = json_decode($depJson);
                    $content[$depName] = $array;
                    $this->writeConfiguration($environment, $content);
                }
            }
            return;
        }
    }
}