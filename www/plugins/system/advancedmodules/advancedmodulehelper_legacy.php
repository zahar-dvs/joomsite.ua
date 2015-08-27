<?php
/**
 * Plugin Module Helper File
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

jimport('joomla.filesystem.file');

/*
 * ModuleHelper methods
 */

class PlgSystemAdvancedModuleHelper
{
	public function onRenderModule(&$module)
	{
		// do nothing if is not frontend
		if (!JFactory::getApplication()->isSite())
		{
			return false;
		}

		// return true if module is empty (this will empty the content)
		if ($this->isEmpty($module))
		{
			return true;
		}


		return false;
	}

	public function isEmpty(&$module)
	{
		$params = isset($module->adv_param) ? $module->adv_param : (isset($module->advancedparams) ? $module->advancedparams : null);

		// return false if hideempty is off in module params
		if (empty($params) || !isset($params->hideempty) || !$params->hideempty)
		{
			return false;
		}

		$config = $this->getConfig();

		// return false if show_hideempty is off in main config
		if (!$config->show_hideempty)
		{
			return false;
		}

		$content = trim($module->content);
		// return true if module is empty
		if ($content == '')
		{
			// return true will prevent the module from outputting html
			return true;
		}

		// remove html and hidden whitespace
		$content = str_replace(chr(194) . chr(160), ' ', $content);
		$content = str_replace(array('&nbsp;', '&#160;'), ' ', $content);
		// remove comment tags
		$content = preg_replace('#<\!--.*?-->#si', '', $content);
		// remove all closing tags
		$content = preg_replace('#</[^>]+>#si', '', $content);
		// remove tags to be ignored
		$tags = 'p|div|span|strong|b|em|i|ul|font|br|h[0-9]|fieldset|label|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|form';
		$s = '#<(' . $tags . ')([^a-z0-9>][^>]*)?>#si';
		if (@preg_match($s . 'u', $content))
		{
			$s .= 'u';
		}
		if (preg_match($s, $content))
		{
			$content = preg_replace($s, '', $content);
		}

		// return false if module is not empty
		if (trim($content) != '')
		{
			return false;
		}

		// return true will prevent the module from outputting html
		return true;
	}


	public function onCreateModuleQuery(&$query)
	{
		// return if is not frontend
		if (!JFactory::getApplication()->isSite())
		{
			return;
		}

		foreach ($query as $type => $strings)
		{
			foreach ($strings as $i => $string)
			{
				if ($type == 'select')
				{
					$query->{$type}[$i] = str_replace(', mm.menuid', '', $string);
				}
				else if (strpos($string, 'mm.') !== false || strpos($string, 'm.publish_') !== false)
				{
					unset($query->{$type}[$i]);
				}
			}
		}
		$query->select[] = 'am.params as advancedparams, 0 as menuid, m.publish_up, m.publish_down';
		$query->join[] = '#__advancedmodules as am ON am.moduleid = m.id';
		$query->order = array('m.ordering, m.id');
	}

	public function onPrepareModuleList(&$modules)
	{
		// return if is not frontend
		if (!JFactory::getApplication()->isSite() || empty($modules))
		{
			return;
		}

		jimport('joomla.filesystem.file');

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/assignments.php';
		$assignments = new NNFrameworkAssignmentsHelper;

		$xmlfile_assignments = JPATH_ADMINISTRATOR . '/components/com_advancedmodules/assignments.xml';

		$config = $this->getConfig();

		// set params for all loaded modules first
		// and make it an associated array (array id = module id)
		$new_modules = array();
		require_once JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/module.php';
		$model = new AdvancedModulesModelModule;
		foreach ($modules as $id => $module)
		{
			if (!isset($module->advancedparams))
			{
				$module->advancedparams = $this->getAdvancedParams($id);
			}

			$module->advancedparams = json_decode($module->advancedparams);
			if (is_null($module->advancedparams))
			{
				$module->advancedparams = new stdClass;
			}
			if (
				!isset($module->advancedparams->assignto_menuitems)
				|| isset($module->advancedparams->assignto_urls_selection_sef)
				|| (
					!is_array($module->advancedparams->assignto_menuitems)
					&& strpos($module->advancedparams->assignto_menuitems, '|') !== false
				)
			)
			{
				$module->advancedparams = (object) $model->initAssignments($module->id, $module);
			}

			$module->advancedparams = $parameters->getParams($module->advancedparams, $xmlfile_assignments);
			$new_modules[$module->id] = $module;
		}
		$modules = $new_modules;
		unset($new_modules);

		foreach ($modules as $id => $module)
		{
			if ($module->advancedparams === 0)
			{
				continue;
			}

			$module->reverse = 0;

			if (!isset($module->published))
			{
				$module->published = 0;
			}
			// Check if module should mirror another modules assignment settings
			if ($module->published)
			{
				$count = 0;
				while ($count++ < 10
					&& !empty($module->advancedparams->mirror_module)
					&& !empty($module->advancedparams->mirror_moduleid)
				)
				{
					if (is_array($module->advancedparams->mirror_moduleid))
					{
						$module->advancedparams->mirror_moduleid = (int) $module->advancedparams->mirror_moduleid['0'];
					}
					$mirror_moduleid = (int) $module->advancedparams->mirror_moduleid;
					$module->reverse = ($module->advancedparams->mirror_module == 2);

					if (!$mirror_moduleid)
					{
						continue;
					}

					if ($mirror_moduleid == $id)
					{
						$empty = new stdClass;
						$mirror_params = $parameters->getParams($empty, $xmlfile_assignments);
					}
					else
					{
						if (isset($modules[$mirror_moduleid]))
						{
							if (!isset($modules[$mirror_moduleid]->adv_param))
							{
								$modules[$mirror_moduleid]->adv_param = $this->getAdvancedParams($mirror_moduleid);
								$modules[$mirror_moduleid]->adv_param = $parameters->getParams($modules[$mirror_moduleid]->adv_param, $xmlfile_assignments);
							}
							$mirror_params = $modules[$mirror_moduleid]->advancedparams;
						}
						else
						{
							$mirror_params = $this->getAdvancedParams($mirror_moduleid);
							$mirror_params = $parameters->getParams($mirror_params, $xmlfile_assignments);
						}
					}

					// Keep the advanced settings that shouldn't be mirrored
					$settings_to_keep = array(
						'hideempty', 'color',
					);
					foreach ($settings_to_keep as $key)
					{
						if (!isset($module->advancedparams->$key))
						{
							continue;
						}

						$mirror_params->$key = $module->advancedparams->$key;
					}

					$module->advancedparams = $mirror_params;
				}
			}

			if ($module->published)
			{
				if (!$config->show_assignto_homepage)
				{
					$module->advancedparams->assignto_homepage = 0;
				}
				if (!$config->show_assignto_usergrouplevels)
				{
					$module->advancedparams->assignto_usergrouplevels = 0;
				}
				if (!$config->show_assignto_date)
				{
					$module->advancedparams->assignto_date = 0;
				}
				if (!$config->show_assignto_languages)
				{
					$module->advancedparams->assignto_languages = 0;
				}
				if (!$config->show_assignto_templates)
				{
					$module->advancedparams->assignto_templates = 0;
				}
				if (!$config->show_assignto_urls)
				{
					$module->advancedparams->assignto_urls = 0;
				}
				if (!$config->show_assignto_os)
				{
					$module->advancedparams->assignto_os = 0;
				}
				if (!$config->show_assignto_browsers)
				{
					$module->advancedparams->assignto_browsers = 0;
				}
				if (!$config->show_assignto_components)
				{
					$module->advancedparams->assignto_components = 0;
				}
				if (!$config->show_assignto_tags)
				{
					$module->advancedparams->show_assignto_tags = 0;
				}
				if (!$config->show_assignto_content)
				{
					$module->advancedparams->assignto_contentpagetypes = 0;
					$module->advancedparams->assignto_cats = 0;
					$module->advancedparams->assignto_articles = 0;
				}

				$ass = $assignments->getAssignmentsFromParams($module->advancedparams);
				$pass = $assignments->passAll($ass, $module->advancedparams->match_method);

				if (!$pass)
				{
					$module->published = 0;
				}

				if ($module->reverse)
				{
					$module->published = $module->published ? 0 : 1;
				}
			}

			$modules[$id] = $module;
		}
	}

	/**
	 * Function that gets the config settings
	 *
	 * @return    Object
	 */
	private function getConfig()
	{
		static $instance;

		if (is_object($instance))
		{
			return $instance;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$instance = $parameters->getComponentParams('advancedmodules');

		return $instance;
	}

	private function getAdvancedParams($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.params')
			->from('#__advancedmodules AS a')
			->where('a.moduleid = ' . (int) $id);
		$db->setQuery($query);

		return $db->loadResult();
	}
}
