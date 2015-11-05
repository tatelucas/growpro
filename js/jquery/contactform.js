jQuery(document).ready(function(){
	label2value();
	jQuery('.contact-form').submit(function () {
	        return false;
	})
	
	jQuery(".button").click(
		function(){
				jQuery(this).attr("src", "media/static/ajax-loader.gif");
				jQuery.ajax({
				  type: "POST",
				  url: "/s/data/contact-form-processor/",
				  data: jQuery(".contact-form").serialize(),
				  success: function(msg){
				    jQuery("#promo-form-wrapper").html(msg);
				  }
				});
		}
	)
})

this.label2value = function(){	

	var inactive = "inactive";
	var active = "active";
	var focused = "focused";
	
	jQuery("label").each(function(){		
		if (jQuery(this).attr("for")){
		obj = document.getElementById(jQuery(this).attr("for"));
		if((jQuery(obj).attr("type") == "text")){			
			jQuery(obj).addClass(inactive);			
			var text = jQuery(this).text();
			jQuery(this).css("display","none");			
			jQuery(obj).val(text);
			jQuery(obj).focus(function(){	
				jQuery(this).addClass(focused);
				jQuery(this).removeClass(inactive);
				jQuery(this).removeClass(active);								  
				if(jQuery(this).val() == text) jQuery(this).val("");
			});	
			jQuery(obj).blur(function(){	
				jQuery(this).removeClass(focused);													 
					if(jQuery(this).val() == "") {
						jQuery(this).val(text);
						jQuery(this).addClass(inactive);
					} else {
						jQuery(this).addClass(active);		
					};				
			});				
		};	
	};
	});		
};