<?php
/**
 *
 */
namespace aae\ui\elements {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui\elements
	 */
	class PaginationNavigation {
        public $template;
        public function init(&$template, $nbrPages) {
            $this->template  = $template;
            $this->_nbrPages = $nbrPages;
        }
        public function setPageMenu($page = 1) {
            $this->template["paginationHideLeftDots"]  = '';
            $this->template["paginationHideRightDots"] = '';
            $this->_setPage($page);
            $this->_setHidden();
        }
        protected function _setLeftJump($page) {
            $this->template["pagination-10"] = $page - 10;
            if ($page < 11) {
                if ($page < 5) $this->template["pagination-10"] = '';
                else if ($this->_nbrPages < 6) $this->template["pagination-10"] = '';
                else $this->template["pagination-10"] = 1;
            }
        }
        protected function _setRightJump($page) {
            $this->template["pagination10"] = $page + 10;
            if ($this->_nbrPages - $page < 11) {
                if ($this->_nbrPages - $page < 4) $this->template["pagination10"] = '';
                elseif ($page == 1 && $this->_nbrPages - $page < 5) $this->template["pagination10"] = '';
                else $this->template["pagination10"] = $this->_nbrPages;
                if ($this->_nbrPages == 6) {
                    $this->template["paginationHideRightDots"] = ' hidden';
                    $this->template["paginationHideLeftDots"]  = ' hidden';
                }
                if ($this->_nbrPages >= 6 && $page < 5) $this->template["pagination10"] = $this->_nbrPages;
            }
        }
        public function _setLeft($page) {
            if ($page < 5) {
                for ($i=1; $i < $page; $i++) if ($page - $i > 0) $this->template["pagination-$i"] = $page - $i;
            } elseif ($this->_nbrPages - $page < 4) for ($i=1; $i < 5 - ($this->_nbrPages - $page); $i++) $this->template["pagination-$i"] = $page - $i;
            else $this->template["pagination-1"] = $page - 1;
        }
        public function _setRight($page) {
            $pagesToBeginning = 5 - $page;
            $pagesToEnd = $this->_nbrPages - $page;
            if ($pagesToBeginning > 0) {
                for ($i=0; $i <= $pagesToBeginning; $i++) if ($page + $i <= $this->_nbrPages) $this->template["pagination$i"] = $page + $i;
            } else if ($pagesToEnd < 4) for ($i=1; $i <= $pagesToEnd; $i++) $this->template["pagination$i"] = $page + $i;
            elseif ($page < $this->_nbrPages) $this->template["pagination1"] = $page + 1;
        }
		protected function _setPage($page) {
            $this->template["pagination-10"] = '';
            $this->template["pagination-4"] = '';
            $this->template["pagination-3"] = '';
            $this->template["pagination-2"] = '';
            $this->template["pagination-1"] = '';
            $this->template["pagination1"] = '';
            $this->template["pagination2"] = '';
            $this->template["pagination3"] = '';
            $this->template["pagination4"] = '';
            $this->template["pagination10"] = '';
            $this->_setLeftJump($page);
            $this->_setRightJump($page);
            $this->_setLeft($page);
            $this->_setRight($page);
            $this->template["pagination0"] = $page;
        }
        protected function _setHidden() {
            for ($i =- 4; $i <= 4 ; $i++) {
               if ($this->template["pagination$i"] == '') {
                   $this->template["paginationHide$i"] = ' hidden';
               } else $this->template["paginationHide$i"] = '';
            }
            if ($this->template["pagination10"] == '') {
                $this->template["paginationHide10"]        = ' hidden';
                $this->template["paginationHideRightDots"] = ' hidden';
            } else $this->template["paginationHide10"] = '';
            if ($this->template["pagination-10"] == '') {
                $this->template["paginationHide-10"]      = ' hidden';
                $this->template["paginationHideLeftDots"] = ' hidden';
            } else $this->template["paginationHide-10"] = '';
        }
	}
}