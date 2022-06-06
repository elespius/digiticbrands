<?php
/**
 * @package     Joomla.Plugins
 * @subpackage  System.shortcut
 *
 * @copyright   (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

Text::script('PLG_SYSTEM_SHORTCUT_CANCEL');
Text::script('PLG_SYSTEM_SHORTCUT_CONFIRM_SHORTCUT');
Text::script('PLG_SYSTEM_SHORTCUT_CURRENT_COMBINATION');
Text::script('PLG_SYSTEM_SHORTCUT_DESCRIPTION');
Text::script('PLG_SYSTEM_SHORTCUT_NEW_COMBINATION');
Text::script('PLG_SYSTEM_SHORTCUT_SET_SHORTCUT');

$app = Factory::getApplication();
$wa = $app->getDocument()->getWebAssetManager();
if (!$wa->assetExists('script', 'keyselectmodal')) {
	$document = $app->getDocument();
	$wa->registerScript('keyselectmodal', 'media/plg_system_shortcut/js/keyselect.js', [], ['defer' => true, 'type' => 'module']);
}
$wa->useScript('keyselectmodal')
	->useScript('bootstrap.modal');

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $options         Options available for this field.
 */
?>
<input type="hidden" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>" />
<button id="<?php echo $id; ?>_btn" data-bs-toggle="modal" data-bs-target="#keySelectModal" class="btn btn-secondary <?php echo $class; ?>" type="button"><?php echo $value; ?></button>
