<?php
/**
 * File: FFFFolio Class
 * 	Fantastic no-Frills Flickr Folio PHP5 Class
 *
 * Version:
 * 	2010.03.14
 *
 * Copyright:
 * 	2010 Jay Williams
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 */

class FFFFolio
{
	var $path;
	var $api;
	var $set_lookup;
	var $collection_lookup;
	var $page;
	var $cached;
	
	// var $key = FLICKR_KEY;
	// var $secret_key = FLICKR_SECRET_KEY;
	var $user_id = FLICKR_USER_ID;
	var $collection_id = FLICKR_COLLECTION_ID;
	
	
	function __construct()
	{
		$this->path = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
		
		$this->api = new FlickrCache();
		$this->api->cache_mode(true, 6400, './app/cache/');
		
		$this->get_tree();
		$this->get_set_lookup();
		
		if (isset($_GET['page']))
			$this->page = preg_replace('/[^0-9\-]/', '', $_GET['page']);
		
		// If the page contains a dash, it is a collection,
		// so we can grab the first set under that collection.
		if (strpos($this->page, '-'))
		{
			$this->get_collection_lookup();
			if (isset($this->collection_lookup[$this->page]))
				$this->page = key($this->collection_lookup[$this->page]);
		}
		
		$this->get_set_info();
		
		// Only load sets that are owned by the user
		if (!empty($this->page) && $this->set->owner != $this->user_id) {
			$this->page   = null;
			$this->set    = new Void;
			$this->photos = array();
			
		}
		
		$this->check_url();
		$this->get_photos();
	}
	
	public function check_url()
	{
		// If we are on an invalid page, output a 404 error, and go to the home page.
		if (empty($this->page) && !empty($_GET['page'])) {
			header($_SERVER["SERVER_PROTOCOL"].' 404 Not Found');
			header("Location: $this->path");
			die();
		}
		
		// Verify that the post slug exists, and is set correctly.
		if (!empty($this->page) && (empty($_GET['slug']) || $_GET['slug'] != $this->slugify($this->set->title)) ) {
			
			$url =  $this->path . $this->set->id . '/' . $this->slugify($this->set->title);
			
			header($_SERVER["SERVER_PROTOCOL"].' 301 Moved Permanently');
			header("Location: $url");
			die();
		}
	}

	public function get_set_info()
	{
		if (isset($this->set))
			return $this->set;
		
		if (empty($this->page)) {
			$this->set = new Void;;
			return false;
		}
		
		$response = $this->api->photosets->get_info(array(
			'photoset_id' => $this->page,
		));
		
		if(!property_exists($response,'photoset'))
		{
			$this->set = new Void;
			return false;
		}
		
		$this->set = (object) $response->photoset;
		
		foreach ($this->set as &$value)
		{
			if (is_object($value) && property_exists($value,'_content'))
				$value = $value->_content;
		}
		
		if (isset($response->_cached))
			$this->cached = (bool) $response->_cached;
		
		return $this->set;
	}

	public function get_photos()
	{
		if (isset($this->photos))
			return $this->photos;
		
		if (empty($this->page)) {
			$this->photos = array();
			return false;
		}
		
		$response = $this->api->photosets->get_photos(array(
			'photoset_id' => $this->page,
			'extras'      => 'path_alias,url_m',
		));
		
		if(!isset($response->photoset) || !property_exists($response->photoset,'photo'))
		{
			$this->photos = array();
			return false;
		}
		
		$this->photos = (array) $response->photoset->photo;

		if (isset($response->_cached))
			$this->cached = (bool) $response->_cached;
		
		return $this->photos;
	}
	
	public function get_tree()
	{
		if (isset($this->collection))
			return $this->collection;
		
		$response = $this->api->collections->get_tree(array(
			'collection_id' => $this->collection_id,
			'user_id'       => $this->user_id,
		));
		
		$this->collection = $response->collections->collection[0];
		
		if(!property_exists($this->collection,'collection') && !property_exists($this->collection,'set'))
		{
			$this->collection = new Void;
			return false;
		}
		
		if (isset($response->_cached))
			$this->cached = (bool) $response->_cached;
		
		return $this->collection;
	}
	
	public function get_set_lookup($tree=false, $parent=array())
	{
		if ($this->cached) {
			// Try to load cache if tree is false, and parent is empty
		}
		
		if (!$tree) $tree = & $this->collection;
		$function = __FUNCTION__;
		
		if(!is_object($tree) || (!property_exists($tree,'collection') && !property_exists($tree,'set')))
			return false;
		
		$parent[$tree->id] = true;
		
		if (property_exists($tree,'collection'))
		{
			foreach ($tree->collection as $collection)
				$this->$function($collection, $parent);
		}
		elseif (property_exists($tree,'set'))
		{
			foreach ($tree->set as $set)
				$this->set_lookup[$set->id] = $parent;
		}
		
		// Save Cache if tree is false parent is empty
		
		return true;
	}

	public function get_collection_lookup($tree=false)
	{
		if ($this->cached) {
			// Try to load cache if tree is false
		}
		
		if (!$tree) $tree = & $this->collection;
		$function = __FUNCTION__;
		
		if(!is_object($tree) || (!property_exists($tree,'collection') && !property_exists($tree,'set')))
			return false;

		if (property_exists($tree,'collection'))
		{
			foreach ($tree->collection as $collection)
				$this->$function($collection);
		}
		elseif (property_exists($tree,'set'))
		{
			foreach ($tree->set as $set)
				$this->collection_lookup[$tree->id][$set->id] = true;
		}
		
		// Save Cache if tree is false
		
		return true;
	}	

	public function get_menu($tree=false, $level=0)
	{
		if (!$tree) $tree = & $this->collection;
		$function = __FUNCTION__;
		$output   = '';
		$tab      = '';
		$level++;
		
		if(!is_object($tree) || (!property_exists($tree,'collection') && !property_exists($tree,'set')))
			return false;
		
		// Add a tab for every level we go down
		for ($i=1; $i < $level; $i++)
			$tab .= "\t";
		
		// Skip the parent collection
		if ($level > 1 || property_exists($tree,'set'))
		{
			if (!empty($this->page) && isset($this->set_lookup[$this->page][$tree->id]) || $this->page == $tree->id)
				$output .= "$tab<li class=\"collection active\">";
			else
				$output .= "$tab<li class=\"collection\">";

			$output .= "<a href=\"$tree->id/".$this->slugify($tree->title)."\">".$this->entities($tree->title)."</a>\n";
			$output .= "$tab\t<ul>\n";
		}

		if (property_exists($tree,'collection'))
		{
			foreach ($tree->collection as $collection)
				 $output .= $this->$function($collection, $level);
		}
		elseif (property_exists($tree,'set'))
		{
			foreach ($tree->set as $set)
			{
				if (!empty($this->page) && isset($this->set_lookup[$this->page][$set->id]) || $this->page == $set->id)
					$output .= "$tab\t\t<li class=\"set active\">";
				else
					$output .= "$tab\t\t<li class=\"set\">";
				
				$output .= "<a href=\"$set->id/".$this->slugify($set->title)."\">".$this->entities($set->title)."</a></li>\n";
			}
		}
		
		// Skip the parent collection
		if ($level > 1 || property_exists($tree,'set'))
		{
			$output .= "$tab\t</ul>\n";
			$output .= "$tab</li>\n";
		}
		
		return $output;
	}
	
	public function entities($value='')
	{
		return htmlentities($value,ENT_QUOTES,'UTF-8');
	}
	
	public function slugify($value='')
	{
		$find = $replace = array();
		
		$find[] = '/[\/\-\_& ]+/';
		$replace[] = '-';
		
		$find[] = '/[^0-9a-z\-\_]/';
		$replace[] = '';
		
		$find[] = '/\-+/';
		$replace[] = '-';
		
		return preg_replace($find, $replace, strtolower($value));
	}
	
	
}
