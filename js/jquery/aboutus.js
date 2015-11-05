
jQuery(document).ready(function(){
	// jQuery('#about_us_clients_scroll_content ul').animate({top: -5000},200000, 'linear');
	jQuery('#about_us_clients_scroll_content').animate({scrollTop: 0},5000, 'linear').animate({scrollTop: jQuery('#about_us_clients_scroll_content ul').height()},900000, 'linear');

	 jQuery(".staff-box p a").fancybox({
	'hideOnContentClick': false,
	'frameWidth': 630,
	'frameHeight': 480	
	 });


	
});


