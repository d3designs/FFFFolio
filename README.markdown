# FFFFolio

Fantastic no-Frills Flickr Folio

A simple and elegant way to host your own Flickr powered portfolio, designed and developed by [Jay Williams](http://myd3.com/).
FFFolio can help you setup an online portfolio that is minimalist, easy to update, and entirely powered by Flickr Collections & Sets.

Initial installation does require some knowledge of HTML, PHP, and Flickr. After installation, everything is updated via Flickr & and the Flickr Organizer.

## Requirements

* [Flickr API Key](http://www.flickr.com/services/apps/create/apply)
* PHP 5.2
	* cURL
	* JSON
	* SimpleXML

## Download

You can download the source code in a [ZIP Archive](http://github.com/jaywilliams/FFFFolio/zipball/master), or clone the project using Git:

	git clone git@github.com:jaywilliams/FFFFolio.git
	cd FFFFolio

## Setup

Rename the config file, located in the app directory, from `config-sample.inc.php` to `config.inc.php` and add your Flickr API key/secret, as well as the User and Collection ID.

The `app/cache/` directory must be writable by the web server. A folder permission of 755 or 777 should be sufficient.

By default, all Flickr API calls are cached for 6 hours, this can be changed in the FFFFolio Class if necessary.

## Examples

* [Orphan Elliott](http://orphanelliott.com/)
	* Powered by this [Flickr Collection](http://www.flickr.com/photos/prismkiller/collections/72157623399341629/) 

## License & Copyright

Files located under the `images/` directory are copyright Mat Hudson and can not be reused.

### FFFFolio

URL: <http://github.com/jaywilliams/FFFFolio>  
Copyright (c) 2010, Jay Williams [MIT-style License](http://www.opensource.org/licenses/mit-license.php)

### MooTools

URL: <http://mootools.net/>  
Copyright (c) 2006-2008 Valerio Proietti [MIT-style License](http://www.opensource.org/licenses/mit-license.php)

### Flickr Class

URL: <http://github.com/skyzyx/flickr>  
Copyright (c) 2009, Ryan Parman [Simplified BSD License](http://opensource.org/licenses/bsd-license.php)

### FlickrCache Class

URL: <http://github.com/jaywilliams/flickr>  
Copyright (c) 2009, Jay Williams [Simplified BSD License](http://opensource.org/licenses/bsd-license.php)

### RequestCore Class

URL: <http://github.com/skyzyx/requestcore>  
Copyright (c) 2009, LifeNexus Digital, Inc., and contributors. [Simplified BSD License](http://opensource.org/licenses/bsd-license.php)
