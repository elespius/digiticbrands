<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.redirect
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

/**
 * Plugin class for redirect handling.
 *
 * @since  1.6
 */
class PlgSystemRedirect extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  3.4
	 */
	protected $autoloadLanguage = true;

	/**
	 * The global exception handler registered before the plugin was instantiated
	 *
	 * @var    callable
	 * @since  3.6
	 */
	private static $previousExceptionHandler;

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Set the JError handler for E_ERROR to be the class' handleError method.
		JError::setErrorHandling(E_ERROR, 'callback', array('PlgSystemRedirect', 'handleError'));

		// Register the previously defined exception handler so we can forward errors to it
		self::$previousExceptionHandler = set_exception_handler(array('PlgSystemRedirect', 'handleException'));
	}

	/**
	 * Method to handle an error condition from JError.
	 *
	 * @param   JException  $error  The JException object to be handled.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function handleError(JException $error)
	{
		self::doErrorHandling($error);
	}

	/**
	 * Method to handle an uncaught exception.
	 *
	 * @param   Exception|Throwable  $exception  The Exception or Throwable object to be handled.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 * @throws  InvalidArgumentException
	 */
	public static function handleException($exception)
	{
		// If this isn't a Throwable then bail out
		if (!($exception instanceof Throwable) && !($exception instanceof Exception))
		{
			throw new InvalidArgumentException(
				sprintf('The error handler requires an Exception or Throwable object, a "%s" object was given instead.', get_class($exception))
			);
		}

		self::doErrorHandling($exception);
	}

	/**
	 * Internal processor for all error handlers
	 *
	 * @param   Exception|Throwable  $error  The Exception or Throwable object to be handled.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	private static function doErrorHandling($error)
	{
		$app = JFactory::getApplication();

		if ($app->isClient('administrator') || ((int) $error->getCode() !== 404))
		{
			// Proxy to the previous exception handler if available, otherwise just render the error page
			if (self::$previousExceptionHandler)
			{
				call_user_func_array(self::$previousExceptionHandler, array($error));
			}
			else
			{
				JErrorPage::render($error);
			}
		}

		$uri = JUri::getInstance();

		$url = StringHelper::strtolower(rawurldecode($uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment'))));
		$urlRel = StringHelper::strtolower(rawurldecode($uri->toString(array('path', 'query', 'fragment'))));

		$urlWithoutQuery = StringHelper::strtolower(rawurldecode($uri->toString(array('scheme', 'host', 'port', 'path', 'fragment'))));
		$urlRelWithoutQuery = StringHelper::strtolower(rawurldecode($uri->toString(array('path', 'fragment'))));

		// Why is this (still) here?
		if ((strpos($url, 'mosConfig_') !== false) || (strpos($url, '=http://') !== false))
		{
			JErrorPage::render($error);
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__redirect_links'))
			->where(
				'('
				. $db->quoteName('old_url') . ' = ' . $db->quote($url)
				. ' OR '
				. $db->quoteName('old_url') . ' = ' . $db->quote($urlRel)
				. ' OR '
				. $db->quoteName('old_url') . ' = ' . $db->quote($urlWithoutQuery)
				. ' OR '
				. $db->quoteName('old_url') . ' = ' . $db->quote($urlRelWithoutQuery)
				. ')'
			);

		$db->setQuery($query);

		$redirect = null;

		try
		{
			$redirects = $db->loadAssocList();
		}
		catch (Exception $e)
		{
			JErrorPage::render(new Exception(JText::_('PLG_SYSTEM_REDIRECT_ERROR_UPDATING_DATABASE'), 500, $e));
		}

		$possibleMatches = array_unique(
			array($url, $urlRel, $urlWithoutQuery, $urlRelWithoutQuery)
		);

		foreach ($possibleMatches as $match)
		{
			if (($index = array_search($match, array_column($redirects, 'old_url'))) !== false)
			{
				$redirect = (object) $redirects[$index];

				if ((int) $redirect->published === 1)
				{
					break;
				}
			}
		}

		// A redirect object was found and, if published, will be used
		if (!is_null($redirect) && ((int) $redirect->published === 1))
		{
			if (!$redirect->header || (bool) JComponentHelper::getParams('com_redirect')->get('mode', false) === false)
			{
				$redirect->header = 301;
			}

			if ($redirect->header < 400 && $redirect->header >= 300)
			{
				$urlQuery = $uri->getQuery();

				$oldUrlParts = parse_url($redirect->old_url);

				if (empty($oldUrlParts['query']) && $urlQuery !== '')
				{
					$redirect->new_url .= '?' . $urlQuery;
				}

				$destination = JUri::isInternal($redirect->new_url) ? JRoute::_($redirect->new_url) : $redirect->new_url;

				$app->redirect($destination, (int) $redirect->header);
			}

			JErrorPage::render(new RuntimeException($error->getMessage(), $redirect->header, $error));
		}
		// No redirect object was found so we create an entry in the redirect table
		elseif (is_null($redirect))
		{
			$params = new Registry(JPluginHelper::getPlugin('system', 'redirect')->params);

			if ((bool) $params->get('collect_urls', true))
			{
				$data = (object) array(
					'id' => 0,
					'old_url' => $url,
					'referer' => $app->input->server->getString('HTTP_REFERER', ''),
					'hits' => 1,
					'published' => 0,
					'created_date' => JFactory::getDate()->toSql()
				);

				try
				{
					$db->insertObject('#__redirect_links', $data, 'id');
				}
				catch (Exception $e)
				{
					JErrorPage::render(new Exception(JText::_('PLG_SYSTEM_REDIRECT_ERROR_UPDATING_DATABASE'), 500, $e));
				}
			}
		}
		// We have an unpublished redirect object, increment the hit counter
		else
		{
			$redirect->hits += 1;

			try
			{
				$db->updateObject('#__redirect_links', $redirect, 'id');
			}
			catch (Exception $e)
			{
				JErrorPage::render(new Exception(JText::_('PLG_SYSTEM_REDIRECT_ERROR_UPDATING_DATABASE'), 500, $e));
			}
		}

		JErrorPage::render($error);
	}

	/**
	 * Check if menu item alias has been changed. If so, display a message that suggests
	 * to add the old alias to the redirection table to automatically redirect the old url
	 * to the new one.
	 *
	 * @param   string   $context   The context of the content passed to the plugin (added in 1.6)
	 * @param   object   $menuItem  A JTableContent object
	 * @param   boolean  $isNew     If the content is just about to be created
	 *
	 * @return  boolean   true if function not enabled, is in frontend or is new. Else true or
	 *                    false depending on success of save function.
	 *
	 * @since   1.6
	 */
	public function onContentbeforeSave($context, $menuItem, $isNew)
	{
		if (!$isNew && $context == 'com_menus.item' && $this->params->get('watch_alias'))
		{
			$menu = & JSite::getMenu();
			$old_alias = $menu->getItem($menuItem->id)->alias;

			if ($menuItem->alias != $old_alias)
			{
				$link = JRoute::_('index.php?option=com_redirect&view=link&layout=edit&old_alias=' . $old_alias . '&new_alias=' . $menuItem->alias);
				$message = JText::sprintf('PLG_SYSTEM_REDIRECT_WATCH_ALIAS_WARNING', $link);
				JFactory::getApplication()->enqueueMessage($message, 'warning');
			}
		}

		return true;
	}

	/**
	 * Adds default values to the redirection editing form
	 *
	 * @param   JForm  $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		// Get default values from GET params
		$app = JFactory::getApplication();

		if ($form->getName() == 'com_redirect.link'
			&& empty($data->id)
			&& $app->input->getString('old_alias', '')
			&& $app->input->getString('new_alias', ''))
		{
			$data->old_url = JURI::root() . $app->input->getString('old_alias');
			$data->new_url = JURI::root() . $app->input->getString('new_alias');
		}

		return true;
	}
}
