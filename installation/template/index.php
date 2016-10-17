<?php
/**
 * @package	Joomla.Installation
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var JDocumentHtml $this */

// Output as HTML5
$this->setHtml5(true);

// Add Stylesheets
JHtml::_('bootstrap.loadCss', true, $this->direction);
JHtml::_('stylesheet', 'installation/template/css/template.css');

// Load the JavaScript behaviors
JHtml::_('bootstrap.framework');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
JHtml::_('script', 'installation/template/js/installation.js');

// Load JavaScript message titles
JText::script('ERROR');
JText::script('WARNING');
JText::script('NOTICE');
JText::script('MESSAGE');

// Add strings for JavaScript error translations.
JText::script('JLIB_JS_AJAX_ERROR_CONNECTION_ABORT');
JText::script('JLIB_JS_AJAX_ERROR_NO_CONTENT');
JText::script('JLIB_JS_AJAX_ERROR_OTHER');
JText::script('JLIB_JS_AJAX_ERROR_PARSE');
JText::script('JLIB_JS_AJAX_ERROR_TIMEOUT');

// Load the JavaScript translated messages
JText::script('INSTL_PROCESS_BUSY');
JText::script('INSTL_FTP_SETTINGS_CORRECT');

// Add inline scripts
$this->addScriptDeclaration("
	jQuery(function()
	{
		// Delay instantiation after document.formvalidation and other dependencies loaded
		window.setTimeout(function(){
			window.Install = new Installation('container-installation', '" . JUri::current() . "');
		}, 500);
	});

	function initElements()
	{
		(function($){
			$('.hasTooltip').tooltip()

			// Chosen select boxes
			$('select').chosen({
				disable_search_threshold : 10,
				allow_single_deselect : true
			});

			// Turn radios into btn-group
			$('.radio.btn-group label').addClass('btn');

			$('fieldset.btn-group').each(function() {
				var \$self = $(this);
				// Handle disabled, prevent clicks on the container, and add disabled style to each button
				if (\$self.prop('disabled'))
				{
					\$self.css('pointer-events', 'none').off('click');
					\$self.find('.btn').addClass('disabled');
				}
			});

			$('.btn-group label:not(.active)').click(function()
			{
				var label = $(this);
				var input = $('#' + label.attr('for'));

				if (!input.prop('checked'))
				{
					label.closest('.btn-group').find('label').removeClass('active btn-success btn-danger btn-primary');

					if (label.closest('.btn-group').hasClass('btn-group-reverse'))
					{
						if (input.val() == '')
						{
							label.addClass('active btn-primary');
						}
						else if (input.val() == 0)
						{
							label.addClass('active btn-danger');
						}
						else
						{
							label.addClass('active btn-success');
						}
					}
					else
					{
						if (input.val() == '')
						{
							label.addClass('active btn-primary');
						}
						else if (input.val() == 0)
						{
							label.addClass('active btn-success');
						}
						else
						{
							label.addClass('active btn-danger');
						}
					}
					input.prop('checked', true);
				}
			});
			$('.btn-group input[checked=\'checked\']').each(function()
			{
				var \$self  = $(this);
				var attrId = \$self.attr('id');

				if (\$self.hasClass('btn-group-reverse'))
				{
					if (\$self.val() == '')
					{
						$('label[for=\'' + attrId + '\']').addClass('active btn-primary');
					}
					else if (\$self.val() == 0)
					{
						$('label[for=\'' + attrId + '\']').addClass('active btn-danger');
					}
					else
					{
						$('label[for=\'' + attrId + '\']').addClass('active btn-success');
					}
				}
				else
				{
					if (\$self.val() == '')
					{
						$('label[for=\'' + attrId + '\']').addClass('active btn-primary');
					}
					else if (\$self.val() == 0)
					{
						$('label[for=\'' + attrId + '\']').addClass('active btn-success');
					}
					else
					{
						$('label[for=\'' + attrId + '\']').addClass('active btn-danger');
					}
				}
			});
		})(jQuery);
	}
	initElements();
");
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
		<!--[if lt IE 9]><script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script><![endif]-->
	</head>
	<body data-basepath="<?php echo JUri::root(true); ?>">
		<!-- Header -->
		<div class="header">
			<img src="<?php echo $this->baseurl; ?>/template/images/joomla.png" alt="Joomla" />
			<hr />
			<h5>
				<?php // Fix wrong display of Joomla!® in RTL language ?>
				<?php $joomla  = '<a href="https://www.joomla.org" target="_blank">Joomla!</a><sup>' . (JFactory::getLanguage()->isRtl() ? '&#x200E;' : '') . '</sup>'; ?>
				<?php $license = '<a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank">' . JText::_('INSTL_GNU_GPL_LICENSE') . '</a>'; ?>
				<?php echo JText::sprintf('JGLOBAL_ISFREESOFTWARE', $joomla, $license); ?>
			</h5>
		</div>
		<!-- Container -->
		<div class="container">
			<jdoc:include type="message" />
			<div id="javascript-warning">
				<noscript>
					<div class="alert alert-error">
						<?php echo JText::_('INSTL_WARNJAVASCRIPT'); ?>
					</div>
				</noscript>
			</div>
			<div id="container-installation">
				<jdoc:include type="component" />
			</div>
			<hr />
		</div>
	</body>
</html>
