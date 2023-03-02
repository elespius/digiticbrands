<?php

/**
 * @package       Joomla.Administrator
 * @subpackage    com_guidedtours
 *
 * @copyright     (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guidedtours\Administrator\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Component Controller
 *
 * @since __DEPLOY_VERSION__
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var   string
     * @since __DEPLOY_VERSION__
     */
    protected $default_view = 'tours';

    /**
     * Method to display a view.
     *
     * @param   boolean $cachable  If true, the view output will be cached
     * @param   array   $urlparams An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  static |boolean  This object to support chaining. False on failure.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function display($cachable = false, $urlparams = [])
    {
        $view   = $this->input->get('view', $this->default_view);
        $layout = $this->input->get('layout', 'default');
        $id     = $this->input->getInt('id');

        if ($view === 'tour' && $layout === 'edit' && !$this->checkEditId('com_guidedtours.edit.tour', $id)) {
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            $this->setRedirect(Route::_('index.php?option=com_guidedtour&view=tours', false));

            return false;
        }

        if ($view === 'step' && $layout === 'edit' && !$this->checkEditId('com_guidedtours.edit.step', $id)) {
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            $this->setRedirect(Route::_('index.php?option=com_guidedtour&view=steps', false));

            return false;
        }

        return parent::display();
    }
}
