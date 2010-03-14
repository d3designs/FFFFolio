<?php
/**
 * File: api-flickrCache
 * 	Handle the JSON formatted Flickr API, and Cache the results to files.
 *
 * Version:
 * 	2009.12.13
 *
 * Copyright:
 * 	2009 Jay Williams - http://myd3.com/
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 */

class FlickrCache extends Flickr
{
	
	/**
	 * Property: cache_mode
	 * 	Whether caching is enabled on the request or not
	 */
	var $cache_mode;
	
	/**
	 * Property: cache_ttl
	 * 	Length of time, in seconds, the cache will be considered valid
	 */
	var $cache_ttl;
	
	/**
	 * Property: cache_path
	 * 	Directory to store the cache files
	 */
	var $cache_path;

	/**
	 * Property: header_mode
	 * 	Whether header response will be include in the output
	 */
	var $header_mode;


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Method: __construct()
	 * 	The constructor.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	key - _string_ (Optional) Your Flickr API Key. If blank, it will look for the <FLICKR_KEY> constant.
	 * 	secret_key - _string_ (Optional) Your Flickr API Secret Key. If blank, it will look for the <FLICKR_SECRET_KEY> constant.
	 * 	subclass - _string_ (Optional) Don't use this. This is an internal parameter.
	 *
	 * Returns:
	 * 	boolean FALSE if no valid values are set, otherwise true.
	 */
	public function __construct($key = null, $secret_key = null, $subclass = null)
	{
		
		// Set default values
		$this->cache_mode  = false;
		$this->cache_ttl   = 3600;
		$this->cache_path  = './cache/';
		$this->header_mode = false;
		
		return parent::__construct($key, $secret_key, $subclass);
	}


	/*%******************************************************************************************%*/
	// SETTERS
	
	/**
	 * Method: cache_mode()
	 * 	Enables request file caching.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	enabled - _boolean_ (Optional) Whether cache is enabled or not.
	 * 	ttl - _integer_ (Optional) Length of time, in seconds, the cache will be considered valid
	 * 	path - _string_ (Optional) Directory to store the cache files
	 *
	 * Returns:
	 * 	void
	 */
	public function cache_mode($enabled = true, $ttl = null, $path = null)
	{
		
		// Set default values
		$this->cache_mode = $enabled;
		
		if ($ttl != null)
			$this->cache_ttl = $ttl;
		
		if ($path != null)
			$this->cache_path = $path;
		
		// Run cache directory checks
		if ($enabled && (!is_dir($this->cache_path) || !is_writable($this->cache_path)))
			throw new Flickr_Exception('Cache directory doesn\'t exist or isn\'t writeable');
	}

	/**
	 * Method: header_mode()
	 * 	Enables header mode within the API. Enabling header mode will include the request header in addition to the body.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	enabled - _boolean_ (Optional) Whether header mode is enabled or not.
	 *
	 * Returns:
	 * 	void
	 */
	public function header_mode($enabled = true)
	{
		// Set default values
		$this->header_mode = $enabled;
	}

	/*%******************************************************************************************%*/
	// MAGIC METHODS
	
	/**
	 * Handle requests to properties
	 */
	public function __get($var)
	{
		// Determine the name of this class
		$class_name = get_class($this);

		// Re-instantiate this class, passing in the subclass value
		$ref = new $class_name($this->key, $this->secret_key, strtolower($var));
		$ref->test_mode($this->test_mode); // Make sure this gets passed through.
		$ref->cache_mode($this->cache_mode, $this->cache_ttl, $this->cache_path); // Make sure this gets passed through.
		$ref->header_mode($this->header_mode); // Make sure this gets passed through.
		
		return $ref;
	}

	/**
	 * Handle requests to methods
	 */
	public function __call($name, $args)
	{
		// Include default arguments
		$default_args = array('format' => 'json', 'nojsoncallback' => 1);
		
		$args[0] = array_merge($default_args, (array)$args[0]);
		
		return parent::__call($name, $args);
	}


	/*%******************************************************************************************%*/
	// REQUEST/RESPONSE

	/**
	 * Method: request()
	 * 	Requests the data, parses it, and returns it. Requires RequestCore and SimpleXML.
	 *
	 * Parameters:
	 * 	url - _string_ (Required) The web service URL to request.
	 *
	 * Returns:
	 * 	ResponseCore object
	 */
	public function request($url)
	{
		if ($this->test_mode)
			return $url;
		
		// Generate cache filename
		$cache = $this->cache_path . get_class() . '_' . md5($url) . '.cache';
		
		// If cache exists, and is still valid, load it
		if($this->cache_mode && file_exists($cache) && (time() - filemtime($cache)) < $this->cache_ttl)
		{
			$response = (object) json_decode(file_get_contents($cache));
			$response->_cached = true; // Add notice that this is a cached file
			
			return $response;
		}
		
		if (!class_exists('RequestCore'))
			throw new Exception('This class requires RequestCore. http://requestcore.googlecode.com');
		
		$http = new RequestCore($url);
		$http->set_useragent(FLICKR_USERAGENT);
		$http->send_request();
		
		$response = (object) $this->parse_response($http->get_response_body());
		
		if ($this->header_mode)
			$response->_header = $http->get_response_header();
		
		// Cache only successfuly requests
		if ($this->cache_mode && $response->stat == 'ok')
		{
			file_put_contents($cache . '_tmp', json_encode($response));
			rename($cache . '_tmp', $cache);
		}
		
		return $response;
	}

	/**
	 * Method: parse_response()
	 * 	Method for parsing the JSON response data.
	 *
	 * Parameters:
	 * 	data - _string_ (Required) The data to parse.
	 *
	 * Returns:
	 * 	mixed data
	 */
	public function parse_response($data)
	{
		return json_decode($data);
	}
}
