<?php

// error_reporting(E_ALL);

include 'app/config.inc.php';
include 'app/requestcore.class.php';
include 'app/flickr.class.php';
include 'app/flickrcache.class.php';
include 'app/ffffolio.class.php';

$folio = new FFFFolio;
// var_dump($folio);

?>
<style type="text/css" media="screen">
	.active > a{
		background-color: red;
	}
</style>
	
<?php

echo "<h1>{$folio->collection->title}</h1>";
echo "<div>{$folio->collection->description}</div>";

echo "<ul id=\"nav\">\n";
echo $folio->get_menu();
echo "</ul>";

echo "<h2>{$folio->set->title}</h2>";
echo "<div>{$folio->set->description}</div>";

var_dump($folio->photos);

echo "<div id=\"content\">\n";
foreach ($folio->photos as $photo) {
	echo "<p>";
	echo "<img src=\"$photo->url_m\"/><br/>";
	// var_dump($photo);
	echo "$photo->title</p>";
}
echo "</div>";

?>