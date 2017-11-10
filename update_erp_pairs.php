<?php
/* 
		This script sends data to SAP and updates record
			INPUT: array of pairs IDs
*/
include ("login_avia.php"); 
include("/webservice/sapconnector.php");
include ("functions.php");
set_time_limit(0);
include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 
	
	$flights= $_REQUEST['to_export'];

	//var_dump($flights);
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
			public $airport_class;
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
			public $ID_AIRPORTCLASS;
			public $ID_AIRCRAFTCLASS;
			
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
			public $MTOWIN;
			public $MTOWOUT;
			public $RETURN2;
			public $BAPIRET2;			
	}
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
				$textsql='';
				$content='<table id="ExportPairs"><caption><b>Результаты</b></caption>
					<tr><th>РЕЙС</th><th>ID заказа</th></tr>';
					
				foreach($flights as $value)
				{
					$order=SAP_export_pair($value);
					
					if($order) //UPDATE FLAGS FOR PAIR AND FLIGHTS
					{
						$textsql="UPDATE flight_pairs SET sent_to_SAP=1, sd_order=$order WHERE id=$value";
						$answsql=mysqli_query($db_server,$textsql);
						if(!$answsql) die("UPDATE of flight_pairs table failed: ".mysqli_error($db_server));
						//DRAW INDIVIDUAL FLIGHTS
						$textsql_fl="SELECT in_id,out_id FROM flight_pairs WHERE id=$value";
						$answsql_fl=mysqli_query($db_server,$textsql_fl);
						if(!$answsql_fl) die("SELECT TO flight_pairs table failed: ".mysqli_error($db_server));
						$row=mysqli_fetch_row($answsql_fl);
						$in_=$row[0];
						$out_=$row[1];
						$textsql_fl_update="UPDATE flights SET sent_to_SAP=1, sdorder=$order WHERE id=$in_";
						$answsql=mysqli_query($db_server,$textsql_fl_update);
						if(!$answsql) die("UPDATE of flights table failed: ".mysqli_error($db_server));
						$textsql_fl_update="UPDATE flights SET sent_to_SAP=1, sdorder=$order WHERE id=$out_";
						$answsql=mysqli_query($db_server,$textsql_fl_update);
						if(!$answsql) die("UPDATE of flights table failed: ".mysqli_error($db_server));
					}
					$content.="<tr><td>$value</td><td>$order</td></th>";
				}
				$content.="</table>";
				Show_page($content);
				//echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>