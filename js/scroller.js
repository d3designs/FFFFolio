//    This script was lovingly created by: D3 <www.myd3.com>
//    Using only the finest ingredients available, allowing us
//    to create a perfect, fast, and standards compliant site.
//                         _ ____                      
//                        | |___ \                     
//     _ __ ___  _   _  __| | __) | ___ ___  _ __ ___  
//    | '_ ` _ \| | | |/ _` ||__ < / __/ _ \| '_ ` _ \ 
//    | | | | | | |_| | (_| |___) | (_| (_) | | | | | |
//    |_| |_| |_|\__, |\__,_|____(_)___\___/|_| |_| |_|
//                __/ |                                
//               |___/        


var scroller = null;
var items = null;
var current = 0;
	
window.addEvent('domready', function() {
	
	items = $$('.item');
	
	// Set the width of the items wrapper, depending on the number of contents
	
	$('items').setStyle('width', (items.length * 540 + (screen.width - 540)) );
	
	scroller = new Fx.Scroll(window, {
		link: 'cancel',
		offset: {
			'x': -230, // Compensate for the sidebar
			'y': 0
		}
	});
	
	// Possibly use the mootols element method ".isVisible()"?

	document.addEvent('keydown', function(event){
	
		if(event.key == 'j' || event.key == 'left' ||
			event.key == 'k' || event.key == 'right') {
				// Determine which image is being viewed, based off of the location on the screen.
	
				current = Math.ceil(window.getScroll().x / 539) - 1;
	
				if (current >= items.length || current < 1) current = 0;
		}
	
		if(event.key == 'j' || event.key == 'left') {
			// Previous Item, Subtract 1 from Current
	
			current = current - 1;
			if (current < 0) current = 0;
	
			scroller.toElement(items[current])
	
			event.stop();
			return false;
	
		} else if (event.key == 'k' || event.key == 'right') {
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
