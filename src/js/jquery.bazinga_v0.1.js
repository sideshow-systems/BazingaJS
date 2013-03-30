/*
 *  Project: BazingaJS
 *  Description: Check the source of a webpage quick and painless
 *  Author: Florian Binder <fb@sideshow-systems.de>
 *  License: TODO: add me!
 */

;
(function($, window, document, undefined) {

	var base = null;
	var pluginName = "bazinga";
	var defaults = {
		parentEl: "body",
		validateOnLoad: false,
		serverSide: null,
		ajaxType: 'post',
		showOutputPanelOnValid: false
	};

	// The actual plugin constructor
	function Plugin(element, options) {
		this.element = element;

		this.options = $.extend({}, defaults, options);

		this._defaults = defaults;
		this._name = pluginName;

		base = this;

		this.init();
	}

	Plugin.prototype = {
		// init method
		init: function() {
			// cancel everything if path to server side is not defined!
			if (this.options.serverSide === null || this.options.serverSide === undefined) {
				jQuery.error('Please set an url to the server side!');
				return false;
			}

			// create the bazinga dom container and append it
			var $baz = $('<div id="bazinga_container" />').appendTo($(this.options.parentEl));
			this.element.controller = $('<div class="controller"><span>Bazinga</span></div>').appendTo($baz);
			this.element.outp_panel = $('<div class="outp_panel" />').appendTo($baz);

			// add click event to controller
			this.element.controller.click(function() {
				base.validate(base.element, base.options);
			});

			// add hover to controller - #1 maybe we add this later...
//			this.element.controller.hover(
//				function() {
//					base.showOutputPanel(base.element.outp_panel);
//				},
//				function() {
//					base.hideOutputPanel(base.element.outp_panel);
//				}
//			);

			// trigger validation if validateOnLoad is true
			if (this.options.validateOnLoad) {
				this.validate(this.element, this.options);
			}
		},
		// show output panel
		showOutputPanel: function(panel) {
			panel.animate({
				opacity: 1,
				top: '60px'
			}, 400);
		},
		// hide output panel
		hideOutputPanel: function(panel) {
			panel.animate({
				opacity: 0,
				top: '100px'
			}, 100, function() {
				$(this).css('top', '80px');
			});
		},
		// validate the current page
		validate: function(el, options) {
			// remove some classes
			el.controller.removeClass('respcode_0').removeClass('respcode_1').removeClass('respcode_2');

			// hide output panel
			this.hideOutputPanel(el.outp_panel);

			// do ajax call to server side
			$.ajax({
				url: options.serverSide,
				type: options.ajaxType,
				data: {
					url2validate: this.getBrowserUrl()
				},
				beforeSend: function() {
					el.controller.addClass('loading');
				},
				success: function(resp, status) {
					el.outp_panel.html('');
					if (resp.length > 0) {
						var data = jQuery.parseJSON(resp);
//						console.log(data);

						// set cnt data to output panel
						el.outp_panel.append('<div class="cnt"><span class="warnings">Warnings: <strong>' + data.cnt_warning + '</strong></span><span class="errors">Errors: <strong>' + data.cnt_error + '</strong></span></div>');

						// set info data
						jQuery.each(data.info, function(key, value) {
							value.line = value.line.replace('Warning:', '<span class="respcode_1">Warning:</span>');
							value.line = value.line.replace('Error:', '<span class="respcode_2">Error:</span>');
							el.outp_panel.append('<pre>' + value.line + '</pre>');
							if (value.htmlrel !== null) {
								el.outp_panel.append('<p class="line_data">' + value.htmlrel + '</p>');
							}
						});

						// set class of controller based on response code
						el.controller.addClass('respcode_' + data.status);

						// set class of output panel based on response code
						el.outp_panel.addClass('respcode_' + data.status);

						// show output panel
						var showOutputPanel = true;
						if (data.status === 0 && !options.showOutputPanelOnValid) {
							showOutputPanel = false;
						}

						if (showOutputPanel) {
							base.showOutputPanel(el.outp_panel);
						}
					} else {
						jQuery.error('No data from server. Please check serverSide param!');
					}
				},
				complete: function() {
					el.controller.removeClass('loading');
				}
			});
		},
		// get current browser url
		getBrowserUrl: function() {
			return document.URL;
		}
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function(options) {
		return this.each(function() {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName, new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);