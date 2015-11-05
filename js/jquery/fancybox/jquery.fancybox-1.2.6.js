/*
 * FancyBox - jQuery Plugin
 * simple and fancy lightbox alternative
 *
 * Copyright (c) 2009 Janis Skarnelis
 * Examples and documentation at: http://fancybox.net
 * 
 * Version: 1.2.6 (16/11/2009)
 * Requires: jQuery v1.3+
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */

;(function($) {
	$.fn.fixPNG = function() {
		return this.each(function () {
			var image = jQuery(this).css('backgroundImage');

			if (image.match(/^url\(["']?(.*\.png)["']?\)$/i)) {
				image = RegExp.$1;
				jQuery(this).css({
					'backgroundImage': 'none',
					'filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=" + (jQuery(this).css('backgroundRepeat') == 'no-repeat' ? 'crop' : 'scale') + ", src='" + image + "')"
				}).each(function () {
					var position = jQuery(this).css('position');
					if (position != 'absolute' && position != 'relative')
						jQuery(this).css('position', 'relative');
				});
			}
		});
	};

	var elem, opts, busy = false, imagePreloader = new Image, loadingTimer, loadingFrame = 1, imageRegExp = /\.(jpg|gif|png|bmp|jpeg)(.*)?$/i;
	var ieQuirks = null, IE6 = $.browser.msie && $.browser.version.substr(0,1) == 6 && !window.XMLHttpRequest, oldIE = IE6 || ($.browser.msie && $.browser.version.substr(0,1) == 7);

	$.fn.fancybox = function(o) {
		var settings		= $.extend({}, $.fn.fancybox.defaults, o);
		var matchedGroup	= this;

		function _initialize() {
			elem = this;
			opts = $.extend({}, settings);

			_start();

			return false;
		};

		function _start() {
			if (busy) return;

			if ($.isFunction(opts.callbackOnStart)) {
				opts.callbackOnStart();
			}

			opts.itemArray		= [];
			opts.itemCurrent	= 0;

			if (settings.itemArray.length > 0) {
				opts.itemArray = settings.itemArray;

			} else {
				var item = {};

				if (!elem.rel || elem.rel == '') {
					var item = {href: elem.href, title: elem.title};

					if (jQuery(elem).children("img:first").length) {
						item.orig = jQuery(elem).children("img:first");
					} else {
						item.orig = jQuery(elem);
					}

					if (item.title == '' || typeof item.title == 'undefined') {
						item.title = item.orig.attr('alt');
					}
					
					opts.itemArray.push( item );

				} else {
					var subGroup = jQuery(matchedGroup).filter("a[rel=" + elem.rel + "]");
					var item = {};

					for (var i = 0; i < subGroup.length; i++) {
						item = {href: subGroup[i].href, title: subGroup[i].title};

						if (jQuery(subGroup[i]).children("img:first").length) {
							item.orig = jQuery(subGroup[i]).children("img:first");
						} else {
							item.orig = jQuery(subGroup[i]);
						}

						if (item.title == '' || typeof item.title == 'undefined') {
							item.title = item.orig.attr('alt');
						}

						opts.itemArray.push( item );
					}
				}
			}

			while ( opts.itemArray[ opts.itemCurrent ].href != elem.href ) {
				opts.itemCurrent++;
			}

			if (opts.overlayShow) {
				if (IE6) {
					jQuery('embed, object, select').css('visibility', 'hidden');
					jQuery("#fancy_overlay").css('height', jQuery(document).height());
				}

				jQuery("#fancy_overlay").css({
					'background-color'	: opts.overlayColor,
					'opacity'			: opts.overlayOpacity
				}).show();
			}
			
			jQuery(window).bind("resize.fb scroll.fb", $.fn.fancybox.scrollBox);

			_change_item();
		};

		function _change_item() {
			jQuery("#fancy_right, #fancy_left, #fancy_close, #fancy_title").hide();

			var href = opts.itemArray[ opts.itemCurrent ].href;

			if (href.match("iframe") || elem.className.indexOf("iframe") >= 0) {
				$.fn.fancybox.showLoading();
				_set_content('<iframe id="fancy_frame" onload="jQuery.fn.fancybox.showIframe()" name="fancy_iframe' + Math.round(Math.random()*1000) + '" frameborder="0" hspace="0" src="' + href + '"></iframe>', opts.frameWidth, opts.frameHeight);

			} else if (href.match(/#/)) {
				var target = window.location.href.split('#')[0]; target = href.replace(target, ''); target = target.substr(target.indexOf('#'));

				_set_content('<div id="fancy_div">' + jQuery(target).html() + '</div>', opts.frameWidth, opts.frameHeight);

			} else if (href.match(imageRegExp)) {
				imagePreloader = new Image; imagePreloader.src = href;

				if (imagePreloader.complete) {
					_proceed_image();

				} else {
					$.fn.fancybox.showLoading();
					jQuery(imagePreloader).unbind().bind('load', function() {
						jQuery("#fancy_loading").hide();

						_proceed_image();
					});
				}
			} else {
				$.fn.fancybox.showLoading();
				$.get(href, function(data) {
					jQuery("#fancy_loading").hide();
					_set_content( '<div id="fancy_ajax">' + data + '</div>', opts.frameWidth, opts.frameHeight );
				});
			}
		};

		function _proceed_image() {
			var width	= imagePreloader.width;
			var height	= imagePreloader.height;

			var horizontal_space	= (opts.padding * 2) + 40;
			var vertical_space		= (opts.padding * 2) + 60;

			var w = $.fn.fancybox.getViewport();
			
			if (opts.imageScale && (width > (w[0] - horizontal_space) || height > (w[1] - vertical_space))) {
				var ratio = Math.min(Math.min(w[0] - horizontal_space, width) / width, Math.min(w[1] - vertical_space, height) / height);

				width	= Math.round(ratio * width);
				height	= Math.round(ratio * height);
			}

			_set_content('<img alt="" id="fancy_img" src="' + imagePreloader.src + '" />', width, height);
		};

		function _preload_neighbor_images() {
			if ((opts.itemArray.length -1) > opts.itemCurrent) {
				var href = opts.itemArray[opts.itemCurrent + 1].href || false;

				if (href && href.match(imageRegExp)) {
					objNext = new Image();
					objNext.src = href;
				}
			}

			if (opts.itemCurrent > 0) {
				var href = opts.itemArray[opts.itemCurrent -1].href || false;

				if (href && href.match(imageRegExp)) {
					objNext = new Image();
					objNext.src = href;
				}
			}
		};

		function _set_content(value, width, height) {
			busy = true;

			var pad = opts.padding;

			if (oldIE || ieQuirks) {
				jQuery("#fancy_content")[0].style.removeExpression("height");
				jQuery("#fancy_content")[0].style.removeExpression("width");
			}

			if (pad > 0) {
				width	+= pad * 2;
				height	+= pad * 2;

				jQuery("#fancy_content").css({
					'top'		: pad + 'px',
					'right'		: pad + 'px',
					'bottom'	: pad + 'px',
					'left'		: pad + 'px',
					'width'		: 'auto',
					'height'	: 'auto'
				});

				if (oldIE || ieQuirks) {
					jQuery("#fancy_content")[0].style.setExpression('height',	'(this.parentNode.clientHeight - '	+ pad * 2 + ')');
					jQuery("#fancy_content")[0].style.setExpression('width',		'(this.parentNode.clientWidth - '	+ pad * 2 + ')');
				}
			} else {
				jQuery("#fancy_content").css({
					'top'		: 0,
					'right'		: 0,
					'bottom'	: 0,
					'left'		: 0,
					'width'		: '100%',
					'height'	: '100%'
				});
			}

			if (jQuery("#fancy_outer").is(":visible") && width == jQuery("#fancy_outer").width() && height == jQuery("#fancy_outer").height()) {
				jQuery("#fancy_content").fadeOut('fast', function() {
					jQuery("#fancy_content").empty().append(jQuery(value)).fadeIn("normal", function() {
						_finish();
					});
				});

				return;
			}

			var w = $.fn.fancybox.getViewport();

			var itemTop		= (height	+ 60) > w[1] ? w[3] : (w[3] + Math.round((w[1] - height	- 60) * 0.5));
			var itemLeft	= (width	+ 40) > w[0] ? w[2] : (w[2] + Math.round((w[0] - width	- 40) * 0.5));

			var itemOpts = {
				'left':		itemLeft,
				'top':		itemTop,
				'width':	width + 'px',
				'height':	height + 'px'
			};

			if (jQuery("#fancy_outer").is(":visible")) {
				jQuery("#fancy_content").fadeOut("normal", function() {
					jQuery("#fancy_content").empty();
					jQuery("#fancy_outer").animate(itemOpts, opts.zoomSpeedChange, opts.easingChange, function() {
						jQuery("#fancy_content").append(jQuery(value)).fadeIn("normal", function() {
							_finish();
						});
					});
				});

			} else {

				if (opts.zoomSpeedIn > 0 && opts.itemArray[opts.itemCurrent].orig !== undefined) {
					jQuery("#fancy_content").empty().append(jQuery(value));

					var orig_item	= opts.itemArray[opts.itemCurrent].orig;
					var orig_pos	= $.fn.fancybox.getPosition(orig_item);

					jQuery("#fancy_outer").css({
						'left':		(orig_pos.left	- 20 - opts.padding) + 'px',
						'top':		(orig_pos.top	- 20 - opts.padding) + 'px',
						'width':	jQuery(orig_item).width() + (opts.padding * 2),
						'height':	jQuery(orig_item).height() + (opts.padding * 2)
					});

					if (opts.zoomOpacity) {
						itemOpts.opacity = 'show';
					}

					jQuery("#fancy_outer").animate(itemOpts, opts.zoomSpeedIn, opts.easingIn, function() {
						_finish();
					});

				} else {

					jQuery("#fancy_content").hide().empty().append(jQuery(value)).show();
					jQuery("#fancy_outer").css(itemOpts).fadeIn("normal", function() {
						_finish();
					});
				}
			}
		};

		function _set_navigation() {
			if (opts.itemCurrent !== 0) {
				jQuery("#fancy_left, #fancy_left_ico").unbind().bind("click", function(e) {
					e.stopPropagation();

					opts.itemCurrent--;
					_change_item();

					return false;
				});

				jQuery("#fancy_left").show();
			}

			if (opts.itemCurrent != ( opts.itemArray.length -1)) {
				jQuery("#fancy_right, #fancy_right_ico").unbind().bind("click", function(e) {
					e.stopPropagation();

					opts.itemCurrent++;
					_change_item();

					return false;
				});

				jQuery("#fancy_right").show();
			}
		};

		function _finish() {
			if ($.browser.msie) {
				jQuery("#fancy_content")[0].style.removeAttribute('filter');
				jQuery("#fancy_outer")[0].style.removeAttribute('filter');
			}

			_set_navigation();

			_preload_neighbor_images();

			jQuery(document).bind("keydown.fb", function(e) {
				if (e.keyCode == 27 && opts.enableEscapeButton) {
					$.fn.fancybox.close();

				} else if(e.keyCode == 37 && opts.itemCurrent !== 0) {
					jQuery(document).unbind("keydown.fb");
					opts.itemCurrent--;
					_change_item();
					

				} else if(e.keyCode == 39 && opts.itemCurrent != (opts.itemArray.length - 1)) {
					jQuery(document).unbind("keydown.fb");
					opts.itemCurrent++;
					_change_item();
				}
			});

			if (opts.hideOnContentClick) {
				jQuery("#fancy_content").click($.fn.fancybox.close);
			}

			if (opts.overlayShow && opts.hideOnOverlayClick) {
				jQuery("#fancy_overlay").bind("click", $.fn.fancybox.close);
			}

			if (opts.showCloseButton) {
				jQuery("#fancy_close").bind("click", $.fn.fancybox.close).show();
			}

			if (typeof opts.itemArray[ opts.itemCurrent ].title !== 'undefined' && opts.itemArray[ opts.itemCurrent ].title.length > 0) {
				var pos = jQuery("#fancy_outer").position();

				jQuery('#fancy_title div').text( opts.itemArray[ opts.itemCurrent ].title).html();

				jQuery('#fancy_title').css({
					'top'	: pos.top + jQuery("#fancy_outer").outerHeight() - 32,
					'left'	: pos.left + ((jQuery("#fancy_outer").outerWidth() * 0.5) - (jQuery('#fancy_title').width() * 0.5))
				}).show();
			}

			if (opts.overlayShow && IE6) {
				jQuery('embed, object, select', jQuery('#fancy_content')).css('visibility', 'visible');
			}

			if ($.isFunction(opts.callbackOnShow)) {
				opts.callbackOnShow( opts.itemArray[ opts.itemCurrent ] );
			}

			if ($.browser.msie) {
				jQuery("#fancy_outer")[0].style.removeAttribute('filter'); 
				jQuery("#fancy_content")[0].style.removeAttribute('filter'); 
			}
			
			busy = false;
		};

		return this.unbind('click.fb').bind('click.fb', _initialize);
	};

	$.fn.fancybox.scrollBox = function() {
		var w = $.fn.fancybox.getViewport();
		
		if (opts.centerOnScroll && jQuery("#fancy_outer").is(':visible')) {
			var ow	= jQuery("#fancy_outer").outerWidth();
			var oh	= jQuery("#fancy_outer").outerHeight();

			var pos	= {
				'top'	: (oh > w[1] ? w[3] : w[3] + Math.round((w[1] - oh) * 0.5)),
				'left'	: (ow > w[0] ? w[2] : w[2] + Math.round((w[0] - ow) * 0.5))
			};

			jQuery("#fancy_outer").css(pos);

			jQuery('#fancy_title').css({
				'top'	: pos.top	+ oh - 32,
				'left'	: pos.left	+ ((ow * 0.5) - (jQuery('#fancy_title').width() * 0.5))
			});
		}
		
		if (IE6 && jQuery("#fancy_overlay").is(':visible')) {
			jQuery("#fancy_overlay").css({
				'height' : jQuery(document).height()
			});
		}
		
		if (jQuery("#fancy_loading").is(':visible')) {
			jQuery("#fancy_loading").css({'left': ((w[0] - 40) * 0.5 + w[2]), 'top': ((w[1] - 40) * 0.5 + w[3])});
		}
	};

	$.fn.fancybox.getNumeric = function(el, prop) {
		return parseInt($.curCSS(el.jquery?el[0]:el,prop,true))||0;
	};

	$.fn.fancybox.getPosition = function(el) {
		var pos = el.offset();

		pos.top	+= $.fn.fancybox.getNumeric(el, 'paddingTop');
		pos.top	+= $.fn.fancybox.getNumeric(el, 'borderTopWidth');

		pos.left += $.fn.fancybox.getNumeric(el, 'paddingLeft');
		pos.left += $.fn.fancybox.getNumeric(el, 'borderLeftWidth');

		return pos;
	};

	$.fn.fancybox.showIframe = function() {
		jQuery("#fancy_loading").hide();
		jQuery("#fancy_frame").show();
	};

	$.fn.fancybox.getViewport = function() {
		return [jQuery(window).width(), jQuery(window).height(), jQuery(document).scrollLeft(), jQuery(document).scrollTop() ];
	};

	$.fn.fancybox.animateLoading = function() {
		if (!jQuery("#fancy_loading").is(':visible')){
			clearInterval(loadingTimer);
			return;
		}

		jQuery("#fancy_loading > div").css('top', (loadingFrame * -40) + 'px');

		loadingFrame = (loadingFrame + 1) % 12;
	};

	$.fn.fancybox.showLoading = function() {
		clearInterval(loadingTimer);

		var w = $.fn.fancybox.getViewport();

		jQuery("#fancy_loading").css({'left': ((w[0] - 40) * 0.5 + w[2]), 'top': ((w[1] - 40) * 0.5 + w[3])}).show();
		jQuery("#fancy_loading").bind('click', $.fn.fancybox.close);

		loadingTimer = setInterval($.fn.fancybox.animateLoading, 66);
	};

	$.fn.fancybox.close = function() {
		busy = true;

		jQuery(imagePreloader).unbind();

		jQuery(document).unbind("keydown.fb");
		jQuery(window).unbind("resize.fb scroll.fb");

		jQuery("#fancy_overlay, #fancy_content, #fancy_close").unbind();

		jQuery("#fancy_close, #fancy_loading, #fancy_left, #fancy_right, #fancy_title").hide();

		__cleanup = function() {
			if (jQuery("#fancy_overlay").is(':visible')) {
				jQuery("#fancy_overlay").fadeOut("fast");
			}

			jQuery("#fancy_content").empty();
			
			if (opts.centerOnScroll) {
				jQuery(window).unbind("resize.fb scroll.fb");
			}

			if (IE6) {
				jQuery('embed, object, select').css('visibility', 'visible');
			}

			if ($.isFunction(opts.callbackOnClose)) {
				opts.callbackOnClose();
			}

			busy = false;
		};

		if (jQuery("#fancy_outer").is(":visible") !== false) {
			if (opts.zoomSpeedOut > 0 && opts.itemArray[opts.itemCurrent].orig !== undefined) {
				var orig_item	= opts.itemArray[opts.itemCurrent].orig;
				var orig_pos	= $.fn.fancybox.getPosition(orig_item);

				var itemOpts = {
					'left':		(orig_pos.left	- 20 - opts.padding) + 'px',
					'top': 		(orig_pos.top	- 20 - opts.padding) + 'px',
					'width':	jQuery(orig_item).width() + (opts.padding * 2),
					'height':	jQuery(orig_item).height() + (opts.padding * 2)
				};

				if (opts.zoomOpacity) {
					itemOpts.opacity = 'hide';
				}

				jQuery("#fancy_outer").stop(false, true).animate(itemOpts, opts.zoomSpeedOut, opts.easingOut, __cleanup);

			} else {
				jQuery("#fancy_outer").stop(false, true).fadeOut('fast', __cleanup);
			}

		} else {
			__cleanup();
		}

		return false;
	};

	$.fn.fancybox.build = function() {
		var html = '';

		html += '<div id="fancy_overlay"></div>';
		html += '<div id="fancy_loading"><div></div></div>';

		html += '<div id="fancy_outer">';
		html += '<div id="fancy_inner">';

		html += '<div id="fancy_close"></div>';

		html += '<div id="fancy_bg"><div class="fancy_bg" id="fancy_bg_n"></div><div class="fancy_bg" id="fancy_bg_ne"></div><div class="fancy_bg" id="fancy_bg_e"></div><div class="fancy_bg" id="fancy_bg_se"></div><div class="fancy_bg" id="fancy_bg_s"></div><div class="fancy_bg" id="fancy_bg_sw"></div><div class="fancy_bg" id="fancy_bg_w"></div><div class="fancy_bg" id="fancy_bg_nw"></div></div>';

		html += '<a href="javascript:;" id="fancy_left"><span class="fancy_ico" id="fancy_left_ico"></span></a><a href="javascript:;" id="fancy_right"><span class="fancy_ico" id="fancy_right_ico"></span></a>';

		html += '<div id="fancy_content"></div>';

		html += '</div>';
		html += '</div>';
		
		html += '<div id="fancy_title"></div>';
		
		jQuery(html).appendTo("body");

		jQuery('<table cellspacing="0" cellpadding="0" border="0"><tr><td class="fancy_title" id="fancy_title_left"></td><td class="fancy_title" id="fancy_title_main"><div></div></td><td class="fancy_title" id="fancy_title_right"></td></tr></table>').appendTo('#fancy_title');

		if ($.browser.msie) {
			jQuery(".fancy_bg").fixPNG();
		}

		if (IE6) {
			jQuery("div#fancy_overlay").css("position", "absolute");
			jQuery("#fancy_loading div, #fancy_close, .fancy_title, .fancy_ico").fixPNG();

			jQuery("#fancy_inner").prepend('<iframe id="fancy_bigIframe" src="javascript:false;" scrolling="no" frameborder="0"></iframe>');

			// Get rid of the 'false' text introduced by the URL of the iframe
			var frameDoc = jQuery('#fancy_bigIframe')[0].contentWindow.document;
			frameDoc.open();
			frameDoc.close();
			
		}
	};

	$.fn.fancybox.defaults = {
		padding				:	10,
		imageScale			:	true,
		zoomOpacity			:	true,
		zoomSpeedIn			:	0,
		zoomSpeedOut		:	0,
		zoomSpeedChange		:	300,
		easingIn			:	'swing',
		easingOut			:	'swing',
		easingChange		:	'swing',
		frameWidth			:	560,
		frameHeight			:	340,
		overlayShow			:	true,
		overlayOpacity		:	0.3,
		overlayColor		:	'#666',
		enableEscapeButton	:	true,
		showCloseButton		:	true,
		hideOnOverlayClick	:	true,
		hideOnContentClick	:	true,
		centerOnScroll		:	true,
		itemArray			:	[],
		callbackOnStart		:	null,
		callbackOnShow		:	null,
		callbackOnClose		:	null
	};

	jQuery(document).ready(function() {
		ieQuirks = $.browser.msie && !$.boxModel;

		if (jQuery("#fancy_outer").length < 1) {
			$.fn.fancybox.build();
		}
	});

})(jQuery);