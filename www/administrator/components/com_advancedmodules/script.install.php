<?php
/**
 * Install script
 *
 * @package         Advanced Module Manager
 * @version         5.0.3
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class Com_AdvancedModulesInstallerScript extends Com_AdvancedModulesInstallerScriptHelper
{
	public $name = 'ADVANCED_MODULE_MANAGER';
	public $alias = 'advancedmodulemanager';
	public $extname = 'advancedmodules';
	public $extension_type = 'component';

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, $folder = 'system');
	}

	public function onBeforeInstall()
	{
		// Fix incorrectly formed versions because of issues in old packager
		$this->fixFileVersions(
			array(
				JPATH_ADMINISTRATOR . '/components/com_advancedmodules/advancedmodules.xml',
				JPATH_PLUGINS . '/system/advancedmodules/advancedmodules.xml',
			)
		);
	}

	public function onAfterInstall()
	{
		$this->createTable();
		$this->fixAssets();
		$this->removeAdminMenu();
		$this->removeFrontendComponentFromDB();
		$this->fixAssignments();
		$this->deleteOldFiles();
	}

	public function createTable()
	{
		// main table
		$query = "CREATE TABLE IF NOT EXISTS `#__advancedmodules` (
			`moduleid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
			`params` TEXT NOT NULL,
			PRIMARY KEY (`moduleid`)
		) DEFAULT CHARSET=utf8;";
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function fixAssets()
	{
		// add asset_id column
		$query = "SHOW COLUMNS FROM `" . $this->db->getPrefix() . "advancedmodules` LIKE 'asset_id'";
		$this->db->setQuery($query);
		$hasasset = $this->db->loadResult();
		if (!$hasasset)
		{
			$query = "ALTER TABLE `#__advancedmodules` ADD `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `moduleid`";
			$this->db->setQuery($query);
			$this->db->execute();
		}
	}

	public function removeAdminMenu()
	{
		// hide admin menu
		$query = $this->db->getQuery(true)
			->delete('#__menu')
			->where($this->db->quoteName('path') . ' = ' . $this->db->quote('advancedmodules'))
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('component'))
			->where($this->db->quoteName('client_id') . ' = 1');
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function removeFrontendComponentFromDB()
	{
		// remove frontend component from extensions table
		$query = $this->db->getQuery(true)
			->delete('#__extensions')
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote('com_advancedmodules'))
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('component'))
			->where($this->db->quoteName('client_id') . ' = 0');
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function fixAssignments()
	{
		// remove initial menu assignment settings
		$query = $this->db->getQuery(true)
			->update('#__advancedmodules as a')
			->set('a.params = ' . $this->db->quote(''))
			->where('a.params = ' . $this->db->quote('{"assignto_menuitems":0,"assignto_menuitems_selection":[]}'));
		$this->db->setQuery($query);
		$this->db->execute();

		// correct old keys and values
		$query = $this->db->getQuery(true)
			->select('a.moduleid,a. params')
			->from('#__advancedmodules as a');
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		foreach ($rows as $row)
		{
			if (empty($row->params))
			{
				continue;
			}

			if ($row->params['0'] != '{')
			{
				$row->params = str_replace('assignto_secscats', 'assignto_cats', $row->params);
				$row->params = str_replace('flexicontent', 'fc', $row->params);

				$params = JRegistryFormat::getInstance('INI')->stringToObject($row->params);
			}
			else
			{
				$params = json_decode($row->params);
				if (is_null($params))
				{
					$params = new stdClass;
				}
			}

			// move tooltip to notes field
			if (!empty($params->tooltip))
			{
				$query->clear()
					->update('#__modules as m')
					->set('m.note = ' . $this->db->quote($params->tooltip))
					->where('m.id = ' . (int) $row->moduleid);
				$this->db->setQuery($query);
				$this->db->execute();
				unset($params->tooltip);
			}

			// concatenate sef and non-sef url fields
			if (isset($params->assignto_urls_selection_sef))
			{
				$params->assignto_urls_selection = trim($params->assignto_urls_selection . "\n" . $params->assignto_urls_selection_sef);
				unset($params->assignto_urls_selection_sef);
				unset($params->show_url_field);
			}

			// set urls_regex value if assignto_urls is used
			if (!empty($params->assignto_urls) && !isset($params->assignto_urls_regex))
			{
				$params->assignto_urls_regex = 1;
			}

			foreach ($params as $k => &$v)
			{
				switch ($k)
				{
					case 'assignto_php_selection':
					case 'assignto_urls_selection':
					case 'assignto_ips_selection':
						$v = str_replace(array('\n', '\|'), array("\n", '|'), $v);
						break;
					case 'color':
						$v = str_replace('#', '', $v);
						$v = (empty($v) || $v == 'none') ? 'none' : $v;
						if ($v && $v != 'none')
						{
							$v = '#' . strtolower($v);
						}
						break;
					case 'assignto_users_selection':
						$v = implode(',', explode('|', $v));
						break;
					default:
						if (
							(substr($k, -10) == '_selection' || substr($k, -4) == '_inc')
							&& !is_array($v)
						)
						{
							// convert | separated strings to arrays
							$v = explode('|', $v);
						}
						break;
				}
			}

			if (!empty($params->assignto_cats_selection))
			{
				foreach ($params->assignto_cats_selection as $key => $val)
				{
					if (strpos($val, ':') !== false)
					{
						$params->assignto_cats_selection[$key] = substr($val, strpos($val, ':') + 1);
					}
				}
			}

			$params = json_encode($params);

			if ($params == $row->params)
			{
				continue;
			}

			$query->clear()
				->update('#__advancedmodules as a')
				->set('a.params = ' . $this->db->quote($params))
				->where('a.moduleid = ' . (int) $row->moduleid);
			$this->db->setQuery($query);
			$this->db->execute();
		}
	}

	private function deleteOldFiles()
	{
		JFile::delete(
			array(
				JPATH_ADMINISTRATOR . '/components/com_advancedmodules/script.advancedmodules.php',
				JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/forms/filter_modules.xml',
				JPATH_SITE . '/components/com_advancedmodules/advancedmodules.xml',
				JPATH_SITE . '/components/com_advancedmodules/script.advancedmodules.php',
				JPATH_SITE . '/plugins/system/advancedmodules/modulehelper.php',
			)
		);

		$this->deleteFolders(
			array(
				JPATH_ADMINISTRATOR . '/components/com_advancedmodules/layouts/joomla/searchtools',
				JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/fields',
			)
		);
	}
}
