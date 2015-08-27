<?php
/**
 * @package         Advanced Module Manager
 * @version         5.0.3
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Modules Component Positions Model
 *
 * @since  1.6
 */
class AdvancedModulesModelPositions extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'value',
				'templates',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = trim($this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		$clientId = $app->input->getInt('client_id', 0);
		$this->setState('filter.client_id', $clientId);

		$template = $this->getUserStateFromRequest($this->context . '.filter.template', 'filter_template', '', 'string');
		$this->setState('filter.template', $template);

		$type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '', 'string');
		$this->setState('filter.type', $type);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_advancedmodules');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('value', 'asc');
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		if (!isset($this->items))
		{
			$lang = JFactory::getLanguage();
			$search = trim($this->getState('filter.search'));
			$state = $this->getState('filter.state');
			$clientId = $this->getState('filter.client_id');
			$filter_template = $this->getState('filter.template');
			$type = $this->getState('filter.type');
			$ordering = $this->getState('list.ordering');
			$direction = $this->getState('list.direction');
			$limitstart = $this->getState('list.start');
			$limit = $this->getState('list.limit');
			$client = JApplicationHelper::getClientInfo($clientId);

			if ($type != 'template')
			{
				// Get the database object and a new query object.
				$query = $this->_db->getQuery(true)
					->select('DISTINCT(m.position) as value')
					->from('#__modules as m')
					->where('m.client_id = ' . (int) $clientId);
				if ($search)
				{
					$search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
					$query->where('m.position LIKE ' . $search);
				}

				$this->_db->setQuery($query);

				try
				{
					$positions = $this->_db->loadObjectList('value');
				}
				catch (RuntimeException $e)
				{
					$this->setError($e->getMessage());

					return false;
				}

				foreach ($positions as $value => $position)
				{
					$positions[$value] = array();
				}
			}
			else
			{
				$positions = array();
			}

			// Load the positions from the installed templates.
			foreach (ModulesHelper::getTemplates($clientId) as $template)
			{
				$path = JPath::clean($client->path . '/templates/' . $template->element . '/templateDetails.xml');

				if (file_exists($path))
				{
					$xml = simplexml_load_file($path);

					if (isset($xml->positions[0]))
					{
						$lang->load('tpl_' . $template->element . '.sys', $client->path, null, false, true)
						|| $lang->load('tpl_' . $template->element . '.sys', $client->path . '/templates/' . $template->element, null, false, true);

						foreach ($xml->positions[0] as $position)
						{
							$value = (string) $position['value'];
							$label = (string) $position;

							if (!$value)
							{
								$value = $label;
								$label = preg_replace('/[^a-zA-Z0-9_\-]/', '_', 'TPL_' . $template->element . '_POSITION_' . $value);
								$altlabel = preg_replace('/[^a-zA-Z0-9_\-]/', '_', 'COM_MODULES_POSITION_' . $value);

								if (!$lang->hasKey($label) && $lang->hasKey($altlabel))
								{
									$label = $altlabel;
								}
							}

							if ($type == 'user' || ($state != '' && $state != $template->enabled))
							{
								unset($positions[$value]);
							}
							elseif (preg_match(chr(1) . $search . chr(1) . 'i', $value) && ($filter_template == '' || $filter_template == $template->element))
							{
								if (!isset($positions[$value]))
								{
									$positions[$value] = array();
								}

								$positions[$value][$template->name] = $label;
							}
						}
					}
				}
			}

			$this->total = count($positions);

			if ($limitstart >= $this->total)
			{
				$limitstart = $limitstart < $limit ? 0 : $limitstart - $limit;
				$this->setState('list.start', $limitstart);
			}

			if ($ordering == 'value')
			{
				if ($direction == 'asc')
				{
					ksort($positions);
				}
				else
				{
					krsort($positions);
				}
			}
			else
			{
				if ($direction == 'asc')
				{
					asort($positions);
				}
				else
				{
					arsort($positions);
				}
			}

			$this->items = array_slice($positions, $limitstart, $limit ? $limit : null);
		}

		return $this->items;
	}

	/**
	 * Method to get the total number of items.
	 *
	 * @return  int    The total number of items.
	 *
	 * @since   1.6
	 */
	public function getTotal()
	{
		if (!isset($this->total))
		{
			$this->getItems();
		}

		return $this->total;
	}
}