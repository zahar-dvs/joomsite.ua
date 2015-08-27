<?php
/**
 * @package Plugin Google Maps GTMap Locator
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author      Gotour WebSolutions Team <webmaster@gotour.it> - http://support.gotour.it
 */
defined('_JEXEC') or die('Accesso negato');

class AdsManagerGTMapLocator {

	var $_db;
	
	function getListDisplay($contentid,$field)
	{
	  return AdsManagerGTMapLocator::getDetailsDisplay($contentid,$field);
	}

	function getDetailsDisplay($content,$field)
	{
	//Field name
	$nlat = $field->name."_lat";
	$nlng = $field->name."_lng";
	
	$lat = (float)$content->$nlat;
	$lng = (float)$content->$nlng;

	$db =& JFactory::getDBO();
	$uri = JURI::root();
	$document =& JFactory::getDocument();
	$css = $uri . 'images/com_adsmanager/plugins/AdsManagerGTMapLocator/style1.css';
	$document->addStyleSheet($css);
	$contentid = $content->id;
	
	$fieldid = $field->fieldid;
	if (($lat!=NULL) && ($lng!=NULL))
		{
		
			$db =& JFactory::getDBO();
			$query  = $db->getQuery(true);
			$query
				->select('*')
				->from('#__adsmanager_gtmaplocator_adm')
				->where('fieldid = '. (int)$fieldid);
		
		$this->_db->setQuery($query);
		$conf  = $this->_db->loadObject();
		
		$map_width = $conf->map_width;
		$map_height= $conf->map_height;
		$zoom= $conf->zoom;
	

	$htmlmap ='<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
	$htmlmap .= '<script type="text/javascript" src="'.$uri.'images/com_adsmanager/plugins/AdsManagerGTMapLocator/jquery-1.4.2.min.js"></script>';
	$htmlmap .= '<script type="text/javascript" src="'.$uri.'images/com_adsmanager/plugins/AdsManagerGTMapLocator/jquery-ui-1.8.1.custom.min.js"></script>';
	$htmlmap .= '<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>';
	$htmlmap .= '<script type="text/javascript" src="'.$uri.'images/com_adsmanager/plugins/AdsManagerGTMapLocator/main.js"></script>';
	
	$htmlmap .= '<script type="text/javascript">';
	$htmlmap .= 'var PostCodeid'.$contentid.' = "#address";';
    $htmlmap .= 'var longval'.$contentid.' = "#hidLong";';
    $htmlmap .= 'var latval'.$contentid.' = "#hidLat";';
    $htmlmap .= 'var geocoder'.$contentid.';';
    $htmlmap .= 'var map'.$contentid.';';
    $htmlmap .= 'var marker'.$contentid.';';

        $htmlmap .= 'function initialize'.$contentid.'() {';
		$htmlmap .= 'geocoder'.$contentid.' = new google.maps.Geocoder();';
		
        $htmlmap .= 'initialLat = '.$lat.';';
		$htmlmap .= 'initialLong = '.$lng.';';
		$htmlmap .= 'var latlng'.$contentid.' = new google.maps.LatLng(initialLat, initialLong);';
		$htmlmap .= 'var options = { zoom: '.$zoom.', center: latlng'.$contentid.', mapTypeId: google.maps.MapTypeId.ROADMAP };';
		$htmlmap .= 'map'.$contentid.' = new google.maps.Map(document.getElementById("geomap_'.$contentid.'"), options);';
		   
		$htmlmap .= 'marker'.$contentid.' = new google.maps.Marker({ map: map'.$contentid.', draggable: false, position: latlng'.$contentid.' });';
        $htmlmap .= '};';
		 
	$htmlmap .= '</script>';
	$htmlmap .= '<div id="geomap_'.$contentid.'" style="width:'.$map_width.'px; height:'.$map_height.'px;"><p>Loading Please Wait...</p></div>';
	$htmlmap .= '<script type="text/javascript">initialize'.$contentid.'();</script>';

		return ($htmlmap);
		
	}
	}

	function getFormDisplay($content,$field, $default=null)
	{
		
	//Field name
	$nlat = $field->name."_lat";
	$nlng = $field->name."_lng";
	
	$lat = (float)$content->$nlat;
	$lng = (float)$content->$nlng;
	
	//Show map
	
	$contentid = $content->id;
	$db =& JFactory::getDBO();
	$uri = JURI::root();
	$document =& JFactory::getDocument();
	$css = $uri.'images/com_adsmanager/plugins/AdsManagerGTMapLocator/js/style1.css';
	$document->addStyleSheet($css);
	
		$fieldid = $field->fieldid;
		$this->_db->setQuery("SELECT * FROM #__adsmanager_gtmaplocator_adm WHERE fieldid = ".(int)$field->fieldid);
		$conf = $this->_db->loadObject();
		
		if (($lat==NULL||0) && ($lng==NULL||0)){
		$lat = $conf->lat;
			$lng = $conf->lng;
		}
	
	$map_width = $conf->map_width;
	$map_height= $conf->map_height;
	$zoom= $conf->zoom;

	$htmlmap  ='<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
	$htmlmap .= '<script type="text/javascript" src="'.$uri.'images/com_adsmanager/plugins/AdsManagerGTMapLocator/jquery-1.4.2.min.js"></script>';
	$htmlmap .= '<script type="text/javascript" src="'.$uri.'images/com_adsmanager/plugins/AdsManagerGTMapLocator/jquery-ui-1.8.1.custom.min.js"></script>';
	$htmlmap .= '<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>';
	$htmlmap .= '<script type="text/javascript" src="'.$uri.'images/com_adsmanager/plugins/AdsManagerGTMapLocator/main.js"></script>';
	
	$htmlmap .= '<style type="text/css">';
	$htmlmap .= '.ui-autocomplete { overflow-y: hidden; width:450px; background-color:#E4E4E4; border: 1px solid #666; }';
	$htmlmap .= '* html .ui-autocomplete { /* IE max- */height: expression( this.scrollHeight > 400 ? "400px" : "auto" ); }';
	//$htmlmap .= '.ui-autocomplete { max-height: 400px; }';
	$htmlmap .= '.ui-autocomplete li { font-size:12pt; float:left; list-style-type:none; padding: 3px; }';
	$htmlmap .= '</style>';
	
	$htmlmap .= '<script type="text/javascript">';

	$htmlmap .= 'var PostCodeid = "#address";';
    $htmlmap .= 'var longval = "#hidLong";';
    $htmlmap .= 'var latval = "#hidLat";';
    $htmlmap .= 'var geocoder;';
    $htmlmap .= 'var map;';
    $htmlmap .= 'var marker;';
	$htmlmap .= 'function initialize() {';
		$htmlmap .= 'initialLat = $(latval).val();';
		$htmlmap .= 'initialLong = $(longval).val();';
		$htmlmap .= 'var latlng = new google.maps.LatLng(initialLat, initialLong);';
		$htmlmap .= 'var options = { zoom: '.$zoom.', center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP };';
		$htmlmap .= 'map = new google.maps.Map(document.getElementById("geomap"), options);';
		$htmlmap .= 'geocoder = new google.maps.Geocoder();';   
		$htmlmap .= 'marker = new google.maps.Marker({ map: map, draggable: true, position: latlng });';
		$htmlmap .= 'google.maps.event.addListener(marker, "dragstart", function() { });';
		$htmlmap .= 'google.maps.event.addListener(marker, "dragend", function (event) {';
		$htmlmap .= 'document.getElementById("hidLat").value = marker.getPosition().lat();';
		$htmlmap .= 'document.getElementById("hidLong").value = marker.getPosition().lng();';
		$htmlmap .= 'var point = marker.getPosition();';
		$htmlmap .= 'map.panTo(point);';
		$htmlmap .= '});';
	$htmlmap .= '};';
	$htmlmap .= '</script>';
		$htmlmap .= '&#1042;&#1074;&#1077;&#1076;&#1080;&#1090;&#1077; &#1072;&#1076;&#1088;&#1077;&#1089; &#1080; &#1085;&#1072;&#1078;&#1084;&#1080;&#1090;&#1077; &quot;&#1053;&#1072;&#1081;&#1090;&#1080; &#1085;&#1072; &#1082;&#1072;&#1088;&#1090;&#1077;&quot;, &#1077;&#1089;&#1083;&#1080; &#1072;&#1076;&#1088;&#1077;&#1089; &#1085;&#1072; &#1082;&#1072;&#1088;&#1090;&#1077; &#1086;&#1090;&#1086;&#1073;&#1088;&#1072;&#1079;&#1080;&#1083;&#1089;&#1103; &#1085;&#1077;&#1090;&#1086;&#1095;&#1085;&#1086;, &#1087;&#1077;&#1088;&#1077;&#1090;&#1072;&#1097;&#1080;&#1090;&#1077; &#1084;&#1099;&#1096;&#1082;&#1086;&#1081; &#1084;&#1077;&#1090;&#1082;&#1091; &#1074; &#1085;&#1091;&#1078;&#1085;&#1086;&#1077; &#1084;&#1077;&#1089;&#1090;&#1086;. .<br><input id="address" name="address" type="text"><input type="submit" id="findbutton" value="&#1053;&#1072;&#1081;&#1090;&#1080; &#1085;&#1072; &#1082;&#1072;&#1088;&#1090;&#1077;" />';
	$htmlmap .= '<div id="geomap" style="width:'.$map_width.'px; height:'.$map_height.'px;"><p>Loading Please Wait...</p></div>';
	$htmlmap .= '<input id="hidLat" name="hidLat" value="'.$lat.'">';
	$htmlmap .= '<input id="hidLong" name="hidLong" value="'.$lng.'">' ;

	return ($htmlmap);

	}

	function onFormSave(&$content,$field)
	{
	
		$lat = JRequest::getFloat("hidLat",0);
		$lng = JRequest::getFloat("hidLong",0);
		
		if (!isset($content->data)) {
			$content->data = array();
			$content->data['fields'] = array();
		}
		$content->data['fields'][$field->name.'_lat'] = $lat;
		$content->data['fields'][$field->name.'_lng'] = $lng;
		
		return null;
	
	}

	function onDelete($directory,$contentid = -1)
	{

	}

	function getEditFieldJavaScriptDisable()
	{
		$return  = "elem=getObject('divGtMaplocator');";
        $return .= "elem.style.visibility = 'hidden';";
		$return .= "elem.style.display = 'none';";
		$return .= "elem=getObject('gtmap_map_width');";
		$return .= "elem.setAttribute('mosReq',0);";
		$return .= "elem=getObject('gtmap_map_height');";
		$return .= "elem.setAttribute('mosReq',0);";
		$return .= "elem=getObject('gtmap_zoom');";
		$return .= "elem.setAttribute('mosReq',0);";
		$return .= "elem=getObject('gtmap_lat');";
		$return .= "elem.setAttribute('mosReq',0);";
		$return .= "elem=getObject('gtmap_lng');";
		$return .= "elem.setAttribute('mosReq',0);";
		return $return;
		
	}
	
	function getEditFieldJavaScriptActive()
	{
        $return = "disableAll();";
		$return .= "elem=getObject('divGtMaplocator');";
        $return .= "elem.style.visibility = 'visible';";
        $return .= "elem.style.display = 'block';";
		$return .= "elem=getObject('gtmap_map_width');";
		$return .= "elem.setAttribute('mosReq',1);";
		$return .= "elem=getObject('gtmap_map_height');";
		$return .= "elem.setAttribute('mosReq',1);";
		$return .= "elem=getObject('gtmap_zoom');";
		$return .= "elem.setAttribute('mosReq',1);";
		$return .= "elem=getObject('gtmap_lat');";
		$return .= "elem.setAttribute('mosReq',1);";
		$return .= "elem=getObject('gtmap_lng');";
		$return .= "elem.setAttribute('mosReq',1);";
		return $return;
	}
	
	function getEditFieldOptions($fieldid)
	{
		
		$db =& JFactory::getDBO();
		$query  = $db->getQuery(true);
		//$this->_db->setQuery("SELECT * FROM ".$db->nameQuote('#__adsmanager_gtmaplocator_adm')."WHERE fieldid = '$fieldid'");
		//$row = $this->_db->loadObject();
		
		$query
			->select('*')
			->from('#__adsmanager_gtmaplocator_adm')
			->where('fieldid = '. (int)$fieldid);
		
		$this->_db->setQuery($query);
		$row  = $this->_db->loadObject();
		

		$htmlmap = "<div id='divGtMaplocator'><hr><b>GT MapLocator Plugin by Gotour WebSolutions</b><br><br>If you use AdsMapLocation Module & Plugin, <br>please post a rating and a review at the Joomla! Extensions Directory.<br><br>
		<b>To Request support or ask informations open ticket </b><a href=http://support.gotour.it>Here</a> or Visit our site <a href=http://websolution.gotour.it>link</a>
		<br><br>=== SETTINGS ===<br>";
		$htmlmap .= "<table class='adminform'>";
		$htmlmap .= "<tr>";
		$htmlmap .= "<td width='20%'>Map Width</td>";
		$htmlmap .= "<td width='20%' align=left><input type='text' id='gtmap_map_width' name='gtmap_map_width' mosReq=1 mosLabel='Map Width' class='inputbox' value='".@$row->map_width."' /></td>";
		$htmlmap .= "<td>&nbsp;</td>";
		$htmlmap .= "</tr>";
		$htmlmap .= "<tr>";
		$htmlmap .= "<td width='20%'>Map Height</td>";
		$htmlmap .= "<td width='20%' align=left><input type='text' id='gtmap_map_height' name='gtmap_map_height' mosReq=1 mosLabel='Map Height' class='inputbox' value='".@$row->map_height."' /></td>";
		$htmlmap .= "<td>&nbsp;</td>";
		$htmlmap .= "</tr>";
		$htmlmap .= "<tr>";
		$htmlmap .= "<td width='20%'>Default Zoom</td>";
		$htmlmap .= "<td width='20%' align=left><input type='text' id='gtmap_zoom' name='gtmap_zoom' mosReq=1 mosLabel='Default Zoom' class='inputbox' value='".@$row->zoom."' /></td>";
		$htmlmap .= "<td>&nbsp;</td>";
		$htmlmap .= "</tr>";
		$htmlmap .= "<tr>";
		$htmlmap .= "<td width='20%'>Default Lat</td>";
		$htmlmap .= "<td width='20%' align=left><input type='text' id='gtmap_lat' name='gtmap_lat' mosReq=1 mosLabel='Default Lat' class='inputbox' value='".@$row->lat."' /></td>";
		$htmlmap .= "<td>&nbsp;</td>";
		$htmlmap .= "</tr>";
		$htmlmap .= "<tr>";
		$htmlmap .= "<td width='20%'>Default Lng</td>";
		$htmlmap .= "<td width='20%' align=left><input type='text' id='gtmap_lng' name='gtmap_lng' mosReq=1 mosLabel='Default Lng' class='inputbox' value='".@$row->lng."' /></td>";
		$htmlmap .= "<td>&nbsp;</td>";
		$htmlmap .= "</tr>";
		$htmlmap .= "</table>";
		$htmlmap .= "</div>";
		return $htmlmap;
	}

	function saveFieldOptions($field)
	{
		$this->install();
		$fieldid = $field->fieldid;
		$map_width = JRequest::getInt("gtmap_map_width",400);
		$map_height =JRequest::getInt("gtmap_map_height",300);
		$lat = JRequest::getInt("gtmap_lat",20);
		$lng = JRequest::getInt("gtmap_lng",20);
		$zoom = JRequest::getInt("gtmap_zoom",13);
	
		$this->_db->setQuery("DELETE FROM #__adsmanager_gtmaplocator_adm WHERE fieldid = ".(int)$fieldid);
		$this->_db->query();
		$this->_db->setQuery("INSERT INTO #__adsmanager_gtmaplocator_adm VALUES ($fieldid,$map_width,$map_height,$zoom,$lat,$lng)");
		$this->_db->query();
		
		//rimosse per joomla 3.0 da fixare
		//$this->_db->setQuery("ALTER IGNORE TABLE #__adsmanager_ads ADD ".$field->name."_lat TEXT default NULL;");
		//$this->_db->query();
		//$this->_db->setQuery("ALTER IGNORE TABLE #__adsmanager_ads ADD ".$field->name."_lng TEXT default NULL;");
		//$this->_db->query();
		
		$query = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."_lat` FLOAT default NULL;";
		$this->_db->setQuery($query);
        try{
            $this->_db->query();
        }catch(Exception $e){
            
        }
		
		$query = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."_lng` FLOAT default NULL;";
		$this->_db->setQuery($query);
        try{
            $this->_db->query();
        }catch(Exception $e){
            
        }
		
		
		
	
		return;
	}
	
	function getFieldName()
	{
		return "GTMapLocator";
	}
	
	function install()
	{
		
		$query = "CREATE TABLE IF NOT EXISTS `#__adsmanager_gtmaplocator_adm` ( ".
			  "`fieldid` int(10) unsigned default NULL, ".
			  "`map_width` int(10) unsigned default '500', ".
			  "`map_height` int(10) unsigned default '300', ".
			  "`zoom` int(10) unsigned default '13', ".
			  "`lat` TEXT default NULL, ".
			  "`lng` TEXT default NULL, ".
			  "PRIMARY KEY  (`fieldid`) ".
			  "); ";
			 
		$this->_db->setQuery($query);
		$this->_db->query();
	
	$result = $this->_db->loadObject();
		
	}
	
	function uninstall()
	{
		$query = "DROP TABLE `#__adsmanager_gtmaplocator_adm`";
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	function __construct()
	{
		$db	=& JFactory::getDBO();
		$this->_db = $db;
	}
}

$plugins["GTMapLocator"] = new AdsManagerGTMapLocator();
?>