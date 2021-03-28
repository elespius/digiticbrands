<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_csp
 *
 * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Csp\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

/**
 * Csp Controller
 *
 * @since  4.0.0
 */
class ReportController extends BaseController
{
	/**
	 * The list of valid directives based on: https://www.w3.org/TR/CSP3/#csp-directives
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	private $validDirectives = [
		'child-src',
		'connect-src',
		'default-src',
		'font-src',
		'frame-src',
		'img-src',
		'manifest-src',
		'media-src',
		'prefetch-src',
		'object-src',
		'script-src',
		'script-src-elem',
		'script-src-attr',
		'style-src',
		'style-src-elem',
		'style-src-attr',
		'worker-src',
		'base-uri',
		'plugin-types',
		'sandbox',
		'form-action',
		'frame-ancestors',
		'navigate-to',
		'report-uri',
		'report-to',
		'block-all-mixed-content',
		'upgrade-insecure-requests',
		'require-sri-for',
	];

	/**
	 * Log the CSP request
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function log()
	{
		$params = $this->app->getParams();

		$cspEnabled    = (int) $params->get('contentsecuritypolicy', 0);
		$cspClient     = (string) $params->get('contentsecuritypolicy_client', 'site');
		$mode          = (string) $params->get('contentsecuritypolicy_mode', 'report');
		$reportEnabled = $mode == 'report' || $params->get('report_enabled', 0);

		// Make sure we we are in detect mode and csp is active
		if (!($cspEnabled && ($this->app->isClient($cspClient) || $cspClient === 'both') && $reportEnabled))
		{
			$this->app->close();
		}

		$data = $this->input->json->get('csp-report', [], 'Array');

		// No data has been passed
		if (empty($data))
		{
			$this->app->close();
		}

		$report = new \stdClass;

		// Make sure the client reported is enabled to get reports.
		$report->client = $this->app->getInput()->get('client', false);

		// Make sure the client is passed and has an valid value
		if ($report->client === false || !in_array($report->client, ['site', 'administrator']))
		{
			$this->app->close();
		}

		// Make sure the client reported is enabled to get reports.
		$configuredCspClient = Factory::getApplication()->getParams()->get('contentsecuritypolicy_client', 'site');

		if ($report->client !== $configuredCspClient && $configuredCspClient !== 'both')
		{
			$this->app->close();
		}

		// Check the document-uri field
		$documentUri = (string) ArrayHelper::getValue($data, 'document-uri');

		// Make sure the document-uri is a valid url
		if (filter_var($documentUri, FILTER_VALIDATE_URL) === false)
		{
			$this->app->close();
		}

		$parsedDocumentUri = Uri::getInstance($documentUri);
		$report->document_uri = $parsedDocumentUri->toString();

		// Check the blocked-uri field
		$blockedUri = (string) ArrayHelper::getValue($data, 'blocked-uri');
		$report->blocked_uri = false;

		// Check for "eval" or "inline" lets make sure they get reported in the correct way
		if (in_array($blockedUri, ['eval', 'inline']))
		{
			$report->blocked_uri = 'unsafe-' . $blockedUri;
		}

		// Handle data reports correctly
		if ($blockedUri === 'data')
		{
			$report->blocked_uri = 'data:';
		}

		// The blocked-uri is not a special keyword but an valid URL.
		if ($report->blocked_uri === false && filter_var($blockedUri, FILTER_VALIDATE_URL) !== false)
		{
			$parsedBlockedUri = Uri::getInstance($blockedUri);
			$report->blocked_uri = $parsedBlockedUri->toString(['scheme', 'host', 'port']);
		}

		// The blocked-uri is not a valid URL an not an special keyword
		if ($report->blocked_uri === false)
		{
			$this->app->close();
		}

		// Check the violated-directive && effective-directive fields
		$report->directive = $this->cleanReportDirective((string) ArrayHelper::getValue($data, 'violated-directive', ''));
		$effectiveDirective = $this->cleanReportDirective((string) ArrayHelper::getValue($data, 'effective-directive', ''));

		// Fallback to the effective-directive when the violated-directive is not set.
		if ($report->directive === false && $effectiveDirective !== false)
		{
			$report->directive = $effectiveDirective;
		}

		// We have an unknown or invalid directive
		if ($report->directive === false)
		{
			$this->app->close();
		}

		$report->script_sample = ArrayHelper::getValue($data, 'script-sample', '');
		$report->line_number = ArrayHelper::getValue($data, 'line-number', 0, 'int');
		$report->column_number = ArrayHelper::getValue($data, 'column-number', 0, 'int');

		$now = Factory::getDate()->toSql();

		$report->created  = $now;
		$report->modified = $now;

		$db = Factory::getDbo();

		$db->lockTable('#__csp');

		if ($this->isEntryExisting($report))
		{
			$db->unlockTables();

			$this->app->close();
		}

		$table = $this->app->bootComponent('com_csp')->getMVCFactory()->createTable('Report', 'Administrator');

		$table->bind($report);
		$table->store();

		$db->unlockTables();

		$this->app->close();
	}

	/**
	 * Check if we already logged this entry
	 *
	 * @param   object  $report  The generated report row
	 *
	 * @return  boolean
	 *
	 * @since   4.0.0
	 */
	private function isEntryExisting($report)
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true);

		$query
			->select('COUNT(*)')
			->from($db->quoteName('#__csp'))
			->where($db->quoteName('script_sample') . ' = :script_sample')
			->where($db->quoteName('blocked_uri') . ' = :blocked_uri')
			->where($db->quoteName('directive') . ' = :directive')
			->where($db->quoteName('client') . ' = :client')
			->bind(':script_sample', $report->script_sample)
			->bind(':blocked_uri', $report->blocked_uri)
			->bind(':directive', $report->directive)
			->bind(':client', $report->client);

		$db->setQuery($query);

		try
		{
			$result = (int) $db->loadResult();
		}
		catch (\RuntimeException $e)
		{
			return false;
		}

		return $result > 0;
	}

	/**
	 * Clean the directive where browsers do append more stuff than we don't need
	 *
	 * @param   object  $reportedDirective  The directive from the browsers.
	 *
	 * @return  mixed
	 *
	 * @since   4.0.0
	 */
	private function cleanReportDirective($reportedDirective)
	{
		// Explode the reported directive (e.g. "default-src 'self'") by space.
		$explodeDirective = explode(' ', $reportedDirective);

		// Note: Directive names are case-insensitive, that is: script-SRC 'none' and ScRiPt-sRc 'none' are equivalent.
		$cleandedDirective = strtolower($explodeDirective[0]);

		// Make sure this is a valid directive.
		if (!in_array($cleandedDirective, $this->validDirectives))
		{
			return false;
		}

		// Return the validated directive
		return $cleandedDirective;
	}
}
