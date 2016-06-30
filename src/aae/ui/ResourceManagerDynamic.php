<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class ResourceManagerDynamic extends ResourceManagerStatic implements ResourceManagerInterface {
		protected $_displayComments = false, $_compression = false, $_replacementVars = NULL, $_resourceFiles = [];

		public function __construct($compression = false, $displayComments = false) {
			$this->_compression = $compression;
			$this->_displayComments = $displayComments;

            $this->_varResolver = new \aae\str\VarSubstituter();
		}

		public function setReplacementVars($replacements) {
			if (is_null($this->_replacementVars)) {
				$this->_replacementVars = $replacements;
			}
		}

		public function addLink($link, $resources = null) {
			parent::addLink($link);
			if (!is_null($resources)) {
				$content = $this->_combineResources($resources);
				if (strlen($content) > 0) {
					$this->_writeResources($link, $content);
				}
			}
		}
		private function _resolveIncludes($fileName) {
			$content = "";
			$file = fopen($fileName, 'r');
			while (true) {
				$line = fgets($file);
				preg_match("/(^\/\*\*\*\s*include\s+)(.*)(\s+\*\*\*\/)/",
		        	$line,
		        	$result
		        );
		        if (array_key_exists(2, $result)) {
		        	$includeDir = $result[2];
		        	if (pathinfo($includeDir, PATHINFO_EXTENSION) == "css") {
		        		$this->addLink($resourceDir = "/css/dynamic/" . $includeDir, [$includeDir]);
		        	} else {
			        	$content .= $this->_combineResources([$includeDir]);
			        }
		        } else break;
			}
			fclose($file);
			return $content;
		}

		private function _combineResources($resources) {
			$content = "";
			foreach ($resources as $resourceDir) {
				if (array_key_exists($resourceDir, $this->_resourceFiles)) continue;
		        $this->_resourceFiles[$resourceDir] = true;

		        $ext = pathinfo($resourceDir, PATHINFO_EXTENSION);
		        switch ($ext) {
		        	case 'css':
		        		$resourceDirResolved = $this->_buildPath($this->_cssBaseDirs, $resourceDir);
		        		break;
		        	case 'js':
		        		$resourceDirResolved = $this->_buildPath($this->_jsBaseDirs, $resourceDir);
		        		break;

		        	default:
		        		$resourceDirResolved = $this->_buildPath($resourceDir);
		        		break;
		        }

		        if (!$resourceDirResolved) throw new \Exception("Resource $resourceDir does not exist", 1002151636);

				$content .= $this->_resolveIncludes($resourceDirResolved);

		        if ($this->_displayComments) {
		        	$content .= "\n\n/***********************************************\n\tResource: $resourceDir\n***********************************************/\n\n\n\n";
		        }
		        $content .= file_get_contents($resourceDirResolved);
			}
			if (!is_null($this->_replacementVars)) {
				$content = $this->_varResolver->resolveVars($content, $this->_replacementVars);
			}
			return $content;
		}

		private function _writeResources($link, $content) {
			$dir = (string)(new \aae\fs\File($this->_modRewriteDocumentRoot . $this->_buildPathReverseAbsolute($link), true));
			file_put_contents($dir, $content);
		}

		private function _buildPath($paths, $pathAppend = '') {
			foreach ($paths as $path) {
				if (strlen($pathAppend)      > 0   &&
					$pathAppend[0]          != "/" &&
					substr($path, -1, 1) != "/"
				) $pathAppend = '/'.$pathAppend;
				$result = $this->_pathBuilderHelper($path.$pathAppend);
				if ($result) return $result;
			}
			return false;
		}
		private function _pathBuilderHelper($path) {
			try {
				return (string)(new \aae\fs\File($path));
			} catch (\Exception $e) {
				return false;
			}
		}
		private function _buildPathReverseAbsolute($path) {
			if ($path[0] != "/") $path = $this->_baseDir.$path;
			else $path = substr($path, 1);
			return $path;
		}
	}
}