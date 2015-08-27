<?php

/**
 * @package AdsMap Location Module for AdsManager
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author      Gotour WebSolutions Team <webmaster@gotour.it> - http://support.gotour.it
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the syndicate functions only once
require_once( dirname( __FILE__ ) . DS . 'helper.php' );
$map = modAdsMapLocationHelper::getMapHTML($params);
require(JModuleHelper::getLayoutPath('mod_adsmaplocation'));

?>