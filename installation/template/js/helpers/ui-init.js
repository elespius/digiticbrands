/**
 * @package Joomla.Installation
 * @subpackage JavaScript
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters. All rights
 *            reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

define([ "jquery", "bootstrap", "chosen", "domready" ], function($, Bootstrap, Chosen, domReady) {
	
	domReady(function () {
		$('*[rel=tooltip]').tooltip();
		$('*[rel=popover]').popover();
	
		// Chosen select boxes
		$("select").chosen({
			disable_search_threshold : 10,
			allow_single_deselect : true
		});
	
		// Turn radios into btn-group
		$('.radio.btn-group label').addClass('btn');
		$(".btn-group label:not(.active)").click(
				function() {
					var label = $(this);
					var input = $('#' + label.attr('for'));
	
					if (!input.prop('checked')) {
						label.closest('.btn-group').find("label").removeClass(
								'active btn-success btn-danger');
						if (input.val() === 0) {
							label.addClass('active btn-danger');
						} else {
							label.addClass('active btn-success');
						}
						input.prop('checked', true);
					}
				});
		$(".btn-group input[checked=checked]").each(function() {
			var label = $("label[for=" + $(this).attr('id') + "]");
			if ($(this).val() === 0) {
				label.addClass('active btn-danger');
			} else {
				label.addClass('active btn-success');
			}
		});
	});

});