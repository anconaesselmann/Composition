<?php
/**
 *
 */
namespace aae\data {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\data
	 */
	interface ValidatorInterface {
		public function validate();

		public function getDefault();
	}
}