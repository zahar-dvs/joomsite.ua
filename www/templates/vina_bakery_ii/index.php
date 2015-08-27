<?php
/**
 * @package Vina Bakery
 * @author VinaGecko.com http://VinaGecko.com
 * @copyright Copyright (C) 2014 www.VinaGecko.com
 * @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');   

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"  lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"  lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"  lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?php echo $this->language; ?>"> <!--<![endif]-->
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <jdoc:include type="head" />
        <?php
			$style  = array('gallery.css');
			$script = array('jquery.touchSwipe.min.js','jquery.isotope.min.js');
			
			if($this->helix->Param('scroll_effect')) {
				$script[] = 'wow.js';
				$style[]  = 'animate.css';
			}
            
			$this->helix->Header()
            ->setLessVariables(array(
				'preset'=>$this->helix->Preset(),
				'header_color'=> $this->helix->PresetParam('_header'),
				'bg_color'=> $this->helix->PresetParam('_bg'),
				'text_color'=> $this->helix->PresetParam('_text'),
				'link_color'=> $this->helix->PresetParam('_link')
            ))
            ->addLess('master', 'template')
			->addJs($script)
			->addCss($style)
            ->addLess( 'presets',  'presets/'.$this->helix->Preset() );
        ?>
    </head>
    <body <?php echo $this->helix->bodyClass('bg hfeed clearfix'); ?>>
		<div class="row-offcanvas row-offcanvas-left">
			<div>
				<div class="body-innerwrapper">
				<!--[if lt IE 8]>
				<div class="chromeframe alert alert-danger" style="text-align:center">You are using an <strong>outdated</strong> browser. Please <a target="_blank" href="http://browsehappy.com/">upgrade your browser</a> or <a target="_blank" href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</div>
				<![endif]-->
				<?php
					$this->helix->layout();
					$this->helix->Footer();
				?>
				<jdoc:include type="modules" name="debug" />
				</div>
			</div>
			<div class="hidden-desktop sp-mobile-menu nav-collapse collapse sidebar-offcanvas">
				<button type="button" class="hidden-desktop btn btn-primary vina-menu-small" data-toggle="offcanvas">
					<i class="icon-remove"></i>
				</button>
				<?php
					$mobilemenu = $this->helix->loadMobileMenu();
					echo $mobilemenu->showMenu(); 
				?>   
			</div>
		</div>
    </body>
	<?php if($this->helix->Param('scroll_effect')) : ?>
	<script type="text/javascript">
		Array.prototype.random = function (length) {
			return this[Math.floor((Math.random()*length))];
		}
		
		var effects      = ['fadeInDown', 'pulse', 'bounceIn', 'fadeIn', 'flipInX'];
		var scrollEffect = "<?php echo $this->helix->Param('effect'); ?>";
		<?php if($this->helix->Param('effect') == 'random') echo 'scrollEffect = effects.random(effects.length);'; ?>
		
		var wow = new WOW(
		{
			boxClass:     'row-fluid',      // animated element css class (default is wow)
			animateClass:  scrollEffect +' animated', // animation css class (default is animated)
			offset:       0,          // distance to the element when triggering the animation (default is 0)
			mobile:       false        // trigger animations on mobile devices (true is default)
		});
		wow.init();
	</script>
	<?php endif; ?>
</html>