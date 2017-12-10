<?php
function SAP_connector($params)
{

	include("login_avia.php");
	
	ini_set("soap.wsdl_cache_enabled", "0");
	set_time_limit(0);
	$locale = 'ru';
	
	$client = new SoapClient($wsdlurl, array('login'=> $SAP_username,
											'password'=> $SAP_password,
											'trace'=>1)
							); 

	 // Формирование заголовков SOAP-запроса
	$client->__setSoapHeaders(
	array(
		new SoapHeader('API', 'user', $SAP_username, false),
		new SoapHeader('API', 'password', $SAP_password, false)
		)
	);


	// Выполнение запроса к серверу SAP ERP
	try
	{
		//$result = $client->ZsdOrderAviCrud($params);
		$result = $client->Z_SD_ORDER_ZAVI_CRUD($params);
	}
	catch(SoapFault $fault)
	{
	// <xmp> tag displays xml output in html
		echo 'Request : <br/><pre><xmp>',
		$client->__getLastRequest(),
		'</xmp></pre><br/><br/> Error Message : <br/>',
		$fault->getMessage();
	} 
	
	//обработчик ответа
	//var_dump($result);
	$order=SAP_response_handler($result);
	
	
	// Вывод запроса и ответа
	echo "Запрос:<pre>".htmlspecialchars($client->__getLastRequest()) ."</pre>";
	echo "Ответ:<pre>".htmlspecialchars($client->__getLastResponse())."</pre>";
	
	// Вывод отладочной информации в случае возникновения ошибки
	if (is_soap_fault($result)) 
	{ 
		echo("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring}, detail: {$result->detail})"); 
	}

	return $order;
}

function SAP_response_handler($Return2)
{

	$content='';
	// Building up the message content
	
	//echo '<table><tr><th>PARAMETER</th><th>VALUE</th></tr>';
	foreach($Return2->RETURN2->item as $result)
	{
	//$result=$Return2->Return2->item[2]->Type;
		$message=$result->MESSAGE;
			$contract=$result->MESSAGE_V1;
			$position=$result->MESSAGE_V2;
			$date=$result->MESSAGE_V3;
			$number=$result->MESSAGE_V4;
			$system=$result->SYSTEM;
	/*
		echo "<tr><td colspan=\"2\" ><hr color=\"black\" ></td></tr>";		
		if ($result->TYPE=='E')
		{
			echo "<tr><td>RESULT:</td><td>ERROR</td></tr>";	
		
			echo "<tr><td>Message:</td><td>$message</td></tr>";
			echo "<tr><td>Number #:</td><td>$number</td></tr>";
			echo "<tr><td>Contract #:</td><td>$contract</td></tr>";
			echo "<tr><td>Position #:</td><td>$position</td></tr>";
			echo "<tr><td>Date :</td><td>$date</td></tr>";
		}
		else
		{
			echo "<tr><td>RESULT:</td><td>SUCCESS!</td></tr>";
			echo "<tr><td>Message:</td><td>$message</td></tr>";
			echo "<tr><td>Number #:</td><td>$number</td></tr>";
			echo "<tr><td>Type:</td><td>$contract</td></tr>";
			echo "<tr><td>Position:</td><td>$position</td></tr>";
			echo "<tr><td>System :</td><td>$system</td></tr>";
		}*/
	}
	//echo '</table>';
	return $position;
}

/* Search a flight by internal ID and post SD order */

function SAP_export_flight($flightid)
{

	include("login_avia.php");
	ini_set("soap.wsdl_cache_enabled", "0");
	
		
		//Setting up the object
		$flight= new Flight();
		$flight->id=$flightid;
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//  LOCATE flight data
			$textsql='SELECT * FROM  flights WHERE id="'.$flightid.'"';
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data= mysqli_fetch_row($answsql);
				
				//SETTING UP Flight's Object
				$flight->id_NAV=$flight_data[1];
				$flight->flight_date=$flight_data[2];
				$flight->flight_num=$flight_data[3];
				$flight->direction=$flight_data[4];
				$flight->plane_id=$flight_data[7];
				$flight->plane_type=$flight_data[8];
				$flight->plane_mow=$flight_data[9];
				//$flight->airport=$flight_data[10];
				$flight->passengers_adults=$flight_data[11];
				$flight->passengers_kids=$flight_data[12];
				$flight->customer=$flight_data[13];
				$flight->bill_to=$flight_data[14];
				$flight->plane_owner=$flight_data[15];
				$flight->time_fact=$flight_data[19];
			
		// Bill Date is now a date of flight
			$billdate=$flight_data[2];
		
		// Locate Airport IATA code
			$aportsql='SELECT code,domain FROM airports WHERE id="'.$flight_data[10].'"';
				
			$answsql=mysqli_query($db_server,$aportsql);
				
			if(!$answsql) die("Database SELECT in airports table failed: ".mysqli_error($db_server));	
			
			$aport= mysqli_fetch_row($answsql);
			if(isset($aport[0])) 
				$flight->airport=$aport[0];
			else 
				echo "ERROR: Airport CODE COULD NOT BE LOCATED!!! <br/>";
		
		//  LOCATE all services relevant to the flight
			$textsql='SELECT service,quantity FROM  service_reg WHERE flight="'.$flight_data[1].'"';
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));	
			
			$rows = $answsql->num_rows;
			for ($j=0; $j<$rows; $j++)
			{
				$row= mysqli_fetch_row($answsql);
				$flight->services[]=$row;
				
			}
			$count=count($flight->services);
			
			//Prepare request for SAP ERPclass Item
	
	$req = new Request();
	
	// Preparing Items
		$items=new ItemList();
		for($it=0;$it<$count;$it++)
		{	
			$item1 = new Item();
			// 1. Item number
			$item_num=($it+1).'0';
			$item1->ItmNumber=$item_num;
			
			// 2. Material code
			$service_id=$flight->services[$it][0];
			
			//2.1 LOCATE SAP SERVICE Id
			
			$servicesql='SELECT id_SAP FROM services WHERE id_NAV="'.$service_id.'"';
				
			$answsql=mysqli_query($db_server,$servicesql);
				
			if(!$answsql) die("Database SELECT in services table failed: ".mysqli_error($db_server));	
			
			$sap_service_id= mysqli_fetch_row($answsql);
			
			echo "SERVICE ID: $service_id |--> SAP ID: $sap_service_id[0]<br/>  ";
			if (isset($sap_service_id[0]))
			{	
				echo "GOT IT! <br/>";
				$item1->Material=$sap_service_id[0];
				$item1->TargetQty=$flight->services[$it][1];
				$item1->CondType='ZK01';
				$item1->CondValue=1;
				$item1->Currency='';
			}
			else 
			{	
				echo "No SAP service ID located for service: $service_id  FLIGHT $flightid CANCELLED <br/> ";
				return 0;
			}
			//Item List section
			
			$items->item[$it] = $item1;
		}
	$req->SalesItemsIn = $items;
	
		// Locate Sales Contract ID
		// Currently the contract is selected by the payer (bill-to)
			$client_id=$flight->bill_to;  
			//echo "CLIENT ID: $client_id <br/>  ";
			$contractsql='SELECT id_SAP,isBased FROM contracts WHERE id_NAV="'.$client_id.'" AND isValid=1';
				
			$answsql=mysqli_query($db_server,$contractsql);
				
			if(!$answsql) die("Database SELECT in contracts table failed: ".mysqli_error($db_server));	
			
			$client_contract= mysqli_fetch_row($answsql);
			$contract_id=$client_contract[0];
			$isBased=$client_contract[1];
			if (isset($client_contract[0]))
			{	
				//echo "GOT IT! Contract # $contract_id<br/>";
				$req->IdSalescontract = $contract_id;
			}
			else 
			{
				echo "No contract defined for Client ID: $client_id  FLIGHT $flightid CANCELLED<br/>";
				return 0;
			}
		// Locate Customer ID for SAP ERP
		// it is going to be used as Owner
			
			$clientsql='SELECT id_SAP,id FROM clients WHERE id_NAV="'.$client_id.'" AND isValid=1';
				
			$answsql=mysqli_query($db_server,$clientsql);
				
			if(!$answsql) die("Database SELECT in CLIENTS table failed: ".mysqli_error($db_server));	
			
			$client_rec= mysqli_fetch_row($answsql);
			$client_id_SAP=$client_rec[0];
			if (isset($client_rec[0]))
			{	
				//echo "GOT IT! Client # $client_id_SAP<br/>";
				$req->IdPlaneowner = $client_id_SAP;
			}
			else 
			{
				echo "No SAP ERP ID defined for Client ID: $client_id  => FLIGHT $flightid CANCELLED<br/>";
				return 0;
			}
				// General request section
			if($flight->direction)
					$SalesDist='01';
			else
					$SalesDist='00';
			$req->Servicemode = 'SO_C'; 		// CREATE
			$req->IdSalesorder = '';
			$req->IdFlight=$flight->flight_num;
			$req->IdAircraft = $flight->plane_id;
			$req->IdAircraftclass = $flight->plane_type;
			$req->IdAirport = $flight->airport;
			$req->IdDirection = $flight->direction;
			$req->Billdate = $billdate; 		// it is set earlier
			$req->IdAodb = $flight->id_NAV;
			$req->Aodbdate = $flight->flight_date;
			$req->IdAirportclass = $SalesDist;
			$req->IdTerminal = 1;
			$req->Return2 = '';
			
			//var_dump($req);
			
			$sdorder_num=SAP_connector($req);
			
			if($sdorder_num)
			echo "SUCCESS: order # $sdorder_num created! <br/>";
		
	mysqli_close($db_server);
	return $sdorder_num;
}
/*
Same as before (above) but for a pair of flights

 Search a pair of flights by ID in flight_pairs and post SD order */

function SAP_export_pair($rec_id)
{
//return SD order
//OR 0 - if failed
	include("login_avia.php");
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once ("apply_discounts.php");
	include_once ("apply_package.php");
	include_once ("apply_bundle.php");	
	//include_once ("parking_time.php");	
		
		// PRICE SETTINGS FOR PARKING
		$parking_price_rus=36.13*0.05; // 361.3 RUR per TON per HOUR
		$parking_price_ROSSIJA=35.989*0.05; // FAULTY! MUST BE 359.89 RUR per TON per HOUR
		$parking_price_int=1.47; // 1.47 EUR per TON per DAY
		
		
		//Setting up the object
		$flight_in= new Flight();
		$flight_out= new Flight();
		// CHECK PARKING OVERTIME
		$parking_time=time_over_parking($rec_id);
		//if($parking_time<0) $parking_time=-$parking_time;//INVERSE TIME
		$parking_time-=3.25;
		//echo "PARKING TIME IS: ".$parking_time."<br/>";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
	//1.	
		//  LOCATE data for the pair
			$textsql="SELECT in_id,out_id,sent_to_SAP FROM  flight_pairs WHERE id=$rec_id";
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			if (!$answsql->num_rows)
			{
				echo "WARNING: No flights found for a given ID in flight_pairs <br/>";
				return 0;
			}	
			$pair_data= mysqli_fetch_row($answsql);
				
			// CHECKING IF THE FLIGHT WAS ALREADY PROCESSED
			if($pair_data[2])
			{
				echo "WARNING: flight data for the pair=$rec_id has been already exported to SAP ERP! Process aborted.<br/>";
				return 0;
			}	
			
	//2.		
				//  SETTING UP Flight's Objects			
				//  LOCATE incoming flight data
			$in_id=$pair_data[0];
			$out_id=$pair_data[1];
			
			//a. APPLY PACKAGE 
			// ALL SERVICES GO TO INCOMING FLIGHT
			
			if(!ApplyPackage($rec_id))
							echo "WARNING: COULD NOT APPLY TEMPLATE TO THE PAIR of FLIGHTS: $rec_id - FAILED! <br/>";
			else
							echo "SUCCESS: APPLIED TEMPLATE TO THE FLIGHTS: $in_id, $out_id  ! <br/>";
		
			
			// b. AND BUNDLE
			if(!ApplyBundle($rec_id))
							echo "WARNING: COULD NOT APPLY BUNDLE TO THE PAIR of FLIGHTS: $rec_id - FAILED! <br/>";
			else
							echo "SUCCESS: APPLIED BUNDLE TO THE FLIGHTS: $in_id, $out_id  ! <br/>";
			
			// c. DEFINE ARRAYS FOR DISCOUNT
				
				$discounts=array();
				$discounts_out=array();
				$result_svs=array();
			
			
			
			$textsql="SELECT flights.id_NAV,date,flight,direction,plane_num,flight_type,plane_type,plane_mow,
						passengers_adults,passengers_kids,customer_id, bill_to_id,owner,category,time_fact,airport,
							clients.isRusCarrier,clients.id
						FROM flights 
						LEFT JOIN clients ON flights.bill_to_id=clients.id_NAV
						WHERE flights.id=$in_id";
				
			$answsql1=mysqli_query($db_server,$textsql);	
			if(!$answsql1) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data= mysqli_fetch_row($answsql1);
				
				//SETTING UP Incoming Flight's Object
				$flight_in->id=$in_id;
				$flight_in->id_NAV=$flight_data[0];
				$flight_in->flight_date=$flight_data[1];
				$flight_in->flight_num=$flight_data[2];
				$flight_in->direction=$flight_data[3];
				$flight_in->plane_id=$flight_data[4];
				$flight_in->flight_type=$flight_data[5];
				$flight_in->plane_type=$flight_data[6];
				$flight_in->plane_mow=$flight_data[7];
				$flight_in->passengers_adults=$flight_data[8];
				$flight_in->passengers_kids=$flight_data[9];
				$flight_in->customer=$flight_data[10];
				$flight_in->bill_to=$flight_data[11];
				$flight_in->plane_owner=$flight_data[12];
				$flight_in->flight_cat=$flight_data[13];
				$flight_in->time_fact=$flight_data[14];
				
				$airport_in=$flight_data[15];
				$client_geo=$flight_data[16];
				$client_my_id=$flight_data[17]; // INTERNAL ID, NOT NAVISION
				if ($flight_in->passengers_adults||$flight_in->passengers_kids) $SZV_in_flag=1;
				else $SZV_in_flag=0;
				$SZV_exclude=' AND service <> "P0300422"'; // EXCLUDE SZV service if there are PAX on the flight
			// Bill Date is now a date of incoming flight
			$billdate=$flight_data[2];
		
			// Locate Airport IATA code
			$aportsql='SELECT code,domain FROM airports WHERE id="'.$airport_in.'"';	
			$answsql=mysqli_query($db_server,$aportsql);	
			if(!$answsql) die("Database SELECT in airports table failed: ".mysqli_error($db_server));	
			
			$aport= mysqli_fetch_row($answsql);
			if(isset($aport[0])) 
			{
				$flight_in->airport=$aport[0];
				
				$flight_in->airport_class=$aport[1];
			}
			else 
				echo "ERROR: Airport CODE COULD NOT BE LOCATED!!! <br/>";
			
			// LOCATE CLASS OF AIRCRAFT
			// KEEP IN MIND aircrats TABLE NEEDS to be UPDATED regularly
			$aircraftsql='SELECT air_class FROM aircrafts WHERE reg_num="'.$flight_in->plane_id.'"';	
			$answsql_air=mysqli_query($db_server,$aircraftsql);	
			if(!$answsql_air) die("Database SELECT in aircrafts table failed: ".mysqli_error($db_server));	
			
			$aircraft= mysqli_fetch_row($answsql_air);
			if(isset($aircraft[0])) 
				$flight_in->plane_class=$aircraft[0];
			else 
				echo "ERROR: Aircraft record COULD NOT BE LOCATED. UPDATE TABLE OF AIRCRAFTS!!! <br/>";
			
			//  LOCATE all services relevant for the flight IN
			$textsqlin='SELECT service,quantity,price FROM  service_reg 
						WHERE flight="'.$flight_in->id_NAV.'" 
						AND isValid=1 AND quantity>0
						AND (aodb_msg NOT IN (SELECT aodb_msg FROM services_exclude) OR aodb_msg IS NULL)';
			if($SZV_in_flag) $textsqlin.=$SZV_exclude;
			$answsql=mysqli_query($db_server,$textsqlin);
			if(!$answsql) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));
			$rows = $answsql->num_rows;
			for ($j=0; $j<$rows; $j++)
			{
				$row= mysqli_fetch_row($answsql);
				$flight_in->services[]=$row;
				
			}
			$services_count_in=count($flight_in->services);

	//3.
			// SETTING UP OUTGOING FLIGHT	
			//  LOCATE outgoing flight's data
			
			$textsqlout="SELECT flights.id_NAV,date,flight,direction,plane_num,flight_type,plane_type,plane_mow,
						passengers_adults,passengers_kids,customer_id,bill_to_id,owner,category,time_fact,airport,isHelicopter
							FROM flights WHERE id=$out_id";	
			$answsql2=mysqli_query($db_server,$textsqlout);	
			if(!$answsql2) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			$flight_data_out= mysqli_fetch_row($answsql2);
				
				//SETTING UP outgoing Flight's Object
				$flight_out->id=$out_id;
				$flight_out->id_NAV=$flight_data_out[0];
				$flight_out->flight_date=$flight_data_out[1];
				$flight_out->flight_num=$flight_data_out[2];
				$flight_out->direction=$flight_data_out[3];
				$flight_out->plane_id=$flight_data_out[4];
				$flight_out->flight_type=$flight_data_out[5];
				$flight_out->plane_type=$flight_data_out[6];
				$flight_out->plane_mow=$flight_data_out[7];
				$flight_out->passengers_adults=$flight_data_out[8];
				$flight_out->passengers_kids=$flight_data_out[9];
				$flight_out->customer=$flight_data_out[10];
				$flight_out->bill_to=$flight_data_out[11];
				$flight_out->plane_owner=$flight_data_out[12];
				$flight_out->flight_cat=$flight_data_out[13];
				$flight_out->time_fact=$flight_data_out[14];
				
				$airport_out=$flight_data_out[15];
				
				$heli_flag=$flight_data_out[16];
				// SZV EXCLUDE SECTION
				if ($flight_out->passengers_adults||$flight_out->passengers_kids) $SZV_out_flag=1;
				else $SZV_out_flag=0;
			// Locate Airport IATA code
			$aportsql='SELECT code,domain FROM airports WHERE id="'.$airport_out.'"';	
			$answsql=mysqli_query($db_server,$aportsql);
			if(!$answsql) die("Database SELECT in airports table failed: ".mysqli_error($db_server));	
	
			$aport_out= mysqli_fetch_row($answsql);
			if(isset($aport_out[0])) 
			{	
				if ($aport_out[1]) $airport_location=1;//ABROAD
				else $airport_location=0; //HOME
				$flight_out->airport=$aport_out[0];
				$destination_zone=$airport_location;  // <-- TAKEN BY THE DEPARTURE AIRPORT
				$flight_out->airport_class=$airport_location;
			}
			else 
				echo "ERROR: Airport CODE COULD NOT BE LOCATED!!! <br/>";
		
			// LOCATE CLASS OF AIRCRAFT
			// KEEP IN MIND aircrats TABLE NEEDS to be UPDATED regularly
			$aircratsql='SELECT air_class FROM aircrafts WHERE reg_num="'.$flight_out->plane_id.'"';	
			$answsql_air=mysqli_query($db_server,$aircraftsql);
				
			if(!$answsql_air) die("Database SELECT in aircrafts table failed: ".mysqli_error($db_server));	
			
			$aircraft= mysqli_fetch_row($answsql_air);
			if(isset($aircraft[0])) 
				$flight_out->plane_class=$aircraft[0];
			else 
				echo "ERROR: Aircraft record COULD NOT BE LOCATED!!! <br/>";
	
	//===========================================================
	//	CLIENT SET UP
	//-----------------------------------------------------------
	// Locate Sales Contract ID
		// Currently the contract is selected by the payer (bill-to)
			$client_id=$flight_out->bill_to;  	
	
			$contractsql='SELECT id_SAP,isBased FROM contracts WHERE id_NAV="'.$client_id.'" AND isValid=1';
				
			$answsql=mysqli_query($db_server,$contractsql);
				
			if(!$answsql) die("Database SELECT in contracts table failed: ".mysqli_error($db_server));	
			
			$client_contract= mysqli_fetch_row($answsql);
			$contract_id=$client_contract[0];
			$isBased=$client_contract[1];
	
				
			
			// BOOK PARKING IF APPLICABLE
			if($parking_time)
			{
				if($client_geo) //RUSSIAN AIRLINES
				{
					$service_parking="A0100207";			// HARDCODED!!!
					$parking_time=round($parking_time);		// ROUND UP
					if ($flight_out->bill_to=='К07814') 	// SPECIAL PRICE FOR ROSSIJA
						$parking_price=$parking_price_ROSSIJA;
					else	
						$parking_price=$parking_price_rus;
				}
				else
				{//FOREIGN IN DAYS
					$service_parking="A0100208";
					$parking_time=ceil($parking_time/24); // ROUNDS UP 
					$parking_price=$parking_price_int;
				}
				if($flight_in->flight_cat==2) $parking_time=0;
				if ($isBased) $parking_time=0;
				//echo "PARKING PRICE IS: $parking_price <br/>";
				$mtow=$flight_out->plane_mow/1000;
				$parking_price*=$mtow;
				if ($parking_time>0)
				{
					$parking_svs='INSERT INTO service_reg
									(flight,service,quantity,price) 
									VALUES
									("'.$flight_out->id_NAV.'","'.$service_parking.'","'.$parking_time.'","'.$parking_price.'")';
								
					$answsql_parking=mysqli_query($db_server,$parking_svs);
								
					if(!$answsql_parking) die("INSERT into service_reg TABLE failed: ".mysqli_error($db_server));
				}
			}
			//CHECK OUT MEDICAL SERVICES
			$sql_med_out='SELECT SUM(qty) FROM  medical_reg WHERE flight="'.$flight_out->flight_num.'" AND date="'.$flight_out->flight_date.'" AND isValid=1';
			$answsql_med=mysqli_query($db_server,$sql_med_out);
			if(!$answsql_med) die("Database SELECT in medical_reg table failed: ".mysqli_error($db_server));
			$row_med= mysqli_fetch_row($answsql_med);
			$qty_med=$row_med[0];
			if($qty_med)//BOOK MEDICAL
			{
				if($client_geo) //RUSSIAN AIRLINES
				{
					$service_med="A0300462";// HARDCODED!!!
				}
				else
				{
					$service_med="A0300462";
				}
				$med_svs='INSERT INTO service_reg
									(flight,service,quantity) 
									VALUES
									("'.$flight_out->id_NAV.'","'.$service_med.'","'.$qty_med.'")';
				echo $med_svs."<br/>"; 				
								$answsql_med_ins=mysqli_query($db_server,$med_svs);
								
								if(!$answsql_med_ins) die("INSERT into service_reg TABLE failed: ".mysqli_error($db_server));
			}
			
			//  LOCATE all services relevant to the flight OUT
			$textsql='SELECT service,quantity,price FROM  service_reg 
						WHERE flight="'.$flight_out->id_NAV.'" 
						AND isValid=1 AND quantity>0
						AND (aodb_msg NOT IN (SELECT aodb_msg FROM services_exclude) OR aodb_msg IS NULL)';//EXCLUDING SOME SERVICES BASED ON AODB MSG
			if($SZV_out_flag) $textsql.=$SZV_exclude;
			
			$answsql=mysqli_query($db_server,$textsql);	
			if(!$answsql) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));	
			
			$rows = $answsql->num_rows;
			for ($j=0; $j<$rows; $j++)
			{
				$row= mysqli_fetch_row($answsql);
				$flight_out->services[]=$row;	
			}
			$services_count_out=count($flight_out->services);
	
	//============================================================
	// 			DISCOUNT SECTION
	//------------------------------------------------------------
	// A. CHECK ALL APPLICABLE DISCOUNTS
	//	1. GROUP: RUSSIAN CARRIERS OR FOREIGN AND ALL
		//$result_svs=array();
		$flight_date=$flight_out->flight_date;
		if($client_geo)
			$get_services='SELECT DISTINCT service_id
							FROM discounts_grp_reg 
							LEFT JOIN discounts_group on discounts_group.id = discounts_grp_reg.discount_id 
									WHERE (discounts_group.group_id=1 OR discounts_group.group_id=0) 
									AND discounts_group.isValid=1 AND discounts_group.valid_from<="'.$flight_date.'" AND discounts_group.valid_to>="'.$flight_date.'"';//If we need zero as unlimited in valid_to, add it after additional OR here
		else 
			$get_services='SELECT DISTINCT service_id
							FROM discounts_grp_reg 
							LEFT JOIN discounts_group on discounts_group.id = discounts_grp_reg.discount_id 
									WHERE (discounts_group.group_id=2 OR discounts_group.group_id=0) 
									AND discounts_group.isValid=1 AND discounts_group.valid_from<="'.$flight_date.'" AND discounts_group.valid_to>="'.$flight_date.'"';//If we need zero as unlimited in valid_to, add it after additional OR here

		
		//echo 'STARTING WITh discounts: 1. '.$get_services.'group <br/>';
		$answsql_group=mysqli_query($db_server,$get_services);
		
			if(!$answsql_group) die("Database SELECT in discounts_group table failed: ".mysqli_error($db_server));
				//echo 'THERE ARE:'.$answsql->num_rows.' group discounts in the system<br/>';
			if($answsql_group->num_rows)
			{
				while($row_srv= mysqli_fetch_row($answsql_group))
				{	
					$index_=$row_srv[0];
					$result_svs[$index_]=1;
					
				}
			}
		
		// 2. INDIVIDUAL DISCOUNTS
			$get_services_ind='SELECT DISTINCT service_id
							FROM discounts_ind_reg 
							LEFT JOIN discounts_individual on discounts_individual.id = discounts_ind_reg.discount_id 
									WHERE discounts_individual.client_id="'.$client_my_id.'"
										AND discounts_individual.isValid=1  
									 AND discounts_individual.valid_from<="'.$flight_date.'" AND discounts_individual.valid_to>="'.$flight_date.'"';//If we need zero as unlimited in valid_to, add it after additional OR here

		//echo 'INDIVIDUAL DISCOUNTS 2. '.$get_services_ind.' INDIVIDUAL <br/>';
		$answsql_ind=mysqli_query($db_server,$get_services_ind);
		
			if(!$answsql_ind) die("Database SELECT in discounts_individual table failed: ".mysqli_error($db_server));
				//echo 'THERE ARE:'.$answsql_ind->num_rows.' DISTINCT individual discounts in the system<br/>';
			if($answsql_ind->num_rows)
			{
				while($row_srv_ind= mysqli_fetch_row($answsql_ind))
				{	
					$index_=$row_srv_ind[0];
					$result_svs[$index_]=1;
				}
			}
			var_dump($result_svs);
		//--------------------------------------------------------------------------
		// BUILD A LIST OF SERVICES
		$list_srv='SELECT DISTINCT services.id FROM service_reg 
						LEFT JOIN services ON service_reg.service=services.id_NAV
						WHERE flight="'.$flight_out->id_NAV.'" OR flight="'.$flight_in->id_NAV.'"
						AND service_reg.isValid=1 AND quantity>0
						AND (aodb_msg NOT IN (SELECT aodb_msg FROM services_exclude) OR aodb_msg IS NULL)';
		$services_list=mysqli_query($db_server,$list_srv);
		
			if(!$services_list) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));
				//echo 'THERE ARE:'.$answsql->num_rows.' DISTINCT individual discounts in the system<br/>';
			if($services_list->num_rows)
			{
				while($row_services= mysqli_fetch_row($services_list))
				{	
					$svs_on_flight=$row_services[0];
					if(array_key_exists($svs_on_flight,$result_svs))
					{
						//CHECK APPLICABILITY FOR THIS SERVICE
						//PUSH INTO DISCOUNTS TABLE
						
						$res_discount=CheckDiscountApp($flight_out,$row_services[0],$client_my_id,$flight_in->time_fact,$heli_flag,$airport_out);
						//$discounts=array_merge($discounts,$discounts_out);
						if ($res_discount)
						{
							echo "DISCOUNTS HAVE BEEN CALCULATED!<br/>";
							$discounts[$svs_on_flight]=$res_discount;
						}
						else 
							echo "NO DISCOUNTS FOR $svs_on_flight <br/>";
						
					}
				}
			}
			echo "DISCOUNTS ON THE EXIT:<pre>";
			var_dump($discounts);
			echo "</pre><br/>";
			//***********************************************************	
			// Prepare request for SAP ERPclass Item
			//-----------------------------------------------------------
			$req = new Request();
			
			// Set up params
			$terminal='T1'; // AIRPORT's terminal of departure
			$disc_type='ZK01'; //  Type of discount
			$disc_value=1;		// and it's value 
			$currency='';	// Currency in invoice
			
			// CONTRACT DATA
			if (isset($client_contract[0]))
			{	
				$req->ID_SALESCONTRACT = $contract_id;
			}
			else 
			{
				echo "No contract defined for Client ID: $client_id  FLIGHT $out_id CANCELLED<br/>";
				return 0;
			}	
		
			// Preparing Items for INCOMING FLIGHT
			$items=new ItemList();
			for($it=0;$it<$services_count_in;$it++)
			{	
				//if($flight_in->services[$it][1])//THIS BANS ZERO QUANTITY ITEMS
				//{
				 $item1 = new Item();
				 // 1. Item number
				 $item_num=($it+1).'0';
				 $item1->ITM_NUMBER=$item_num;
			
				 // 2. Material code
				 $service_id=$flight_in->services[$it][0];
			
			     //2.1 LOCATE SAP SERVICE Id
			
				 $servicesql='SELECT id_SAP,id,id_mu FROM services WHERE id_NAV="'.$service_id.'"';	
				 $answsql=mysqli_query($db_server,$servicesql);	
				 if(!$answsql) die("Database SELECT in services table failed: ".mysqli_error($db_server));	

				 $sap_service_id= mysqli_fetch_row($answsql);
			     //echo "SERVICE ID: $service_id |--> SAP ID: $sap_service_id[0]<br/>  ";
				 if (isset($sap_service_id[0]))
				 {	
					//LOCATE AND APPLY DISCOUNT
					$service_id=$sap_service_id[1];
					if(array_key_exists($service_id,$discounts))
					{
						$disc_value=$discounts[$service_id];
					}
					else
					{
						$disc_value=0;
					}
					$qty=$flight_in->services[$it][1];
					if($sap_service_id[2]==3) $qty=round($qty/1000);//HERE WE FIX KILOS TO TONS NAVISION ISSUE
					$item1->MATERIAL=$sap_service_id[0];
					$item1->TARGET_QTY=$qty;
					$item1->COND_TYPE=$disc_type;
					$item1->COND_VALUE=$disc_value;
					$item1->CURRENCY=$currency;
					$item1->ID_AODB=$flight_in->id_NAV;
					$item1->ID_TERMINAL=$terminal;
					$item1->ID_AIRPORT=$flight_in->airport;
					$item1->ID_AIRPORTCLASS=$flight_in->airport_class;
					$item1->ID_AIRCRAFTCLASS=$flight_in->plane_class;
				 }
				 else 
				 {	
				 	echo "No SAP service ID located for service: $service_id  FLIGHT $out_id CANCELLED <br/> ";
					return 0;
				 }
				 $items->item[$it] = $item1;
				//}
			//Item List section
			
			}
		
		// AND ADDING UP ONES FOR THE OUTGOING
		$services_total=$services_count_in+$services_count_out;
		for($it_o=$it;$it_o<$services_total;$it_o++)
		{	
			$k=$it_o-$it;
			$item2 = new Item();
			// 1. Item number
			$item_num=($it_o+1).'0';
			$item2->ITM_NUMBER=$item_num;
			
			// 2. Material code
			$service_id=$flight_out->services[$k][0];
			
			//2.1 LOCATE SAP SERVICE Id
			
			$servicesql='SELECT id_SAP,id,id_mu FROM services WHERE id_NAV="'.$service_id.'"';	
			$answsql=mysqli_query($db_server,$servicesql);	
			if(!$answsql) die("Database SELECT in services table failed: ".mysqli_error($db_server));	
			$sap_service_id= mysqli_fetch_row($answsql);
			
			if (isset($sap_service_id[0]))
			{	
				$service_id=$sap_service_id[1];
					
				if(array_key_exists($service_id,$discounts))
					{
						$disc_value=$discounts[$service_id];
					}
					else
					{
						$disc_value=0;
					}
					
				$qty_out=$flight_out->services[$k][1];
					if($sap_service_id[2]==3) $qty_out=round($qty_out/1000);     //ALSO FIXING NAVISION KILOS
				$price_out=$flight_out->services[$k][2];
				//PARKING SECTION	
				if(($service_id==61)||($service_id==52))//PARKING FOR RUS & INTERNATIONAL
				{
					$disc_type='ZPR0';
					$disc_value=$price_out;
				}
				if($service_id==52)
					$currency='EUR';
				$item2->MATERIAL=$sap_service_id[0];
				$item2->TARGET_QTY=$qty_out;
				$item2->COND_TYPE=$disc_type;
				$item2->COND_VALUE=$disc_value;//HERE WE FIX DECIMAL SEPARATOR ISSUE (".",",")
				$item2->CURRENCY=$currency;
				$item2->ID_AODB=$flight_out->id_NAV;
				$item2->ID_TERMINAL=$terminal;
				$item2->ID_AIRPORT=$flight_out->airport;
				$item2->ID_AIRPORTCLASS=$flight_out->airport_class;
				$item2->ID_AIRCRAFTCLASS=$flight_out->plane_class;
			}
			else 
			{	
				echo "WARNING: NO SAP service ID located for service: $service_id  FLIGHT $out_id CANCELLED <br/> ";
				return 0;
			}
			//Item List section
			
			$items->item[$it_o] = $item2;
		}
		$req->SALES_ITEMS_IN = $items;
	//5.
		// GENERAL SECTION (HEADER)
		
		// Locate Customer ID for SAP ERP
		// it is going to be used as Owner
			
			$clientsql='SELECT id_SAP FROM clients WHERE id_NAV="'.$client_id.'" AND isValid=1';	
			$answsql=mysqli_query($db_server,$clientsql);
			if(!$answsql) die("Database SELECT in contracts table failed: ".mysqli_error($db_server));	
			
			$client_rec= mysqli_fetch_row($answsql);
			$client_id_SAP=$client_rec[0];
			if (isset($client_rec[0]))
			{	
				$req->ID_PLANEOWNER = $client_id_SAP;
			}
			else 
			{
				echo "WARNING: NO SAP ERP ID defined for Client ID: $client_id  => FLIGHT $flightid CANCELLED<br/>";
				return 0;
			}
				// General request section
				
			$service_mode='SO_C';	// CREATE
			if($flight_out->direction)
					$SalesDist='1';
			else
					$SalesDist='0';
			$Sales_foreign='XX';
			if($destination_zone)
					$Sales_foreign='01';
			else
					$Sales_foreign='00';
			$req->SERVICEMODE = $service_mode; 		
			
			$req->FLIGHTDATEIN=$flight_in->flight_date;
			$req->FLIGHTDATEOUT=$flight_out->flight_date;
			$req->FLIGHTTIMEIN=$flight_in->time_fact;
			$req->FLIGHTTIMEOUT=$flight_out->time_fact;
			$req->ID_AIRCRAFTTYPEIN = $flight_in->plane_type;
			$req->ID_AIRCRAFTTYPEOUT = $flight_out->plane_type;
			$req->ID_NOOFFLIGHTIN=$flight_in->flight_num;
			$req->ID_NOOFFLIGHTOUT=$flight_out->flight_num;
			$req->ID_REGISTRATIONIN=$flight_in->plane_id;
			$req->ID_REGISTRATIONOUT=$flight_out->plane_id;
			$req->ID_NOOFFLIGHTOUT=$flight_out->flight_num;
			$req->ID_FLIGHTCATEGORY = $flight_out->flight_cat;
			$req->ID_FLIGHTTYPE = $flight_out->flight_type;
			$req->ID_AIRPORTCLASS = $Sales_foreign;// BUT ALTERNATIVELY IT COULD BE DONE VIA $destination_zone
			$req->MTOWIN=$flight_in->plane_mow;
			$req->MTOWOUT=$flight_out->plane_mow;
			$req->RETURN2 = '';
			//$req->BAPIRET2 = '';
			
			$sdorder_num=SAP_connector($req);
			
			//if($sdorder_num)
			//echo "SUCCESS: order # $sdorder_num created! <br/>";
		
	mysqli_close($db_server);
	return $sdorder_num;
}			//END OF SAP_export_pair

function time_over_parking($pair_id)
{
/* 
	INPUT: 	PAIR ID
	OUTPUT: TIME IN HOURS
*/
include 'login_avia.php';

//include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$textsql='SELECT in_id,out_id FROM flight_pairs WHERE id="'.$pair_id.'"';
			
			$answsql=mysqli_query($db_server,$textsql);	
			if(!$answsql) die("Database SELECT TO flight_pairs table failed: ".mysqli_error($db_server));	
			
			$pair= mysqli_fetch_row($answsql);
			$in_id=$pair[0];
			$out_id=$pair[1];
			$textsqlout='SELECT id,time_fact,date FROM flights WHERE id="'.$in_id.'" OR id="'.$out_id.'"';	
			$answsql2=mysqli_query($db_server,$textsqlout);	
			if(!$answsql2) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
			$flight_data_in= mysqli_fetch_row($answsql2);
				
				//SETTING UP in and outgoing Flight's Object
				//IN CASE OUTGOING GOT INTO THE DATABASE EARLIER WE HAVE TO CHECK WHICH ONE WE GOT FIRST
				if($flight_data_in[0]==$in_id)
				{
					$flight_in_time_fact=$flight_data_in[1];
					$flight_in_date=$flight_data_in[2];
				}
				else
				{
					$flight_out_time_fact=$flight_data_in[1];
					$flight_out_date=$flight_data_in[2];
				}
				
				$flight_data_out= mysqli_fetch_row($answsql2);
				
				if($flight_data_out[0]==$out_id)
				{
					$flight_out_time_fact=$flight_data_out[1];
					$flight_out_date=$flight_data_out[2];
				}
				else
				{
					$flight_in_time_fact=$flight_data_out[1];
					$flight_in_date=$flight_data_out[2];
				}
				//echo "FLIGHT IN $flight_in_date | $flight_in_time_fact <br/>";
				//echo "FLIGHT OUT $flight_out_date | $flight_out_time_fact <br/>";
				$in_Y=(int)substr($flight_in_date,0,4);
				$out_Y=(int)substr($flight_out_date,0,4);
				
				$in_Mo=(int)substr($flight_in_date,5,2);
				$out_Mo=(int)substr($flight_out_date,5,2);
				
				
				$in_D=(int)substr($flight_in_date,-2);
				$out_D=(int)substr($flight_out_date,-2);

					
				$in_H=(int)substr($flight_in_time_fact,0,2);
				$in_S=(int)substr($flight_in_time_fact,-2);
				$in_M=(int)substr($flight_in_time_fact,3,2);
				$out_H=(int)substr($flight_out_time_fact,0,2);
				$out_S=(int)substr($flight_out_time_fact,-2);
				$out_M=(int)substr($flight_out_time_fact,3,2);
				//echo "$in_H, $in_M, $in_S, $in_Mo, $in_D, $in_Y <br/>";
				//echo "$out_H, $out_M, $out_S, $out_Mo, $out_D, $out_Y <br/>";
				$time_stamp_in=date('U',mktime($in_H, $in_M, $in_S, $in_Mo, $in_D, $in_Y));
				$time_stamp_out=date('U',mktime($out_H, $out_M, $out_S, $out_Mo, $out_D, $out_Y));
				$time_stamp_diff=$time_stamp_out-$time_stamp_in;
				$res=( $time_stamp_diff/3600);//IN HOURS
				//echo "<p>" . ( $time_stamp_diff/3600) . "</p>";
					
	mysqli_close($db_server);
	
return $res;
}
function CheckDiscountApp($flight_out,$sid,$client_id,$time_fact,$isHelicopter, $airport_out)
{
/* 
	CHECKS APPLICABILITY OF DISCOUNT FOR THIS PAIR FOR PARTICULAR SERVICE 
	INPUT: 	
		$flight_out - FLIGHT OBJECT 
		$sid 		- ID of SERVICE
		$client_id	- ID of CLIENT
		$time_fact	- TIME OF ARRIVAL!
		$isHelicopter - HELICOPTER FLAG
	OUTPUT:  discount value 
*/
	include 'login_avia.php';
	
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
	//echo "HELICOPTER FLAG IS $isHelicopter <br/> ";
	$result_discount=array();// OBSOLETE ! CLEAN!
	$disc_val=0;
		$hour_of_arrival=substr($time_fact,0,2);
		$flightid=$flight_out->id;
		$airport=$airport_out;
		$zone=$flight_out->airport_class; //DOMAIN
		$plane_id=$flight_out->plane_id;
		$plane_type=$flight_out->plane_type;
		$plane_mow=$flight_out->plane_mow;
		$passengers_adults=$flight_out->passengers_adults;	
		$passengers_kids=$flight_out->passengers_kids;
		$category=$flight_out->flight_cat;
		$flight_date=$flight_out->flight_date;
	echo "AIRPORT is $airport <br/>";
		
		//	1. GROUP discounts
						$sqlservices='SELECT discount_id,discounts_group.discount_val 
									FROM discounts_grp_reg 
									LEFT JOIN discounts_group on discounts_group.id = discounts_grp_reg.discount_id 
									LEFT JOIN clients ON clients.id="'.$client_id.'"
									WHERE service_id="'.$sid.'" AND discounts_group.isValid=1 
									AND ((clients.isRusCarrier=0 AND discounts_group.group_id=2) OR discounts_group.group_id=0 OR (clients.isRusCarrier=1 AND discounts_group.group_id=1) )
									AND discounts_group.valid_from<="'.$flight_date.'" AND discounts_group.valid_to>="'.$flight_date.'"';//If we need zero as unlimited in valid_to, add it after additional OR here
				
						//echo '1. '.$sqlservices.'group <br/>';
						$answsql2=mysqli_query($db_server,$sqlservices);
						//var_dump($answsql2);
						if($answsql2->num_rows)
						{	
			// Process  discount from the list		
							while($discount= mysqli_fetch_row($answsql2))
							{
			// Get conditions for applicability of discount
								$disc_id=$discount[0];
								$disc_val=$discount[1];
								$flag=0;
								//echo "2. Discounts are: $disc_id, $disc_val <br/>";
								$sqlgetconditions="SELECT condition_id,composition FROM discount_grp_content 
												WHERE discount_id=$disc_id ORDER BY sequence";
								//echo '3. '.$sqlgetconditions.' group conditions<br/>';
								$answsql3=mysqli_query($db_server,$sqlgetconditions);
								if($answsql3->num_rows) 
								{
					//Process individual condition
									while ($condition=mysqli_fetch_row($answsql3))
									{	
										$cond_id=$condition[0];
										$cond_comp=$condition[1];
										$sqlgetconditiondata="SELECT param_id,from_val,to_val,enum_of_values,condition_id FROM discount_conditions 
												WHERE id=$cond_id AND isValid=1";
										//echo '4.'.$sqlgetconditiondata.' inside condition <br/>';
										$answsql4=mysqli_query($db_server,$sqlgetconditiondata);
										if($answsql4)
										{	
											
											$cond_data=mysqli_fetch_row($answsql4);
											//echo "5. Analyzing condition: ".$cond_data[0]."<br/>";
											if($cond_data)
											{
					// Process applicability of condition!
												$param=$cond_data[0];
												$start_val=$cond_data[1];
												$end_val=$cond_data[2];
												$enum_string=$cond_data[3];
												$comparison=$cond_data[4];
												$flag=0;
												switch($param)
												{
													case 1: // Is the destination domestic or foreign?
														//Now check the condition value and register the discount
															if($zone==2) $zone=1; // eliminating CIS!
															if($zone==$start_val) 
															$flag=1;
															
														break;
													
													case 2:  // Discount for the given aircraft number
														if(strcasecmp($start_val, $plane_id) == 0)
															$flag=1;
														break;
													
													case 3:  // based on plane type
														switch($comparison){
															case 0: // " = "
															if((int)$start_val==(int)$plane_type)
															{	
															//echo "FLAG IS SET! for PLANE TYPE $plane_type <br/>";
																$flag=1;
															}
															break;
															case 6: // " [ ... ] "
															if($enum_string)
															{	
																$values=explode(',',$enum_string);
																$values=explode(',',$enum_string);
																$compare=array_keys($values,$plane_type);
																$total=count($compare);
																if ($total) $flag=1;
																break;
															}
															break;
														}
														break;
												
													case 4:  // based on plane MOW (not checking type of condition now)
														if(($start_val<=$plane_mow)&&($end_val>=$plane_mow))
														{	
															$flag=1;
															//echo "Flag was set! PLANE MOW = $plane_mow <br/>";
														}
														break;
													
													case 5:  // based on destination (no support for diapazone yet)
														if((int)$start_val==(int)$airport)
															$flag=1;
														else "AIRPORT $airport DOES NOT MATCH $start_val! <br/>";
														break;	

													case 6:  // PAX only if above the limit
														if($start_val<=$passengers_adults)
															$flag=1;
														break;	
													
													case 7:  // PAX only if above the limit
														if($start_val<=$passengers_kids)
															$flag=1;
														break;	
													
													case 8:  // category is equal to
														if($start_val==$category)
															$flag=1;
														break;		
												
													case 9:  // Helicopter
														
														if($isHelicopter)
														{
															echo "FLAG IS SET FOR HELICOPTER! <br/>";
															$flag=1;
														}
														break;	
													
													case 10:  // Time of arrival START_VAL, END_VAL must be time!!!
														if(($hour_of_arrival>=$start_val)||($hour_of_arrival<=$end_val))
														{
															echo "NIGHT TIME! : START FROM ->$start_val END BY ->$end_val ||| ACTUAL $hour_of_arrival <br/>";
															$flag=1;
														}
														break;
												
													default:
													echo "WARNING: Parameter for condition for a service: $service_id  does not exist! <br/>";
												}
											}
										}
										if(($flag==0)&&($cond_comp==1)) break; // If we have combination of conditions and condition with AND is FALSE discount is not appplied
									}
								}
								
							// make a record of it
								if($flag)
								{
									$textsql='INSERT INTO discounts_journal
										(flight_id,service_id,discount_id,isGroup,condition_id,value)
										VALUES( '.$flightid.','.$sid.','.$disc_id.',1,'.$cond_id.','.$disc_val.')';
									//echo 'FINISH grp.'.$textsql.'<br/>';				
									$answsql6=mysqli_query($db_server,$textsql);
									if(!$answsql6) die("Insert INTO discounts_journal table failed: ".mysqli_error($db_server));
									
									// DISCOUNTS MULTIPLIED
									if(isset($result_discount[$sid])) $result_discount[$sid]*=$disc_val;
									else $result_discount[$sid]=$disc_val;
								}
								else
									$disc_val=0;
								//else echo "NO GROUP CONDITIONS DISCOVERED for THE SERVICE: $sid, SWITCHING TO INDIVIDUAL <br/>";
							}//end of processing individual discount
						}//end of processing group discounts
					
			// 2. INDIVIDUAL COMPANY discounts	
						
						$sqlservices='SELECT discount_id,discounts_individual.discount_val 
									FROM discounts_ind_reg 
									LEFT JOIN discounts_individual on discounts_individual.id = discounts_ind_reg.discount_id 
									WHERE service_id="'.$sid.'" 
										AND discounts_individual.client_id="'.$client_id.'"
										AND discounts_individual.isValid=1 
										AND discounts_individual.valid_from<="'.$flight_date.'" 
										AND (discounts_individual.valid_to>="'.$flight_date.'" OR discounts_individual.valid_to="0000-00-00")';//If we need zero as unlimited in valid_to, add it after additional OR here
				
						//echo "WARNING: $sqlservices <br/>";
						$answsql2=mysqli_query($db_server,$sqlservices);
						//var_dump($answsql2);
						if($answsql2->num_rows)
						{	
			// Process individual discount from the list		
							
							while($discount= mysqli_fetch_row($answsql2))
							{
			// Get conditions for applicability of discount
								$disc_id=$discount[0];
								$disc_val=$discount[1];
								$flag=0;
								//echo "ENTERED PROCESSING INDIVIDUAL DISCOUNT $disc_id , $disc_val % <br/>";
								//echo "2 ind. Discounts are: $disc_id, $disc_val <br/>";
								$sqlgetconditions="SELECT condition_id,composition FROM discount_ind_content 
												WHERE discount_id=$disc_id ORDER BY sequence";
								//echo "3 ind. ".$sqlgetconditions.' individual<br/>';
								$answsql3=mysqli_query($db_server,$sqlgetconditions);
								if($answsql3->num_rows) 
								{
					//Process individual condition
									while ($condition=mysqli_fetch_row($answsql3))
									{	
										$cond_id=$condition[0];
										$cond_comp=$condition[1];
										$sqlgetconditiondata="SELECT param_id,from_val,to_val,enum_of_values,condition_id FROM discount_conditions 
												WHERE id=$cond_id AND isValid=1";
										//echo "4 ind. LOOKING FOR CONDITIONS".$sqlgetconditiondata.'individual <br/>';
										$answsql4=mysqli_query($db_server,$sqlgetconditiondata);
										if($answsql4->num_rows)
										{	
											$cond_data=mysqli_fetch_row($answsql4);
											if($cond_data)
											{
					// Process applicability of condition!
												
												$param=$cond_data[0];
												$start_val=$cond_data[1];
												$end_val=$cond_data[2];
												$enum_string=$cond_data[3];
												$comparison=$cond_data[4];
												//echo "ENTERED PROCESSING CONDITIONS: param is  $param , start from: $start_val airport: $airport, VALUES: $enum_string <br/>";
												//echo "RESULT OF COMPARISON: ".strpos($start_val, $airport)."<br/>";
												$flag=0;
												switch($param)
												{
													case 1: // Is the destination domestic or foreign?
												
															if($zone==2) $zone=1; // eliminating CIS!
															if($zone==$start_val) 
																$flag=1;
														break;
													
													case 2:  // Discount for the given aircraft number
														if(strcasecmp($start_val, $plane_id) == 0)
															$flag=1;
														break;
													
													case 3:  // based on plane type
														switch($comparison){
															case 0: // " = "
															if((int)$start_val==(int)$plane_type)
															{	
															//echo "FLAG IS SET! for PLANE TYPE $plane_type <br/>";
																$flag=1;
															}
															break;
															case 6: // " [ ... ] "
															if($enum_string)
															{	
																$values=explode(',',$enum_string);
																$compare=array_keys($values,$plane_type);
																$total=count($compare);
																if ($total) $flag=1;
																break;
															}
															break;
														}
														break;
												
													case 4:  // based on plane MOW (not checking type of condition now)
														if(($start_val<=$plane_mow))
														{	
															$flag=1;
															//echo "Flag was set! Condition 4. <br/>";
														}
														break;
													
													case 5:  // based on destination (no support for diapazone yet)
														if((int)$start_val==(int)$airport)
														{
															$flag=1;
															//echo "DESTINATION AIRPORT CONDITION! <br/>";
														}
														else "AIRPORT $airport DOES NOT MATCH $start_val! <br/>";
														break;

													case 6:  // PAX only if above the limit
														if($start_val<=$passengers_adults)
															$flag=1;
														break;	
													
													case 7:  // PAX only if above the limit
														if($start_val<=$passengers_kids)
															$flag=1;
														break;	
													
													case 8:  // category is equal to
														
														//echo "CHECKING OUT CATEGORY OF FLIGHT: $category <br/>";
														if($start_val==$category)
															$flag=1;
														break;		
												
													case 9:  // Helicopter
														if($isHelicopter)
														{
															//echo "FLAG IS SET FOR HELICOPTER! <br/>";
															$flag=1;
														}
														break;	
													
													case 10:  // Time of arrival START_VAL, END_VAL must be time!!!
														if(($time_fact>=$start_val)&&($time_fact<$end_val))
															$flag=1;
														break;	
												
													default:
													echo "WARNING: Parameter for condition for a service: $service_id  does not exist! <br/>";
												}
											}
										}
										if(($flag==0)&&($cond_comp==1)) break; // If we have combination of conditions and condition with AND is FALSE discount is not appplied
									}
								}
							// make a record of it
								if($flag)
								{
								 $textsql='INSERT INTO discounts_journal
									(flight_id,service_id,discount_id,isGroup,condition_id,value)
									VALUES( '.$flightid.','.$sid.','.$disc_id.',0,'.$cond_id.','.$disc_val.')';
								   //echo "FINISH ind. ".$textsql.'  JOURNAL individual<br/>';				
							 	 $answsql6=mysqli_query($db_server,$textsql);
								 if(!$answsql6) die("Insert INTO discounts_journal table failed: ".mysqli_error($db_server));
								 // DISCOUNTS MULTIPLIED
									if(isset($result_discount[$sid])) $result_discount[$sid]*=$disc_val;
									else $result_discount[$sid]=$disc_val;
								}
								else
									$disc_val=0;
								//else echo "NO INDIVIDUAL DISCOUNTS APPLIED FOR THE SERVICE $sid: SWITCHING TO THE NEXT SERVICE<br/>";
							}//end of processing individual discount
						}
						//end of company discounts
	mysqli_close($db_server);
	if (isset($result_discount[$sid]))
		return $result_discount[$sid];
	else return 0;
}
?>