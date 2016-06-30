<?php
/**
 *
 */
namespace aae\di {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\di
	 */
	Interface DependencyResolverInterface {
		public function resolveAllowNoMatch($class, $method, $noMatch = NULL);
		public function resolve($class, $method);
	}
}