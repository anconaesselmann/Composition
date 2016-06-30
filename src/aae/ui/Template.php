<?php
/**
 *
 */
namespace aae\ui {
    /**
     * @author Axel Ancona Esselmann
     * @package aae\ui
     */
    class Template implements \ArrayAccess, TemplateInterface {
        private $_assoc, $_baseDir = "", $_modRewriteDocumentRoot = "", $_resourceManager, $_outputEscaper, $_htmlString = "", $_outputAssoc = [], $_outputAssocUnescaped = [], $_templateName = NULL, $_localizer = NULL, $_varResolver =NULL, $_noTemplate = false;

        /**
         *
         * Template is a Document that can build itself from multiple template
         * source files. An associative array passed in the constructor dictates
         * the structure of the final HTML DOM. The document structure is not flat,
         * elements can have child elements listed in an array with a "children"
         * key. Those elements are inserted into their respective parent tags
         * by CSS id. The parent's CSS id's are located in each child's "id" key.
         * The template files for each element to be loaded should be provided
         * with a "template" key.
         *
         * Unless a "base_dir" is present ( see load() ), paths cant be relative.
         * Paths start one directory below the document root.
         *
         *
         * The document.html file has a div with a "top" id
         * and a div with a "main" id.
         * <body>
         *   <div id="top"></div>
         *   <div id="top"></div>
         * </body>
         *
         * Both, top.html and main.html have valid HTML inside <body> tags.
         *
         * {
         *   "template": "/protected/templates/document.html",
         *   "children":
         *     [
         *       {
         *         "div": "top",
         *         "template": "/protected/templates/top.html"
         *       },
         *       {
         *         "div": "main",
         *         "template": "/protected/templates/main.html"
         *       }
         *     ]
         *   }
         * }
         *
         * (For readability the associative array is displayed in JSON)
         *
         * The example above builds the following document structure:
         *
         * <body>
         *   <div id="top">
         *     <div>Content of child nodes of <body> tag in top.html</div>
         *   </div>
         *   <div id="top">
         *     <div>Content of child nodes of <body> tag in main.html</div>
         *   </div>
         * </body>
         *
         *
         * @param array  $assoc template configuration
         * @param boolean $load true build the template during instantiation
         */
        public function __construct(
            $assoc = null,
            \aae\ui\ResourceManagerInterface $resourceManager = null,
            \aae\ui\Escapeable $escaper = null,
            $load = true
        ) {
            $this->_assoc = $assoc;
            $this->_resourceManager = $resourceManager;
            if (!is_null($resourceManager) && array_key_exists("css_vars", $this->_assoc)) $this->_resourceManager->setReplacementVars($this->_assoc["css_vars"]);
            $this->_outputEscaper = $escaper;
            $this->_varResolver = new \aae\str\VarSubstituter($this->_outputEscaper);
            if ($load) $this->load();
        }

        public function getTemplate($templateName) {
            $template = new Template($this->_assoc, $this->_resourceManager, $this->_outputEscaper, true);
            $template->load($templateName);
            return $template;
        }
        public function objectSet($object) {
            foreach ($object->toArray() as $varName => $value) $this->_outputAssoc[$varName] = $value;
        }

        /**
         *
         * The loading of Template from a configuration file can be separated from
         * instantiation. To take advantage of this feature, pass false as a
         * second argument to the Template constructor.
         *
         * If load() is used, a specific template in the assoc array can be used
         * to load Template. The structure of the assoc passed to the constructor
         * should then be as follows:
         *
         * {
         *    "theNameOfTheTemplate": {
         *      ...
         *    }
         * }
         *
         * (For readability the associative array is displayed in JSON)
         *
         * @param  string $templateName the key name of the template to be loaded
         */
        public function load($templateName = null) {
            if (is_array($this->_assoc)) {
                if (is_null($this->_templateName)) {
                    $this->_templateName = $templateName;
                }
                $templateConfig = $this->_getTemplateConfig($templateName);
                $this->_setConfigConstants($this->_assoc);
                try {
                    $this->_buildTemplate($templateConfig);
                    $this->_insertResources($templateName);
                    $this->_insertChildren($templateConfig);
                } catch (\aae\fs\FileDoesNotExistException $e) {
                    $this->_noTemplate = true;
                }
            }
        }
        public function loadFromFile($path) {
            $extension = ".".basename(dirname($path));
            if (substr($path, -strlen($extension)) !== $extension) {
                $path .= $extension;
            }
            $path = new \aae\fs\File($path);

            $this->_htmlString = file_get_contents($path);
        }
        public function setLocalizer($localizer) {
            $this->_localizer = $localizer;
        }
        public function __toString() {
            if ($this->_noTemplate) {
                $this->_collapseArrays();
                $joined = array_merge($this->_outputAssoc, $this->_outputAssocUnescaped);
                // var_dump(json_decode(json_encode($joined)));
                return json_encode($joined);
            }
            if (is_null($this->_templateName)) $this->load("DefaultView");
            $this->_insertResourceLinks();
            $this->_collapseArrays();
            $out = $this->_resolveVars($this->_htmlString, $this->_outputAssocUnescaped);
            $out = $this->_resolveVars($out, $this->_outputAssoc, true);
            $out = $this->_resolveLocalizerVars($out);
            return $out;
        }
        public function getBaseDir() {
            return $this->_baseDir;
        }
        public function offsetSet($offset, $value) {
            if (is_null($offset)) throw new \Exception("Provide an offset add to the template.", 1208141630);
            $this->_outputAssoc[$offset] = $value;
        }
        public function offsetExists($offset) {
            return isset($this->_outputAssoc[$offset]);
        }
        public function offsetUnset($offset) {
            unset($this->_outputAssoc[$offset]);
        }
        public function offsetGet($offset) {
            return isset($this->_outputAssoc[$offset]) ? $this->_outputAssoc[$offset] : null;
        }
        public function arraySet($array) {
            if (!is_array($array)) throw new \Exception("arraySet expects an array of arguments", 204151756);
            foreach ($array as $key => $value) {
                $this[$key] = $value;
            }
        }
        private function _collapseArrays() {
            foreach ($this->_outputAssoc as $varName => $varValue) {
                if (is_array($varValue)) {
                    $this->_collapseArray($varName);
                } else {
                    if (is_a($varValue, get_class($this))) {
                        $this->_outputAssoc[$varName] = new \aae\ui\encoding\IsHtml($varValue->__toString());
                    } else if (is_subclass_of($varValue, "\\aae\\db\\Persistable")) {
                        $objAsArray = $varValue->toArray();
                        $this->_outputAssoc[$varName] = [$objAsArray];
                        $this->_collapseArray($varName);
                    }
                }
            }
        }
        private function _collapseArray($templateVarName) {
            $replacementElements = [];
            $subTemplateName = ($this->_templateName)."_".$templateVarName;

            foreach ($this->_outputAssoc[$templateVarName] as $key => $element) {
                if (is_subclass_of($element, "\\aae\\db\\Persistable")) {
                    $element = $element->toArray();
                }
                $elementString = $this->_outputEscaper->getArrayElementOpen();
                $subTemplate = new Template(
                    $this->_assoc,
                    $this->_resourceManager,
                    $this->_outputEscaper,
                    false
                );
                $subTemplate->_baseDir = $this->_baseDir;
                $subTemplate->load($subTemplateName);
                if (is_array($element)) {
                    foreach ($element as $varName => $varValue) {
                        $subTemplate[$varName] = $varValue;
                    }
                }
                $elementString .= $subTemplate->__toString();
                $elementString .= $this->_outputEscaper->getArrayElementClose();
                $replacementElements[] = $elementString;
            }

            $replacement  = $this->_outputEscaper->getArrayOpen();
            $replacement .= implode($replacementElements, $this->_outputEscaper->getArrayElementSeparator());
            $replacement .= $this->_outputEscaper->getArrayClose();
            $this->_outputAssocUnescaped[$templateVarName] = $replacement;
            unset($this->_outputAssoc[$templateVarName]);
        }
        protected function _insertResourceLinks() {
            $resourceLinks = $this->_resourceManager->getLinks();
            $this->_outputAssocUnescaped["cssLinks"] = "";
            $this->_outputAssocUnescaped["jsLinks"]  = "";
            if (count($resourceLinks) > 0) {
                foreach ($resourceLinks as $link) {
                    if (pathinfo($link, PATHINFO_EXTENSION) == "js") {
                        $this->_outputAssocUnescaped["jsLinks"] .= "\n<script type=\"text/javascript\" src=\"$link\"></script>";
                    } else if (pathinfo($link, PATHINFO_EXTENSION) == "css") {
                        $this->_outputAssocUnescaped["cssLinks"] .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"$link\" />";
                    }
                }
            }
        }
        private function _resolveVars($string, $replacements, $escape = false) {
            return $this->_varResolver->resolveVars($string, $replacements, $escape);
        }
        private function _resolveLocalizerVars($string) {
            if (is_null($this->_localizer)) return $string;
            return $this->_varResolver->resolveVars(
                $string,
                $this->_localizer->localizeAll(),
                true,
                "@"
            );
        }
        private function _insertResources($templateName) {
            if (!is_null($templateName)) {
                $viewPos = strpos($templateName, "View");
                if ($viewPos) {
                    $jsDependencyName = substr($templateName, 0, $viewPos)."JS";
                    $this->_addResource($jsDependencyName);
                    $cssDependencyName = substr($templateName, 0, $viewPos)."CSS";
                    $this->_addResource($cssDependencyName);
                }
            }
        }
        private function _addResource($dependencyName) {
            if (array_key_exists($dependencyName, $this->_assoc)) {
                foreach ($this->_assoc[$dependencyName] as $dependency) {
                    $this->_resourceManager->addResource($dependency);
                }
            }
        }
        private function _getResolvedTemplateDir($templateName) {
            $extension = ".".basename($this->_baseDir);
            $path = new \aae\fs\File($this->_baseDir.$templateName.$extension);
            return $path;
        }
        private function _getTemplateConfig($templateName) {
            if (!is_null($templateName)) {
                if (!array_key_exists($templateName, $this->_assoc)) {
                    try {
                        $path = $this->_getResolvedTemplateDir($templateName);
                        if (file_exists($path)) {
                            return ["template" => $templateName];
                        }
                    } catch (\aae\fs\FileDoesNotExistException $e) {
                        return ["template" => false];
                        // throw new \aae\fs\FileDoesNotExistException("No entry for template $templateName in the template configuration file.", 225141923);
                    }
                }
                return $this->_assoc[$templateName];
            } else {
                return $this->_assoc;
            }
        }
        private function _setConfigConstants($assoc) {
            if (array_key_exists("base_dir", $assoc)) {
                $this->_baseDir = $assoc["base_dir"];
            }
            if (array_key_exists("mod_rewrite_document_root", $assoc)) {
                $this->_modRewriteDocumentRoot = $assoc["mod_rewrite_document_root"];
            }
            if (!is_null($this->_resourceManager)) {
                if (array_key_exists("js_base_dir", $assoc)) {
                    $this->_resourceManager->addJsBaseDir($assoc["js_base_dir"]);
                }
                if (array_key_exists("js_model_dir", $assoc)) {
                    $this->_resourceManager->addJsBaseDir($assoc["js_model_dir"]);
                }
                if (array_key_exists("css_base_dir", $assoc)) {
                    $this->_resourceManager->addCssBaseDir($assoc["css_base_dir"]);
                }
                if (array_key_exists("css_model_dir", $assoc)) {
                    $this->_resourceManager->addCssBaseDir($assoc["css_model_dir"]);
                }
            }
            if (!is_null($this->_resourceManager)) {
                $this->_resourceManager->setBaseDir($this->_baseDir);
                $this->_resourceManager->setModRewriteDocumentRoot($this->_modRewriteDocumentRoot);
            }
        }
        private function _buildTemplate($assoc) {
            if (array_key_exists("template", $assoc)) {
                $templateDir = $assoc["template"];
                $templateDir = $this->_buildPath($templateDir);
                $this->loadFromFile($templateDir);
            } else if (array_key_exists("view", $assoc)) {
                $templateConfig = $assoc["view"];
                $this->load($templateConfig);
            }
        }
        private function _buildPath($path) {
            if ($path[0] != "/") {
                $path = $this->_baseDir.$path;
            } else {
                $path = substr($path, 1);
            }
            return $path;
        }
        private function _insertChildren($templateConfig) {
            if (array_key_exists("children", $templateConfig)) {
                foreach ($templateConfig["children"] as $child) {
                    $childTemplate     = new Template($this->_assoc, $this->_resourceManager, $this->_outputEscaper, false);
                    if (array_key_exists("view", $child)) {
                        $childTemplate->load($child["view"]);
                        $childDiv          = $child["id"];

                        $this->_htmlString = $this->_resolveVars($this->_htmlString, [$childDiv => $childTemplate->_htmlString]);
                    } else if (array_key_exists("template", $child)) {
                        // throw new \Exception("Not implemented!!!!", 1);
                        $child["base_dir"] = $this->_baseDir;
                        $childTemplate     = new Template($child, null, $this->_outputEscaper);
                        $childDiv          = $child["id"];
                        $this->_htmlString = $this->_resolveVars($this->_htmlString, [$childDiv => $childTemplate->_htmlString]);
                        //$this->insertHtmlAtId($childTemplate, $childDiv, false);
                    }
                }
            }
        }
    }
}