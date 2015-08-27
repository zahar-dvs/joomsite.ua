<?php
/*
# ------------------------------------------------------------------------
# TCVN Item Scroller for K2 - Joomla 2.5
# ------------------------------------------------------------------------
# Copyright(C) 2012-2013 www.TheCoders.vn. All Rights Reserved.
# @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
# Author: TheCoders.vn
# Websites: http://TheCoders.com
# ------------------------------------------------------------------------
*/
 
// no direct access
defined('_JEXEC') or die;
if(!defined('DS')){define('DS',DIRECTORY_SEPARATOR);}
// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';

// Include the syndicate functions only once
if(K2_JVERSION == '16') {
	$language = &JFactory::getLanguage();
	$language->load('mod_k2.j16', JPATH_ADMINISTRATOR, null, true);
}

$componentParams 	= &JComponentHelper::getParams('com_k2');
$items 				= modTCVNK2ScrollerHelper::getItems($params);

if(count($items)<1) return;

// Load style, javescript
modTCVNK2ScrollerHelper::loadMediaFiles();

// Get params
$moduleclass_sfx	= $params->get('moduleclass_sfx');
$theme				= $params->get('theme', 'theme1');
$sliderWidth 		= $params->get('sliderWidth', 562);
$sliderHeight 		= $params->get('sliderHeight', 370);
$showTitle			= $params->get('itemTitle', 1);
$linkTitle			= $params->get('linkTitle', 1);
$showDesc			= $params->get('itemIntroText', 1);
$showread           = $params->get('showread', 1);

$slideview 			= $params->get('slideview', 2);
$slideSpacing 		= $params->get('slideSpacing', 30);
$touchenabled 		= $params->get('touchenabled', "on");
$mouseWheel 		= $params->get('mouseWheel', "on");
$hoverAlpha 		= $params->get('hoverAlpha', "off");
$hovereffect 		= $params->get('hovereffect', "on");
$slideshow 			= $params->get('slideshow', 3000);
$styleslide 		= $params->get('styleslide', 'theme1');
$imageWidth			= $params->get('imageWidth', 250);
$imageHeight		= $params->get('imageHeight', 130);
$selectthumb        = $params->get('selectthumb',    1);
// Include layout
require(JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default')));
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#tcvn-scroller-k2<?php echo $module->id; ?>').services({
		width: <?php if($theme=="theme1") echo $sliderWidth-61; else echo $sliderWidth-41; ?>,
		height: <?php echo $sliderHeight-60; ?>,
		slideAmount: <?php echo $slideview ?>,
		slideSpacing: <?php echo $slideSpacing; ?>,
		touchenabled: "<?php echo $touchenabled; ?>",
		mouseWheel: "<?php echo $mouseWheel; ?>",
		hoverAlpha: "<?php echo $hoverAlpha; ?>",
		slideshow: <?php echo $slideshow; ?>,
		hovereffect: "<?php echo $hovereffect; ?>", 
		callBack:function() { }
	});
}); 
</script>