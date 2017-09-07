<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package		Slice
 * @author		Gustavo Martins <gustavo_martins92@hotmail.com>
 * @link		https://github.com/GustMartins/Slice-Library
 * @since		Version 1.0.0
 */

/*
|--------------------------------------------------------------------------
| Slice File Extension
|--------------------------------------------------------------------------
|
| Set the file extension for the slice template
|
*/
$config['slice_ext'] = '.slice.php';

/*
|--------------------------------------------------------------------------
| Cache Expiration Time
|--------------------------------------------------------------------------
|
| Set the amount of time to keep the file in cache
|
*/
$config['cache_time'] = 3600;

/*
|--------------------------------------------------------------------------
| Enable/Disable Autoload
|--------------------------------------------------------------------------
|
| Set to TRUE to autoload CodeIgniter Libraries and Helpers
|
*/
$config['enable_autoload'] = FALSE;

/*
|--------------------------------------------------------------------------
| Resources to Autoload
|--------------------------------------------------------------------------
|
| List of Libraries and Helpers to autoload with Slice-Library.
|
| WARNING: To autoload this resources you must set 'enable_autoload'
| variable to TRUE.
|
*/
$config['libraries'] = array();
$config['helpers'] = array();

/*
|--------------------------------------------------------------------------
| Load Slice Helper
|--------------------------------------------------------------------------
|
| Set to TRUE and Slice helper file will be loaded in the initialization.
| Make sure you have the proper file at application/helper/slice_helper.php
|
*/
$config['enable_helper'] = TRUE;
