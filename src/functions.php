<?php

use ThowsenMedia\Flattery\CMS;

/**
 * Get a nested value from an associative array using dot-notation. For example
 * @example array_get('one.two', array('one' => array('two' => 'hello')))
 * @return mixed the value
 */
function array_get($key, $array) {
	if (!is_array($array)) throw new Exception("array_get expects parameter 2 to be of type Array", 1);

	$current = $array;
	
	$keys = explode('.', $key);
	foreach($keys as $key) {
		if (isset($current[$key])) {
			$current = $current[$key];
		}else {
			return null;
		}
	}

	return $current;
}

/**
 * Get a reference to a nested value from an associaative array using dot-notation.
 * @return mixed the value
 */
function &array_get_reference($key, &$array) {
	if (!is_array($array)) throw new Exception("array_get expects parameter 2 to be of type Array", 1);

	$current = &$array;
	
	$keys = explode('.', $key);
	foreach($keys as $key) {
		if (isset($current[$key])) {
			$current = &$current[$key];
		}else {
			return null;
		}
	}

	return $current;
}


function array_set($key, $value, &$array) {
	if (!is_array($array)) throw new Exception("array_set expects parameter 3 to be of type Array", 1);

	$current = &$array;
	$keys = explode('.', trim($key, '.'));

	foreach($keys as $key) {
		if (isset($current[$key])) {
			$current = &$current[$key];
		}else {
			# set!
			$current[$key] = $value;
			return;
		}
	}

	$current = $value;
}


function array_unset($key, &$array) {
	if (!is_array($array)) throw new Exception("array_unset expects parameter 2 to be of type Array", 1);

	$current = &$array;
	$keys = explode('.', trim($key, '.'));

	$count = count($keys)-1;
	foreach($keys as $index => $key) {
		# last?
		if ($count == $index) {
			# got it, unset it
			unset($current[$key]);
			return;
		}
		else if (isset($current[$key])) {
			$current = &$current[$key];
		}
		else {
			throw new Exception("Nested element $key does not exist!", 1);
		}
	}

}



/**
 * Check if an associative array has the given nested key using dot-notation.
 * @param  string $key   key, using dot-notation, i.e "one.two.three"
 * @param  array $array the array to check in
 * @return boolean        true or false
 */
function array_has($key, $array) {
	if (!is_array($array)) throw new Exception("array_has expects parameter 2 to be of type Array", 1);

	$keys = explode('.', trim($key, '.'));

	foreach($keys as $key) {
		if ( !isset($array[$key]) ) return false;
		$array = $array[$key];
	}

	return true;
}


if (substr(PHP_VERSION, 0, 1) !== '8') {
	/**
	 * Check if string starts with something
	 * 
	 * @param string haystack
	 * @param string $needle
	 */
	function str_starts_with( $haystack, $needle ) {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}


	/**
	 * Check if string ends with something
	 * 
	 * @param string haystack
	 * @param string $needle
	 */
	function str_ends_with( $haystack, $needle ) {
		$length = strlen( $needle );
		if( !$length ) {
			return true;
		}
		return substr( $haystack, -$length ) === $needle;
	}
}


/**
 * Var dump some data.
 */
function dump(...$vars)
{
	echo '<pre>';
	var_dump($vars);
	echo '</pre>';
}


function flattery(?string $name = null)
{
	$cms = CMS::getInstance();
	if ($name !== null) {
		return $cms->$name;
	}
}