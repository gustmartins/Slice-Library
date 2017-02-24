<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Slice Class for Codeigniter
 *
 * This class is based on Laravel's Blade templating system!
 * To see the usage documentation please visit the link below.
 *
 * @package		Slice
 * @subpackage	Library
 * @category	Library
 * @author		Gustavo Martins <gustavo_martins92@hotmail.com>
 * @link		https://github.com/GustMartins/Slice-Library
 * @version 	1.0.0
 */
class Slice {

	// --------------------------------------------------------------------------
	// --------------------------------------------------------------------------

	/**
	 *  The file extension for the slice template
	 *
	 *  @var   string
	 */
	public $slice_ext		= '.slice.php';
	
	/**
	 *  The amount of time to keep the file in cache
	 *
	 *  @var   integer
	 */
	public $cache_time		= 3600;
	
	// --------------------------------------------------------------------------

	/**
	 *  Reference to CodeIgniter instance
	 *
	 *  @var   object
	 */
	protected $CI;

	/**
	 *  Global array of data for Slice Template
	 *
	 *  @var   array
	 */
	protected $_data		= array();

	/**
	 *  The content of each section
	 *
	 *  @var   array
	 */
	protected $_sections	= array();

	/**
	 *  The stack of current sections being buffered
	 *
	 *  @var   array
	 */
	protected $_buffer		= array();

	/**
	 *  Custom compile functions by the user
	 *
	 *  @var   array
	 */
	protected $_directives	= array();

	// --------------------------------------------------------------------------

	/**
	 *  All of the compiler methods used by Slice to simulate 
	 *  Laravel Blade Template
	 *
	 *  @var   array
	 */
	private $_compilers		= array(
		'directive',
		'comment',
		'ternary',
		'preserved',
		'echo',
		'forelse',
		'empty',
		'endforelse',
		'opening_statements',
		'else',
		'continueIf',
		'continue',
		'breakIf',
		'break',
		'closing_statements',
		'unless',
		'endunless',
		'includeIf',
		'include',
		'extends',
		'yield',
		'show',
		'opening_section',
		'closing_section',
		'php',
		'endphp'
	);
	
	// --------------------------------------------------------------------------
	// --------------------------------------------------------------------------

	/**
	 *  Slice Class Constructor
	 *
	 *  @param   array   $params = array()
	 *  @return	 void
	 */
	public function __construct(array $params = array())
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();
		$this->CI->load->driver('cache');
		$this->CI->config->load('slice');

		empty($params) or $this->initialize($params);

		log_message('info', 'Slice Template Class Initialized');
	}

	// --------------------------------------------------------------------------

	/**
	 *  __set magic method
	 *
	 *  Handles writing to the data property
	 *
	 *  @param   string   $name
	 *  @param   mixed    $value
	 */
	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	}

	// --------------------------------------------------------------------------

	/**
	 *  __unset magic method
	 *
	 *  Handles unseting to the data property
	 *
	 *  @param   string   $name
	 */
	public function __unset($name)
	{
		unset($this->_data[$name]);
	}

	// --------------------------------------------------------------------------

	/**
	 *  __get magic method
	 *
	 *  Handles reading of the data property
	 *
	 *  @param    string   $name
	 *  @return   mixed
	 */
	public function __get($name)
	{
		if (key_exists($name, $this->_data))
		{
			return $this->_data[$name];
		}

		return $this->CI->$name;
	}

	// --------------------------------------------------------------------------

	/**
	 * Initializes preferences
	 *
	 * @param	array	$params
	 * @return	Slice
	 */
	public function initialize(array $params = array())
	{
		$this->clear();

		foreach ($params as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Initializes some important variables
	 *
	 * @return	Slice
	 */
	public function clear()
	{
		$this->slice_ext	= config_item('slice_ext');
		$this->cache_time	= config_item('cache_time');
		$this->_data		= array();

		return $this;
	}

	// --------------------------------------------------------------------------
	// --------------------------------------------------------------------------

	/**
	 *  Sets one single data to Slice Template
	 *
	 *  @param    string   $name
	 *  @param    mixed    $value
	 *  @return   Slice
	 */
	public function with($name, $value = '')
	{
		$this->_data[$name] = $value;
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Sets one or more data to Slice Template
	 *
	 *  @param   mixed   $data
	 *  @param   mixed   $value
	 *  @return  Slice
	 */
	public function set($data, $value = '')
	{
		if (is_array($data))
		{
			$this->_data = array_merge($this->_data, $data);
		}
		else
		{
			$this->_data[$data] = $value;
		}

		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Appends or concatenates a value to a data in Slice Template
	 *  
	 *  If data type is array it will append
	 *  If data type is string it will concatenate
	 *
	 *  @param    string   $name
	 *  @param    mixed    $value
	 *  @return   Slice
	 */
	public function append($name, $value)
	{
		if (is_array($this->_data[$name]))
		{
			$this->_data[$name][] = $value;
		}
		else
		{
			$this->_data[$name] .= $value;
		}

		return $this;
	}

	// --------------------------------------------------------------------------
	// --------------------------------------------------------------------------

	/**
	 *  Outputs template content
	 *
	 *  @param    string    $template
	 *  @param    array     $data
	 *  @param    boolean   $return
	 *  @return   string
	 */
	public function view($template, $data = NULL, $return = FALSE)
	{
		if (isset($data))
		{
			$this->set($data);
		}

		//	Compile and execute the template
		$content = $this->_run($this->_compile($template), $this->_data);

		if ( ! $return)
		{
			$this->CI->output->append_output($content);
		}

		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Verifies if a file exists! Even if you are using Modular Extensions
	 *
	 *  @param    string    $filename
	 *  @param    boolean   $show_error
	 *  @return   mixed
	 */
	public function exists($filename, $show_error = FALSE)
	{
		$view_name = preg_replace('/([a-z]\w+)\./', '$1/', $filename);
		
		//	The default path to the file
		$default_path = VIEWPATH.$view_name.$this->slice_ext;

		//	If you are using Modular Extensions it will be detected
		if (method_exists($this->CI->router, 'fetch_module'))
		{
			$module = $this->CI->router->fetch_module();
			list($path, $_view) = Modules::find($view_name . $this->blade_ext, $module, 'views/');

			if ($path)
			{
				$default_path = $path . $_view;
			}
		}

		//	Verify if the page really exists
		if (is_file($default_path))
		{
			if ($show_error === TRUE)
			{
				return $default_path;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			if ($show_error === TRUE)
			{
				show_error('Sorry! We couldn\'t find the view: '.$view_name.$this->slice_ext);
			}
			else
			{
				return FALSE;
			}
		}
	}

	// --------------------------------------------------------------------------

	/**
	 *  Sets custom compilation function
	 *
	 *  @param    string   $compilator
	 *  @return   Slice
	 */
	public function directive($compilator)
	{
		$this->_directives[] = $compilator;
		return $this;
	}

	// --------------------------------------------------------------------------
	// --------------------------------------------------------------------------

	/**
	 *  Compiles a template and save it in the cache
	 *
	 *  @param    string   $template
	 *  @return   string
	 */
	protected function _compile($template)
	{
		$view_path	= $this->exists($template, TRUE);
		$cache_name	= 'slice-'.md5($view_path);

		//	Verifies if exists a cached version of the file
		if ($cached_version = $this->CI->cache->file->get($cache_name))
		{
			if (ENVIRONMENT == 'production')
			{
				return $cached_version;
			}

			$cached_meta = $this->CI->cache->file->get_metadata($cache_name);

			if ($cached_meta['mtime'] > filemtime($view_path))
			{
				return $cached_version;
			}
		}

		$content = file_get_contents($view_path);

		//	Compile the content
		foreach ($this->_compilers as $compiler)
		{
			$method = "_compile_{$compiler}";
			$content = $this->$method($content);
		}

		//	Store in the cache
		$this->CI->cache->file->save($cache_name, $content, $this->cache_time);

		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Runs the template with its data
	 *
	 *  @param    string   $template
	 *  @param    array    $data
	 *  @return   string
	 */
	protected function _run($template, $data = NULL)
	{
		if (is_array($data))
		{
			extract($data);
		}

		ob_start();
		eval(' ?>'.$template.'<?php ');

		$content = ob_get_clean();

		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Returns a protected variable
	 *
	 *  @param    string   $variable
	 *  @return   string
	 */
	protected function _untouch($variable)
	{
		return '{{'.$variable.'}}';
	}

	// --------------------------------------------------------------------------

	/**
	 *  Gets the content of a template to use inside the current template
	 *  It will inherit all the Global data
	 *
	 *  @param    string   $template
	 *  @param    array    $data
	 *  @return   string
	 */
	protected function _include($template, $data = NULL)
	{
		$data = isset($data) ? array_merge($this->_data, $data) : $this->_data;

		//	Compile and execute the template
		return $this->_run($this->_compile($template), $data);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Gets the content of a section
	 *
	 *  @param    string   $section
	 *  @param    string   $default
	 *  @return   string
	 */
	protected function _yield($section, $default = '')
	{
		return isset($this->_sections[$section]) ? $this->_sections[$section] : $default;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Starts buffering the content of a section
	 *  If the param $value is different of NULL it will be the content of 
	 *  the current section
	 *
	 *  @param    string   $section
	 *  @param    mixed    $value
	 */
	protected function _opening_section($section, $value = NULL)
	{
		array_push($this->_buffer, $section);

		if ($value !== NULL)
		{
			$this->_closing_section($value);
		}
		else
		{
			ob_start();
		}
	}

	// --------------------------------------------------------------------------

	/**
	 *  Stops buffering the content of a section
	 *  If the param $value is different of NULL it will be the 
	 *  content of the current section
	 *
	 *  @param    mixed    $value
	 *  @return   string
	 */
	protected function _closing_section($value = NULL)
	{
		$last_section = array_pop($this->_buffer);
		
		if ($value !== NULL)
		{
			$this->_extend_section($last_section, $value);
		}
		else
		{
			$this->_extend_section($last_section, ob_get_clean());
		}

		return $last_section;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites custom directives defined by the user
	 *
	 *  @param    string   $value
	 *  @return   string
	 */
	protected function _compile_directive($value)
	{
		foreach ($this->_directives as $compilator)
		{
			$value = call_user_func($compilator, $value);
		}

		return $value;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade comment into PHP comment
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_comment($content)
	{
		$pattern = '/\{\{--(.+?)(--\}\})?\n/';
		$return_pattern = '/\{\{--((.|\s)*?)--\}\}/';

		$content = preg_replace($pattern, "<?php // $1 ?>", $content);

		return preg_replace($return_pattern, "<?php /* $1 */ ?>\n", $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade conditional echo statement into PHP echo statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_ternary($content)
	{
		$pattern = '/\{\{\s\$(.\w*)\sor.[\'"]([^\'"]+)[\'"]\s\}\}/';

		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		foreach ($matches as $var)
		{
			$content = isset($this->_data[$var[1]]) ? str_replace($var[0], "<?php echo \$$var[1]; ?>", $content) : str_replace($var[0], "<?php echo '$var[2]'; ?>", $content);
		}
		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Preservers an expression to be displayed in the browser
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_preserved($content)
	{
		$pattern = '/@(\{\{(.+?)\}\})/';

		return preg_replace($pattern, '<?php echo $this->_untouch("$2"); ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade echo statement into PHP echo statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_echo($content)
	{
		$pattern = '/\{\{(.+?)\}\}/';

		return preg_replace($pattern, '<?php echo $1; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade forelse statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_forelse($content)
	{
		$pattern = '/(\s*)@forelse(\s*\(.*\))(\s*)/';
		
		preg_match_all($pattern, $content, $matches);

		foreach ($matches[0] as $forelse)
		{
			$variable_pattern = '/\$[^\s]*/';

			preg_match($variable_pattern, $forelse, $variable);

			$if_statement = "<?php if (count({$variable[0]}) > 0): ?>";
			$search_pattern = '/(\s*)@forelse(\s*\(.*\))/';
			$replacement = '$1'.$if_statement.'<?php foreach $2: ?>';

			$content = str_replace($forelse, preg_replace($search_pattern, $replacement, $forelse), $content);
		}

		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade empty statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_empty($content)
	{
		return str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade endforelse statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_endforelse($content)
	{
		return str_replace('@endforelse', '<?php endif; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade opening structures into PHP opening structures
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_opening_statements($content)
	{
		$pattern = '/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php $2$3: ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade else statement into PHP else statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_else($content)
	{
		$pattern = '/(\s*)@(else)(\s*)/';

		return preg_replace($pattern, '$1<?php $2: ?>$3', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade continue() statement into PHP continue statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_continueIf($content)
	{
		$pattern = '/(\s*)@(continue)(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php if $3: ?>$1<?php $2; ?>$1<?php endif; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade continue statement into PHP continue statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_continue($content)
	{
		$pattern = '/(\s*)@(continue)(\s*)/';

		return preg_replace($pattern, '$1<?php $2; ?>$3', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade break() statement into PHP break statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_breakIf($content)
	{
		$pattern = '/(\s*)@(break)(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php if $3: ?>$1<?php $2; ?>$1<?php endif; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade break statement into PHP break statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_break($content)
	{
		$pattern = '/(\s*)@(break)(\s*)/';

		return preg_replace($pattern, '$1<?php $2; ?>$3', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade closing structures into PHP closing structures
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_closing_statements($content)
	{
		$pattern = '/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/';

		return preg_replace($pattern, '$1<?php $2; ?>$3', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade unless statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_unless($content)
	{
		$pattern = '/(\s*)@unless(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php if ( ! ($2)): ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade endunless statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_endunless($content)
	{
		return str_replace('@endunless', '<?php endif; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade @includeIf statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_includeIf($content)
	{
		$pattern = "/(\s*)@includeIf\s*(\('(.*?)'.*\))/";

		return preg_replace($pattern, '$1<?php echo ($this->exists("$3", FALSE) === TRUE) ? $this->_include$2 : ""; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade @include statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_include($content)
	{
		$pattern = '/(\s*)@include(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php echo $this->_include$2; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade @extends statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_extends($content)
	{
		$pattern = '/(\s*)@extends(\s*\(.*\))/';

		// Find and if there is none, just return the content
		if ( ! preg_match_all($pattern, $content, $matches, PREG_SET_ORDER))
		{
			return $content;
		}

		$content = preg_replace($pattern, '', $content);

		// Layouts are included in the end of template
		foreach ($matches as $include)
		{
			$content .= "\n".$include[1].'<?php echo $this->_include'.$include[2]."; ?>";
		}

		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade @yield statement into Section statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_yield($content)
	{
		$pattern = '/(\s*)@yield(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php echo $this->_yield$2; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade Show statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_show($content)
	{
		return str_replace('@show', '<?php echo $this->_yield($this->_closing_section()); ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade @section statement into Section statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_opening_section($content)
	{
		$pattern = '/(\s*)@section(\s*\(.*\))/';

		return preg_replace($pattern, '$1<?php $this->_opening_section$2; ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade @endsection statement into Section statement
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_closing_section($content)
	{
		return str_replace('@endsection', '<?php $this->_closing_section(); ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade @php statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_php($content)
	{
		return str_replace('@php', '<?php', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Rewrites Blade @endphp statement into valid PHP
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	protected function _compile_endphp($content)
	{
		return str_replace('@endphp', '?>', $content);
	}

	// --------------------------------------------------------------------------
	// --------------------------------------------------------------------------

	/**
	 *  Stores the content of a section
	 *  It alse replaces the Blade @parent statement with the previous section
	 *
	 *  @param    string   $section
	 *  @param    string   $content
	 */
	private function _extend_section($section, $content)
	{
		if (isset($this->_sections[$section]))
		{
			$this->_sections[$section] = str_replace('@parent', $content, $this->_sections[$section]);
		}
		else
		{
			$this->_sections[$section] = $content;
		}
	}
}
