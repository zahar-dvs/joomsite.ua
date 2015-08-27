<?php
/**
 * @package Vina Bakery
 * @author VinaGecko.com http://VinaGecko.com
 * @copyright Copyright (C) 2014 www.VinaGecko.com
 * @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

$modChromes = array('sp_xhtml', 'sp_flat', 'sp_raw', 'sp_menu', 'none', 'sp_border'  );

function modChrome_sp_xhtml($module, $params, $attribs)
{ ?>
<div class="module <?php echo $params->get('moduleclass_sfx'); ?>">	
	<div class="mod-wrapper clearfix">		
		<?php if ($module->showtitle != 0) { ?>
			<h3 class="header">			
				<?php 
					echo '<span>'.$module->title.'</span>';
				?>
			</h3>
			<?php
				$modsfx=$params->get('moduleclass_sfx');
				if ($modsfx !='') echo '<span class="sp-badge ' . $modsfx . '"></span>';
			?>
			<?php } ?>
		<div class="mod-content clearfix">	
			<div class="mod-inner clearfix">
				<?php echo $module->content; ?>
			</div>
		</div>
	</div>
</div>
<div class="gap"></div>
<?php
}

function modChrome_sp_flat($module, $params, $attribs)
{ ?>
<div class="module <?php echo $params->get('moduleclass_sfx'); ?>">	
	<div class="mod-wrapper-flat clearfix">		
		<?php if ($module->showtitle != 0) { ?>
			<h3 class="header">			
				<?php 
					echo '<span>'.$module->title.'</span>';
				?>
			</h3>
			<?php
				$modsfx=$params->get('moduleclass_sfx');
				if ($modsfx !='') echo '<span class="sp-badge ' . $modsfx . '"></span>';
			?>
			<?php } ?>
		<?php echo $module->content; ?>
	</div>
</div>
<div class="gap"></div>
<?php
}

function modChrome_sp_border($module, $params, $attribs)
{ ?>
<div class="module vina-border <?php echo $params->get('moduleclass_sfx'); ?>">	
	<div class="mod-wrapper-flat clearfix">		
		<?php if ($module->showtitle != 0) { ?>
			<h3 class="header">			
				<?php 
					echo '<span>'.$module->title.'</span>';
				?>
			</h3>
			<?php
				$modsfx=$params->get('moduleclass_sfx');
				if ($modsfx !='') echo '<span class="sp-badge ' . $modsfx . '"></span>';
			?>
			<?php } ?>
			<div class="vina-modcontent clearfix">
				<?php echo $module->content; ?>
			</div>
	</div>
</div>
<div class="gap"></div>
<?php
}

function modChrome_sp_raw($module, $params, $attribs)
{ 
	echo $module->content; 
}

function modChrome_sp_menu($module, $params, $attribs)
{ ?>
<div class="module <?php echo $params->get('moduleclass_sfx'); ?>">	
	<div class="mod-wrapper-menu clearfix">
		<?php echo $module->content; ?>
	</div>
</div>
<?php
}