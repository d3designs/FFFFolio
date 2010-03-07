<?php

error_reporting(E_ALL);

include 'app/requestcore.class.php';
include 'app/flickr.class.php';
include 'app/flickrcache.class.php';

$flickr = new FlickrCache();
// $flickr->cache_mode(true, 6400, './app/_cache/');

function get($collection_id=FLICKR_COLLECTION_ID)
{
	global $flickr;
	
	$response = $flickr->collections->get_tree(array(
		'collection_id' => $collection_id,
		'user_id' => FLICKR_USER_ID,
	));
	
	return $collection = $response->collections->collection[0];
}


$data = get();

$breadcrumb = array();

// var_dump($data->collection);
// echo "<pre>";


function process($class,$level=0,$parent=array())
{
	global $breadcrumb;
	// $breadcrumb = array();
	
	if (property_exists($class,'collection')) {
		
		$indent = '';
		
		for ($i=0; $i < $level; $i++) { 
			$indent .= "\t";
		}
		
		echo "$indent<li><a href=\"$class->id\">$class->title</a>\n$indent\t<ul>\n";
		$level++;
		
		$parent[] = $class->id;
		
		foreach ($class->collection as $collection) {
			process($collection,$level,$parent);
		}
		
		echo "$indent\t</ul>\n$indent</li>\n";
	
	}elseif (property_exists($class,'set')) {
		
		$indent = '';
		
		for ($i=0; $i < $level; $i++) { 
			$indent .= "\t";
		}
		
		echo "$indent<li><a href=\"$class->id\">$class->title</a>\n$indent\t<ul>\n";
		$parent[] = $class->id;
		// $indent .= "\t";
		
		foreach ($class->set as $set) {
			echo "$indent\t\t<li><a href=\"$set->id\">$set->title</a></li>\n";
			
			$breadcrumb[$set->id] = $parent;
		}
		
		echo "$indent\t</ul>\n$indent</li>\n";
	}
	
	
	// foreach ($data->collection as $collection) {
	// 	if (property_exists($collection,'collection')) {
	// 		echo "$collection->title\n";
	// 	}elseif (property_exists($collection,'set')) {
	// 		echo "\t$collection->title\n";
	// 	}
	// }
	
	// return $breadcrumb;
}

echo "<ul>\n";
process($data);
echo "</ul>\n";

print_r($breadcrumb);


// echo "</pre>";
?>