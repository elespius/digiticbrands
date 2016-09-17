<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Application\Cli\CliOutput;
use Joomla\Cms\Application\Autoconfigurable;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;

/**
 * Base class for a Joomla! command line application.
 *
 * @since  11.4
 * @note   As of 4.0 this class will be abstract
 */
class JApplicationCli extends JApplicationBase
{
	use Autoconfigurable;

	/**
	 * @var    CliOutput  The output type.
	 * @since  3.3
	 */
	protected $output;

	/**
	 * @var    JApplicationCli  The application instance.
	 * @since  11.1
	 */
	protected static $instance;

	/**
	 * Class constructor.
	 *
	 * @param   JInputCli            $input       An optional argument to provide dependency injection for the application's
	 *                                            input object.  If the argument is a JInputCli object that object will become
	 *                                            the application's input object, otherwise a default input object is created.
	 * @param   Registry             $config      An optional argument to provide dependency injection for the application's
	 *                                            config object.  If the argument is a Registry object that object will become
	 *                                            the application's config object, otherwise a default config object is created.
	 * @param   DispatcherInterface  $dispatcher  An optional argument to provide dependency injection for the application's
	 *                                            event dispatcher.  If the argument is a DispatcherInterface object that object will become
	 *                                            the application's event dispatcher, if it is null then the default event dispatcher
	 *                                            will be created based on the application's loadDispatcher() method.
	 *
	 * @see     JApplicationBase::loadDispatcher()
	 * @since   11.1
	 */
	public function __construct(JInputCli $input = null, Registry $config = null, DispatcherInterface $dispatcher = null)
	{
		// Close the application if we are not executed from the command line.
		// @codeCoverageIgnoreStart
		if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			$this->close();
		}
		// @codeCoverageIgnoreEnd

		// If an input object is given use it.
		if ($input instanceof JInput)
		{
			$this->input = $input;
		}
		// Create the input based on the application logic.
		else
		{
			if (class_exists('JInput'))
			{
				$this->input = new JInputCli;
			}
		}

		// If a config object is given use it.
		if ($config instanceof Registry)
		{
			$this->config = $config;
		}
		// Instantiate a new configuration object.
		else
		{
			$this->config = new Registry;
		}

		if ($dispatcher)
		{
			$this->setDispatcher($dispatcher);
		}

		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData());

		// Set the execution datetime and timestamp;
		$this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
		$this->set('execution.timestamp', time());

		// Set the current directory.
		$this->set('cwd', getcwd());
	}

	/**
	 * Returns a reference to the global JApplicationCli object, only creating it if it doesn't already exist.
	 *
	 * This method must be invoked as: $cli = JApplicationCli::getInstance();
	 *
	 * @param   string  $name  The name (optional) of the JApplicationCli class to instantiate.
	 *
	 * @return  JApplicationCli
	 *
	 * @since   11.1
	 */
	public static function getInstance($name = null)
	{
		// Only create the object if it doesn't exist.
		if (empty(self::$instance))
		{
			if (class_exists($name) && (is_subclass_of($name, 'JApplicationCli')))
			{
				self::$instance = new $name;
			}
			else
			{
				self::$instance = new JApplicationCli;
			}
		}

		return self::$instance;
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function execute()
	{
		// Trigger the onBeforeExecute event.
		$this->triggerEvent('onBeforeExecute');

		// Perform application routines.
		$this->doExecute();

		// Trigger the onAfterExecute event.
		$this->triggerEvent('onAfterExecute');
	}

	/**
	 * Write a string to standard output.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  JApplicationCli  Instance of $this to allow chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   11.1
	 */
	public function out($text = '', $nl = true)
	{
		$output = $this->getOutput();
		$output->out($text, $nl);

		return $this;
	}

	/**
	 * Get an output object.
	 *
	 * @return  CliOutput
	 *
	 * @since   3.3
	 */
	public function getOutput()
	{
		if (!$this->output)
		{
			// In 4.0, this will convert to throwing an exception and you will expected to
			// initialize this in the constructor. Until then set a default.
			$default = new Joomla\Application\Cli\Output\Xml;
			$this->setOutput($default);
		}

		return $this->output;
	}

	/**
	 * Set an output object.
	 *
	 * @param   CliOutput  $output  CliOutput object
	 *
	 * @return  JApplicationCli  Instance of $this to allow chaining.
	 *
	 * @since   3.3
	 */
	public function setOutput(CliOutput $output)
	{
		$this->output = $output;

		return $this;
	}

	/**
	 * Get a value from standard input.
	 *
	 * @return  string  The input string from standard input.
	 *
	 * @codeCoverageIgnore
	 * @since   11.1
	 */
	public function in()
	{
		return rtrim(fread(STDIN, 8192), "\n");
	}
}
