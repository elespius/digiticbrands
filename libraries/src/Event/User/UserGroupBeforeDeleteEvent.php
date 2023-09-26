<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Event\User;

use Joomla\CMS\Event\Model\BeforeDeleteEvent as ModelBeforeDeleteEvent;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Class for Model event.
 * Example:
 *  new UserGroupBeforeDeleteEvent('onEventName', ['context' => 'com_example.example', 'subject' => $itemObjectToDelete]);
 *
 * @since  __DEPLOY_VERSION__
 */
final class UserGroupBeforeDeleteEvent extends ModelBeforeDeleteEvent
{
    /**
     * The argument names, in order expected by legacy plugins.
     *
     * @var array
     *
     * @since  __DEPLOY_VERSION__
     * @deprecated 5.0 will be removed in 6.0
     *
     * @TODO: In Joomla 6 the event should use 'context', 'subject' only
     */
    protected $legacyArgumentsOrder = ['data', 'context', 'subject'];
}
