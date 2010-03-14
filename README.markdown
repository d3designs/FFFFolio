# FFFFolio

Fantastic no-Frills Flickr Folio

## Requirements

* [Flickr API Key](http://www.flickr.com/services/apps/create/apply)
* PHP 5.2
	* cURL
	* JSON
	* SimpleXML

## Download

	git clone git@github.com:jaywilliams/FFFFolio.git
	cd FFFFolio

## Setup

Rename the config file, located in the app directory, from `config-sample.inc.php` to `config.inc.php` and add your Flickr API key/secret, as well as the User and Collection ID.

The `app/cache/` directory must be writable by the web server. A folder permission of 755 or 777 should be sufficient.

## Examples

* [Orphan Elliott](http://orphanelliott.com/)

## License & Copyright

Files located under the `images/` directory are copyright Mat Hudson and can not be reused.

### FFFFolio

URL: <http://github.com/jaywilliams/FFFFolio>  
Copyright (c) 2010, Jay Williams [Simplified BSD License](http://opensource.org/licenses/bsd-license.php)

### Flickr Class

URL: <http://github.com/skyzyx/flickr>  
Copyright (c) 2009, Ryan Parman [Simplified BSD License](http://opensource.org/licenses/bsd-license.php)

### FlickrCache Class

URL: <http://github.com/jaywilliams/flickr>  
Copyright (c) 2009, Jay Williams [Simplified BSD License](http://opensource.org/licenses/bsd-license.php)

### RequestCore Class

URL: <http://github.com/skyzyx/requestcore>  
Copyright (c) 2009, LifeNexus Digital, Inc., and contributors. [Simplified BSD License](http://opensource.org/licenses/bsd-license.php)
