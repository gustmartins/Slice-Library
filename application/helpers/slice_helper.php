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

		//	Library not loaded
		if ( ! isset(get_instance()->$make))
		{
			//	Special case 'cache' is a driver
			if ($make == 'cache')
			{
				get_instance()->load->driver($make, $params);
			}

			//	The type of what is being loaded, i.e. a model or a library
			$loader = (ends_with($class, '_model'))
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
