<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Saves the start time and memory usage.
$startTime = microtime(1);
$startMem  = memory_get_usage();

if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	include_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

/** @var \Joomla\DI\Container $container */
$container = require_once JPATH_LIBRARIES . '/container.php';

// Set profiler start time and memory usage and mark afterLoad in the profiler.
JDEBUG ? JProfiler::getInstance('Application')->setStart($startTime, $startMem)->mark('afterLoad') : null;

// Get the application from the container
$app = $container->get('SiteApplication');

// Execute the application.
$app->execute();
