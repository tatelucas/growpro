jQuery(document).ready(function(){
	jQuery("#product-tabs").tabs();
    if (jQuery('table.price-table').length < 1 || jQuery('table.price-table').get(0) == document) {jQuery('.qty-box').css("display", "block"); }

	if (window.priceChart !== undefined) {
		var product = new pageSelection(cartItems, priceChart, packCount);

		jQuery("table.price-table tr td").click( function() {
				clickedOptName = jQuery("table.price-table thead th").eq(jQuery(this).parent().find('> *').index(this)).text();
				clickedOpt = jQuery(this).attr('class').split(' ')[0];
				clickedQty = (jQuery(this).siblings().filter(":first").text()) / packCount;
				clickedEl = jQuery(this);
				product.choose(clickedEl);
		});
	
		jQuery("table.price-table tr td:not(:first)").hover( 
				function() {
					hoverOptName = jQuery("table.price-table thead th").eq(jQuery(this).parent().find('> *').index(this)).text();
					hoverOpt = jQuery(this).attr('class').split(' ')[0];
					hoverQty = (jQuery(this).siblings().filter(":first").text()) / packCount;
					product.preview(hoverQty, hoverOpt, hoverOptName);
					jQuery(this).parent().addClass('hover');  
					jQuery(this).addClass('hover');  	
				},
			 	function() {  
			   		jQuery(this).removeClass('hover');  
					jQuery(this).parent().removeClass('hover');  
				});
		}
	// jQuery('#cat-discount-widget').hide();
		jQuery('#add-to-holder').hide();
		jQuery(' .product-bonus-bucks-cd #add-to-holder').show();
		jQuery(' .product-bulletin-articles-cd #add-to-holder').show();

	
	
	if (cartItems == 0) {
		jQuery('#cat-discount-widget').hide();
	}
			
});

function pageSelection (cartItems, priceChart, packCount) {
	// DATA MEMBERS
	//static product data
	var cartItems = cartItems;
	var priceChart = priceChart;
	var packCount = packCount;
	
	//selection
	var selectQty = 1; 				//the quantity selected
	var selectOpt = 'test'; 		//the option selected
	var displayQty = selectQty * packCount;
	
	//price
	var unitPrice; 					//the price per unit, based on total quantity in selection and cart
	var displayPrice; 				// The difference between the price of item in cart and cart + selection

	//DOM ELEMENTS
	//inputs
	var domPriceTable 		= jQuery("table.price-table");	//the tiered pricing table
	if ( jQuery( "dd#type" ).length ) { //added the option to select by dynamic type -dwl - 3-7-2016
		var domOptInput   		= jQuery("dd#type select");		//the dropdown for type selection
	} else {
		var domOptInput   		= jQuery("dd#imprint select");		//the dropdown for imprint selection
	}
	var domQty		  		= jQuery("#qty");	
	
	
	//selection Display (common)
	var domSelectStatus 	= jQuery("#selection-status");
	var domSelect			= domOptInput ;
	var domPreview			= jQuery('#disount-msg');


	function calculate(qty, opt){
			CselectOpt 		= opt;
			// if (cartItems[0][0] == 'price'){Cselectopt = 'non-imprint'};
			CselectQty 		= qty;
			CdisplayQty		= CselectQty * packCount;
			CcartQty		= cartItems[CselectOpt];
				if (CcartQty !== parseFloat(CcartQty)) {CcartQty	= 0;};	
			CtotalQty		= CselectQty + CcartQty;
			//CunitPrice 		= priceChart[CselectOpt][CtotalQty];
				// If the totalQuantity is not in the pricechart, find the tier the totalQuantity belongs to
				if (priceChart[CselectOpt][CtotalQty] !== undefined){	
									CunitPrice = priceChart[CselectOpt][CtotalQty];
									} else {
										CnextQty = CtotalQty - 1;
									 	while (priceChart[CselectOpt][CnextQty] == NaN || jQuery.type(priceChart[CselectOpt][CnextQty]) === "undefined") {
											CnextQty = CnextQty - 1;
										}
										CunitPrice = priceChart[CselectOpt][CnextQty];
									}
				
			CtotalPrice		= CunitPrice * CtotalQty;  	

				// protect against absolutely huge orders in the cart not being able to see correct pricing
				if (priceChart[CselectOpt][CcartQty] !== undefined){	
									CcartPrice 		= CcartQty * priceChart[CselectOpt][CcartQty];
									} else {
										CcartnextQty = CcartQty - 1;
									 	while (priceChart[CselectOpt][CcartnextQty] == NaN || jQuery.type(priceChart[CselectOpt][CcartnextQty]) === "undefined") {
											CcartnextQty = CcartnextQty - 1;
										}
										CcartPrice = CcartQty * priceChart[CselectOpt][CcartnextQty];
									}
	
			//CcartPrice 		= CcartQty * priceChart[CselectOpt][CcartQty];		
			CdisplayPrice 	= (CtotalPrice - CcartPrice).toFixed(2);
			return {
					DisplayQty: 	CdisplayQty, 
					DisplayOption:  CselectOpt, 
					DisplayPrice :  CdisplayPrice
			};	
	}
	
	function setSelection(qty, opt, optName) {
		// Set data members to new selection
			selectQty = qty;
			selectOpt = opt;			

		//	alert(selectOpt);
		// Calculate price to display
			calc = calculate(selectQty, selectOpt);
			
	//SET DOM ELEMENTS
		// price chart
			jQuery(".price-table tr.selected, .price-table tr td.selected").removeClass("selected");
			
			selectTR = jQuery("table.price-table tr td.quantity").filter(function(){
				return jQuery(this).text() == calc.DisplayQty;}).parent();
			selectTR.addClass('selected');
	
			selectTD = selectTR.find('td.' + selectOpt);
			selectTD.addClass('selected');
			
		// option dropdown
			domSelect.children('option:contains('+ optName + ')').attr('selected', 'selected');
						
		// quantity field
		    domQty.val(selectQty);
			
		// display text
			if (optName == 'Price')  {strOption = '';} else {strOption = ' with ' + optName;}
			var strStatus = "<h4>Current Selection:</h4> <div class='price'> " + (qty * packCount) + strOption + " - $" + calc.DisplayPrice + "</div>"; 
			domSelectStatus.html(strStatus);
			jQuery('#add-to-holder').show();
	
	}
			
	domSelect.change(function() {
		domOptName = jQuery(this).children("option:selected").text();
		domSelectOpt = sanitize(domOptName);
        setSelection(selectQty, domSelectOpt, domOptName);
	});

	return {
		choose: function (clickedEl){
			clickedOptName = jQuery("table.price-table thead th").eq(clickedEl.parent().find('> *').index(clickedEl)).text();
			clickedOpt = clickedEl.attr('class').split(' ')[0];
			clickedQty = (clickedEl.siblings().filter(":first").text()) / packCount;
			setSelection(clickedQty, clickedOpt, clickedOptName);	
		},		
		
		preview: function (qty, opt, optName) {
			pre = calculate(qty, opt);
			// strOptName = '';
			if (optName !== 'Price') { optName = ' with ' + optName ;} else {optName = '';};
			msg = ("Add " + pre.DisplayQty + " more" + optName + " for $" + pre.DisplayPrice);
			// alert(msg);
			domPreview.text(msg);
		}
	};
}	
function sanitize(string) {
	string =  string.toLowerCase();
	string = jQuery.trim(string);
	string = string.replace(/ /g,"-");
	return string;
}


