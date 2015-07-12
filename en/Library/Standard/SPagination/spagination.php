<?php 
/**
 * Spagination class for any PHP5+ application
 * 
 * @author	Kemal Delalic <twitter.com/delalick>
 * @version	1.0.0
 */
class Spagination {

	/**
	 * @var 	array	Default config array
	 */
	protected static $_defaults;

	/**
	 * @var 	array	Translation array
	 */
	protected static $_i18n;
	
	/**
	 * Factory method for easier method chaining
	 * 
	 * @param	int		Initial count (optional)
	 * @param	int		Initial limit (optional)
	 * @return	Spagination
	 */
	public static function create($count = NULL, $limit = NULL)
	{
		$params = array();
		
		if (isset($count))
		{
			$params['count'] = $count;
		}
		
		if (isset($limit))
		{
			$params['limit'] = $limit;
		}
		
		return new Spagination($params);
	}
	
	/**
	 * Constructor accepts initial array of data to avoid later chaining methods
	 * Usage example:
	 * 	$pagination = new Spagination(array(
	 * 		'count' 	=> $count,
	 * 		'limit' 	=> 15,
	 * 	));
	 *
	 * @note	This is a factory class, you should the create() method
	 * @param	array	Initial parameters (optional)
	 * @return	void
	 */
	protected function __construct(array $params = NULL)
	{
		// Load the default configuration only once
		if ( ! isset(Spagination::$_defaults))
		{
			Spagination::$_defaults = include dirname(__FILE__).'/config.php';	
		}
		
		// Load configuration
		$this->_data = Spagination::$_defaults;
		
		// Load the translations array only once
		if ( ! isset(Spagination::$_i18n))
		{
			Spagination::$_i18n = include dirname(__FILE__).'/i18n.php';
		}
		
		if (NULL !== $params)
		{
			$this->setup($params);
		}
	}
	
	/**
	 * Getter for internal data
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->_data))
			return $this->_data[$key];
			
		throw new Exception('Undefined key requested: '.$key);
	}
	
	public function __isset($key)
	{
		return isset($this->_data[$key]);
	}
	
	/**
	 * Setter for internal data
	 */
	public function __set($key, $value)
	{
		if (array_key_exists($key, $this->_data))
		{
			$this->_data[$key] = $value;
			
			return;
		}
		
		throw new Exception('Undefined key set: '.$key);
	}
	
	public function __unset($key)
	{
		$this->setup(array($key => NULL));
	}
	
	/**
	 * This method is called whenever the object is used as string
	 * Usage example:
	 * 	$pagination = new Spagination(initial data...);
	 * 	echo $pagination;
	 *	$as_string = (string) $pagination;
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			return 'Error rendering pagination: '.$e->getMessage();
		}
	}
	
	/**
	 * Getter / setter for pagination colour
	 *
	 * @chainable
	 * @param	string	$colour to use
	 * @param	string	$colour (without the param)
	 * @return	object	$this (chainable) as setter
	 */
	public function color($colour = NULL)
	{
		if (NULL === $colour)
			return $this->color;
			
		$this->setup(array('color' => $colour));
		
		return $this;
	}
	
	/**
	 * Getter / setter for total count
	 *
	 * @chainable
	 * @param	int		$total (optional, use method as setter)
	 * @return	int		$total (if used without passing the param)
	 * @return	object	$this (chainable) as setter
	 */
	public function count($total = NULL)
	{
		if (NULL === $total)
			return $this->count;
			
		$this->setup(array('count' => $total));
		
		return $this;
	}
	
	/**
	 * Sets the current page
	 * 
	 * @chainable
	 * @param	int		$value (setter)
	 * @return	int		$current_page (getter)
	 * @return	object	$this (chainable) as setter
	 */
	public function current_page($value = NULL)
	{
		if (NULL === $value)
			return $this->current_page;
		
		$this->setup(array('current_page' => $value));
		
		return $this;
	}
	
	/**
	 * Escapes a string using current object's charset
	 * 
	 * @param	string	To escape
	 * @return	string	Escaped HTML with current charset
	 */
	public function escape($string)
	{
		return htmlspecialchars($string, ENT_QUOTES, $this->charset);
	}
	
	/**
	 * Sets the pagination language
	 * 
	 * @chainable
	 * @param	string	$lang code on set
	 * @return	string	$lang as getter
	 * @return	object 	$this (chainable) as setter
	 * @throws	Exception	In case nonexisting language is requested
	 */
	public function lang($lang = NULL)
	{
		if (NULL === $lang)
			return $this->lang;

		if (isset(Spagination::$_i18n[$lang]))
		{
			$this->lang = $lang;
			
			return $this;
		}
		elseif ($lang === FALSE)
		{
			$this->lang = 'en';
			
			return $this;
		}
		
		throw new Exception('Undefined language set: '.$lang.'. You must first define the language translation in i18n.php');
	}
	 
	/**
	 * Limit setter / getter
	 * 
	 * @chainable
	 * @param	int		$items
	 * @return	int		$items as getter
	 * @return	object	$this (chainable) as setter
	 */
	public function limit($items = NULL)
	{
		if (NULL === $items)
			return $this->limit;
			
		$this->setup(array('limit' => $items));
		
		return $this;
	}
	
	/**
	 * Pagination offset getter
	 * 
	 * @chainable
	 * @return	int		Offset to use in the query
	 */
	public function offset()
	{
		return $this->offset;
	}
	
	/**
	 * Renders the pagination using the current template file
	 * 
	 * @param	bool	$return whether to return or echo
	 * @return	string	Pagination HTML
	 * @return	void	if $return is set to FALSE, template will be echoed
	 */
	public function render($return = TRUE)
	{
		// Start new output buffer
		ob_start();
		
		try
		{
			// Extract all data variables into current scope
			extract($this->_data);
			
			$pagi = $this;
			
			include dirname(__FILE__).'/templates/'.$this->template.'.php';
		}
		catch (Exception $e)
		{
			ob_end_clean();
			
			throw $e;
		}
		
		if ($return === TRUE)
		{
			return ob_get_clean();
		}
		
		echo ob_get_clean();
	}
	
	/**
	 * Sets up pagination parameters
	 *
	 * @param	array	Additional config params
	 * @return	object	$this
	 */
	public function setup(array $params)
	{
		$this->_data = array_merge($this->_data, $params);
		
		if (isset($params['current_page']) OR isset($params['count']) OR isset($params['limit']) OR ($this->current_page === NULL))
		{
			if ( ! empty($params['current_page']))
			{
				$this->current_page = (int) $params['current_page'];
			}
			elseif (empty($this->current_page))
			{
				$this->current_page = (int) isset($_GET[$this->query_key]) ? $_GET[$this->query_key] : 1;
			}
			
			$this->_detect_settings();
		}
		
		return $this;
	}
	
	/**
	 * Getter / setter for pagination skin
	 *
	 * @chainable
	 * @param	string	name of the skin to use
	 * @param	string	skin name (without the param)
	 * @return	object	$this (chainable) as setter
	 */
	public function skin($name = NULL)
	{
		if (NULL === $name)
			return $this->skin;
			
		$this->setup(array('skin' => $name));
		
		return $this;
	}
	
	/**
	 * Translation method, provides easy i18n
	 * 
	 * @param 	string 	$string to translate
	 * @return	string	Translated string or the passed one if no translation
	 */
	public function t($string)
	{
		if ($this->lang !== 'en')
		{
			if (isset(Spagination::$_i18n[$this->lang][$string]))
			{
				return Spagination::$_i18n[$this->lang][$string];
			}
		}
		
		return $string;
	}
	
	/**
	 * Getter / setter for template file name
	 *
	 * @chainable
	 * @param	string	$name Template file name without .php extension
	 * @return	string	$name If param not passed, template name is returned
	 * @return	object	$this as setter
	 */
	public function template($name = NULL)
	{
		if (NULL === $name)
			return $this->template;
			
		$this->setup(array('template' => $name));
		
		return $this;
	}
	
	/**
	 * Get URL
	 * The default URL handler can be overriden using Spagination::url_handler()
	 *
	 * @param	array	params
	 * @return	string	URL
	 */
	public function url($page, $html = TRUE)
	{
		$page = max(1, (int) $page);
		
		if ( ! $this->show_first_page AND $page === 1)
		{
			$page = NULL;
		}
		
		if (isset($this->_url_handler) AND is_callable($this->_url_handler))
		{
			$url = call_user_func($this->_url_handler, $page);
		}
		else
		{
			$url = $this->_url_query($page);
		}
		
		return $html ? $this->escape($url) : $url;
	}
	
	/**
	 * Sets the URL handler
	 * Can be any type of callback:
	 * 
	 * 	array($object, 'method')
	 * 	'Static::method'
	 * 	'normal_function'
	 *	function($page){ // lambda if you're on PHP 5.3
	 * 
	 * as long as it accepts the page param and returns a valid URL, handling the
	 * pagination in it.
	 * 
	 * @chainable
	 * @param	mixed	$callback function
	 * @return	mixed	$callback function on get
	 * @return	object	$this (chainable) on set
	 */
	public function url_handler($callback = NULL)
	{
		if (NULL === $callback)
			return $this->_url_handler;
		
		$this->_url_handler = $callback;
		
		return $this;
	}
	 
	/**
	 * Query key setter / getter
	 * 
	 * @chainable
	 * @param	string	
	 * @return	string	$query_key as getter
	 * @return	object	$this (chainable) as setter
	 */
	public function query_key($name = NULL)
	{
		if (NULL === $name)
			return $this->query_key;
			
		$this->query_key = $name;
		
		$page = isset($_GET[$name]) ? $_GET[$name] : 1;
		
		$this->current_page($page);
		
		return $this;
	}
	
	/**
	 * @var	array	Pagination logic data
	 */
	protected $_data;
	
	/**
	 * @var	mixed	Callback for handling URLs
	 */
	protected $_url_handler;
	
	/**
	 * Detects the rest of pagination settings based on current setup
	 */
	protected function _detect_settings()
	{
		$this->limit 				= (int) max(1, $this->limit);
		
		$this->count 				= (int) max(0, $this->count);
		
		$this->current_total_pages 	= (int) ceil($this->count / $this->limit);
		
		$this->current_page 		= (int) min(max(1, $this->current_page), max(1, $this->current_total_pages));
		
		$this->offset 				= (int) (($this->current_page - 1) * $this->limit);
		
		$this->current_first_item 	= (int) min((($this->current_page - 1) * $this->limit) + 1, $this->count);
		
		$this->current_last_item 	= (int) min($this->current_first_item + $this->limit - 1, $this->count);
		
		$this->current_first_page 	= ($this->current_page === 1) ? FALSE : 1;
		
		$this->current_prev_page 	= ($this->current_page > 1) ? $this->current_page - 1 : FALSE;
		
		$this->current_next_page 	= ($this->current_page < $this->current_total_pages) ? $this->current_page + 1 : FALSE;
		
		$this->current_last_page 	= ($this->current_page >= $this->current_total_pages) ? FALSE : $this->current_total_pages;
		
		return $this;
	}
	
	/**
	 * Default URL handler, relies on GET query params
	 * @param unknown_type $page
	 */
	protected function _url_query($page)
	{
		$current_uri = $_SERVER['REQUEST_URI'];
		
		// Parse the current URL
		$uri = parse_url($current_uri);
		
		if (isset($uri['query']))
		{
			// Parse the current query
			parse_str($uri['query'], $query);
		}
		else
		{
			// Or create a blank one
			$query = array();
		}
		
		if ($page === NULL)
		{
			$query[$this->query_key] = 1;
		}
		else
		{
			$query[$this->query_key] = $page;
		}
		
		$url = $uri['path'];
		
		if (count($query) > 0)
		{
			$url .= '?'.http_build_query($query, '', '&');
		}
		
		return $url;
	}
	
	/**
	 * Custom URL handler for path based paging
	 * 
	 * @note	You should override this and set it as url_handler() to work
	 * @param 	int		$page
	 * @param 	int		$param_position
	 */
	protected function _url_path($page, $param_position)
	{
		// do the magic and return relative URL with page
	}
	
}
