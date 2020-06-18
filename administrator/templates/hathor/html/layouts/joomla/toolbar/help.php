<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Template.hathor
 *
 * @copyright   © 2013 Open Source Matters, Inc. <https://www.joomla.org/contribute-to-joomla.html>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$doTask = $displayData['doTask'];
$text   = $displayData['text'];

?>

<a href="javascript:void(0)" onclick="<?php echo $doTask; ?>" rel="help" class="toolbar">
	<span class="icon-32-help"></span>
	<?php echo $text; ?>
</a>
