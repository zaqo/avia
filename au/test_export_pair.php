<?php 

	include ("header.php"); 
	include_once("login_avia.php");
	include_once ("functions.php"); 
	include_once ("apply_discounts.php");

	include("/webservice/sapconnector.php");
		class Flight
	{
			public $id;						
			public $id_NAV;
			public $flight_date;
			public $time_fact;
			public $flight_num;
			public $flight_type;
			public $flight_cat;
			public $direction;
			public $plane_id;
			public $plane_type;
			public $plane_class;
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
			public $ITM_NUMBER;						
			public $MATERIAL;
			public $TARGET_QTY;
			public $COND_TYPE;
			public $COND_VALUE;
			public $CURRENCY;
			public $ID_AODB;
			public $ID_TERMINAL;
			public $ID_AIRPORT;
			public $ID_AIRCRAFTCLASS;
			//public $PurchNoS;
			//public $PoDatS;
			//public $PoMethS;
			//public $SalesDist;
	}

	class ItemList
	{
			public $item;
	}
	class Request
	{
			public $SERVICEMODE;
			public $ID_SALESCONTRACT;
			public $FLIGHTDATEIN;
			public $FLIGHTDATEOUT;
			public $FLIGHTTIMEIN;
			public $FLIGHTTIMEOUT;
			public $ID_AIRCRAFTTYPEIN;
			public $ID_AIRCRAFTTYPEOUT;
			public $ID_NOOFFLIGHTIN;
			public $ID_NOOFFLIGHTOUT;
			public $ID_REGISTRATIONIN;
			public $ID_REGISTRATIONOUT;
			public $ID_FLIGHTTYPE;
			public $ID_FLIGHTCATEGORY;
			public $ID_PLANEOWNER;
			public $SALES_ITEMS_IN;
			public $ID_AIRPORTCLASS;
			public $RETURN2;
			//public $IdTerminal;
			//public $IdAodb;
			//public $Aodbdate;
			//public $IdSalesorder;
			//public $IdAircraft;
			//public $IdAircraftclass;
			//public $IdAirport;
			//public $IdDirection;
			//public $IdFlight;
			//public $Billdate;
	}
	//ini_set("soap.wsdl_cache_enabled", "0");	
	
	$content='';
	
	echo '<hr><br>';
	
	//$result=SAP_connector($req);
	$res=SAP_export_pair(157);
	if ($res) echo "Flights exported to SAP successfully! <br/>";
	else echo "ERROR: export operation aborted! <br/>";
	//Show_page($content);
?>
	