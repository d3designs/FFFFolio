/*
---

script: Scroller.js

description: Sets up the page width, horizontal mousewheel scrolling, and keyboard navigation.

license: MIT-style License

copyright: Copyright (c) 2010 [Jay Williams](http://myd3.com/).

This script was lovingly created by: D3 <www.myd3.com>
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
...
*/

var scroller = null;
var items = null;
var current = 0;
	
window.addEvent('domready', function() {
	
	items = $$('.item');
	
	// Set the page body width, depending on the number of contents
	document.body.setStyle('width', (items.length * 540 + (screen.width - 540)) );
	
	scroller = new Fx.Scroll(window, {
		link: 'cancel',
		offset: {
			'x': -230, // Compensate for the sidebar
			'y': 0
		}
	});
	
	window.addEvent('mousewheel', function(event){
		
		event.stop();
		
		var current = window.getScroll();
		var step = current.x + (event.wheel / 3) * -60;
		
		scroller.set(step, current.y);
	});

	document.addEvent('keydown', function(event){
	
		if(event.key == 'k' || event.key == 'left') {
			// Previous Item, Subtract 1 from Current
	
			current = current - 1;
			if (current < 0) current = 0;
	
			scroller.toElement(items[current])
	
			event.stop();
			return false;
	
		} else if (event.key == 'j' || event.key == 'right') {
			// Next Item, Add 1 to Current
	
			current = current + 1;
			if (current >= items.length) current = 0;
	
			scroller.toElement(items[current])
	
			event.stop();
			return false;
		} 
	
		return true;
	
	}); // end keydown
	
	
});
