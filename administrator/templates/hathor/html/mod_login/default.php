<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Template.hathor
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login">
	<fieldset class="loginform">

		<label id="mod-login-username-lbl" for="mod-login-username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
		<input name="username" id="mod-login-username" type="text" size="15" autofocus="true" />

		<label id="mod-login-password-lbl" for="mod-login-password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
		<input name="passwd" id="mod-login-password" type="password" size="15" />
		<?php if (count($twofactormethods) > 1): ?>
			<div class="control-group">
				<div class="controls">
					<label for="mod-login-secretkey">
						<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>
					</label>
					<input name="secretkey" autocomplete="off" tabindex="3" id="mod-login-secretkey" type="text" class="input-medium" size="15"/>
				</div>
			</div>
		<?php endif; ?>
		<?php if (count($extraFields)):
			$extraFieldCounter = 0;
			/** @var JAuthenticationFieldInterface $extraField */
			foreach ($extraFields as $extraField):
				if ($extraField->getType() != 'field') continue;
				?>
				<div id="form-login-extrafield-<?php echo ++$extraFieldCounter ?>" class="control-group">
					<div class="controls">
						<label><?php echo $extraField->getLabel(); ?></label>
						<?php echo $extraField->getInput(); ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if (!empty ($langs)) : ?>
			<label id="mod-login-language-lbl" for="lang"><?php echo JText::_('MOD_LOGIN_LANGUAGE'); ?></label>
			<?php echo $langs; ?>
		<?php endif; ?>

		<div class="clr"></div>

		<div class="button-holder">
			<div class="button1">
				<div class="next">
					<a href="#" onclick="document.getElementById('form-login').submit();">
						<?php echo JText::_('MOD_LOGIN_LOGIN'); ?></a>
				</div>
			</div>
			<?php if (count($extraFields)):
				$extraFieldCounter = 0;
				/** @var JAuthenticationFieldInterface $extraField */
				foreach ($extraFields as $extraField):
					if ($extraField->getType() != 'button') continue;
					?>
					<div class="button1">
						<div class="next">
							<a id="form-login-button-<?php echo ++$extraFieldCounter ?>" href="<?php echo $extraField->getInput() ?>">
								<?php if ($extraField->getIcon()): ?>
									<span class="<?php echo $extraField->getIcon() ?>"></span>
								<?php endif; ?>
								<?php echo $extraField->getLabel(); ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div class="clr"></div>
		<input type="submit" class="hidebtn" value="<?php echo JText::_('MOD_LOGIN_LOGIN'); ?>" />
		<input type="hidden" name="option" value="com_login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
