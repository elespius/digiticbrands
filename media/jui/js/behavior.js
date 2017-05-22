/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Common behaviors
 */
!(function(Joomla){
	'use strict';

	/**
	 * Image captions
	 *
	 * @param JoomlaEvent event Event object where:
	 *
	 * 		event.name    Name of active event
	 * 		event.target  Affected DOM container
	 * 		event.options Array of selectors
	 */
	Joomla.Behavior.add('caption', 'load update', function(event){
		var selector;

		for (var i = 0, l = event.options.length; i < l; i++ ) {
			selector = event.options[i];
			new JCaption(selector);
		}
	}, true);

	/**
	 * Submenu switcher
	 *
	 * @param JoomlaEvent event Event object where:
	 *
	 * 		event.name    Name of active event
	 * 		event.target  Affected DOM container
	 */
	Joomla.Behavior.add('switcher', 'ready', function(event){
		document.switcher = null;
		var toggler = document.getElementById('submenu');
		var element = document.getElementById('config-document');
		if (element) {
			document.switcher = new JSwitcher(toggler, element);
		}
	}, true);

	/**
	 * Support for a hover tooltips.
	 *
	 * @TODO: is it still used somwhere ?
	 */
	//Joomla.Behavior.add('tooltip', 'ready update', function(event){});

	/**
	 * Modal
	 *
	 * @param JoomlaEvent event Event object where:
	 *
	 * 		event.name    Name of active event
	 * 		event.target  Affected DOM container
	 * 		event.options Possible options, in format {selector1 : options1, selector2 : options2}
	 */
	Joomla.Behavior.add('modal', 'ready update', function(event){
		var $target = jQuery(event.target), options;

		if (!window.jModalClose) {
    		SqueezeBox.initialize({});

    		window.jModalClose = function () {
    			SqueezeBox.close();
    		}
		}

		for (var selector in event.options) {
			// Prepare options
			options = event.options[selector] || {};
			options.parse = options.parse ? options.parse : 'rel';

			if (options.fullScreen){
				options.size = {
					x: jQuery(window).width() - 80,
					y: jQuery(window).height() - 80
				};
			}

			SqueezeBox.assign($target.find(selector).get(), options);
		}
	}, true);

	/**
	 * Behavior to allow shift select in grids
	 *
	 * @param JoomlaEvent event Event object where:
	 *
	 * 		event.name    Name of active event
	 * 		event.target  Affected DOM container
	 * 		event.options Array of selectors
	 */
	Joomla.Behavior.add('multiselect', 'ready', function(event){
		var selector;

		for (var i = 0, l = event.options.length; i < l; i++ ) {
			selector = event.options[i];
			Joomla.JMultiSelect(selector);
		}
	}, true);

	/**
	 * Support for a collapsible tree
	 *
	 * @TODO: is it still used somwhere ?
	 */
	//Joomla.Behavior.add('tree', 'ready update', function(event){});

	/**
	 * Color picker
	 *
	 * @param JoomlaEvent event Event object where:
	 *
	 * 		event.name    Name of active event
	 * 		event.target  Affected DOM container
	 * 		event.options Possible options, in format {selector1 : options1, selector2 : options2}
	 */
	Joomla.Behavior.add('colorpicker', 'ready update', function(event){
		var $target = jQuery(event.target), options;

		for (var selector in event.options) {
			options = event.options[selector] || {};
			options.theme = options.theme || 'bootstrap';

			$target.find(selector).each(function() {
				options.control  = jQuery(this).attr('data-control') || options.control || 'hue';
				options.position = jQuery(this).attr('data-position') || options.position || 'right';

    			jQuery(this).minicolors(options);
    		});
		}
	}, true);

	Joomla.Behavior.add('colorpicker', 'remove', function(event){
		jQuery(event.target).find('.minicolors-input').minicolors('destroy');
	});

	/**
	 * Simple color picker
	 *
	 * @param JoomlaEvent event Event object where:
	 *
	 * 		event.name    Name of active event
	 * 		event.target  Affected DOM container
	 * 		event.options Array of selectors
	 */
	Joomla.Behavior.add('simplecolorpicker', 'ready update', function(event){
		var $target = jQuery(event.target),
			selector = event.options.join(', ');

		$target.find(selector).simplecolors();
	}, true);

	/**
	 * Highlight some words via Javascript
	 *
	 * @param JoomlaEvent event Event object where:
	 *
	 * 		event.name    Name of active event
	 * 		event.target  Affected DOM container
	 * 		event.options Array of possible options
	 */
	Joomla.Behavior.add('highlighter', 'ready update', function(event){
		var options;

		if (!Joomla.Highlighter) {
			return;
		}

		for (var i = 0, l = event.options.length; i < l; i++ ) {
			options = event.options[i];
			options.start = options.start || 'highlighter-start';
			options.end   = options.end || 'highlighter-end';
			options.tag   = options.tag || 'span';
			options.className = options.className || 'highlight';

			var start = document.getElementById(options.start);
			var end = document.getElementById(options.end);
			if (!start || !end || !options.terms) {
				continue;
			}

			new Joomla.Highlighter({
				startElement: start,
				endElement: end,
				className: options.className,
				onlyWords: false,
				tag: options.tag,
			}).highlight(options.terms);
			$(start).remove();
			$(end).remove();
		}
	}, true);

})(Joomla);
