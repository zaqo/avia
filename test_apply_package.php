<?php 

	include ("header.php"); 
	include_once ("functions.php"); 
	require_once 'login_avia.php';

	include("/webservice/sapconnector.php");
	//include_once("login_avia.php");
	//ini_set("soap.wsdl_cache_enabled", "0");	
	class Flight
	{
			public $id;						
			public $id_NAV;
			public $flight_date;
			public $flight_num;
			public $direction;
			public $plane_id;
			public $plane_type;
			public $plane_mow;
			public $airport;
			public $passengers_adults;
			public $passengers_kids;
			public $customer;
			public $bill_to;
			public $plane_owner;
			public $services;
	}
	$content='';
	
	echo '<hr><br>';
	
	//$result=SAP_connector($req);
	$res=ApplyPackage(2);
	if ($res) echo "Package applied successfully! <br/>";
	else echo "ERROR: package application aborted! <br/>";
	//Show_page($content);
?>
	