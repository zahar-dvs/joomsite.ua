<?php
/*
# ------------------------------------------------------------------------
# TCVN Breaking News for Joomla 3.1
# ------------------------------------------------------------------------
# Copyright(C) 2008-2013 www.TheCoders.vn. All Rights Reserved.
# @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
# Author: TheCoders.vn
# Websites: http://TheCoders.com
# ------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<style>
#tcvn-header-top-marquee<?php echo $module->id; ?> {
	width:<?php echo $params->get('moduleWidth', '100%'); ?>;
	height:<?php echo $params->get('moduleHeight', 'auto'); ?>;
	background-color:#<?php echo $params->get('bgColor', '333333') ?> !important;
	border-color:#<?php echo $params->get('bgboderColor', 'D10000') ?> !important;
}
#tcvn-marquee-head<?php echo $module->id; ?> {
	<?php
		$margin = 0; 
		if($params->get('showCategory', 1)) $margin += 8;
		if($params->get('showCreatedate', 1)) $margin +=8;
	?>
	margin-top:<?php echo $margin ?>px;
	color:#<?php echo $params->get('titleColor', 'FFFFFF') ?> !important;
}
#tcvn-copyright<?php echo $module->id; ?> {
	height:<?php if($params->get('copyright', 1))echo 'auto'; else echo '0'; ?>
}
#tcvn-marquee<?php echo $module->id; ?> div a{
	color:#<?php echo $params->get('titleColor', 'FFFFFF') ?> !important;
}
#tcvn-marquee<?php echo $module->id; ?> div span{
	color:#<?php echo $params->get('titleColor', 'FFFFFF') ?> !important;
}
</style>
<div id="tcvn-header-top-marquee<?php echo $module->id; ?>" class="tcvn-header-top-marquee<?php echo $params->get('moduleclass_sfx', ''); ?>">
	<span class="vina-br">&nbsp;</span>
	<?php if($params->get('showTitle', 1)): ?>
    <div class="tcvn-marquee-head" id="tcvn-marquee-head<?php echo $module->id; ?>"><?php echo $params->get("moduleTitle", "BREAKING NEWS") ?></div>
    <?php endif;?>
	<div class="tcvn-marquee-wrapper">
		<div id="tcvn-marquee<?php echo $module->id; ?>" class="tcvn-marquee">
        	<?php foreach($list as $item): ?>
            <div>
            	<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
                <?php if($params->get('showCategory', 1)) {?><br />
                <span>Category: <?php echo $item->category_title; ?><span> <?php } ?>
                <?php if($params->get('showCreatedate', 1)){ ?><br /> 
                <span>Create Date: <?php echo date('F d, Y', strtotime($item->created));?></span><?php }?>
            </div>
            <?php endforeach;?>
		</div>
    </div>
	<div class="tcvn-clear"></div>
</div>
<div id="tcvn-copyright<?php echo $module->id; ?>">
	<a target="_blank" href="http://Thecoders.vn"><i>TheCoders.vn - Free Joomla 2.5 and 3.1 Extensions, Joomla Modules, Joomla Plugins.</i></a>
</div>