<?php
/**
 * File: FFFFolio Configuration
 * 	Fantastic no-Frills Flickr Folio Configuration
 * 
 * Fill out this configuration file, and rename it to "config.inc.php".
 */

/**
 * Default Timezone & Error Reporting
 */
date_default_timezone_set('America/Los_Angeles');
error_reporting(0);
// error_reporting(E_ALL|E_STRICT); // Enable when developing


/**
 * Constant: FLICKR_CACHE_TIME
 * 	Length of time, in seconds, to cache Flickr API Calls (0 disables the cache)
 * 	3600 = 1 Hour
 */
define('FLICKR_CACHE_TIME', 3600);

/**
 * Constant: FLICKR_KEY
 * 	Flickr API Key. <http://www.flickr.com/services/api/keys/>
 */
define('FLICKR_KEY', '');

/**
 * Constant: FLICKR_SECRET_KEY
 * 	Flickr API Secret Key. <http://www.flickr.com/services/api/keys/>
 */
define('FLICKR_SECRET_KEY', '');


/**
 * Constant: FLICKR_USER_ID
 * 	Flickr User ID e.g. 1234567890@N00
 */
define('FLICKR_USER_ID', '');


/**
 * Constant: FLICKR_COLLECTION_ID
 * 	Flickr Collection ID, which contains the content to be displayed
 */
define('FLICKR_COLLECTION_ID', '');
