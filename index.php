<?php
/**
 * Include FFFFolio Application
 */

require_once 'app/config.inc.php';
require_once 'app/void.class.php';
require_once 'app/requestcore.class.php';
require_once 'app/flickr.class.php';
require_once 'app/flickrcache.class.php';
require_once 'app/ffffolio.class.php';

$folio = new FFFFolio;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title><?php if(isset($folio->set->title)) echo $folio->entities($folio->set->title) . ' — '; ?><?php echo $folio->entities($folio->collection->title); ?></title>
	
	<!-- <base href="/"/> -->
	
	<link rel="alternate" type="application/rss+xml" title="<?php echo $folio->entities($folio->set->title); ?>" href="http://api.flickr.com/services/feeds/photoset.gne?set=<?php echo $folio->set->id ?>&amp;nsid=<?php echo $folio->user_id; ?>&amp;lang=en-us&amp;format=rss_200"/>
	
	<link rel="stylesheet" href="css/reset.css" type="text/css" media="all"/>
	<link rel="stylesheet" href="css/text.css" type="text/css" media="all"/>
	<link rel="stylesheet" href="css/layout.css" type="text/css" media="all"/>
	
	<!--[if lte IE 6]>
	<link rel="stylesheet" href="css/ie.css" type="text/css" media="all"/>
	<script src="js/ie.js" type="text/javascript" charset="utf-8"></script>
	<![endif]-->
	
	<script src="js/mootools-1.2.4-core-yc.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/mootools-1.2.4.4-more-yc.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/scroller.js" type="text/javascript" charset="utf-8"></script>

	<!-- 
        This site was lovingly created by: D3 <www.myd3.com>
        Using only the finest ingredients available, allowing us
        to create a perfect, fast, and standards compliant site.
                             _ ____                      
                            | |___ \                     
         _ __ ___  _   _  __| | __) | ___ ___  _ __ ___  
        | '_ ` _ \| | | |/ _` ||__ < / __/ _ \| '_ ` _ \ 
        | | | | | | |_| | (_| |___) | (_| (_) | | | | | |
        |_| |_| |_|\__, |\__,_|____(_)___\___/|_| |_| |_|
                    __/ |                                
                   |___/        
	-->
</head>

<body>

<div id="sidebar">
	<!-- <div id="header"> -->
		<a href="./"><img src="images/logo.png" id="logo" width="165" height="98" alt="Orphan Elliott"/></a>
		<em id="tagline"><?php echo $folio->collection->description; ?></em>
	<!-- </div> -->
	
	<ul id="nav"> 
	<?php echo $folio->get_menu(); ?>
	</ul>
	
	<hr/>
	
	<ul>
		<li><a href="http://orphanelliott.tumblr.com/">Blog</a></li>
		<li><a href="http://www.flickr.com/photos/prismkiller/">Flickr</a></li>
		<li><a href="http://twitter.com/orphanelliott">Twitter</a></li>
	</ul>
	
	<div id="footer">
		Use <span title="or J/K if you prefer">arrow keys</span><br/> to navigate<br/><br/>
		
		<img src="images/oe.png" width="18" height="11" alt="OE"/><br/>
		© <?php echo date('Y'); ?> Mat Hudson
	</div>
	
</div> <!-- End Sidebar -->



<div id="content">
	
	<div id="project">
		<h2><a href="http://www.flickr.com/photos/<?php echo $folio->set->owner; ?>/sets/<?php echo $folio->set->id; ?>/"><?php echo $folio->entities($folio->set->title); ?></a></h2>
		<div class="description"><?php echo nl2br($folio->set->description); ?></div>
	</div>

	
	<div id="items">
		
		<?php foreach ($folio->photos as $photo): ?>
		<div class="item" id="item_<?php echo $photo->id; ?>">
			<a href="http://www.flickr.com/photos/<?php echo $photo->pathalias; ?>/<?php echo $photo->id; ?>/in/set-<?php echo $folio->set->id; ?>/">
				<img src="<?php echo $photo->url_m; ?>" width="<?php echo $photo->width_m; ?>" height="<?php echo $photo->height_m; ?>" alt="<?php echo $folio->entities($photo->title); ?>" title="<?php echo $folio->entities($photo->title); ?>"/>
			</a>
		</div>
		<?php endforeach ?>
	
	</div>

</div> <!-- End Content -->

</body>
</html>