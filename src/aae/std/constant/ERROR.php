<?php
namespace aae\std\constant {
	/**
	 *
	 * @package aae\std\constant
	 */
	final class ERROR extends \aae\abstr\Uninstantiable {
		const WRONG_TYPE = 'The argument "%s" must be of type "%s".';
		const INVALID_POSITION = 'The position can not be 0. The first element is pos = 1, the last is pos = -1';
		const COULD_NOT_RESOLVE_NAMESPACE = 'Could not resolve "%s" to a valid namespace.';
		const INVALID_JSON = "The string\n\"%s\"\nis invalid JSON.";
		const STATIC_FUNCTION_DOES_NOT_EXIST = 'The static function call to "%s::%s" failed because the function does not exist or is not static.';
		const FILE_NOT_FOUND = 'The file "%s" could not be found.';
		const WARNING_CLASS_NAME_NOT_NAMESPACED = 'The class name "%s" is not name-spaced. Consider providing a fully name-spaced class-identifier, or aae\autoload\AutoLoader might produce "Cannot redeclare class" errors.';

		// inside ReadWrapper
		const READ_ONLY = 'Access through a ReadWrapper is "read only"';
		const FUNCTION_CALLS_DISALLOWED = 'Function calls, except to getters and for drawing purposes, can not be made through a ReadWrapper.';

		// BitField
		const INIT_VALUE_LARGER_THAN_MAX_SIZE = 'The value "%s" has more bits than the specified maximum number of "%d" bits.';
		const WRONG_PRIMITIVE_TYPE = 'The argument "$%s" has to be of type "%s"';
		const NOT_POSITIVE_INT = 'The property "%s" has to be a positive integer.';
		const OFFSET_LARGER_THAN_MAX_SIZE = 'The offset with value "%d" has to be smaller than the maxSize of "%d".';

		const AUTOLOADERS_CAN_NOT_BE_INSTANTIATED = "Autoloaders can not be instantiated.";
	}
}