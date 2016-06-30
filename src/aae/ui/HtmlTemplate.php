<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class HtmlTemplate {
        private   $_relativeBaseDir = "";
        protected $_content         = '{$main}';
        private   $_replacer;
        private   $_replacements;

		public function __construct($relativeBaseDir, $relativeDir = null) {
            $this->_replacer        = new \aae\str\VarSubstituter();
            $this->_replacements    = array();
            $this->_replacements['cssLinks'] = "\n";
            $this->_replacements['jsLinks'] = "\n";
            $this->_relativeBaseDir = $relativeBaseDir;
            if (!is_null($relativeDir)) {
                $temp = $this->getTemplate($relativeDir);
                $this->_content = $temp->_content;
            }
        }
        public function getTemplate($relativeDir) {
            $resolvedDir        = $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.$this->_relativeBaseDir.DIRECTORY_SEPARATOR.$relativeDir.".html";
            $content            = file_get_contents($resolvedDir);
            $template           = new HtmlTemplate($this->_relativeBaseDir);
            $template->_content = $content;
            return $template;
        }
        public function __toString() {
            return $this->_replacer->resolveVars($this->_content, $this->_replacements);
        }
        public function build() {
            $this->_content = $this->_replacer->resolveVars($this->_content, $this->_replacements);
        }
        public function set($varName, $content) {
            $this->_replacements[$varName] = (string)$content;
        }
        public function insert($varName, $content) {
            $this->set($varName, $content);
            $this->build();
        }
        public function setContent($content) {
            $this->_content = $content;
        }
        public function setCss($linkDir) {
            $cssLinkString = '    <link rel="stylesheet" type="text/css" href="'.$linkDir.'" />'."\n";
            $this->_replacements['cssLinks'] .= $cssLinkString;
        }
        public function setJs($linkDir) {
            $jsLinkString = '    <script type="text/javascript" src="'.$linkDir.'"></script>'."\n";
            $this->_replacements['jsLinks']  .= $jsLinkString;
        }
	}
}