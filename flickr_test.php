<?php

// error_reporting(E_ALL);

include 'app/config.inc.php';
include 'app/requestcore.class.php';
include 'app/flickr.class.php';
include 'app/flickrcache.class.php';

/**
 * FFFFolio
 * Fantastic no-Frills Flickr Folio
 */
class FFFFolio
{
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
		
		$this->get_info();
		$this->get_photos();
		
	}

	public function get_info()
	{
		if (isset($this->info))
			return $this->info;
		
		$response = $this->api->photosets->get_info(array(
			'photoset_id' => $this->page,
		));
		
		if(!property_exists($response,'photoset'))
		{
			$this->info = null;
			return false;
		}
		
		$this->info = $response->photoset;
		
		foreach ($this->info as &$value)
		{
			if (is_object($value) && property_exists($value,'_content'))
				$value = $value->_content;
		}
		
		if (isset($response->_cached))
			$this->cached = (bool) $response->_cached;
		
		return $this->info;
	}

	public function get_photos()
	{
		if (isset($this->photos))
			return $this->photos;
		
		$response = $this->api->photosets->get_photos(array(
			'photoset_id' => $this->page,
			'extras'      => 'path_alias,url_m',
		));
		
		if(!property_exists($response->photoset,'photo'))
		{
			$this->photos = null;
			return false;
		}
		
		$this->photos = $response->photoset->photo;

		if (isset($response->_cached))
			$this->cached = (bool) $response->_cached;
		
		return $this->photos;
	}
	
	public function get_tree()
	{
		if (isset($this->tree))
			return $this->tree;
		
		$response = $this->api->collections->get_tree(array(
			'collection_id' => $this->collection_id,
			'user_id'       => $this->user_id,
		));
		
		$this->tree = $response->collections->collection[0];
		
		if(!property_exists($this->tree,'collection') && !property_exists($this->tree,'set'))
		{
			$this->tree = null;
			return false;
		}
		
		if (isset($response->_cached))
			$this->cached = (bool) $response->_cached;
		
		return $this->tree;
	}
	
	public function get_set_lookup($tree=false, $parent=array())
	{
		if ($this->cached) {
			// Try to load cache if tree is false, and parent is empty
		}
		
		if (!$tree) $tree = & $this->tree;
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
		
		if (!$tree) $tree = & $this->tree;
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
		if (!$tree) $tree = & $this->tree;
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

			$output .= "<a href=\"?page=$tree->id&/".$this->slugify($tree->title)."\">$tree->title</a>\n";
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
				
				$output .= "<a href=\"?page=$set->id&/".$this->slugify($set->title)."\">$set->title</a></li>\n";
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


$folio = new FFFFolio;
// var_dump($folio);

?>
<style type="text/css" media="screen">
	.active > a{
		background-color: red;
	}
</style>
	
<?php

echo "<h1>{$folio->tree->title}</h1>";
echo "<div>{$folio->tree->description}</div>";

echo "<ul id=\"nav\">\n";
echo $folio->get_menu();
echo "</ul>";

echo "<h2>{$folio->info->title}</h2>";
echo "<div>{$folio->info->description}</div>";

echo "<div id=\"content\">\n";
foreach ($folio->photos as $photo) {
	echo "<p>";
	echo "<img src=\"$photo->url_m\"/><br/>";
	// var_dump($photo);
	echo "$photo->title</p>";
}
echo "</div>";

?>