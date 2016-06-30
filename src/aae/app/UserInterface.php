<?php
/**
 *
 */
namespace aae\app {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app
	 */
	interface UserInterface {
		public function login($email, $password, $LoginType = 0);
	}
}