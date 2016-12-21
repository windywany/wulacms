$.root_ = $('body');

$.navAsAjax = true;

$.sound_path = "sound/";
$.sound_on = true;

var root = this,

throttle_delay = 350,

menu_speed = 235,

menu_accordion = true,

enableJarvisWidgets = true,

localStorageJarvisWidgets = true,

sortableJarvisWidgets = true,

enableMobileWidgets = false,

fastClick = false,
	ignore_key_elms = [ "#header, #superbox-overlay, #superbox-wrapper, #left-panel, #right-panel, #main, div.page-footer, #shortcut, #divSmallBoxes, #divMiniIcons, #divbigBoxes, #voiceModal, script, .ui-chatbox,.keepit" ];

$.intervalArr = [];

var calc_body_height = function(){	
	if(!$.root_.hasClass('menu-on-top')){
		var mh = $('#left-panel nav').offset().top + $('#left-panel nav').height()+100;		
		$.root_.css('min-height',mh);
	}
};
/*
 * Calculate nav height
 */
var calc_navbar_height = function() {
	var height = null;
	if ($('#header').length)
		height = $('#header').height();
	if (height === null)
		height = $('<div id="header"></div>').height();
	if (height === null)
		return 49;
	return height;
}, navbar_height = calc_navbar_height, shortcut_dropdown = $('#shortcut'), bread_crumb = $('#ribbon ol.breadcrumb'), topmenu = false, thisDevice = null, ismobile = (/iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i
		.test(navigator.userAgent.toLowerCase())), jsArray = {}, initApp = (function(
		app) {
	app.addDeviceType = function() {

		if (!ismobile) {
			// Desktop
			$.root_.addClass("desktop-detected");
			thisDevice = "desktop";
			return false;
		} else {
			// Mobile
			$.root_.addClass("mobile-detected");
			thisDevice = "mobile";

			if (fastClick) {
				// Removes the tap delay in idevices
				// dependency: js/plugin/fastclick/fastclick.js
				$.root_.addClass("needsclick");
				FastClick.attach(document.body);
				return false;
			}

		}

	};
	/* ~ END: ADD DEVICE TYPE */

	/*
	 * CHECK FOR MENU POSITION Scans localstroage for menu position (vertical or
	 * horizontal)
	 */
	app.menuPos = function() {
		if ($.root_.hasClass("menu-on-top") || localStorage.getItem('sm-setmenu') == 'top') {
			topmenu = true;
			$.root_.addClass("menu-on-top");
		}
	};
	/* ~ END: CHECK MOBILE DEVICE */
	/*
	 * SMART ACTIONS
	 */
	app.SmartActions = function() {
		var smartActions = {
			// LAUNCH FULLSCREEN
			launchFullscreen : function(element) {

				if (!$.root_.hasClass("full-screen")) {

					$.root_.addClass("full-screen");

					if (element.requestFullscreen) {
						element.requestFullscreen();
					} else if (element.mozRequestFullScreen) {
						element.mozRequestFullScreen();
					} else if (element.webkitRequestFullscreen) {
						element.webkitRequestFullscreen();
					} else if (element.msRequestFullscreen) {
						element.msRequestFullscreen();
					}

				} else {

					$.root_.removeClass("full-screen");

					if (document.exitFullscreen) {
						document.exitFullscreen();
					} else if (document.mozCancelFullScreen) {
						document.mozCancelFullScreen();
					} else if (document.webkitExitFullscreen) {
						document.webkitExitFullscreen();
					}
				}
			},

			// MINIFY MENU
			minifyMenu : function($this) {
				if (!$.root_.hasClass("menu-on-top")) {
					$.root_.toggleClass("minified");
					$.root_.removeClass("hidden-menu");
					$('html').removeClass("hidden-menu-mobile-lock");
					$this.effect("highlight", {}, 500);
				}
			},

			// TOGGLE MENU
			toggleMenu : function() {
				if (!$.root_.hasClass("menu-on-top")) {
					$('html').toggleClass("hidden-menu-mobile-lock");
					$.root_.toggleClass("hidden-menu");
					$.root_.removeClass("minified");
				} else if ($.root_.hasClass("menu-on-top")
						&& $(window).width() < 979) {
					$('html').toggleClass("hidden-menu-mobile-lock");
					$.root_.toggleClass("hidden-menu");
					$.root_.removeClass("minified");
				}
			},
			// TOGGLE SHORTCUT
			toggleShortcut : function() {

				if (shortcut_dropdown.is(":visible")) {
					shortcut_buttons_hide();
				} else {
					shortcut_buttons_show();
				}

				// SHORT CUT (buttons that appear when clicked on user name)
				shortcut_dropdown.find('a').click(function(e) {
					e.preventDefault();
					window.location = $(this).attr('href');
					setTimeout(shortcut_buttons_hide, 300);

				});

				// SHORTCUT buttons goes away if mouse is clicked outside of
				// the area
				$(document)
						.mouseup(
								function(e) {
									if (!shortcut_dropdown.is(e.target)
											&& shortcut_dropdown.has(e.target).length === 0) {
										shortcut_buttons_hide();
									}
								});

				// SHORTCUT ANIMATE HIDE
				function shortcut_buttons_hide() {
					shortcut_dropdown.animate({
						height : "hide"
					}, 300, "easeOutCirc");
					$.root_.removeClass('shortcut-on');

				}

				// SHORTCUT ANIMATE SHOW
				function shortcut_buttons_show() {
					shortcut_dropdown.animate({
						height : "show"
					}, 200, "easeOutCirc");
					$.root_.addClass('shortcut-on');
				}

			}

		};

		$.root_.on('click', '[data-action="launchFullscreen"]', function(e) {
			smartActions.launchFullscreen(document.documentElement);
			e.preventDefault();
		});

		$.root_.on('click', '[data-action="minifyMenu"]', function(e) {
			var $this = $(this);
			smartActions.minifyMenu($this);
			e.preventDefault();

			// clear memory reference
			$this = null;
		});

		$.root_.on('click', '[data-action="toggleMenu"]', function(e) {
			smartActions.toggleMenu();
			e.preventDefault();
		});

		$.root_.on('click', '[data-action="toggleShortcut"]', function(e) {
			smartActions.toggleShortcut();
			e.preventDefault();
		});

	};
	/* ~ END: SMART ACTIONS */

	/*
	 * ACTIVATE NAVIGATION Description: Activation will fail if top navigation
	 * is on
	 */
	app.leftNav = function() {

		// INITIALIZE LEFT NAV
		if (!topmenu) {
			if (!null) {
				$('nav ul').jarvismenu({
					accordion : menu_accordion || true,
					speed : menu_speed || true,
					closedSign : '<em class="fa fa-plus-square-o"></em>',
					openedSign : '<em class="fa fa-minus-square-o"></em>'
				});
			} else {
				alert("Error - menu anchor does not exist");
			}
		}

	};
	/* ~ END: ACTIVATE NAVIGATION */

	/*
	 * MISCELANEOUS DOM READY FUNCTIONS Description: fire with
	 * jQuery(document).ready...
	 */
	app.domReadyMisc = function() {

		/*
		 * FIRE TOOLTIPS
		 */
		if ($("[rel=tooltip]").length) {
			$("[rel=tooltip]").tooltip();
		}

		// SHOW & HIDE MOBILE SEARCH FIELD
		$('#search-mobile').click(function() {
			$.root_.addClass('search-mobile');
		});

		$('#cancel-search-js').click(function() {
			$.root_.removeClass('search-mobile');
		});

		// ACTIVITY
		// ajax drop
		$('#activity').click(
				function(e) {
					var $this = $(this);

					if ($this.find('.badge').hasClass('bg-color-red')) {
						$this.find('.badge').removeClassPrefix('bg-color-');
						$this.find('.badge').text("0");
					}

					if (!$this.next('.ajax-dropdown').is(':visible')) {
						$this.next('.ajax-dropdown').fadeIn(150);
						$this.addClass('active');
					} else {
						$this.next('.ajax-dropdown').fadeOut(150);
						$this.removeClass('active');
					}

					var theUrlVal = $this.next('.ajax-dropdown').find(
							'.btn-group > .active > input').attr('id');

					// clear memory reference
					$this = null;
					theUrlVal = null;

					e.preventDefault();
				});

		$('input[name="activity"]').change(function() {
			var $this = $(this);

			url = $this.attr('id');
			container = $('.ajax-notifications');

			loadURL(url, container);

			// clear memory reference
			$this = null;
		});

		// close dropdown if mouse is not inside the area of .ajax-dropdown
		$(document).mouseup(
				function(e) {
					if (!$('.ajax-dropdown').is(e.target)
							&& $('.ajax-dropdown').has(e.target).length === 0) {
						$('.ajax-dropdown').fadeOut(150);
						$('.ajax-dropdown').prev().removeClass("active");
					}
				});

		// loading animation (demo purpose only)
		$('button[data-btn-loading]').on('click', function() {
			var btn = $(this);
			btn.button('loading');
			setTimeout(function() {
				btn.button('reset');
			}, 3000);
		});

		// NOTIFICATION IS PRESENT
		// Change color of lable once notification button is clicked

		$this = $('#activity > .badge');

		if (parseInt($this.text()) > 0) {
			$this.addClass("bg-color-red bounceIn animated");

			// clear memory reference
			$this = null;
		}

	};
	/* ~ END: MISCELANEOUS DOM */
	app.layoutSetting = function() {
		$("#reset-smart-widget-style")
				.bind(
						"click",
						function(e) {
							e.preventDefault();
							var $this = $(this);
							$.widresetMSG = $this.data('reset-msg');
							$
									.SmartMessageBox(
											{
												title : "<i class='fa fa-refresh' style='color:green'></i> 重置组件样式",
												content : $.widresetMSG,
												buttons : '[否][是]'
											}, function(ButtonPressed) {
												if (ButtonPressed == "是"
														&& localStorage) {
													localStorage.clear();
													location.reload();													
												}
											});
							return false;
						});
		// LOGOUT BUTTON
		$('a[data-action="logoutUser"]').click(function(e) {
			// get the link
			var $this = $(this);
			$.loginURL = $this.attr('href');
			$.logoutMSG = $this.data('logout-msg');
			// ask verification
			$.SmartMessageBox({
				title : "<i class='fa fa-sign-out txt-color-orangeDark'></i> 注销 <span class='txt-color-orangeDark'><strong>"
						+ $('#show-shortcut').text()
						+ "</strong></span> ?",
				content : $.logoutMSG,
				buttons : '[否][是]'
			},
			function(ButtonPressed) {
				if (ButtonPressed == "是") {
					if(localStorage){
						localStorage.setItem('dashboard-cp-url','');
					}
					$.root_.addClass('animated fadeOutUp');
					setTimeout(function() {
						window.location = $.loginURL;
					}, 500);
				}
			});
			e.preventDefault();
		});
		// RESET WIDGETS
		$('#refresh').click(function(e) {
			checkURL();
			e.preventDefault();
		});
	};
	/*
	 * MISCELANEOUS DOM READY FUNCTIONS Description: fire with
	 * jQuery(document).ready...
	 */
	app.mobileCheckActivation = function() {

		if ($(window).width() < 979) {
			$.root_.addClass('mobile-view-activated');
			$.root_.removeClass('minified');
		} else if ($.root_.hasClass('mobile-view-activated')) {
			$.root_.removeClass('mobile-view-activated');
		}

	}
	/* ~ END: MISCELANEOUS DOM */

	return app;

})({});

initApp.addDeviceType();
initApp.menuPos();
/*
 * DOCUMENT LOADED EVENT Description: Fire when DOM is ready
 */
jQuery(document).ready(function() {
	initApp.SmartActions();
	initApp.leftNav();
	initApp.domReadyMisc();
	initApp.layoutSetting();
	if($(window).width() >= 1280 ){
		$('body').removeClass('minified');
	}
	$(window).resize(calc_body_height);
	calc_body_height();
	$('#content').on('click','.barhr',function () {
		var i = $(this).find('i'),pt = $('#content').find('.hasr');
		if(i.hasClass('fa-times')){
			i.removeClass('fa-times');
			pt.removeClass('openr');
		}else{
			i.addClass('fa-times');
			pt.addClass('openr');
		}
	});
	$('#content').on('click','.barhl',function () {
		var i = $(this).find('i'),pt = $('#content').find('.hasl');
		if(i.hasClass('fa-times')){
			i.removeClass('fa-times');
			pt.removeClass('openl');
		}else{
			i.addClass('fa-times');
			pt.addClass('openl');
		}
	});
});
/*
 * RESIZER WITH THROTTLE Source:
 * http://benalman.com/code/projects/jquery-resize/examples/resize/
 */
(function($, window, undefined) {

	var elems = $([]), jq_resize = $.resize = $.extend($.resize, {}), timeout_id, str_setTimeout = 'setTimeout', str_resize = 'resize', str_data = str_resize
			+ '-special-event', str_delay = 'delay', str_throttle = 'throttleWindow';

	jq_resize[str_delay] = throttle_delay;

	jq_resize[str_throttle] = true;

	$.event.special[str_resize] = {

		setup : function() {
			if (!jq_resize[str_throttle] && this[str_setTimeout]) {
				return false;
			}

			var elem = $(this);
			elems = elems.add(elem);
			try {
				$.data(this, str_data, {
					w : elem.width(),
					h : elem.height()
				});
			} catch (e) {
				$.data(this, str_data, {
					w : elem.width, // elem.width();
					h : elem.height
				// elem.height();
				});
			}

			if (elems.length === 1) {
				loopy();
			}
		},
		teardown : function() {
			if (!jq_resize[str_throttle] && this[str_setTimeout]) {
				return false;
			}

			var elem = $(this);
			elems = elems.not(elem);
			elem.removeData(str_data);
			if (!elems.length) {
				clearTimeout(timeout_id);
			}
		},

		add : function(handleObj) {
			if (!jq_resize[str_throttle] && this[str_setTimeout]) {
				return false;
			}
			var old_handler;

			function new_handler(e, w, h) {
				var elem = $(this), data = $.data(this, str_data);
				data.w = w !== undefined ? w : elem.width();
				data.h = h !== undefined ? h : elem.height();

				old_handler.apply(this, arguments);
			}
			if ($.isFunction(handleObj)) {
				old_handler = handleObj;
				return new_handler;
			} else {
				old_handler = handleObj.handler;
				handleObj.handler = new_handler;
			}
		}
	};

	function loopy() {
		timeout_id = window[str_setTimeout](function() {
			elems.each(function() {
				var width;
				var height;

				var elem = $(this), data = $.data(this, str_data); // width =
				// elem.width(),
				// height =
				// elem.height();

				// Highcharts fix
				try {
					width = elem.width();
				} catch (e) {
					width = elem.width;
				}

				try {
					height = elem.height();
				} catch (e) {
					height = elem.height;
				}
				// fixed bug

				if (width !== data.w || height !== data.h) {
					elem.trigger(str_resize,
							[ data.w = width, data.h = height ]);
				}

			});
			loopy();

		}, jq_resize[str_delay]);

	}

})(jQuery, this);
/*
 * ADD CLASS WHEN BELOW CERTAIN WIDTH (MOBILE MENU) Description: tracks the page
 * min-width of #CONTENT and NAV when navigation is resized. This is to counter
 * bugs for minimum page width on many desktop and mobile devices. Note: This
 * script utilizes JSthrottle script so don't worry about memory/CPU usage
 */
$('#main').resize(function() {
	initApp.mobileCheckActivation();
});

/* ~ END: NAV OR #LEFT-BAR RESIZE DETECT */
var ie = (function() {

	var undef, v = 3, div = document.createElement('div'), all = div
			.getElementsByTagName('i');

	while (div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
			all[0])
		;

	return v > 4 ? v : undef;

}());
/* ~ END: DETECT IE VERSION */

/*
 * CUSTOM MENU PLUGIN
 */
$.fn.extend({
	// pass the options variable to the function
	jarvismenu : function(options) {
		var defaults = {
			accordion : 'true',
			speed : 200,
			closedSign : '[+]',
			openedSign : '[-]'
		},
		// Extend our default options with those provided.
		opts = $.extend(defaults, options),
		// Assign current element to variable, in this case is UL
		// element
		$this = $(this);
		// add a mark [+] to a multilevel menu
		$this.find("li").each(function() {
			if ($(this).find("ul").size() !== 0) {						
				$(this).find("a:first").append(	"<b class='collapse-sign' style='width: 50px;text-align: right'>"+ opts.closedSign+ "</b>");
				if ($(this).find("a:first").attr('href') == "#") {
					$(this).find("a:first").click(function() {
						$(this).find('b.collapse-sign').click();
						return false;
					});
				}
			}
		});

		// open active level
		$this.find("li.active").each(function() {
			$(this).parents("ul").slideDown(opts.speed);
			$(this).parents("ul").parent("li").find("b:first").html(opts.openedSign);
			$(this).parents("ul").parent("li").addClass("open");
		});
		
		$this.find("li b.collapse-sign").click(function(e) {
			e.preventDefault();
			e.stopPropagation();
			var $ma = $(this).parent();
			if ($ma.parent().find("ul").size() !== 0) {
				if (opts.accordion) {
					// Do nothing when the list is open
					if (!$ma.parent().find("ul").is(':visible')) {
						parents = $ma.parent().parents("ul");
						visible = $this.find("ul:visible");
						visible.each(function(visibleIndex) {
							var close = true;
							parents.each(function(parentIndex) {
								if (parents[parentIndex] == visible[visibleIndex]) {
									close = false;
									return false;
								}
							});
							if (close) {
								if ($(this).parent().find("ul") != visible[visibleIndex]) {
									$(visible[visibleIndex]).slideUp(
											opts.speed,
											function() {
												$(this).parent("li").find("b:first").html(
														opts.closedSign);
												$(this).parent("li").removeClass("open");
												calc_body_height();
											});

								}
							}
						});
					}
				}// end if
				if ($ma.parent().find("ul:first").is(":visible")
						&& !$ma.parent().find("ul:first").hasClass("active")) {
					$ma.parent().find("ul:first").slideUp(
							opts.speed,
							function() {
								$(this).parent("li").removeClass("open");
								$(this).parent("li").find("b:first").delay(opts.speed)
										.html(opts.closedSign);
								calc_body_height();
							});

				} else {
					$ma.parent().find("ul:first").slideDown(
							opts.speed,
							function() {					
								$(this).parent("li").addClass("open");
								$(this).parent("li").find("b:first").delay(opts.speed)
										.html(opts.openedSign);
								calc_body_height();
							});
				} // end else
			} // end if

		});
	} // end function
});
/* ~ END: CUSTOM MENU PLUGIN */

jQuery.fn.doesExist = function() {
	return jQuery(this).length > 0;
};
/* ~ END: ELEMENT EXIST OR NOT */

function runAllForms(container) {
	if ($.fn.slider) {
		container.find('.slider').slider();
	}
	if($.superbox){
		$.superbox();		
	}
	$('button[data-loading-text]').on('click', function() {
		var btn = $(this);
		btn.button('loading');
		setTimeout(function() {
			btn.button('reset');
			// clear memory reference
			btn = null;
		}, 3000);
	});
}
/* ~ END: INITIALIZE FORMS */

/*
 * INITIALIZE CHARTS Description: Sparklines, PieCharts
 */
function runAllCharts(container) {
	if ($.fn.sparkline) {
		// variable declearations:
		var barColor, sparklineHeight, sparklineBarWidth, sparklineBarSpacing, sparklineNegBarColor, sparklineStackedColor, thisLineColor, thisLineWidth, thisFill, thisSpotColor, thisMinSpotColor, thisMaxSpotColor, thishighlightSpotColor, thisHighlightLineColor, thisSpotRadius, pieColors, pieWidthHeight, pieBorderColor, pieOffset, thisBoxWidth, thisBoxHeight, thisBoxRaw, thisBoxTarget, thisBoxMin, thisBoxMax, thisShowOutlier, thisIQR, thisBoxSpotRadius, thisBoxLineColor, thisBoxFillColor, thisBoxWhisColor, thisBoxOutlineColor, thisBoxOutlineFill, thisBoxMedianColor, thisBoxTargetColor, thisBulletHeight, thisBulletWidth, thisBulletColor, thisBulletPerformanceColor, thisBulletRangeColors, thisDiscreteHeight, thisDiscreteWidth, thisDiscreteLineColor, thisDiscreteLineHeight, thisDiscreteThrushold, thisDiscreteThrusholdColor, thisTristateHeight, thisTristatePosBarColor, thisTristateNegBarColor, thisTristateZeroBarColor, thisTristateBarWidth, thisTristateBarSpacing, thisZeroAxis, thisBarColor, sparklineWidth, sparklineValue, sparklineValueSpots1, sparklineValueSpots2, thisLineWidth1, thisLineWidth2, thisLineColor1, thisLineColor2, thisSpotRadius1, thisSpotRadius2, thisMinSpotColor1, thisMaxSpotColor1, thisMinSpotColor2, thisMaxSpotColor2, thishighlightSpotColor1, thisHighlightLineColor1, thishighlightSpotColor2, thisFillColor1, thisFillColor2;
		container.find('.sparkline:not(:has(>canvas))').each(function() {
			var $this = $(this), sparklineType = $this.data('sparkline-type') || 'bar';
			// BAR CHART
			if (sparklineType == 'bar') {
				barColor = $this.data('sparkline-bar-color') || $this.css('color') || '#0000f0';
				sparklineHeight = $this.data('sparkline-height') || '26px';
				sparklineBarWidth = $this.data('sparkline-barwidth') || 5;
				sparklineBarSpacing = $this.data('sparkline-barspacing') || 2;
				sparklineNegBarColor = $this.data('sparkline-negbar-color') || '#A90329';
				sparklineStackedColor = $this.data('sparkline-barstacked-color') || [ "#A90329", "#0099c6", "#98AA56", "#da532c", "#4490B1","#6E9461", "#990099", "#B4CAD3" ];
				$this.sparkline('html', {
					barColor : barColor,
					type : sparklineType,
					height : sparklineHeight,
					barWidth : sparklineBarWidth,
					barSpacing : sparklineBarSpacing,
					stackedBarColor : sparklineStackedColor,
					negBarColor : sparklineNegBarColor,
					zeroAxis : 'false'
				});
				$this = null;
			}
			// LINE CHART
			if (sparklineType == 'line') {
				sparklineHeight = $this.data('sparkline-height') || '20px';
				sparklineWidth = $this.data('sparkline-width') || '90px';
				thisLineColor = $this.data('sparkline-line-color') || $this.css('color') || '#0000f0';
				thisLineWidth = $this.data('sparkline-line-width') || 1;
				thisFill = $this.data('fill-color') || '#c0d0f0';
				thisSpotColor = $this.data('sparkline-spot-color') || '#f08000';
				thisMinSpotColor = $this.data('sparkline-minspot-color') || '#ed1c24';
				thisMaxSpotColor = $this.data('sparkline-maxspot-color') || '#f08000';
				thishighlightSpotColor = $this.data('sparkline-highlightspot-color')|| '#50f050';
				thisHighlightLineColor = $this.data('sparkline-highlightline-color')|| 'f02020';
				thisSpotRadius = $this.data('sparkline-spotradius') || 1.5;
				thisChartMinYRange = $this.data('sparkline-min-y') || 'undefined';
				thisChartMaxYRange = $this.data('sparkline-max-y') || 'undefined';
				thisChartMinXRange = $this.data('sparkline-min-x') || 'undefined';
				thisChartMaxXRange = $this.data('sparkline-max-x') || 'undefined';
				thisMinNormValue = $this.data('min-val') || 'undefined';
				thisMaxNormValue = $this.data('max-val') || 'undefined';
				thisNormColor = $this.data('norm-color') || '#c0c0c0';
				thisDrawNormalOnTop = $this.data('draw-normal') || false;
				$this.sparkline('html', {
					type : 'line',
					width : sparklineWidth,
					height : sparklineHeight,
					lineWidth : thisLineWidth,
					lineColor : thisLineColor,
					fillColor : thisFill,
					spotColor : thisSpotColor,
					minSpotColor : thisMinSpotColor,
					maxSpotColor : thisMaxSpotColor,
					highlightSpotColor : thishighlightSpotColor,
					highlightLineColor : thisHighlightLineColor,
					spotRadius : thisSpotRadius,
					chartRangeMin : thisChartMinYRange,
					chartRangeMax : thisChartMaxYRange,
					chartRangeMinX : thisChartMinXRange,
					chartRangeMaxX : thisChartMaxXRange,
					normalRangeMin : thisMinNormValue,
					normalRangeMax : thisMaxNormValue,
					normalRangeColor : thisNormColor,
					drawNormalOnTop : thisDrawNormalOnTop
				});
				$this = null;
			}
			// PIE CHART
			if (sparklineType == 'pie') {
				pieColors = $this.data('sparkline-piecolor') || [ "#B4CAD3", "#4490B1", "#98AA56", "#da532c", "#6E9461","#0099c6", "#990099", "#717D8A" ];
				pieWidthHeight = $this.data('sparkline-piesize') || 90;
				pieBorderColor = $this.data('border-color') || '#45494C';
				pieOffset = $this.data('sparkline-offset') || 0;
				$this.sparkline('html',{
					type : 'pie',
					width : pieWidthHeight,
					height : pieWidthHeight,
					tooltipFormat : '<span style="color: {{color}}">&#9679;</span> ({{percent.1}}%)',
					sliceColors : pieColors,
					borderWidth : 1,
					offset : pieOffset,
					borderColor : pieBorderColor
				});
				$this = null;
			}
		});
	}// end if
	if ($.fn.easyPieChart) {
		container.find('.easy-pie-chart').each(function() {
			var $this = $(this), barColor = $this.css('color')
					|| $this.data('pie-color'), trackColor = $this
					.data('pie-track-color')
					|| 'rgba(0,0,0,0.04)', size = parseInt($this
					.data('pie-size')) || 25;
			$this.easyPieChart({
				barColor : barColor,
				trackColor : trackColor,
				scaleColor : false,
				lineCap : 'butt',
				lineWidth : parseInt(size / 8.5),
				animate : 1500,
				rotate : -90,
				size : size,
				onStep : function(from, to, percent) {
					$(this.el).find('.percent').text(Math.round(percent));
				}
			});
			$this = null;
		});
	} // end if
}
/* ~ END: INITIALIZE CHARTS */

/*
 * INITIALIZE JARVIS WIDGETS Setup Desktop Widgets
 */
function setup_widgets_desktop() {

	if ($.fn.jarvisWidgets && enableJarvisWidgets) {

		$('#widget-grid')
				.jarvisWidgets(
						{
							grid : 'article',
							widgets : '.jarviswidget',
							localStorage : localStorageJarvisWidgets,
							deleteSettingsKey : '#deletesettingskey-options',
							settingsKeyLabel : 'Reset settings?',
							deletePositionKey : '#deletepositionkey-options',
							positionKeyLabel : 'Reset position?',
							sortable : sortableJarvisWidgets,
							buttonsHidden : false,
							// toggle button
							toggleButton : true,
							toggleClass : 'fa fa-minus | fa fa-plus',
							toggleSpeed : 200,
							onToggle : function() {
							},
							// delete btn
							deleteButton : true,
							deleteMsg : 'Warning: This action cannot be undone!',
							deleteClass : 'fa fa-times',
							deleteSpeed : 200,
							onDelete : function() {
							},
							// edit btn
							editButton : true,
							editPlaceholder : '.jarviswidget-editbox',
							editClass : 'fa fa-cog | fa fa-save',
							editSpeed : 200,
							onEdit : function() {
							},
							// color button
							colorButton : true,
							// full screen
							fullscreenButton : true,
							fullscreenClass : 'fa fa-expand | fa fa-compress',
							fullscreenDiff : 3,
							onFullscreen : function() {
							},
							// custom btn
							customButton : false,
							customClass : 'folder-10 | next-10',
							customStart : function() {
								alert('Hello you, this is a custom button...');
							},
							customEnd : function() {
								alert('bye, till next time...');
							},
							// order
							buttonOrder : '%refresh% %custom% %edit% %toggle% %fullscreen% %delete%',
							opacity : 1.0,
							dragHandle : '> header',
							placeholderClass : 'jarviswidget-placeholder',
							indicator : true,
							indicatorTime : 600,
							ajax : true,
							timestampPlaceholder : '.jarviswidget-timestamp',
							timestampFormat : 'Last update: %m%/%d%/%y% %h%:%i%:%s%',
							refreshButton : true,
							refreshButtonClass : 'fa fa-refresh',
							labelError : 'Sorry but there was a error:',
							labelUpdated : 'Last Update:',
							labelRefresh : 'Refresh',
							labelDelete : 'Delete widget:',
							afterLoad : function() {
							},
							rtl : false, // best not to toggle this!
							onChange : function() {

							},
							onSave : function() {

							},
							ajaxnav : $.navAsAjax
						});

	}

}
/*
 * SETUP DESKTOP WIDGET
 */
function setup_widgets_mobile() {

	if (enableMobileWidgets && enableJarvisWidgets) {
		setup_widgets_desktop();
	}

}
/* ~ END: LOAD SCRIPTS */
if ($.navAsAjax) {
	$(document).on('click','nav a[href!="#"]',function(e) {
		e.preventDefault();
		var $this = $(e.currentTarget);
		if (/* !$this.parent().hasClass("active") && */!$this.attr('target')) {
			if ($.root_.hasClass('mobile-view-activated')) {
				$.root_.removeClass('hidden-menu');
				$('html').removeClass("hidden-menu-mobile-lock");
				window.setTimeout(function() {
					if (window.location.search) {
						window.location.href = window.location.href.replace(
								window.location.search, '').replace(
								window.location.hash, '')
								+ '#' + $this.attr('href');
					} else {
						window.location.hash = $this.attr('href');
					}
				}, 150);
			} else {
				if (window.location.search) {
					window.location.href = window.location.href.replace(
							window.location.search, '').replace(window.location.hash,
							'')
							+ '#' + $this.attr('href');
				} else {
					window.location.hash = $this.attr('href');
				}
			}
		}
	});

	// fire links with targets on different window
	$(document).on('click', 'nav a[target="_blank"]', function(e) {
		e.preventDefault();
		var $this = $(e.currentTarget);
		window.open($this.attr('href'));
	});

	// fire links with targets on same window
	$(document).on('click', 'nav a[target="_top"]', function(e) {
		e.preventDefault();
		var $this = $(e.currentTarget);
		window.location = ($this.attr('href'));
	});

	// all links with hash tags are ignored
	$(document).on('click', 'nav a[href="#"]', function(e) {
		e.preventDefault();		
	});
	
	// DO on hash change
	$(window).on('hashchange', function() {
		checkURL();
	});
}
/*
 * CHECK TO SEE IF URL EXISTS
 */
function checkURL() {
	// get the url by removing the hash
	var url = location.href.split('#').splice(1).join('#');
	// BEGIN: IE11 Work Around
	if (!url) {
		try {
			var documentUrl = window.document.URL;
			if (documentUrl) {
				if (documentUrl.indexOf('#', 0) > 0
						&& documentUrl.indexOf('#', 0) < (documentUrl.length + 1)) {
					url = documentUrl.substring(documentUrl.indexOf('#', 0) + 1);
				}
			}
		} catch (err) {
		}
	}
	// END: IE11 Work Around
	if(!url && localStorage){
		url = localStorage.getItem('dashboard-cp-url');
		if(url){
			url = url.replace(/^#+/gm,'#');
			if(url.indexOf('#') == 0){
				location.href = location.href+url;
			}else{
				location.href = location.href+'#'+url;
			}
			return;
		}
	}	
	container = $('#content');	
	if (url) {
		// remove all active class
		$('nav li.active').removeClass("active");
		// match the url and add the active class
		var urls = url.split('/');
		var nave = $('nav li:has(a[href="' + url + '"])');
		var title = '';
		while(nave.length == 0 && urls.length > 1){
			urls.pop();
			nave = $('nav li:has(a[href="' + urls.join('/') + '/"])');
		}		
		if(nave.length > 0){
			nave.addClass("active");
			title = nave.attr('title');
		}
		// change page title from global var
		if(title){
			document.title = title + ' - '+ ($.siteName?$.siteName:'乌拉CMS');
		}
		loadURL(url + location.search, container);
	} else {
		// grab the first URL from nav
		var $this = $('nav > ul > li:first-child > a[href!="#"]');
		// update hash
		window.location.hash = $this.attr('href').replace(/^\//, '');
		// clear dom reference
		$this = null;
	}
}
/*
 * LOAD AJAX PAGES
 */
function loadURL(url, container) {
	url = url.replace(/^\//, '').replace(/^#+/gm,'');
	$.ajax({
				type : "GET",
				url : KissCms.AppURL + url,
				dataType : 'html',
				cache : true, 				
				beforeSend : function(jqXHR) {
					if(nUI && localStorage){
						localStorage.setItem('dashboard-cp-url',url);
					}
					// destroy all datatable instances
					if ($.navAsAjax && $('.dataTables_wrapper')[0]
							&& (container[0] == $("#content")[0])) {
						var tables = $.fn.dataTable.fnTables(true);
						$(tables).each(function() {
							if ($(this).find('.details-control').length != 0) {
								$(this).find('*').addBack().off().remove();
								$(this).dataTable().fnDestroy();
							} else {
								$(this).dataTable().fnDestroy();
							}
						});
					}
					// end destroy
					// pop intervals (destroys jarviswidget related intervals)
					if ($.navAsAjax && $.intervalArr.length > 0
							&& (container[0] == $("#content")[0])
							&& enableJarvisWidgets) {
						while ($.intervalArr.length > 0){
							clearInterval($.intervalArr.pop());
						}
					}
					// end pop intervals
					// destroy all widget instances
					if ($.navAsAjax && (container[0] == $("#content")[0])
							&& enableJarvisWidgets && $("#widget-grid")[0]) {
						$("#widget-grid").jarvisWidgets('destroy');
					}
					// end destroy all widgets					
					if ($.navAsAjax && (container[0] == $("#content")[0])) {
						if (typeof pagedestroy == 'function') {
							try {
								pagedestroy();
							} catch (err) {
								pagedestroy = undefined;
							}
						}
						// destroy all inline charts
						if ($.fn.sparkline && $("#content .sparkline")[0]) {
							$("#content .sparkline").sparkline('destroy');

						}
						if ($.fn.easyPieChart
								&& $("#content .easy-pie-chart")[0]) {
							$("#content .easy-pie-chart").easyPieChart(
									'destroy');
						}
						if ($.fn.select2 && $("#content select.select2")[0]) {
							$("#content select.select2").select2('destroy');
						}
						if ($.fn.mask && $('#content [data-mask]')[0]) {
							$('#content [data-mask]').unmask();
						}

						if ($.fn.datepicker && $('#content .datepicker')[0]) {
							$('#content .datepicker').off();
							$('#content .datepicker').remove();

						}

						if ($.fn.slider && $('#content .slider')[0]) {
							$('#content .slider').off();
							$('#content .slider').remove();
						}
						// end destroy form controls
					}
					// end cluster destroy				
					pagefunction = null;
					container.removeData().html("");
					// place cog
					//container.html('<h1 class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Loading...</h1>');
					$('#ribbon i.fa-refresh').addClass('fa-spin');
					// Only draw breadcrumb if it is main content material
					if (container[0] == $("#content")[0]) {						
						$('body').find('> *').filter(
								':not(' + ignore_key_elms + ')').empty()
								.remove();
						// draw breadcrumb
						drawBreadCrumb();
						// scroll up
						$("html").animate({
							scrollTop : 0
						}, "fast");
					}
					// end if
				},
				success : function(data) {
					$(window).trigger('unload-container');
					// dump data to container
					container.css({
						opacity : '0.0'
					}).html(data).delay(50).animate({
						opacity : '1.0'
					}, 300);
					container.applyNUI();
					// clear data var
					data = null;
					container = null;
					$('#ribbon i.fa-refresh').removeClass('fa-spin');
				},
				error : function(xhr, ajaxOptions, thrownError) {
					$(window).trigger('unload-container');
					var msg = checkResponse(xhr, true);
					container.html('<h4 class="ajax-loading-error"><i class="fa fa-warning txt-color-orangeDark"></i> Error!.</h4><div>'
									+ msg + '</div>');
					$('#ribbon i.fa-refresh').removeClass('fa-spin');
				},
				async : true
			});

}
/*
 * UPDATE BREADCRUMB
 */
function drawBreadCrumb(opt_breadCrumbs) {
	var a = $("nav li.active > a"), b = a.length;
	bread_crumb.empty(), a
			.each(function() {
				bread_crumb.append($("<li></li>").html(
						$.trim($(this).clone().children(".badge").remove()
								.end().text()))), --b
						|| (document.title = bread_crumb.find("li:last-child")
								.text() + ' - '+ ($.siteName?$.siteName:'乌拉CMS'))
			});	
	if (opt_breadCrumbs != undefined) {
		$.each(opt_breadCrumbs, function(index, value) {
			bread_crumb.append($("<li></li>").html(value));
			document.title = bread_crumb.find("li:last-child").text();
		});
	}
}
/* ~ END: APP AJAX REQUEST SETUP */
function pageSetUp(container) {
	container = container || $('body');
	if (thisDevice === "desktop") {		
		container.find("[rel=tooltip], [data-rel=tooltip]").tooltip();		
		container.find("[rel=popover], [data-rel=popover]").popover();		
		container.find("[rel=popover-hover], [data-rel=popover-hover]")
				.popover({
					trigger : "hover"
				});		
		setup_widgets_desktop();		
		runAllCharts(container);		
		runAllForms(container);
	} else {		
		container.find("[rel=popover], [data-rel=popover]").popover();
		// activate popovers with hover states
		container.find("[rel=popover-hover], [data-rel=popover-hover]").popover({
					trigger : "hover"
		});
		// activate inline charts
		runAllCharts(container);
		// setup widgets
		setup_widgets_mobile();
		// run form elements
		runAllForms(container);
	}
}
/* ~ END: PAGE SETUP */
$('body').on('click',function(e) {
	$('[rel="popover"], [data-rel="popover"]').each(function() {						
		if (!$(this).is(e.target)
				&& $(this).has(e.target).length === 0
				&& $('.popover').has(e.target).length === 0) {
			$(this).popover('hide');
		}
	});
});
/* ~ END: ONE POP OVER THEORY */
$('body').on('hidden.bs.modal', '.modal', function() {
	$(this).removeData('bs.modal');
});
/* ~ END: DELETE MODEL DATA ON HIDDEN */
function getParam(name) {
	name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(window.location.href);
	if (results == null)
		return "";
	else
		return results[1];
}
function tableHeightSize() {	
	if ($('body').hasClass('menu-on-top')) {
		var menuHeight = 68;
		var tableHeight = ($(window).height() - 180) - menuHeight;
		if (tableHeight < (320 - menuHeight)) {
			$('.table-wrap').css('height', (320 - menuHeight) + 'px');
		} else {
			$('.table-wrap').css('height', tableHeight + 'px');
		}
	} else {
		var tableHeight = $(window).height() - 180;
		if (tableHeight < 320) {
			$('.table-wrap').css('height', 320 + 'px');
		} else {
			$('.table-wrap').css('height', tableHeight + 'px');
		}
	}
}
/* ~ END: HELPFUL FUNCTIONS */