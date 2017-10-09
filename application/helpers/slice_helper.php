<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *  Slice-Library Helper functions
 *
 *  @package		CodeIgniter
 *	@subpackage		Helpers
 *	@category		Helpers
 *	@author			Gustavo Martins <gustavo_martins92@hotmail.com>
 *	@link			https://github.com/GustMartins/Slice-Library
 *	@since			1.3.0
 */

// ------------------------------------------------------------------------

if ( ! function_exists('__'))
{
	/**
	 *  Translate the given message
	 *
	 *  @param     string    $key
	 *  @param     array     $replace
	 *  @return    string
	 */
	function __($key, $replace = array())
	{
		return app('slice')->i18n($key, $replace);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('app'))
{
	/**
	 *  Get the available library instance
	 *
	 *  @param     string    $make
	 *  @param     array     $params
	 *  @return    mixed
	 */
	function app($make = NULL, $params = array())
	{
		if (is_null($make))
		{
			return get_instance();
		}

		//	Special cases 'user_agent' and 'unit_test' are loaded
		//	with diferent names
		if ($make !== 'user_agent')
		{
			$lib = ($make == 'unit_test') ? 'unit' : $make;
		}
		else
		{
			$lib = 'agent';
		}

		//	Library not loaded
		if ( ! isset(get_instance()->$lib))
		{
			//	Special case 'cache' is a driver
			if ($make == 'cache')
			{
				get_instance()->load->driver($make, $params);
			}

			//	The type of what is being loaded, i.e. a model or a library
			$loader = (ends_with($make, '_model'))
				? 'model'
				: 'library';

			get_instance()->load->$loader($make, $params);
		}

		//	Special name for 'unit_test' is 'unit'
		if ($make == 'unit_test')
		{
			return get_instance()->unit;
		}
		//	Special name for 'user_agent' is 'agent'
		elseif ($make == 'user_agent')
		{
			return get_instance()->agent;
		}

		return get_instance()->$make;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_add'))
{
	/**
	 *  Add an element to an array using 'dot' notation if it doesn't exist
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @param     mixed     $value
	 *  @return    array
	 */
	function array_add($array, $key, $value)
	{
		if (is_null(get($array, $key)))
		{
			set($array, $key, $value);
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_collapse'))
{
	/**
	 *  Collapse an array of arrays into a single array
	 *
	 *  @param     array    $array
	 *  @return    array
	 */
	function array_collapse($array)
	{
		$results = array();

		foreach ($array as $values)
		{
			if ( ! is_array($values))
			{
				continue;
			}

			$results = array_merge($results, $values);
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_divide'))
{
	/**
	 *  Divide an array into two arrays, one with keys and the other with values
	 *
	 *  @param     array    $array
	 *  @return    array
	 */
	function array_divide($array)
	{
		return array(array_keys($array), array_values($array));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_dot'))
{
	/**
	 *  Flatten a multi-dimensional associative array with dots
	 *
	 *  @param     array     $array
	 *  @param     string    $prepend
	 *  @return    array
	 */
	function array_dot($array, $prepend = '')
	{
		$results = array();

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$results = array_merge($results, dot($value, $prepend.$key.'.'));
			}
			else
			{
				$results[$prepend.$key] = $value;
			}
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_except'))
{
	/**
	 *  Get all of the given array except for a specified array of items
	 *
	 *  @param     array           $array
	 *  @param     array|string    $keys
	 *  @return    array
	 */
	function array_except($array, $keys)
	{
		return array_diff_key($array, array_flip((array) $keys));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_first'))
{
	/**
	 *  Return the first element in an array passing a given truth test
	 *
	 *  @param     array       $array
	 *  @param     \Closure    $callback
	 *  @param     mixed       $default
	 *  @return    mixed
	 */
	function array_first($array, $callback, $default = NULL)
	{
		foreach ($array as $key => $value)
		{
			if (call_user_func($callback, $key, $value))
			{
				return $value;
			}
		}

		return value($default);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_flatten'))
{
	/**
	 *  Flatten a multi-dimensional array into a single level
	 *
	 *  @param     array    $array
	 *  @return    array
	 */
	function array_flatten($array)
	{
		$return = array();

		array_walk_recursive($array, function($x) use (&$return)
		{
			$return[] = $x;
		});

		return $return;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_forget'))
{
	/**
	 *  Remove one or many array items from a given array using 'dot' notation
	 *
	 *  @param     array           $array
	 *  @param     array|string    $keys
	 *  @return    void
	 */
	function array_forget(&$array, $keys)
	{
		$original =& $array;

		foreach ((array) $keys as $key)
		{
			$parts = explode('.', $key);

			while (count($parts) > 1)
			{
				$part = array_shift($parts);

				if (isset($array[$part]) && is_array($array[$part]))
				{
					$array =& $array[$part];
				}
			}

			unset($array[array_shift($parts)]);

			// clean up after each pass
			$array =& $original;
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_get'))
{
	/**
	 *  Get an item from an array using 'dot' notation
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function array_get($array, $key, $default = NULL)
	{
		if (is_null($key))
		{
			return $array;
		}

		if (isset($array[$key]))
		{
			return $array[$key];
		}

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) OR ! array_key_exists($segment, $array))
			{
				return value($default);
			}

			$array = $array[$segment];
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_has'))
{
	/**
	 *  Check if an item or items exist in an array using 'dot' notation
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @return    boolean
	 */
	function array_has($array, $key)
	{
		if (empty($array) OR is_null($key))
		{
			return FALSE;
		}

		if (array_key_exists($key, $array))
		{
			return TRUE;
		}

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) OR ! array_key_exists($segment, $array))
			{
				return FALSE;
			}

			$array = $array[$segment];
		}

		return TRUE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_last'))
{
	/**
	 *  Return the last element in an array passing a given truth test
	 *
	 *  @param     array       $array
	 *  @param     \Closure    $callback
	 *  @param     mixed       $default
	 *  @return    mixed
	 */
	function array_last($array, $callback, $default = NULL)
	{
		return first(array_reverse($array), $callback, $default);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_only'))
{
	/**
	 *  Get a subset of the items from the given array
	 *
	 *  @param     array           $array
	 *  @param     array|string    $keys
	 *  @return    array
	 */
	function array_only($array, $keys)
	{
		return array_intersect_key($array, array_flip((array) $keys));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_pluck'))
{
	/**
	 *  Pluck an array of values from an array
	 *
	 *  @param     array     $array
	 *  @param     string    $value
	 *  @param     string    $key
	 *  @return    array
	 */
	function array_pluck($array, $value, $key = NULL)
	{
		$results = array();

		foreach ($array as $item)
		{
			$item_value = data_get($item, $value);

			//	If the key is "null", we will just append the value to
			//	the array and keep looping. Otherwise we will key the
			//	array using the value of the key we received from the
			//	developer. Then we'll return the final array form.

			if (is_null($key))
			{
				$results[] = $item_value;
			}
			else
			{
				$item_key = data_get($item, $key);

				$results[$item_key] = $item_value;
			}
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_prepend'))
{
	/**
	 *  Push an item onto the beginning of an array
	 *
	 *  @param     array    $array
	 *  @param     mixed    $value
	 *  @param     mixed    $key
	 *  @return    array
	 */
	function array_prepend($array, $value, $key = NULL)
	{
		if (is_null($key))
		{
			array_unshift($array, $value);
		}
		else
		{
			$array = array($key => $value) + $array;
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_pull'))
{
	/**
	 *  Get a value from the array, and remove it
	 *
	 *  @param     array     &$array
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function array_pull(&$array, $key, $default = NULL)
	{
		$value = get($array, $key, $default);

		forget($array, $key);

		return $value;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_random'))
{
	/**
	 *  Get a random value from an array
	 *
	 *  @param     array           $array
	 *  @param     integer|null    $amount
	 *  @return    mixed
	 */
	function array_random($array, $amount = NULL)
	{
		if (($amount ?: 1) > count($array))
		{
			return FALSE;
		}

		if (is_null($amount))
		{
			return $array[array_rand($array)];
		}

		$keys		= array_rand($array, $amount);
		$results	= array();

		foreach ((array) $keys as $key)
		{
			$results[] = $array[$key];
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_set'))
{
	/**
	 *  Set an array item to a given value using 'dot' notation
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @param     mixed     $value
	 *  @return    mixed
	 */
	function array_set(&$array, $key, $value)
	{
		//	If no key is given to the method, the entire array will be replaced
		if (is_null($key))
		{
			return $array = $value;
		}

		$keys = explode('.', $key);

		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			//	If the key doesn't exist at this depth, we will just create
			//	an empty array to hold the next value, allowing us to create
			//	the arrays to hold final values at the correct depth. Then
			//	we'll keep digging into the array.
			if ( ! isset($array[$key]) OR ! is_array($array[$key]))
			{
				$array[$key] = array();
			}

			$array =& $array[$key];
		}

		$array[array_shift($keys)] = $value;

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_sort_recursive'))
{
	/**
	 *  Recursively sort an array by keys and values
	 *
	 *  @param     array    $array
	 *  @return    array
	 */
	function array_sort_recursive($array)
	{
		foreach ($array as &$value)
		{
			if (is_array($value))
			{
				$value = array_sort_recursive($value);
			}
		}

		if (array_keys(array_keys($array)) !== array_keys($array))
		{
			ksort($array);
		}
		else
		{
			sort($array);
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_where'))
{
	/**
	 *  Filter the array using the given callback
	 *
	 *  @param     array       $array
	 *  @param     \Closure    $callback
	 *  @return    array
	 */
	function array_where($array, callable $callback)
	{
		$filtered = array();

		foreach ($array as $key => $value)
		{
			if (call_user_func($callback, $key, $value))
			{
				$filtered[$key] = $value;
			}
		}

		return $filtered;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_wrap'))
{
	/**
	 *  If the given value is not an array, wrap it in one
	 *
	 *  @param     mixed    $value
	 *  @return    array
	 */
	function array_wrap($value)
	{
		return is_array($value) ? $value :array($value);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('cache'))
{
	/**
	 *  Get / set the specified cache value
	 *
	 *  If an array is passed, we'll assume you want to put to the cache
	 *
	 *  @param     array|string    $key
	 *  @param     mixed           $value
	 *  @return    mixed
	 */
	function cache($key = NULL, $value = NULL)
	{
		if (is_null($key))
		{
			return app('cache');
		}

		if (is_array($key) && is_int($value))
		{
			foreach ($key as $id => $data)
			{
				app('cache')->file->save($id, $data, ($value * 60));
			}

			return;
		}

		if ($cached = app('cache')->file->get($key))
		{
			return $cached;
		}

		return $value;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('camel_case'))
{
	/**
	 *  Convert a string to camel case
	 *
	 *  @param     string    $str
	 *  @return    string
	 */
	function camel_case($str)
	{
		static $camel_cache = array();

		if (isset($camel_cache[$str]))
		{
			return $camel_cache[$str];
		}

		return $camel_cache[$str] = lcfirst(studly_case($str));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('charset'))
{
	/**
	 *  Get the accepted character sets or a particular character set
	 *
	 *  @param     string         $key
	 *  @return    array|boolean
	 */
	function charset($key = NULL)
	{
		if (is_null($key))
		{
			return app('user_agent')->charsets();
		}

		return app('user_agent')->accept_charset($key);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('choice'))
{
	/**
	 *  Translate the given message based on a count
	 *
	 *  @param     string    $key
	 *  @param     int|array $number
	 *  @param     array     $replace
	 *  @return    string
	 */
	function choice($key, $number, $replace = array())
	{
		return app('slice')->inflector($key, $number, $replace);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('class_basename'))
{
	/**
	 *  Get the class 'basename' of the given object/class
	 *
	 *  @param     string|object    $class
	 *  @return    string
	 */
	function class_basename($class)
	{
		$class = is_object($class) ? get_class($class) : $class;

		return basename(str_replace('\\', '/', $class));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('class_uses_recursive'))
{
	/**
	 *  Return all traits used by a class, it's subclasses and trait of their traits
	 *
	 *  @param     string    $class
	 *  @return    array
	 */
	function class_uses_recursive($class)
	{
		$result = array();

		foreach (array_merge(array($class => $class), class_parents($class)) as $class)
		{
			$result += trait_uses_recursive($class);
		}

		return array_unique($result);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('config'))
{
	/**
	 *  Get / set the specified configuration value
	 *
	 *  @param     array|string    $key
	 *  @param     mixed           $value
	 *  @return    mixed
	 */
	function config($key = NULL, $value = NULL)
	{
		if (is_null($key))
		{
			return app('config');
		}

		if (is_array($key))
		{
			foreach ($key as $item => $val)
			{
				config($item, $val);
			}

			return;
		}

		if ( ! is_null($value))
		{
			return app('config')->set_item($key, $value);
		}

		return app('config')->item($key);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('csrf_field'))
{
	/**
	 *  Generate a CSRF token form field
	 *
	 *  @return    string
	 */
	function csrf_field()
	{
		return helper(
			'form.form_hidden',
			get_instance()->security->get_csrf_token_name(),
			csrf_token()
		);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('csrf_token'))
{
	/**
	 *  Get the CSRF token value
	 *
	 *  @return    string
	 */
	function csrf_token()
	{
		return get_instance()->security->get_csrf_hash();
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('data_get'))
{
	/**
	 *  Get an item from an array or object using 'dot' notation
	 *
	 *  @param     mixed     $target
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function data_get($target, $key, $default = NULL)
	{
		if (is_null($key))
		{
			return $target;
		}

		foreach (explode('.', $key) as $segment)
		{
			if (is_array($target))
			{
				if ( ! array_key_exists($segment, $target))
				{
					return value($default);
				}

				$target = $target[$segment];
			}
			elseif ($target instanceof ArrayAccess)
			{
				if ( ! isset($target[$segment]))
				{
					return value($default);
				}

				$target = $target[$segment];
			}
			elseif (is_object($target))
			{
				if ( ! isset($target->{$segment}))
				{
					return value($default);
				}

				$target = $target->{$segment};
			}
			else
			{
				return value($default);
			}
		}

		return $target;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('dbase'))
{
	/**
	 *  Database Loader
	 *
	 *  @param     string      $group
	 *  @return    object|bool
	 */
	function dbase($group = '')
	{
		return app('load')->database($group, TRUE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('dd'))
{
	/**
	 *  Dump the passed variables and end the script
	 *
	 *  @return    mixed
	 */
	function dd()
	{
		array_map(function ($data)
		{
			var_dump($data);
		},
		func_get_args());

		die(1);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('decrypt'))
{
	/**
	 *  Decrypt a given string
	 *
	 *  @param     string    $value
	 *  @return    string
	 */
	function decrypt($value)
	{
		return app('encryption')->decrypt($value);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('device'))
{
	/**
	 *  Get the agent string or one of this device information: browser
	 *  name, browser version, mobile device, robot name, plataform or
	 *  the referrer
	 *
	 *  @param     string    $key
	 *  @return    string
	 */
	function device($key = NULL)
	{
		if (is_null($key))
		{
			return app('user_agent')->agent_string();
		}

		$devices = array('browser', 'version', 'mobile', 'robot', 'platform', 'referrer');

		if (in_array($key, $devices))
		{
			return app('user_agent')->{$key}();
		}

		return NULL;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('dot'))
{
	/**
	 *  Flatten a multi-dimensional associative array with dots
	 *
	 *  @param     array     $array
	 *  @param     string    $prepend
	 *  @return    array
	 */
	function dot($array, $prepend = '')
	{
		$results = array();

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$results = array_merge($results, dot($value, $prepend.$key.'.'));
			}
			else
			{
				$results[$prepend.$key] = $value;
			}
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('dump'))
{
	/**
	 *  Dump the passed variables
	 *
	 *  @return    mixed
	 */
	function dump()
	{
		array_map(function ($data)
		{
			var_dump($data);
		},
		func_get_args());
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('e'))
{
	/**
	 *  Escape HTML entities in a string
	 *
	 *  @param     string    $value
	 *  @return    string
	 */
	function e($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8', FALSE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('email'))
{
	/**
	 *  Send an email
	 *
	 *  @param     string    $to
	 *  @param     string    $subject
	 *  @param     string    $message
	 *  @return    boolean
	 */
	function email($to = NULL, $subject = NULL, $message = NULL)
	{
		if (is_null($to))
		{
			return app('email');
		}

		app('email')->to($to)->subject($subject)->message($message);

		return app('email')->send();
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('encrypt'))
{
	/**
	 *  Encrypt a given string
	 *
	 *  @param     string    $value
	 *  @return    string
	 */
	function encrypt($value)
	{
		return app('encryption')->encrypt($value);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ends_with'))
{
	/**
	 *  Determine if a given string ends with a given substring
	 *
	 *  @param     string          $haystack
	 *  @param     string|array    $needles
	 *  @return    boolean
	 */
	function ends_with($haystack, $needles)
	{
		foreach ((array) $needles as $needle)
		{
			if (substr($haystack, -strlen($needle)) === (string) $needle)
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('env'))
{
	/**
	 *  Determine if a given environment is the current environment
	 *
	 *  @param     string    $key
	 *  @return    boolean
	 */
	function env($key)
	{
		return (strtolower(ENVIRONMENT) === strtolower($key));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('first'))
{
	/**
	 *  Return the first element in an array passing a given truth test
	 *
	 *  @param     array       $array
	 *  @param     \Closure    $callback
	 *  @param     mixed       $default
	 *  @return    mixed
	 */
	function first($array, callable $callback, $default = NULL)
	{
		foreach ($array as $key => $value)
		{
			if (call_user_func($callback, $key, $value))
			{
				return $value;
			}
		}

		return value($default);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('forget'))
{
	/**
	 *  Remove one or many array items from a given array using 'dot' notation
	 *
	 *  @param     array           $array
	 *  @param     array|string    $keys
	 *  @return    void
	 */
	function forget(&$array, $keys)
	{
		$original =& $array;

		foreach ((array) $keys as $key)
		{
			$parts = explode('.', $key);

			while (count($parts) > 1)
			{
				$part = array_shift($parts);

				if (isset($array[$part]) && is_array($array[$part]))
				{
					$array =& $array[$part];
				}
			}

			unset($array[array_shift($parts)]);

			//	Clean up after each pass
			$array =& $original;
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get'))
{
	/**
	 *  Get an item from an array using 'dot' notation
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function get($array, $key, $default = NULL)
	{
		if (is_null($key))
		{
			return $array;
		}

		if (isset($array[$key]))
		{
			return $array[$key];
		}

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) OR ! array_key_exists($segment, $array))
			{
				return value($default);
			}

			$array = $array[$segment];
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('head'))
{
	/**
	 *  Get the first element of an array (useful for method chaining)
	 *
	 *  @param     array    $array
	 *  @return    mixed
	 */
	function head($array)
	{
		return reset($array);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('helper'))
{
	/**
	 *  Load any CI helper
	 *
	 *  @param     string    $name
	 *  @param     array     $params
	 *  @return    mixed
	 */
	function helper($name, ...$params)
	{
		//	Separate 'file' and 'helper' by dot notation
		list($helper, $func) = array_pad(explode('.', $name), 2, NULL);

		//	If using dot notation
		if ($func !== NULL)
		{
			get_instance()->load->helper($helper);
			$helper = $func;
		}

		return call_user_func_array($helper, $params);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('input'))
{
	/**
	 *  Retrieve input item from the request
	 *
	 *  @param     array|string    $key
	 *  @param     string          $method
	 *  @return    mixed
	 */
	function input($key = NULL, $method = NULL)
	{
		if (is_null($key))
		{
			return app('input');
		}

		if (is_array($key))
		{
			if ( ! is_null($method))
			{
				return app('input')->$method($key);
			}

			return NULL;
		}

		if ($value = app('input')->post_get($key))
		{
			return $value;
		}

		if ($value = app('input')->cookie($key))
		{
			return $value;
		}

		return app('input')->server($key);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is'))
{
	/**
	 *  'Is' functions
	 *
	 *  @param     string     $key
	 *  @param     string     $value
	 *  @return    boolean
	 */
	function is($key, $value = NULL)
	{
		$common		= array('https', 'cli', 'php', 'writable');
		$useragent	= array('browser', 'mobile', 'referral', 'robot');

		if (in_array($key, $useragent))
		{
			return app('user_agent')->{'is_'.$key}($value);
		}

		if (in_array($key, $common))
		{
			$function = ($key == 'writable')
				? 'is_really_writable'
				: 'is_'.$key;

			return $function($value);
		}

		if ($key == 'ajax')
		{
			return app('input')->is_ajax_request();
		}

		if ($key == 'loaded' OR $key == 'load')
		{
			return (bool) app('load')->is_loaded($value);
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('kebab_case'))
{
	/**
	 *  Convert a string to kebab case
	 *
	 *  @param     string    $str
	 *  @return    string
	 */
	function kebab_case($str)
	{
		return snake_case($str, '-');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('lang'))
{
	/**
	 *  Translate the given message
	 *
	 *  @param     string    $key
	 *  @param     array     $replace
	 *  @return    string
	 */
	function lang($key, $replace = array())
	{
		return app('slice')->i18n($key, $replace);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('last'))
{
	/**
	 *  Get the last element from an array
	 *
	 *  @param     array    $array
	 *  @return    mixed
	 */
	function last($array)
	{
		return end($array);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('length'))
{
	/**
     *  Return the length of the given string
     *
     *  @param     string    $value
     *  @param     string    $encoding
     *  @return    integer
     */
	function length($value, $encoding = NULL)
	{
		if ( ! is_null($encoding))
		{
			return mb_strlen($value, $encoding);
		}

		return mb_strlen($value);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('make'))
{
	/**
	 *  Get the available library instance and return a new instance of it
	 *
	 *  @param     string    $class
	 *  @return    object
	 */
	function make($class)
	{
		app($class);
		return new $class();
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mark'))
{
	/**
	 *  Set a benchmark marker or calculate the time difference between
	 *  two marked points.
	 *
	 *  @param     string    $point1
	 *  @param     string    $point2
	 *  @return    void|string
	 */
	function mark($point1, $point2 = NULL)
	{
		if (is_null($point2))
		{
			get_instance()->benchmark->mark($point1);
		}
		else
		{
			return get_instance()->benchmark->elapsed_time($point1, $point2);
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('object_get'))
{
	/**
	 *  Get an item from an object using 'dot' notation
	 *
	 *  @param     object    $object
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function object_get($object, $key, $default = NULL)
	{
		if (is_null($key) OR trim($key) == '')
		{
			return $object;
		}

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_object($object) OR ! isset($object->{$segment}))
			{
				return value($default);
			}

			$object = $object->{$segment};
		}

		return $object;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('query'))
{
	/**
	 *  Execute the query
	 *
	 *  @param     string     $sql
	 *  @param     array      $binds
	 *  @param     boolean    $return_object
	 *  @return    mixed
	 */
	function query($sql, $bind = FALSE, $return_object = NULL)
	{
		return dbase()->query($sql, $bind, $return_object);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('request'))
{
	/**
	 *  Get a single request header or all of the request headers
	 *
	 *  @param     string       $key
	 *  @return    array|string
	 */
	function request($key = NULL)
	{
		if (is_null($key))
		{
			return app('input')->request_headers();
		}

		return app('input')->get_request_header($key);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('resolve'))
{
	/**
	 *  Resolve a library from the current CI instance
	 *
	 *  @param     string    $name
	 *  @return    mixed
	 */
	function resolve($name)
	{
		return app($name);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('retry'))
{
	/**
	 *  Attempt to execute an operation a given number of times
	 *
	 *  @param     int         $attempts
	 *  @param     callable    $callback
	 *  @param     integer     $sleep
	 *  @return    mixed
	 *
	 *  @throws    \Exception
	 */
	function retry($attempts, callable $callback, $sleep = 0)
	{
		$attempts--;	//	Decrement the number of attempts

		beginning:
		try
		{
			return $callback();
		}
		catch (Exception $e)
		{
			if ( ! $attempts)
			{
				throw $e;
			}

			$attempts--;	//	Decrement the number of attempts

			if ($sleep)
			{
				usleep($sleep * 1000);
			}

			goto beginning;
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('segment'))
{
	/**
	 *  Fetch URI Segment
	 *
	 *  @param     int       $n
	 *  @param     mixed     $no_result
	 *  @param     boolean   $rsegment
	 *  @return    mixed
	 */
	function segment($n, $no_result = NULL, $rsegment = FALSE)
	{
		if ($rsegment !== FALSE)
		{
			return app('uri')->rsegment($n, $no_result);
		}

		return app('uri')->segment($n, $no_result);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('session'))
{
	/**
	 *  Get / set the specified session value
	 *
	 *  If an array is passed as the key, we will assume you want to set
	 *  an array of values
	 *
	 *  @param     array|string    $key
	 *  @param     mixed           $value
	 *  @return    mixed
	 */
	function session($key = NULL, $value = NULL)
	{
		if (is_null($key))
		{
			return app('session');
		}

		if (is_array($key))
		{
			return app('session')->set_userdata($key);
		}

		if ( ! is_null($value))
		{
			app('session')->set_userdata($key, $value);
		}

		return app('session')->$key;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set'))
{
	/**
	 *  Set an array item to a given value using 'dot' notation
	 *
	 *  @param    array     $array
	 *  @param    string    $key
	 *  @param    mixed     $value
	 *  @return   array
	 */
	function set(&$array, $key, $value)
	{
		//	If no key is given to the method, the entire array will be replaced
		if (is_null($key))
		{
			return $array = $value;
		}

		$keys = explode('.', $key);

		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			//	If the key doesn't exist at this depth, we will just create
			//	an empty array to hold the next value, allowing us to create
			//	the arrays to hold final values at the correct depth. Then
			//	we'll keep digging into the array.
			if ( ! isset($array[$key]) OR ! is_array($array[$key]))
			{
				$array[$key] = array();
			}

			$array =& $array[$key];
		}

		$array[array_shift($keys)] = $value;

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('slice'))
{
	/**
	 *  Get the Slice-Library instance
	 *
	 *  @param     array    $params
	 *  @return    object
	 */
	function slice($params = NULL)
	{
		if (is_null($params))
		{
			return app('slice');
		}

		return app('slice', (array) $params);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('slug_case'))
{
	/**
	 *  Convert the given string to slug case
	 *
	 *  @param     string     $value
	 *  @param     string     $separator
	 *  @param     boolean    $lowercase
	 *  @return    string
	 */
	function slug_case($str, $separator = '-', $lowercase = TRUE)
	{
		$str = helper('text.convert_accented_characters', $str);

		return helper('url.url_title', $str, $separator, $lowercase);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('snake_case'))
{
	/**
	 *  Convert a string to snake case
	 *
	 *  @param     string    $str
	 *  @param     string    $delimiter
	 *  @return    string
	 */
	function snake_case($str, $delimiter = '_')
	{
		static $snake_cache = array();
		$key = $str.$delimiter;

		if (isset($snake_cache[$key]))
		{
			return $snake_cache[$key];
		}

		if ( ! ctype_lower($str))
		{
			$str = preg_replace('/\s+/u', '', $str);
			$str = preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $str);
		}

		return $snake_cache[$key] = mb_strtolower($str, 'UTF-8');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('starts_with'))
{
	/**
	 *  Determine if a given string starts with a given substring
	 *
	 *  @param     string          $haystack
	 *  @param     string|array    $needles
	 *  @return    boolean
	 */
	function starts_with($haystack, $needles)
	{
		foreach ((array) $needles as $needle)
		{
			if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string) $needle)
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_after'))
{
	/**
	 *  Return the remainder of a string after a given value
	 *
	 *  @param     string    $str
	 *  @param     string    $search
	 *  @return    string
	 */
	function str_after($str, $search)
	{
		if ( ! is_bool(strpos($str, $search)))
		{
			return substr($str, strpos($str, $search) + strlen($search));
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_after_last'))
{
	/**
	 *  Return the remainder of a string after the last given value
	 *
	 *  @param     string    $str
	 *  @param     string    $search
	 *  @return    string
	 */
	function str_after_last($str, $search)
	{
		if ( ! is_bool(strrevpos($str, $search)))
		{
			return substr($str, strrevpos($str, $search) + strlen($search));
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_before'))
{
	/**
	 *  Return the string before the given value
	 *
	 *  @param     string    $str
	 *  @param     string    $search
	 *  @return    string
	 */
	function str_before($str, $search)
	{
		return substr($str, 0, strpos($str, $search));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_before_last'))
{
	/**
	 *  Return the string before the last given value
	 *
	 *  @param     string    $str
	 *  @param     string    $search
	 *  @return    string
	 */
	function str_before_last($str, $search)
	{
		return substr($str, 0, strrevpos($str, $search));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_between'))
{
	/**
	 *  Return the string between the given values
	 *
	 *  @param     string    $str
	 *  @param     string    $search1
	 *  @param     string    $search2
	 *  @return    string
	 */
	function str_between($str, $search1, $search2)
	{
		return str_before(str_after($str, $search1), $search2);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_between_last'))
{
	/**
	 *  Return the string between the last given values
	 *
	 *  @param     string    $str
	 *  @param     string    $search1
	 *  @param     string    $search2
	 *  @return    string
	 */
	function str_between_last($str, $search1, $search2)
	{
		return str_after_last(str_before_last($str, $search2), $search1);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_contains'))
{
	/**
	 *  Determine if a given string contains a given substring
	 *
	 *  @param     string          $haystack
	 *  @param     string|array    $needles
	 *  @return    boolean
	 */
	function str_contains($haystack, $needles)
	{
		foreach ((array) $needles as $needle)
		{
			if ($needle != '' && mb_strpos($haystack, $needle) !== FALSE)
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_finish'))
{
	/**
	 *  Cap a string with a single instance of a given value
	 *
	 *  @param     string    $str
	 *  @param     string    $cap
	 *  @return    string
	 */
	function str_finish($str, $cap)
	{
		$quoted = preg_quote($cap, '/');

		return preg_replace('/(?:'.$quoted.')+$/u', '', $str).$cap;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_is'))
{
	/**
	 *  Determine if a given string matches a given pattern
	 *
	 *  @param     string    $pattern
	 *  @param     string    $value
	 *  @return    boolean
	 */
	function str_is($pattern, $value)
	{
		if ($pattern == $value)
		{
			return TRUE;
		}

		$pattern = preg_quote($pattern, '#');

		//	Asterisks are translated into zero-or-more regular expression wildcards
		//	to make it convenient to check if the strings starts with the given
		//	pattern such as "library/*", making any string check convenient.
		$pattern = str_replace('\*', '.*', $pattern);

		return (bool) preg_match('#^'.$pattern.'\z#u', $value);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_limit'))
{
	/**
	 *  Ellipsize a string
	 *
	 *  @param     string     $str
	 *  @param     integer    $max_length
	 *  @param     integer    $position
	 *  @param     string     $ellipsis
	 *  @return    string
	 */
	function str_limit($str, $max_length = 100, $position = 1, $ellipsis = '&hellip;')
	{
		return helper('text.ellipsize', $str, $max_length, $position, $ellipsis);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_random'))
{
	/**
	 *  Create a "Random" String
	 *
	 *  @param     integer    $length
	 *  @param     string     $type
	 *  @return    string
	 */
	function str_random($length = 16, $type = 'alnum')
	{
		return helper('string.random_string', $type, $length);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('str_replace_array'))
{
	/**
	 *  Replace a given value in the string sequentially with an array
	 *
	 *  @param     string    $search
	 *  @param     array     $replace
	 *  @param     string    $subject
	 *  @return    string
	 */
	function str_replace_array($search, array $replace, $subject)
	{
		foreach ($replace as $value)
		{
			$subject = preg_replace('/'.$search.'/', $value, $subject, 1);
		}

		return $subject;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('strrevpos'))
{
	/**
	 *  Find the position of the last occurrence of a substring in a string
	 *
	 *  @param     string         $haystack
	 *  @param     string         $needle
	 *  @return    string|boolean
	 */
	function strrevpos($haystack, $needle)
	{
		$revpos = strpos(strrev($haystack), strrev($needle));

		if ($revpos !== FALSE)
		{
			return strlen($haystack) - $revpos - strlen($needle);
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('studly_case'))
{
	/**
	 *  Convert a string to studly caps case
	 *
	 *  @param     string    $str
	 *  @return    string
	 */
	function studly_case($str)
	{
		static $studly_cache = array();
		$key = $str;

		if (isset($studly_cache[$key]))
		{
			return $studly_cache[$key];
		}

		$value = ucwords(str_replace(array('-', '_'), ' ', $str));

		return str_replace(' ', '', $value);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('title_case'))
{
	/**
	 *  Convert the given string to title case
	 *
	 *  @param     string    $str
	 *  @return    string
	 */
	function title_case($str)
	{
		return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('trait_uses_recursive'))
{
	/**
	 *  Returns all traits used by a trait and its traits
	 *
	 *  @param     string    $trait
	 *  @return    array
	 */
	function trait_uses_recursive($trait)
	{
		$traits = class_uses($trait);

		foreach ($traits as $trait)
		{
			$traits += trait_uses_recursive($trait);
		}

		return $traits;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('uri'))
{
	/**
	 *  Fetch URI string or Segment Array
	 *
	 *  @param     boolean    $array
	 *  @param     boolean    $rsegment
	 *  @return    array|string
	 */
	function uri($array = FALSE, $rsegment = FALSE)
	{
		$preffix = ($rsegment !== FALSE) ? 'r' : '';

		if ($array !== FALSE)
		{
			return app('uri')->{$preffix.'segment_array'}();
		}

		return app('uri')->{$preffix.'uri_string'}();
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('url'))
{
	/**
	 *  Site URL
	 *
	 *  @param     string|array  $uri
	 *  @param     string        $protocol
	 *  @param     boolean       $base
	 *  @return    string
	 */
	function url($uri = NULL, $protocol = NULL, $base = FALSE)
	{
		if (is_null($uri))
		{
			return app('uri');
		}

		if ($base !== FALSE)
		{
			return app('config')->base_url($uri, $protocol);
		}

		return app('config')->site_url($uri, $protocol);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('validator'))
{
	/**
	 *  Validate post fields with CodeIgniter Form Validation Class
	 *
	 *  @param     array|null    $array
	 *  @param     boolean       $show_errors
	 *  @return    mixed
	 */
	function validator(array $array = NULL, $show_errors = FALSE)
	{
		if (is_null($array))
		{
			return app('form_validation');
		}

		foreach ($array as $fieldset => $rules)
		{
			list($field, $label) = array_pad(explode('.', $fieldset), 2, NULL);

			if ( ! is_null($label))
			{
				app('form_validation')->set_rules($field, $label, $rules);
			}
			else
			{
				app('form_validation')->set_rules($field, $field, $rules);
			}
		}

		if ($show_errors)
		{
			return app('form_validation')->error_array();
		}

		return (app('form_validation')->run());
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('value'))
{
	/**
	 *  Return the default value of the given value
	 *
	 *  @param     mixed    $value
	 *  @return    mixed
	 */
	function value($value)
	{
		return $value instanceof Closure ? $value() : $value;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('view'))
{
	/**
	 *  Get the evaluated view contents for the given view
	 *
	 *  @param     string    $view
	 *  @param     array     $data
	 *  @return    string
	 */
	function view($view, array $data = NULL)
	{
		if (is_null($data))
		{
			return app('slice')->view($view);
		}

		return app('slice')->set($data)->view($view);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('with'))
{
	/**
	 *  Return the given object (useful for chaining)
	 *
	 *  @param     mixed    $object
	 *  @return    mixed
	 */
	function with($object)
	{
		return $object;
	}
}
