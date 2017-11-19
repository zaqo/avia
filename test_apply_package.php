<?php 

	include ("header.php"); 
	include_once("login_avia.php");
	include_once("apply_package.php");
	include_once ("functions.php"); 
	//require_once 'login_avia.php';

	include("/webservice/sapconnector.php");
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
	class Item
	{
			public $ItmNumber;						
			public $Material;
			public $TargetQty;
			public $PurchNoS;
			public $PoDatS;
			public $PoMethS;
			public $SalesDist;
	}

	class ItemList
	{
			public $item;
	}
	class Request
	{
			public $Servicemode;
			public $IdSalescontract;
			public $IdSalesorder;
			public $IdAircraft;
			public $IdAirport;
			public $IdDirection;
			public $IdFlight;
			public $Billdate;
			public $IdPlaneowner;
			public $SalesItemsIn;
			public $Return2;
			
	}
	//ini_set("soap.wsdl_cache_enabled", "0");	
	
	$content='';
	
	echo '<hr><br>';
	
	//$result=SAP_connector($req);
	for ($i=0;$i<20;$i++)
	$res=ApplyPackage(642);
	if ($res) echo "Package applied successfully! <br/>";
	else echo "ERROR: package application aborted! <br/>";
	//Show_page($content);
?>
	