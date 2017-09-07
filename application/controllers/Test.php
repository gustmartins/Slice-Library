<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *  This is a test class to show you how to work with Slice-Library!
 */
class Test extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//	Load the Slice-Library as any CodeIgniter library
		$this->load->library('slice');
		$this->load->helper('url');
	}

	public function index()
	{
		/**
		 *  The with() method adds a pair of key/value data.
		 *  The directive() method inserts a custom directive
		 *  The view() method shows the page located at 'application/views/page.slice.php'
		 */
		$this->slice->with('full_name', 'John Doe')
					->with('users', [['id' => 1, 'name' => 'John'], ['id' => 2, 'name' => 'Jane'], ['id' => 3, 'name' => 'Dave'], ['id' => 4, 'name' => 'Arthur'], ['id' => 5, 'name' => 'Michael'], ['id' => 6, 'name' => 'Ben']])
					->with('members', [])
					->with('rows', '0')
					->directive('Test::custom_slice_directive');

		//	Slice-Library 1.3 comes with helpers to make things easier!
		view('page');
	}

	/**
	 *  This is a custom function that adds a custom directive to Slice-Library!
	 *  This function finds for @slice('string') directive and shows the string
	 */
	static function custom_slice_directive($content)
	{
		$pattern = '/(\s*)@slice\s*\((\'.*\')\)/';
		return preg_replace($pattern, '$1<?php echo "$2"; ?>', $content);
	}
}
