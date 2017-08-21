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

		$lib = ($make == 'user_agent')
			? 'agent'
			: ($make == 'unit_test') ? 'unit' : $make;

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
		return lcfirst(studly_case($str));
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

if ( ! function_exists('choise'))
{
	/**
	 *  Translate the given message based on a count
	 *
	 *  @param     string    $key
	 *  @param     int|array $number
	 *  @param     array     $replace
	 *  @return    string
	 */
	function choise($key, $number, $replace = array())
	{
		return app('slice')->inflector($key, $number, $replace);
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
				? 'is_really_writable' :
				'is_'.$key;

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
	function lang($key = NULL, $replace = array())
	{
		return app('slice')->i18n($key, $replace);
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
		if ( ! ctype_lower($str))
		{
			$str = preg_replace('/\s+/u', '', $str);
			$str = preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $str);
		}

		return mb_strtolower($str, 'UTF-8');
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
		return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $str)));
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

if ( ! function_exists('view'))
{
	/**
	 *  Get the evaluated view contents for the given view
	 *
	 *  @param     string    $view
	 *  @param     array     $data
	 *  @return    string
	 */
	function view($view, $data = NULL)
	{
		if (is_null($data))
		{
			return app('slice')->view($view);
		}

		return app('slice')->set((array) $data)->view($view);
	}
}

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
	function __($key = NULL, $replace = array())
	{
		return app('slice')->i18n($key, $replace);
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
