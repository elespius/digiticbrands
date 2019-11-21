<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Installer.urlinstaller
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbuttonurl = function() {
		var form = document.getElementById("adminForm");

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://" || form.install_url.value == "https://") {
			alert("' . JText::_('PLG_INSTALLER_URLINSTALLER_NO_URL', true) . '");
		} else {
			JoomlaInstaller.showLoading();
			form.installtype.value = "url";
			form.submit();
		}
	};
');
?>
<legend><?php echo JText::_('PLG_INSTALLER_URLINSTALLER_TEXT'); ?></legend>
<div class="control-group">
	<label class="control-label" for="install_url"><?php echo JText::_('PLG_INSTALLER_URLINSTALLER_TEXT'); ?></label>
	<div class="controls">
		<input id="install_url" class="span5 input_box" type="text" name="install_url" size="70" placeholder="https://" />
	</div>
</div>
<div class="form-actions">
	<input id="installbutton_url" class="btn btn-primary" type="button" value="<?php echo JText::_('PLG_INSTALLER_URLINSTALLER_BUTTON'); ?>" onclick="Joomla.submitbuttonurl()" />
</div>
