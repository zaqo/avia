<?php 

	include ("header.php"); 
	include_once ("functions.php"); 
	require_once 'login_avia.php';

	include("/webservice/sapconnector.php");
	//include_once("login_avia.php");
	ini_set("soap.wsdl_cache_enabled", "0");	
	
	$content='';
	
	echo '<hr><br>';
	
	//$result=SAP_connector($req);
	$res=SAP_export_flight(46);
	if ($res) echo "Flight exported successfully! <br/>";
	else echo "ERROR: Flight export aborted! <br/>";
	//Show_page($content);
?>
	