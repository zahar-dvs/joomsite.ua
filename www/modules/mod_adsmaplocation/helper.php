<?php

/**
 * @package AdsMap Location Module for AdsManager
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author      Gotour WebSolutions Team <webmaster@gotour.it> - http://support.gotour.it
 */
// no direct access

defined('_JEXEC') or die('Restricted access');
define('JPATH_BASE', dirname(__FILE__) );

jimport( 'joomla.filesystem.file' );
jimport('joomla.plugin.plugin');
jimport( 'joomla.filter.output' );

$jspathjs = JPATH_BASE.DS.'components'.DS.'com_community';

if (file_exists( $jspathjs )) {

include_once($jspathjs.DS.'libraries'.DS.'core.php');
include_once($jspathjs.DS.'libraries'.DS.'messaging.php');
}

class modAdsMapLocationHelper

{

	function getMapHTML(&$params){



		global $mosConfig_live_site, $mosConfig_absolute_path;

		$uri		= JURI::root();
		$document	=& JFactory::getDocument();
		$css		= $uri. 'modules/mod_adsmaplocation/tmpl/style.css';
		$document->addStyleSheet($css);
		$profileinteg = $params->get('profileinteg', '0');
		$runpms = $params->get('runpms', '4');
	//	$itemid = $params->get('itemid', '');
		$itemid = intval($params->get('itemid', JRequest::getVar( 'Itemid', 0  )));
		$iconmarker = $params->get('iconmarker', 'auto-category');
		$showtitle = $params->get('showtitle', '1');
		$showdesc = $params->get('showdesc', '1');
		$showcat = $params->get('showcat', '1');
		$showprice = $params->get('showprice', '1');
		$valute = $params->get('valute', 'Euro');
		$showhits = $params->get('showhits', '1');
		$jquerycheck = $params->get('jquerycheck', '0');
		$fieldmap = $params->get('fieldmap', '0');
		$responsive = $params->get('resp', '1');
		$width = $params->get('width', '480');
		$height = $params->get('height', '340');
		$zoomlevelmap = intval($params->get('zoomlevelmap', "5"));
		$maptype = $params->get('maptype', "ROADMAP");
		$auto_center = $params->get('autocentermap', '1');
		$latdefault = $params->get('latdefault', '0');
		$lngdefault = $params->get('lngdefault', '0');
		$moduleclass_sfx = $params->get('moduleclass_sfx');
		?>	



		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false" /></script>
        <script type="text/javascript" src="<?php echo $uri; ?>modules/mod_adsmaplocation/js/markerclusterer_packed.js" /></script>

	 	<?php //load JQuery
		if ($jquerycheck==0){
		?>

         <script type="text/javascript" src="<?php echo $uri; ?>modules/mod_adsmaplocation/js/jquery-1.3.2.min.js"></script>

         <?php  } ?>


			    <script type="text/javascript">

			    	var mapgt = null;
					var markerCluster = null;
					var iconmarker = "<?php echo $iconmarker; ?>";
					var latdefault = "<?php echo $latdefault; ?>";
					var lngdefault = "<?php echo $lngdefault; ?>";
			    	var auto_center = "<?php echo $auto_center; ?>";
	
					function initializemap() {

						function toggleBounce(obj) {
								if (obj.getAnimation() != null) {
										obj.setAnimation(null);
												} else {
													stopBounce(markers);//stop bouncing markers
													obj.setAnimation(google.maps.Animation.BOUNCE);
												}
											}

						function stopBounce(markers){
							for (var k = 0; k < markers.length; k++) {
								if (markers[k].getAnimation() != null) { markers[k].setAnimation(null); }
							}        
						}
						    //if (GBrowserIsCompatible()) {
							var latlng = new google.maps.LatLng(latdefault, lngdefault);
							 
							var mapgtProp = {
							  zoom: <?php echo $zoomlevelmap; ?>,
							  mapTypeId:google.maps.MapTypeId.ROADMAP,
							  mapTypeId: google.maps.MapTypeId.<?php echo $maptype; ?>,
							  center: latlng,
							  streetViewControl:true,
							  };
							var mapgt=new google.maps.Map(document.getElementById("map_canvasmod")
							  ,mapgtProp);
							  
														 
					

						var markerType=[];

						markerType["red-dot"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/red-dot.png";
						markerType["red-dot_s"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/red-dot-s.png";
						markerType["house"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/house.png";
						markerType["house_s"] = "";
						markerType["house-flag"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/house-flag.png";
						markerType["house-flag_s"] = "";
						markerType["auto"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/auto.png";
						markerType["auto_s"] = "";
						markerType["basket"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/basket.png";
						markerType["basket_s"] = "";
						markerType["chat"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/chat.png";
						markerType["chat_s"] = "";
						markerType["drink"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/drink.png";
						markerType["drink_s"] = "";
						markerType["food"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/food.png";
						markerType["food_s"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/food-s.png";
						markerType["coffe"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/coffe.png";
						markerType["coffe_s"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/coffe-s.png";
						markerType["flag"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/flag.png";
						markerType["flag_s"] = "";
						markerType["user"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/user.png";
						markerType["user_s"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/user-s.png";
						
						
						
					
						<?php

						function pmsLinkmap($pmssystem, $uid, $username) {


						switch ($pmssystem) {

							case '1':

								//Uddeim
								$pmsLink = JRoute::_('index.php?option=com_uddeim&task=new&recip='.$uid);
								break;
							
							case '2':

								$pmsLink = JRoute::_('index.php?option=com_jam&id='.$username.'#new');
								break; 

							case '3':
								//Primezilla
								$pmsLink = JRoute::_('index.php?option=com_primezilla&page=write&Itemid=0&uid='.$uid);
								break;

							default:

								$pmsLink = '#';

							}

						return $pmsLink;

						}



						// Ricavo valori da url per compilazione mappa automatica



						// Verifico se il campo catid proviene dalla ricerca avanzata

						//Verifico se sono in home

						//$checkhome = "";

						//$checkhome = $_GET['view'];
						$checkhome = JRequest::getVar('view');

						

						//$catid_url = $_GET['catid'];
						$catid_url = JRequest::getVar('catid');



						//Se il valore ГЁ nullo verifico se proviene dalla ricerca semplice



						if($catid_url==NULL)

							$catid_url = $_POST['catid'];





						// Verifico se il campo tsearch proviene dalla ricerca avanzata



						//$text_url = $_POST['ad_text'];



						//Se il valore ГЁ nullo verifico se proviene dalla ricerca semplice

		

							if($text_url==NULL)

							$text_url = $_POST['tsearch'];





						//test import campi ricercabili (devo capire come ricavare il nome del field)



						jimport( 'joomla.session.session' );	



						$currentSession = JSession::getInstance('none',array());


						$filters['fields'] = $currentSession->get("searchfieldssql"," ");

						



						$db =& JFactory::getDBO();

			

			$sqlmap = "SELECT a.*, a.id, a.".$fieldmap."_lat, a.".$fieldmap."_lng, p.name as parent, p.id as parentid, c.name as cat, c.id as catid, u.username as user ".

							   "FROM #__adsmanager_ads as a ". 

							   "LEFT JOIN #__adsmanager_adcat as adcat ON adcat.adid = a.id ".

							   "LEFT JOIN #__users as u ON a.userid = u.id ".

							   "LEFT JOIN #__adsmanager_categories as c ON adcat.catid = c.id ".

							   "LEFT JOIN #__adsmanager_categories as p ON c.parent = p.id ".

					

							    "WHERE a.published = 1 AND c.published = 1 AND (a.ad_zip LIKE '%".$text_url."%' OR a.ad_city LIKE '%".$text_url."%' OR a.ad_headline LIKE '%".$text_url."%') ".substr($filters['fields'], 2)." ";

							   //Controllo se catid ГЁ vuoto, se si allora visualizzo tutti i marker nella mappa

								$sqlmap .= "AND (a.".$fieldmap."_lat > 1)"." ";
								

							   if(($catid_url!=-1)&&($catid_url!=0))

							  	$sqlmap .= "AND adcat.catid = ".$catid_url." ";

								

							   	$sqlmap .= "GROUP BY a.id ORDER BY date_created DESC, id DESC ";



						$db->setQuery($sqlmap);



						$rowmap = $db->loadObjectList();



						//Field name

						$nlat = $fieldmap."_lat";

						$nlng = $fieldmap."_lng";

						
						?>
						var markers = [];
						var tab1map = [];
																
						<?php
						foreach($rowmap as $datamap){

						$latmap =(float)$datamap->$nlat;
						$lngmap =(float)$datamap->$nlng;

						$linkTarget = JRoute::_( "index.php?option=com_adsmanager&view=details&id=".$datamap->id."&catid=".$datamap->catid."&Itemid=".$itemid);

						if ($showtitle==1){
						
						$data->ad_headline = preg_replace ("/(\r\n)+|(\n|\r)+/"," ",stripslashes($datamap->ad_headline));
						$headline="<a href='".$linkTarget."' target='_parent'>".htmlspecialchars($datamap->ad_headline)."</a>";
?>
					 
<?php
						}





						if ($showdesc==1){



						$datamap->ad_text = preg_replace ("/(\r\n)+|(\n|\r)+/"," ",stripslashes($datamap->ad_text));



						$description = htmlspecialchars (mb_substr($datamap->ad_text, 0, 160)." [...]");



						



						}



						



						if ($showcat==1){



						if ($datamap->parent !=NULL){



						$category= $datamap->parent." / ".$datamap->cat;



						$category ="<b>".JText::_('Категория').": </b>".$category;



						}else{



						$category= $datamap->cat;



						$category ="<b>Категория : </b>".$category;



							}



						}



						



						if ($showprice==1){



						$price=$datamap->ad_price." ".$valute;



						}

												



						if ($showhits==1){



						$hitspic = "<img src='".$uri."modules/mod_adsmaplocation/images/ad_chart.png' alt='Hits'/>";



						$hits = $datamap->views;



						$hits = "<b style='font-size:10px; color: #000000;'>Просмотров : ".$hits." ".$hitspic;



						}		
						$db =& JFactory::getDBO();
						//Recupero dal Db le immagini
						$sqlimg = "SELECT * FROM #__adsmanager_ads ";
						$sqlimg .= "WHERE id = $datamap->id ORDER BY id DESC LIMIT 0,1";
			
						$db->setQuery($sqlimg);
						$db->query();
			
						$rigads = $db->loadObject();
						$imgads = $rigads->images;
						
						$my_string = $imgads;			
						$countimg = substr_count($my_string,"thumbnail");
						$suddivisa = explode(",", $my_string);			
						$indeximage = explode(":",$suddivisa[0]);
						$index = $indeximage[1];
						$uri= JURI::root(); 
						
						if ($countimg!=0){
						 
						 $assolutepath = JPATH_BASE."/images/com_adsmanager/ads/".JFilterOutput::stringURLSafe($data->ad_headline)."_".$datamap->id."_".$index."_t.jpg";
							
						$pic = $uri."images/com_adsmanager/ads/".JFilterOutput::stringURLSafe($data->ad_headline)."_".$datamap->id."_1_t.jpg";
						
						$imageExists = JFile::exists($assolutepath);
						

						if ($imageExists)
						{

						$photo="<a href='".$linkTarget."' target='_parent'><img src='".$pic."' style='float:left; border:solid #cccccc 1px;'/></a>";
						}else{
						$photo="<a href='".$linkTarget."' target='_parent'><img src='".$uri."components/com_adsmanager/images/nopic.gif' alt='nopic' style='float:left; border:solid #cccccc 1px;'/></a>"; 
						}
						
						} else {
							
							$photo="<a href='".$linkTarget."' target='_parent'><img src='".$uri."components/com_adsmanager/images/nopic.gif' alt='nopic' style='float:left; border:solid #cccccc 1px;'/></a>"; 
							
							}
						

						if (JFile::exists($assolutepath))
						{

						$photo="<a href='".$linkTarget."' target='_parent'><img src='".$pic."' style='float:left; border:solid #cccccc 1px;'/></a>";
						}else{
						$photo="<a href='".$linkTarget."' target='_parent'><img src='".$uri."components/com_adsmanager/images/nopic.gif' alt='nopic' style='float:left; border:solid #cccccc 1px;'/></a>"; 
						}
						
																

						//$photo="<a href='".$linkTarget."' target='_parent'><img src='".$pic."' style='float:left; border:solid #cccccc 1px;'/></a>";

						$userid = $datamap->userid;
						$username = $datamap->user;
						$allads = JRoute::_("index.php?option=com_adsmanager&view=list&user=".$userid."&Itemid=".$itemid);

						switch ($profileinteg)

						{



							case 0:

							//$target = sefRelToAbs("index.php?option=com_adsmanager&amp;page=show_user&amp;userid=".$userid);

							$target = JRoute::_("index.php?option=com_adsmanager&view=list&user=".$userid);



							break;



							case 1:

							// Build the path to the model based upon a supplied base path

									$path = JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';



									if (file_exists( $path )) {

										include_once($path);

										$target = JRoute::_('index.php?option=com_community&view=profile&userid='.$userid);



										}else{

										$target = JRoute::_('index.php?option=com_community&view=profile&userid='.$userid);

										}



							break;



							case 2:

							$target = JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$userid);

							break; 

						}





						?>

						//Controllo Marker dinamico secondo tipo categoria

						//Controllo se l'immagine esiste

						<?php 

						

						//exists($autocatpath);

						if (JFile::exists(JPATH_SITE.DS.'images'.DS.'com_adsmanager'.DS.'categories'.DS.$datamap->catid.'cat_t.jpg')) {
?>



						markerType["auto-category"] = "<?php echo $uri; ?>images/com_adsmanager/categories/<?php echo $datamap->catid; ?>cat_t.jpg";



						<?php } else { ?>



						markerType["auto-category"] = "<?php echo $uri; ?>modules/mod_adsmaplocation/images/marker/flag.png";



						<?php } ?>



					
						var iconBase = markerType[iconmarker];
						
						 
						<?php /*?>var latlngmap = new GLatLng(<?php echo $latmap; ?>,<?php echo $lngmap; ?>);<?php */?>



						<?php

						$linkpic = "<img src='".$uri."modules/mod_adsmaplocation/images/ad_info.png' alt='View Ad'/>";


							$tab1map= "<table class='contentab' cellspacing='0' cellpadding='2' align='center'><tr><td colspan='2' class='title1'>".$headline."</td></tr><tr><td><div>".$photo."</div></td><td><div style='float:right; color: #000000;'>".$description."</div></td></tr><tr><td class='info' align='left' style='color: red;'>".$price."</td><td class='info' align='right'><a style='color: red;' href='".$linkTarget."' target='_parent' >Подробнее</a> ".$linkpic."</td></tr><tr><td colspan='2'><div align='left' style='font-size:10px; color: #000000;'>".$category."</div></td></tr><tr><td width='20%'><div align='left' style='font-size:10px; color: #000000;'><b>Добавил &nbsp;</b><a href='".$target."'>".JFilterOutput::stringURLSafe($datamap->user)."</a></div></td><td width='80%'><div align='right'><a style='color:red;' href='".$allads."'>Все обявления автора</b></a></div></td></tr><tr>";



								//Returns a link for the selected pms
								switch ($runpms) {

									case '0': // JomSocial

									{

									// Build the path to the model based upon a supplied base path
									$path = JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';


									if (file_exists( $path )) {
										$onclick = CMessaging::getPopup($userid);
										$tab1map .= "<td width='auto'><div align='left'><a href='javascript:void(0)' onclick=".$onclick."><b>".JText::_('MOD_ADSMAPLOCATION RESPOND')."</b></a></div></td>";
										}else{
										$tab1map .= "<td width='auto'><div align='left' style='font-size:10px;'>Pms not configurated</div></td>";

										}

									}

									break;
									case '1': // Uddeim
						$tab1map .= "<td width='auto'><div align='left'><b><a id='pmslink' href='".pmsLinkmap($runpms, $userid, $username)."'><b>Send Message</b></a></div></td>";

									break;
									case '2': // Jam
									  $tab1map .= "<td width='auto'><div align='left'><b><a id='pmslink' href='".pmsLinkmap($runpms, $userid, $username)."'><b>".JText::_('')."</b></a></div></td>";

									  break;
									  case '3': // Primezilla
									  $tab1map .= "<td width='auto'><div align='left'><b><a id='pmslink' href='".pmsLinkmap($runpms, $userid, $username)."'><b>".JText::_('')."</b></a></div></td>";
									  break;
									default:
									  $tab1map.="<td width='auto'></td>";
									break;
								}
								 
								
								$tab1map .= "<td width='auto'><div align='right' style='font-size:10px; color: #000000;'>".$hits."</div></td></tr></table>"; 

							?>
													
						
												  //alert (latLng);												 
						 	var latLng = new google.maps.LatLng(<?php echo $latmap; ?>,<?php echo $lngmap; ?>);
						 	var marker = new google.maps.Marker({'position': latLng, 'icon': iconBase});
						 												  
						    infowindow = null;
							google.maps.event.addListener(marker, 'click', function() {
								
								
								var tab1map = ("<?php echo $tab1map; ?>");
															
								if (infowindow) {
									infowindow.close();
								}
								infowindow = new google.maps.InfoWindow({
									content: "<?php echo $tab1map ?>"
								});
								google.maps.event.addListener(infowindow, 'closeclick', function() {
									
									stopBounce(markers);
								});
								infowindow.open(mapgt, this);
						
								toggleBounce(this);
							});				  
											  markers.push(marker);
				
											
						
							
						//make sure they fit screen
						 var bounds = new google.maps.LatLngBounds();
						  for(var i=0;i<markers.length;i++){
							bounds.extend(markers[i].getPosition());
						  }
						  
						 				
							
						  // AUTO CENTER
						  if(auto_center == "1"){

				 				mapgt.fitBounds(bounds); 

				 			}

						
						
					
					  	

						<?php } ?>
					
						var mcOptions = {gridSize: 50, maxZoom: 15};
						markerCluster = new MarkerClusterer(mapgt, markers, mcOptions);

						  }


				   jQuery.noConflict();

  					
					google.maps.event.addDomListener(window, "load", initializemap);  
					
					<?php if ($responsive==1){ ?>
					google.maps.event.addDomListener(window, 'resize', initializemap);
					<?php } ?>
					
					

			    </script> <?php


				if ($responsive==1){
				$widthmap = '100%';
				}else{
				$widthmap = $width.'px';
				}
		$viewmapmod = '<div align="center"><div id="map_canvasmod" class="'.$moduleclass_sfx.'" style="width:'.$widthmap.'; height:'.$height.'px; background-color:#E5E3DF;"><h3 style="color: #999999; margin: 0.5em;">Loading...</h3><img src="'.$uri.'modules/mod_adsmaplocation/images/loader.gif" id="loaderGif" /></div></div>';



		ob_start();

		$viewmapmod .= ob_get_contents();

		@ob_end_clean();

		return $viewmapmod; 				   

	}

}

?>