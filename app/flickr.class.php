<?php
/**
 * File: api-flickr
 * 	Handle the Flickr API.
 *
 * Version:
 * 	2009.11.14
 *
 * Copyright:
 * 	2009 Ryan Parman
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 */


/*%******************************************************************************************%*/
// CORE DEPENDENCIES

// Include the config file
if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php'))
{
	include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php';
}


/*%******************************************************************************************%*/
// EXCEPTIONS

class Flickr_Exception extends Exception {}


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: FLICKR_NAME
 * 	Name of the software.
 */
define('FLICKR_NAME', 'api-flickr');

/**
 * Constant: FLICKR_VERSION
 * 	Version of the software.
 */
define('FLICKR_VERSION', '1.0');

/**
 * Constant: FLICKR_BUILD
 * 	Build ID of the software.
 */
define('FLICKR_BUILD', gmdate('YmdHis', strtotime(substr('$Date$', 7, 25)) ? strtotime(substr('$Date$', 7, 25)) : filemtime(__FILE__)));

/**
 * Constant: FLICKR_URL
 * 	URL to learn more about the software.
 */
define('FLICKR_URL', 'http://github.com/skyzyx/flickr/');

/**
 * Constant: FLICKR_USERAGENT
 * 	User agent string used to identify the software
 */
define('FLICKR_USERAGENT', FLICKR_NAME . '/' . FLICKR_VERSION . ' (Flickr Toolkit; ' . FLICKR_URL . ') Build/' . FLICKR_BUILD);


/*%******************************************************************************************%*/
// CLASS

/**
 * Class: Flickr
 */
class Flickr
{
	/**
	 * Property: key
	 * 	The Flickr API Key.
	 */
	var $key;

	/**
	 * Property: secret_key
	 * 	The Flickr API Secret Key.
	 */
	var $secret_key;

	/**
	 * Property: subclass
	 * 	The API subclass (e.g. album, artist, user) to point the request to.
	 */
	var $subclass;

	/**
	 * Property: test_mode
	 * 	Whether we're in test mode or not.
	 */
	var $test_mode;


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
		$this->key = null;
		$this->secret_key = null;
		$this->subclass = $subclass;

		// If both a key and secret key are passed in, use those.
		if ($key && $secret_key)
		{
			$this->key = $key;
			$this->secret_key = $secret_key;
			return true;
		}
		// If neither are passed in, look for the constants instead.
		else if (defined('FLICKR_KEY') && defined('FLICKR_SECRET_KEY'))
		{
			$this->key = FLICKR_KEY;
			$this->secret_key = FLICKR_SECRET_KEY;
			return true;
		}

		// Otherwise set the values to blank and return false.
		else
		{
			throw new Flickr_Exception('No valid credentials were used to authenticate with Flickr.');
		}
	}


	/*%******************************************************************************************%*/
	// SETTERS

	/**
	 * Method: test_mode()
	 * 	Enables test mode within the API. Enabling test mode will return the request URL instead of requesting it.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	enabled - _boolean_ (Optional) Whether test mode is enabled or not.
	 *
	 * Returns:
	 * 	void
	 */
	public function test_mode($enabled = true)
	{
		// Set default values
		$this->test_mode = $enabled;
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

		return $ref;
	}

	/**
	 * Handle requests to methods
	 */
	public function __call($name, $args)
	{
		// Change the names of the methods to match what the API expects
		$name = strtolower($name);
		$temp = explode('_', $name);
		$name = array(array_shift($temp));
		foreach ($temp as $n)
		{
			$name[] = ucfirst($n);
		}
		$name = implode('', $name);

		// Construct the rest of the query parameters with what was passed to the method
		$fields = http_build_query((count($args) > 0) ? $args[0] : array(), '', '&');

		// Put together the name of the API method to call
		$method = (isset($this->subclass)) ? sprintf('%s.%s', $this->subclass, $name) : $name;

		// Construct the URL to request
		$api_call = sprintf('http://api.flickr.com/services/rest/?method=flickr.%s&%s&api_key=' . $this->key, $method, $fields);

		// Return the value
		return $this->request($api_call);
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
		if (!$this->test_mode)
		{
			if (class_exists('RequestCore'))
			{
				$http = new RequestCore($url);
				$http->set_useragent(FLICKR_USERAGENT);
				$http->send_request();

				$response = new stdClass();
				$response->header = $http->get_response_header();
				$response->body = $this->parse_response($http->get_response_body());
				$response->status = $http->get_response_code();

				return $response;
			}

			throw new Exception('This class requires RequestCore. http://github.com/skyzyx/requestcore.');
		}

		return $url;
	}

	/**
	 * Method: parse_response()
	 * 	Default method for parsing the response data. You can extend the class and override this method for other response types.
	 *
	 * Parameters:
	 * 	data - _string_ (Required) The data to parse.
	 *
	 * Returns:
	 * 	mixed data
	 */
	public function parse_response($data)
	{
		return new SimpleXMLElement($data, LIBXML_NOCDATA);
	}
}
