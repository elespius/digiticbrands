<?php

require_once 'JoomlaWebdriverTestCase.php';

use SeleniumClient\By;
use SeleniumClient\SelectElement;
use SeleniumClient\WebDriver;
use SeleniumClient\WebDriverWait;
use SeleniumClient\DesiredCapabilities;

class WikihelpTestAllLanguages extends JoomlaWebdriverTestCase
{
	/**
	 *
	 * @var GlobalConfigurationPage
	 */
	protected $testPage = null; // Page under test

	/**
	 * @var defaultLanguage
	 */
	public static $defaultLanguage = '';

	/**
	 *
	 * @var array of all menu links and corresponding page class names.
	 */

	public  $allMenuLinks = array(
		'Control Panel' 		=> array('ControlPanelPage', 'administrator/index.php', 'system'),
		'Global Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config', 'configuration'),
		'Banners Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_banners', 'configuration'),
		'Cache Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_cache', 'configuration'),
		'Check-in Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_checkin', 'configuration'),
		'Contacts Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_contact', 'configuration'),
		'Articles Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_content', 'configuration'),
		'Smart Search Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_finder', 'configuration'),
		'Installation Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_installer', 'configuration'),
		'Joomla! Update Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_joomlaupdate', 'configuration'),
		'Language Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_languages', 'configuration'),
		'Media Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_media', 'configuration'),
		'Menus Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_menus', 'configuration'),
		'Messaging Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_messages', 'configuration'),
		'Module Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_modules', 'configuration'),
		'Newsfeeds Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_newsfeeds', 'configuration'),
		'Plugin Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_plugins', 'configuration'),
		'Post-installation Messages Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_postinstall', 'configuration'),
		'Redirect Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_redirect', 'configuration'),
		'Search Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_search', 'configuration'),
		'Tags Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_tags', 'configuration'),
		'Template Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_templates', 'configuration'),
		'Users Manager Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_users', 'configuration'),
		'Weblinks Configuration'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_config&view=component&component=com_weblinks', 'configuration'),

		'Global Checkin' 		=> array('GenericAdminPage', 'administrator/index.php?option=com_checkin', 'system'),
		'Clear Cache' 			=> array('GenericAdminPage', 'administrator/index.php?option=com_cache', 'system'),
		'Purge Expired Cache' 	=> array('GenericAdminPage', 'administrator/index.php?option=com_cache&view=purge', 'system'),
		'System Information'	=> array('GenericAdminPage', 'administrator/index.php?option=com_admin&view=sysinfo', 'system'),
		'User Manager'			=> array('UserManagerPage', 'administrator/index.php?option=com_users&view=users', 'users'),
		'Add New User'			=> array('UserEditPage', 'administrator/index.php?option=com_users&task=user.add', 'users'),
		'Groups'				=> array('GroupManagerPage', 'administrator/index.php?option=com_users&view=groups', 'users'),
		'Add New Group'			=> array('GroupEditPage', 'administrator/index.php?option=com_users&task=group.add', 'users'),
		'Access Levels'			=> array('LevelManagerPage', 'administrator/index.php?option=com_users&view=levels', 'users'),
		'Add New Access Level'	=> array('LevelEditPage', 'administrator/index.php?option=com_users&task=level.add', 'users'),
		'User Notes'			=> array('UserNotesManagerPage', 'administrator/index.php?option=com_users&view=notes', 'users'),
		'Add User Note'			=> array('UserNotesEditPage', 'administrator/index.php?option=com_users&task=note.add', 'users'),
		'User Notes Categories'	=> array('CategoryManagerPage', 'administrator/index.php?option=com_categories&view=categories&extension=com_users', 'users'),
		'Add User Note Category'	=> array('CategoryEditPage', 'administrator/index.php?option=com_categories&task=category.add&extension=com_users', 'users'),
		'Mass Mail Users'		=> array('GenericAdminPage', 'administrator/index.php?option=com_users&view=mail', 'users'),
		'Menu Manager'			=> array('MenuManagerPage', 'administrator/index.php?option=com_menus&view=menus', 'menus'),
		'Add New Menu'			=> array('MenuEditPage', 'administrator/index.php?option=com_menus&view=menu&layout=edit', 'menus'),
		'Main Menu'				=> array('MenuItemsManagerPage', 'administrator/index.php?option=com_menus&view=items&menutype=mainmenu', 'menus'),
		'Main Menu Add New Menu Item'	=> array('MenuItemEditPage', 'administrator/index.php?option=com_menus&view=item&layout=edit&menutype=mainmenu', 'menus'),
		'Article Manager'		=> array('ArticleManagerPage', 'administrator/index.php?option=com_content', 'content'),
		'Add New Article'		=> array('ArticleEditPage', 'administrator/index.php?option=com_content&task=article.add', 'content'),
		'Category Manager'		=> array('CategoryManagerPage', 'administrator/index.php?option=com_categories&extension=com_content', 'content'),
		'Add New Category'		=> array('CategoryEditPage', 'administrator/index.php?option=com_categories&task=category.add&extension=com_content', 'content'),
		'Featured Articles'		=> array('GenericAdminPage', 'administrator/index.php?option=com_content&view=featured', 'content'),
		'Media Manager'			=> array('GenericAdminPage', 'administrator/index.php?option=com_media', 'content'),
		'Banners'				=> array('BannerManagerPage', 'administrator/index.php?option=com_banners', 'components'),
		'Add New Banner'		=> array('BannerEditPage', 'administrator/index.php?option=com_banners&view=banner&layout=edit', 'components'),
		'Banners Clients'		=> array('GenericAdminPage', 'administrator/index.php?option=com_banners&view=clients', 'components'),
		'Add New Banner Client'	=> array('GenericAdminEditPage', 'administrator/index.php?option=com_banners&view=client&layout=edit', 'components'),
		'Banners Tracks'		=> array('GenericAdminPage', 'administrator/index.php?option=com_banners&view=tracks', 'components'),
		'Contacts'				=> array('ContactManagerPage', 'administrator/index.php?option=com_contact', 'components'),
		'Add New Contact'		=> array('ContactEditPage', 'administrator/index.php?option=com_contact&task=contact.add', 'components'),
		'Joomla! Update'		=> array('GenericAdminPage', 'administrator/index.php?option=com_joomlaupdate', 'components'),
		'Messaging'				=> array('GenericAdminPage', 'administrator/index.php?option=com_messages', 'components'),
		'New Private Message'	=> array('GenericAdminPage', 'administrator/index.php?option=com_messages&task=message.add', 'components'),
		'Read Private Messages'	=> array('GenericAdminPage', 'administrator/index.php?option=com_messages', 'components'),
		'Newsfeeds'				=> array('NewsFeedManagerPage', 'administrator/index.php?option=com_newsfeeds', 'components'),
		'Add New Newsfeed'		=> array('NewsFeedEditPage', 'administrator/index.php?option=com_newsfeeds&task=newsfeed.add', 'components'),
		'Post-installation Messages'	=> array('PostinstallPage', 'administrator/index.php?option=com_postinstall', 'components'),
		'Redirect'				=> array('RedirectManagerPage', 'administrator/index.php?option=com_redirect', 'components'),
		'Add New Redirect'		=> array('RedirectEditPage', 'administrator/index.php?option=com_redirect&view=link&layout=edit', 'components'),
		'Search'				=> array('GenericAdminPage', 'administrator/index.php?option=com_search', 'components'),
		'Smart Search'			=> array('GenericAdminPage', 'administrator/index.php?option=com_finder', 'components'),
		'Tags'					=> array('TagManagerPage', 'administrator/index.php?option=com_tags', 'components'),
		'Add New Tag'			=> array('TagEditPage', 'administrator/index.php?option=com_tags&task=tag.add', 'components'),
		'Weblinks'				=> array('WeblinkManagerPage', 'administrator/index.php?option=com_weblinks', 'components'),
		'Add New Weblink'		=> array('WeblinkEditPage', 'administrator/index.php?option=com_weblinks&task=weblink.add', 'components'),
		'Extension Manager Install'		=> array('GenericAdminPage', 'administrator/index.php?option=com_installer', 'extensions'),
		'Extension Manager Update'		=> array('GenericAdminPage', 'administrator/index.php?option=com_installer&view=update', 'extensions'),
		'Extension Manager Manage'		=> array('GenericAdminPage', 'administrator/index.php?option=com_installer&view=manage', 'extensions'),
		'Extension Manager Discover'	=> array('GenericAdminPage', 'administrator/index.php?option=com_installer&view=discover', 'extensions'),
		'Extension Manager Database'	=> array('GenericAdminPage', 'administrator/index.php?option=com_installer&view=database', 'extensions'),
		'Extension Manager Warnings'	=> array('GenericAdminPage', 'administrator/index.php?option=com_installer&view=warnings', 'extensions'),
		'Extension Manager Install languages'	=> array('GenericAdminPage', 'administrator/index.php?option=com_installer&view=languages', 'extensions'),
		'Module Manager'		=> array('ModuleManagerPage', 'administrator/index.php?option=com_modules', 'extensions'),
		'Plugin Manager'		=> array('PluginManagerPage', 'administrator/index.php?option=com_plugins', 'extensions'),
		'Template Manager'		=> array('TemplateManagerPage', 'administrator/index.php?option=com_templates', 'extensions'),
		'Language Manager Installed'	=> array('LanguageManagerPage', 'administrator/index.php?option=com_languages', 'extensions'),
		'Language Manager Content'		=> array('LanguageManagerPage', 'administrator/index.php?option=com_languages&view=languages', 'extensions'),
		'Add New Langauge'		=> array('LanguageEditPage', 'administrator/index.php?option=com_languages&view=language&layout=edit', 'extensions'),
		'Language Manager Overrides'		=> array('LanguageManagerPage', 'administrator/index.php?option=com_languages&view=overrides', 'extensions'),
	);

	public function setUp()
	{
		iconv_set_encoding("internal_encoding", "UTF-8");
		iconv_set_encoding("output_encoding", "UTF-8");
		iconv_set_encoding("input_encoding", "UTF-8");
		parent::setUp();
		$this->testPage = $this->doAdminLogin();
		$testPage = $this->testPage;
		if (!self::$defaultLanguage)
		{
			$languageManagerPage = $testPage->clickMenu('Language Manager', 'LanguageManagerPage');
			self::$defaultLanguage = strtolower($languageManagerPage->getDefaultLanguage('admin'));
		}
	}

	public function tearDown()
	{
		$this->doAdminLogout();
		parent::tearDown();
	}


	/**
	 * @test
	 */
	public function takeScreenShotsAllMenuLinks()
	{
		$folder = $this->getFolderName('tmp/basic-screens/');
		$defaultLanguage = self::$defaultLanguage;
		$testPage = $this->testPage;
		$gcPage = $testPage->clickMenu('Global Configuration', 'GlobalConfigurationPage');
		$gcPage->setFieldValue('Default List Limit', '5');
		$testPage = $gcPage->saveAndClose('ControlPanelPage');

		foreach ($this->allMenuLinks as $menuText => $linkArray)
		{
			if (strpos($linkArray[1], 'http') !== 0)
			{
				$testPage = $testPage->clickMenuByUrl($linkArray[1], $linkArray[0]);
				$options = array('language' => $defaultLanguage, 'prefix' => $linkArray[2] . '-' . $menuText);
				$name = $testPage->getHelpScreenshotName($options);

				// process additional tabs if available
				if (method_exists($testPage, 'getTabIds'))
				{
					$tabs = $testPage->getTabIds();
					$limit = count($tabs);
					for ($i = 0; $i < $limit; $i++)
					{
						$testPage->selectTab($tabs[$i]);
						if ($i > 0)
						{
							$options = array('language' => $defaultLanguage, 'prefix' => $linkArray[2] . '-' . $menuText, 'tab' => $tabs[$i]);
							$name = $testPage->getHelpScreenshotName($options);
						}
						$this->helpScreenshot($name, $folder);
					}
				}
				else
				{
					$this->helpScreenshot($name, $folder);
				}
			}
		}

	}

	/**
	 * @test
	 */
	public function takeScreenShotsMenuItemTypes()
	{
		$defaultLanguage = self::$defaultLanguage;
		$folder = $this->getFolderName('tmp/menu-item-screens/');
		$testPage = $this->testPage;

		/* @var $menuItemEditPage MenuItemEditPage */

		// First get a list of all menu item types (array like group => 'Articles', type => 'Archived Articles')
		$menuItemsManagerPage = $this->testPage->clickMenu('Main Menu', 'MenuItemsManagerPage');
		$menuItemsManagerPage->clickButton('toolbar-new');
		$menuItemEditPage = $this->getPageObject('MenuItemEditPage');
		$menuItemTypes = $menuItemEditPage->getMenuItemTypes();
		$menuItemEditPage->menuItemTypes = $menuItemTypes;
		$menuItemsManagerPage = $this->testPage->clickMenu('Main Menu', 'MenuItemsManagerPage');
		foreach ($menuItemTypes as $type)
		{
			$menuItemsManagerPage->clickButton('toolbar-new');
			$menuItemEditPage = $this->getPageObject('MenuItemEditPage');
			$menuItemEditPage->menuItemTypes = $menuItemTypes;
			$menuItemEditPage->setMenuItemType($type['type']);
			$menuItemEditPage->setFieldValue('Menu Title', $type['group'] . ' - ' . $type['type']);
			$menuItemEditPage->tabs = null;
			$menuItemEditPage->tabLabels = null;
			$tabs = $menuItemEditPage->getTabIds();
			$limit = count($tabs);
			for ($i = 0; $i < $limit; $i ++)
			{
				// Skip tabs that are common to all menu item types.
				if (in_array($tabs[$i], array('details', 'attrib-menu-options', 'attrib-page-options', 'attrib-metadata', 'modules')))
				{
					continue;
				}
				$menuItemEditPage->selectTab($tabs[$i]);
				if ($i > 0)
				{
					$options = array('tab' => $tabs[$i], 'language' => $defaultLanguage, 'prefix' => 'menus-menu-manager-new-menu-item');
					$name = $menuItemEditPage->getHelpScreenshotName($options);
				}
				else
				{
					$options = array('language' => $defaultLanguage, 'prefix' => 'menus-menu-manager-new-menu-item');
					$name = $menuItemEditPage->getHelpScreenshotName($defaultLanguage, 'menus-menu-manager-new-menu-item', 'code');
				}
				$this->helpScreenshot($name, $folder);
			}
			$menuItemEditPage->clickButton('Cancel');
			$menuItemsManagerPage = $this->getPageObject('MenuItemsManagerPage');
		}

	}

	/**
	 * @test
	 */
	public function takeScreenShotsForModuleTypes()
	{
		$defaultLanguage = self::$defaultLanguage;
		$folder = $this->getFolderName('tmp/module-screens/');

		/* @var $moduleEditPage ModuleEditPage */
		/* @var $moduleManagerPage ModuleManagerPage */

		// First get a list of all menu item types (array like group => 'Articles', type => 'Archived Articles')
		$moduleManagerPage = $this->testPage->clickMenu('Module Manager', 'ModuleManagerPage');
		$moduleManagerPage->moduleTypes = $moduleManagerPage->getModuleTypes();
		foreach ($moduleManagerPage->moduleTypes as $type)
		{
			$moduleManagerPage->setClient($type['client']);
			$moduleManagerPage->clickButton('toolbar-new');
			$el = $this->driver->waitForElementUntilIsPresent(By::xPath('//ul[@id=\'new-modules-list\']//a[contains(., "' . $type["name"] . '")]'));
			$coordinates = $el->getCoordinates();
			if ($coordinates['y'] > 600)
			{
				$this->driver->executeScript("window.scrollBy(0,400)");
			}
			$el->click();
			$moduleEditPage = $this->getPageObject('ModuleEditPage');
			$moduleEditPage->setFieldValues(array('Title' => $type['name']));

			$moduleEditPage->tabs = null;
			$moduleEditPage->tabLabels = null;
			$tabs = $moduleEditPage->getTabIds();
			$limit = count($tabs);
			for ($i = 0; $i < $limit; $i ++)
			{
					// Skip tabs that are common to all menu item types.
				if (in_array($tabs[$i], array('assignment','permissions','attrib-advanced')))
				{
					continue;
				}
				$moduleEditPage->selectTab($tabs[$i]);
				$moduleName = $moduleEditPage->getModuleName();
				if ($i > 0)
				{
					$options = array('tab' => $tabs[$i], 'prefix' => 'modules ' . $type['client'] . '-' . $moduleName, 'language' => self::$defaultLanguage);
					$name = $moduleEditPage->getHelpScreenshotName($options);
				}
				else
				{
					$options = array('prefix' => 'modules ' . $type['client'] . '-' . $moduleName, 'language' => self::$defaultLanguage);
					$name = $moduleEditPage->getHelpScreenshotName($options);
				}
				$this->helpScreenshot($name, $folder);
			}

			$moduleEditPage->clickButton('toolbar-cancel');
			$this->getPageObject('ModuleManagerPage');
	}

	}

	/**
	 * @test
	 */
	public function writeWikiFilesForBasicScreens()
	{
		$testPage = $this->testPage;
		$folder = $this->getFolderName('tmp/wiki-basic-files/');

		foreach ($this->allMenuLinks as $menuText => $linkArray)
		{
			if (strpos($linkArray[1], 'http') !== 0)
			{
				$testPage = $this->testPage->clickMenuByUrl($linkArray[1], $linkArray[0]);
				$testPage->tabs = null;
				$testPage->tabLabels = null;
				if (method_exists($testPage, 'toWikiHelp'))
				{
					$screenshotOptions = array('prefix' => $linkArray[2] . '-' . $menuText, 'language' => self::$defaultLanguage);
					$text = $testPage->toWikiHelp(array(), array(), $screenshotOptions);
					$fileName = $testPage->getHelpFileName($linkArray[2] . '-' . $menuText . '-' . self::$defaultLanguage);
					file_put_contents($folder . '/' . $fileName, $text);
				}
			}
		}
	}

	/**
	 * @test
	 */
	public function writeWikiFilesForMenuItemTypes()
	{
		$folder = $this->getFolderName('tmp/wiki-menu-item-files/');

		/* @var $menuItemEditPage MenuItemEditPage */

		$menuItemsManagerPage = $this->testPage->clickMenu('Main Menu', 'MenuItemsManagerPage');
		$menuItemsManagerPage->clickButton('toolbar-new');
		$menuItemEditPage = $this->getPageObject('MenuItemEditPage');
		$menuItemEditPage->menuItemTypes = $menuItemEditPage->getMenuItemTypes();
		$menuItemsManagerPage = $this->testPage->clickMenu('Main Menu', 'MenuItemsManagerPage');
		foreach ($menuItemEditPage->menuItemTypes as $type)
		{
			$menuItemsManagerPage->clickButton('toolbar-new');
			$this->getPageObject('MenuItemEditPage');
			$menuItemEditPage->setMenuItemType($type['type']);
			$menuItemEditPage->tabs = null;
			$menuItemEditPage->tabLabels = null;
			$screenshotNameOptions = array('prefix' => 'menus-menu-manager-new-menu-item', 'language' => self::$defaultLanguage);
			$text = $menuItemEditPage->toWikiHelp(array('header', 'details', 'attrib-menu-options', 'attrib-page-options', 'attrib-metadata', 'modules'), array(), $screenshotNameOptions);
			if ($text)
			{
				$fileName = $menuItemEditPage->getHelpFileName(trim('menu-item-type-' . $menuItemEditPage->getMenuItemComponent()));
				file_put_contents($folder . '/' . $fileName, $text);
			}
			$menuItemEditPage->clickButton('Cancel');
			$menuItemsManagerPage = $this->getPageObject('MenuItemsManagerPage');
		}
	}

	/**
	 * @test
	 */
	public function writeWikiFilesForModuleTypes()
	{
		$folder = $this->getFolderName('tmp/wiki-module-type-files/');

		/* @var $moduleEditPage ModuleEditPage */
		/* @var $moduleManagerPage ModuleManagerPage */
		// First get a list of all module types
		$moduleManagerPage = $this->testPage->clickMenu('Module Manager', 'ModuleManagerPage');
		$moduleManagerPage->moduleTypes = $moduleManagerPage->getModuleTypes();
		foreach ($moduleManagerPage->moduleTypes as $type)
		{
			$moduleManagerPage->setClient($type['client']);
			$moduleManagerPage->clickButton('toolbar-new');
			$el = $this->driver->waitForElementUntilIsPresent(By::xPath('//ul[@id=\'new-modules-list\']//a[contains(., "' . $type["name"] . '")]'));
			$coordinates = $el->getCoordinates();
			if ($coordinates['y'] > 600)
			{
				$this->driver->executeScript("window.scrollBy(0,400)");
			}
			$el->click();
			$moduleEditPage = $this->getPageObject('ModuleEditPage');
			$moduleEditPage->setFieldValues(array('Title' => $type['name']));
			$moduleEditPage->tabs = null;
			$moduleEditPage->tabLabels = null;
			$excludedTabs = array('assignment','permissions','attrib-advanced');
			$excludedFields = array('jform_showtitle', 'jform_position', 'jform_published', 'jform_publish_up', 'jform_publish_down', 'jform_access', 'jform_ordering', 'jform_language', 'jform_note');
			$moduleName = $moduleEditPage->getModuleName();
			$options = array('prefix' => 'modules-' . $type['client'] . '-' . $moduleName, 'language' => self::$defaultLanguage);
			$text = $moduleEditPage->toWikiHelp($excludedTabs, $excludedFields, $options);
			if ($text)
			{
				$fileName = $moduleEditPage->getHelpFileName(trim('module-' . $type['client'] . '-' . $moduleName));
				file_put_contents($folder . '/' . $fileName, $text);
			}
			$moduleEditPage->clickButton('toolbar-cancel');
			$this->getPageObject('ModuleManagerPage');
		}

	}

	public function getFolderName($parentFolder)
	{
		$basePath = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
		$baseFolder = $basePath . '/' . $parentFolder;
		if (!file_exists($baseFolder))
		{
			mkdir($baseFolder);
		}

		$folder = $baseFolder . self::$defaultLanguage;
		if (!file_exists($folder))
		{
			mkdir($folder);
		}

		return $folder;
	}


}