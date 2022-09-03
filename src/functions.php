<?php

use ThowsenMedia\Flattery\Authentication\Auth;
use ThowsenMedia\Flattery\CMS;
use ThowsenMedia\Flattery\Data\Data;
use ThowsenMedia\Flattery\Event;
use ThowsenMedia\Flattery\HTTP\Request;
use ThowsenMedia\Flattery\HTTP\Response;
use ThowsenMedia\Flattery\HTTP\Routing\Router;
use ThowsenMedia\Flattery\HTTP\Session;
use ThowsenMedia\Flattery\Pages\PageManager;
use ThowsenMedia\Flattery\Validation\Validator;
use ThowsenMedia\Flattery\HTTP\Input;
use ThowsenMedia\Flattery\Theme\Theme;

function slugify(string $string): string
{
	$string = strtolower($string);
	return urlencode($string);
}

function humanize(string $string): string
{
	$string = preg_replace("/[-_]+/", ' ', $string);
	return ucwords($string);
}

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
			throw new \Exception("array_get_reference cannot find $key in the given array.");
		}
	}

	return $current;
}


function array_set($key, $value, &$array) {
	if (!is_array($array)) throw new Exception("array_set expects parameter 3 to be of type Array", 1);

	$current = &$array;
	$keys = explode('.', trim($key, '.'));

	$i = 0;
	foreach($keys as $key) {
		if ($i == count($keys) - 1) {
			$current[$key] = $value;
			return;
		}
		
		if ( ! isset($current[$key]) || ! is_array($current[$key])) {
			$current[$key] = [];
		}

		$current = &$current[$key];

		$i ++;
	}
}

function array_put($key, $value, &$array) {
	if (!is_array($array)) throw new Exception("array_set expects parameter 3 to be of type Array", 1);

	$current = &$array;
	$keys = explode('.', trim($key, '.'));

	$i = 0;
	foreach($keys as $key) {
		if ($i == count($keys) - 1) {
			$current[$key][] = $value;
			return;
		}
		
		if ( ! isset($current[$key]) || ! is_array($current[$key])) {
			$current[$key] = [];
		}

		$current = &$current[$key];

		$i ++;
	}
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
			$value = $current[$key];
			unset($current[$key]);
			return $value;
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


/**
 * Var dump some data.
 */
function dump(...$vars)
{
	echo '<pre>';
	var_dump($vars);
	echo '</pre>';
}

/**
 * @return CMS|mixed
 */
function flattery(?string $name = null)
{
	$cms = CMS::getInstance();
	if ($name !== null) {
		return $cms->get($name);
	}

	return $cms;
}

function page(): Page
{
	return flattery('page');
}

function session(): Session
{
	return flattery('session');
}

function data(?string $file = null, ?string $key = null): mixed
{
	if (isset($file)) {
		return flattery('data')->get($file, $key);
	}

	return flattery('data');
}

function request(): Request
{
	return flattery('request');
}

function input(...$keys): Input|array
{
	if (count($keys) > 0) {
		return flattery('input')->get($keys);
	}else {
		return flattery('input');
	}
}

function event(): Event
{
	return flattery('event');
}

function pages(): PageManager
{
	return flattery('pages');
}

function router(): Router
{
	return flattery('router');
}

function auth(): Auth
{
	return flattery('auth');
}

function redirect(string $to = ""): Response
{
	return Response::redirect($to);
}

function validator(array $rules = []): Validator
{
	$validator = flattery('validator');
	$validator->setRules($rules);
	
	return $validator;
}

function url(string $to): string
{
	$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http");
	$base_url .= "://".$_SERVER['HTTP_HOST'];
	$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

	return rtrim($base_url, '/') .'/' .ltrim($to, '/');
}

function asset(string $to): string
{
	return url('assets/' .ltrim($to, '/'));
}

/**
 * Generate a random string
 */
function str_random(int $length = 10): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function theme():Theme
{
	return flattery('theme');
}