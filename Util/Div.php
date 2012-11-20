<?php

/**
 * Div static methods
 */
class Menta_Util_Div {

	/**
	 * Return the contains statement for xpath
	 *
	 * @param string $needle
	 * @param string $attribute (optional)
	 * @return string
	 */
	public static function contains($needle, $attribute="class") {
		return "contains(concat(' ', @$attribute, ' '), ' $needle ')";
	}

}