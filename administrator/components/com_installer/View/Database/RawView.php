<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Installer\Administrator\View\Database;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Installer\Administrator\Model\DatabaseModel;

/**
 * Class view to download the database snapshot.
 *
 * @since  __DEPLOY_VERSION__
 */
class RawView extends BaseHtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null): void
	{
		/** @var CMSApplication $app */
		$app = Factory::getApplication();

		/** @var DatabaseModel $model */
		$model = $this->getModel();

		// Send the exporter archive to the browser as a download
		$zipFile = $model->getZipFilename();
		$download = 'joomla_db-' . date("Y-m-d\TH-i-s") . '.zip';

		$this->document->setMimeEncoding('application/zip');

		$app->setHeader(
			'Content-disposition',
			'attachment; filename="' . $download . '"',
			true
		)
			->setHeader('Content-Length', filesize($zipFile), true)
			->sendHeaders();

		ob_end_clean();
		readfile($zipFile);
		flush();
		unlink($zipFile);
	}
}
