<?php
/* 
This script sends data to SAP and updates record
OBSOLETE
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
			public $CondType;
			public $CondValue;
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
			public $IdAircraftclass;
			public $IdAirport;
			public $IdDirection;
			public $IdFlight;
			public $Billdate;
			public $IdPlaneowner;
			public $SalesItemsIn;
			public $IdAodb;
			public $Aodbdate;
			public $IdAirportclass;
			public $IdTerminal;
			public $Return2;
			
	}
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
				$textsql='';
				$content='<table id="ExportRes"><caption><b>Результаты</b></caption>
					<tr><th>РЕЙС</th><th>ID заказа</th></tr>
					';
				foreach($flights as $value)
				{
					$order=SAP_export_flight($value);
					
					if($order)
					{
						$textsql="UPDATE flights SET sent_to_SAP=1, sdorder=$order WHERE id=$value";
						
						$answsql=mysqli_query($db_server,$textsql);
						if(!$answsql) die("Database UPDATE failed: ".mysqli_error($db_server));
					}
					$content.="<tr><td>$value</td><td>$order</td></th>";
				}
				$content.="</table>";
				Show_page($content);
				//echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>