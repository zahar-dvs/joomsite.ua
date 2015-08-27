<?php
/**
 * NoNumber Framework Helper File: Assignments: Geo
 *
 * @package         NoNumber Framework
 * @version         15.8.16757
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/assignment.php';

class NNFrameworkAssignmentsGeo extends NNFrameworkAssignment
{
	var $geo = null;

	/**
	 * passContinents
	 */
	function passContinents()
	{
		if (!$geo = self::getGeo($this->params->service))
		{
			return $this->pass(false);
		}

		return $this->passSimple($geo->continent);
	}

	/**
	 * passCountries
	 */
	function passCountries()
	{
		if (!$geo = self::getGeo($this->params->service))
		{
			return $this->pass(false);
		}

		return $this->passSimple($geo->country);
	}

	/**
	 * passRegions
	 */
	function passRegions()
	{
		if (!$geo = self::getGeo($this->params->service))
		{
			return $this->pass(false);
		}

		$region = $geo->country . '-' . $geo->region;

		return $this->passSimple($region);
	}

	function getGeo($service)
	{
		if ($this->geo !== null)
		{
			return $this->geo;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		$func = new NNFrameworkFunctions;

		$ip = $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? '' : $_SERVER['REMOTE_ADDR'];

		$this->geo = false;

		switch ($service)
		{
			case 'geoplugin':
				$json = $func->getContents('http://www.geoplugin.net/json.gp?ip=' . $ip);

				if (empty($json) || !$geo = json_decode($json))
				{
					return false;
				}

				if (!$geo = json_decode($json))
				{
					return false;
				}

				if (isset($geo->geoplugin_status) && $geo->geoplugin_status == 404)
				{
					return false;
				}

				$this->geo = (object) array(
					'continent' => isset($geo->geoplugin_continentCode) ? $geo->geoplugin_continentCode : '',
					'country'   => isset($geo->geoplugin_countryCode) ? $geo->geoplugin_countryCode : '',
					'region'    => isset($geo->geoplugin_regionCode) ? $geo->geoplugin_regionCode : '',
				);
				break;

			case 'telize':
			default:
				$json = $func->getContents('https://www.telize.com/geoip/' . $ip);

				if (empty($json) || !$geo = json_decode($json))
				{
					return false;
				}

				if (!isset($geo->ip))
				{
					return false;
				}

				$this->geo = (object) array(
					'continent' => isset($geo->continent_code) ? $geo->continent_code : '',
					'country'   => isset($geo->country_code) ? $geo->country_code : '',
					'region'    => isset($geo->region_code) ? $geo->region_code : '',
				);
				break;
		}

		return $this->geo;
	}
}
