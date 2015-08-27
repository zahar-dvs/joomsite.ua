<?php
/*
# ------------------------------------------------------------------------
# TCVN Slit Slider Module - Joomla 3.1
# ------------------------------------------------------------------------
# Copyright(C) 2008-2012 www.TheCoders.vn. All Rights Reserved.
# @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
# Author: TheCoders.vn
# Websites: http://TheCoders.com
# Forum:    http://VinaForum.biz
# ------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
if(version_compare(JVERSION,'3.0') < 0) {
    if($params->get('load_jquery', '1')) {
        $doc->addScript('modules/mod_tcvn_slit_slider/assets/js/jquery-1.8.3.min.js', 'text/javascript');
    }
}

$doc->addStyleSheet('modules/mod_tcvn_slit_slider/assets/css/style.css', 'text/css');
$doc->addStyleSheet('modules/mod_tcvn_slit_slider/assets/css/custom.css', 'text/css');
$doc->addScript('modules/mod_tcvn_slit_slider/assets/js/modernizr.custom.79639.js', 'text/javascript');
$doc->addScript('modules/mod_tcvn_slit_slider/assets/js/jquery.ba-cond.min.js', 'text/javascript');
$doc->addScript('modules/mod_tcvn_slit_slider/assets/js/jquery.slitslider.js', 'text/javascript');
?>
<style type="text/css">
<?php for($i=0; $i < count($slides); $i++) : ?>
#tcvn-slit-slider-main<?php echo $module->id; ?> .bg-img-<?php echo $i+ 1; ?> {
	background-image: url(<?php echo JURI::base() . $slides[$i]->img; ?>);
}
<?php endfor; ?>
</style>
<div class="tcvn-slit-slider" id="tcvn-slit-slider-main<?php echo $module->id; ?>">
    <div id="tcvn-slit-slider<?php echo $module->id; ?>" class="sl-slider-wrapper">
        <div class="sl-slider">
            <?php for($i=0; $i < count($slides); $i++) : ?>
            <div class="sl-slide" 
            	data-orientation="<?php echo $slides[$i]->easing; ?>" 
                	data-slice1-rotation="<?php echo rand(-50, 50); ?>" 
                    	data-slice2-rotation="<?php echo rand(-50, 50); ?>" 
                        	data-slice1-scale="<?php echo rand(1, 2); ?>" data-slice2-scale="<?php echo rand(1, 2); ?>">
                <div class="sl-slide-inner">
					<div class="bg-img bg-img-<?php echo $i + 1; ?>">
						<?php echo $slides[$i]->text; ?>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div><!-- /sl-slider -->
    	
        <?php if($params->get('navArrows', 1)) : ?>
        <nav id="tcvn-nav-arrows<?php echo $module->id; ?>" class="nav-arrows">
            <span class="nav-arrow-prev">Previous</span>
            <span class="nav-arrow-next">Next</span>
        </nav>
        <?php endif; ?>
        
        <?php if($params->get('navDots', 1)) : ?>
        <nav id="tcvn-nav-dots<?php echo $module->id; ?>" class="nav-dots">
            <?php for($i=0; $i < count($slides); $i++) : ?>
            <span <?php echo (!$i) ? 'class="nav-dot-current"' : ''?>></span>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">	
jQuery(function($) {
	var Page = (function() {
		var $navArrows = $('#tcvn-nav-arrows<?php echo $module->id; ?>'),
			$nav = $('#tcvn-nav-dots<?php echo $module->id; ?> > span'),
			slitslider = $('#tcvn-slit-slider<?php echo $module->id; ?>').slitslider( {
				speed: 				<?php echo $params->get('speed', 800); ?>,
				optOpacity: 		<?php echo ($params->get('optOpacity', 1)) ? 'true' : 'false'; ?>,
				translateFactor: 	<?php echo $params->get('translateFactor', 230); ?>,
				maxAngle: 			<?php echo $params->get('maxAngle', 25); ?>,
				maxScale: 			<?php echo $params->get('maxScale', 3); ?>,
				autoplay: 			<?php echo ($params->get('autoplay', 1)) ? 'true' : 'false'; ?>,
				keyboard: 			<?php echo ($params->get('keyboard', 1)) ? 'true' : 'false'; ?>,
				interval: 			<?php echo $params->get('interval', 4000); ?>,
				
				onBeforeChange : function( slide, pos ) {
					$nav.removeClass('nav-dot-current');
					$nav.eq(pos).addClass('nav-dot-current');
				}
			} ),
			init = function() {
				initEvents();
			},
			initEvents = function() {
				$navArrows.children(':last').on('click', function() {
					slitslider.next();
					return false;
				});
				$navArrows.children(':first').on('click', function() {
					slitslider.previous();
					return false;
				});
				$nav.each( function(i) {
					$(this).on('click', function(event) {
						var $dot = $( this );
						if(!slitslider.isActive()) {
							$nav.removeClass('nav-dot-current');
							$dot.addClass('nav-dot-current');
						}
						slitslider.jump(i + 1);
						return false;
					} );
				} );
			};
			return { init : init };
	})();
	Page.init();
});
</script>