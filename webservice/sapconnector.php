<?php
function SAP_connector($params)
{

	include("login_avia.php");
	include("parking_time.php");
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
			$contractsql='SELECT id_SAP FROM contracts WHERE id_NAV="'.$client_id.'" AND isValid=1';
				
			$answsql=mysqli_query($db_server,$contractsql);
				
			if(!$answsql) die("Database SELECT in contracts table failed: ".mysqli_error($db_server));	
			
			$client_contract= mysqli_fetch_row($answsql);
			$contract_id=$client_contract[0];
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
			
			$clientsql='SELECT id_SAP FROM clients WHERE id_NAV="'.$client_id.'" AND isValid=1';
				
			$answsql=mysqli_query($db_server,$clientsql);
				
			if(!$answsql) die("Database SELECT in contracts table failed: ".mysqli_error($db_server));	
			
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
		
		//Setting up the object
		$flight_in= new Flight();
		$flight_out= new Flight();
		// CHECK PARKING OVERTIME
		$parking_time=time_over_parking($rec_id);
		echo "PARKING TIME IS: ".$parking_time."<br/>";
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
			/* FOR DEBUIGGING
			echo '<pre>';
				var_dump($discount_in);
				var_dump($discount_out);
			echo '</pre>';
			*/
			if(!ApplyPackage($rec_id))
							echo "WARNING: COULD NOT APPLY TEMPLATE TO THE PAIR of FLIGHTS: $rec_id - FAILED! <br/>";
			else
							echo "SUCCESS: APPLIED TEMPLATE TO THE FLIGHTS: $in_id, $out_id  ! <br/>";
		
			//if(!ApplyPackage($out_id))
				//			echo "WARNING: COULD NOT APPLY PACKAGE TO THE FLIGHT: $out_id - FAILED! <br/>";
			//else
				//			echo "SUCCESS: APPLIED PACKAGE TO THE FLIGHT: $out_id ! <br/>";
			// b. AND BUNDLE
			if(!ApplyBundle($rec_id))
							echo "WARNING: COULD NOT APPLY BUNDLE TO THE PAIR of FLIGHTS: $rec_id - FAILED! <br/>";
			else
							echo "SUCCESS: APPLIED BUNDLE TO THE FLIGHTS: $in_id, $out_id  ! <br/>";
			
			// c.AND DISCOUNT
			$discount_in=ApplyDiscounts($in_id);
			$discount_out=ApplyDiscounts($out_id);			
			
			// NOW DISCOUNTS ARE IN TWO ARRAYS
			
			if(!$discount_in)
							echo "WARNING: NO DISCOUNTS FOR THE FLIGHT: $in_id  <br/>";
			else
							echo "SUCCESS: APPLIED DISCOUNT TO THE FLIGHT: $in_id !<br/>";
			if(!$discount_out)
							echo "WARNING: NO DISCOUNTS FOR THE FLIGHT: $out_id <br/>";
			else
							echo "SUCCESS: APPLIED DISCOUNT TO THE FLIGHT: $out_id !<br/>";
			
			
			$textsql="SELECT flights.id_NAV,date,flight,direction,plane_num,flight_type,plane_type,plane_mow,
						passengers_adults,passengers_kids,customer_id, bill_to_id,owner,category,time_fact,airport,
							clients.isRusCarrier
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
				//$flight_in->plane_type=$flight_data[15];
				
			
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
				//$airport_cl_in=$aport[1];
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
				echo "ERROR: Aircraft record COULD NOT BE LOCATED!!! <br/>";
			
			//CHECK OUT MEDICAL SERVICES
			/* MUST BE FOR DEPARTING ONLY :)
			$sql_med='SELECT quantity FROM  SUM(medical_reg) WHERE flight="'.$flight_in->flight_num'" AND date="'.$flight_in->flight_date'" AND isValid=1';
			$answsql_med=mysqli_query($db_server,$sql_med);
			if(!$answsql_med) die("Database SELECT in medical_reg table failed: ".mysqli_error($db_server));
			$row_med= mysqli_fetch_row($answsql);
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
									("'.$flight_in->id_NAV.'","'.$service_med.'","'.$qty_med.'")';
								
								$answsql_med_ins=mysqli_query($db_server,$med_svs);
								
								if(!$answsql_med_ins) die("INSERT into service_reg TABLE failed: ".mysqli_error($db_server));
			}
			*/
			
			//  LOCATE all services relevant for the flight
			$textsqlin='SELECT service,quantity FROM  service_reg WHERE flight="'.$flight_in->id_NAV.'" AND isValid=1 AND quantity>0';
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
						passengers_adults,passengers_kids,customer_id,bill_to_id,owner,category,time_fact,airport
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
			
			// Locate Airport IATA code
			$aportsql='SELECT code,domain FROM airports WHERE id="'.$airport_out.'"';	
			$answsql=mysqli_query($db_server,$aportsql);
			if(!$answsql) die("Database SELECT in airports table failed: ".mysqli_error($db_server));	
	
			$aport_out= mysqli_fetch_row($answsql);
			if(isset($aport_out[0])) 
			{	
				$flight_out->airport=$aport_out[0];
				$destination_zone=$aport_out[1];  // <-- TAKEN BY THE DEPARTURE AIRPORT
				$flight_out->airport_class=$aport_out[1];
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
			// BOOK PARKING IF APPLICABLE
			if($parking_time)
			{
				if($client_geo) //RUSSIAN AIRLINES
				{
					$service_parking="A0100207";// HARDCODED!!!
				}
				else
				{
					$service_parking="A0100208";
				}
				$parking_svs='INSERT INTO service_reg
									(flight,service,quantity) 
									VALUES
									("'.$flight_out->id_NAV.'","'.$service_parking.'",'.$parking_time.')';
								
								$answsql_parking=mysqli_query($db_server,$parking_svs);
								
								if(!$answsql_parking) die("INSERT into service_reg TABLE failed: ".mysqli_error($db_server));
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
			
			//  LOCATE all services relevant to the flight
			$textsql='SELECT service,quantity FROM service_reg WHERE flight="'.$flight_out->id_NAV.'" AND isValid=1 AND quantity>0';	
			$answsql=mysqli_query($db_server,$textsql);	
			if(!$answsql) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));	
			
			$rows = $answsql->num_rows;
			for ($j=0; $j<$rows; $j++)
			{
				$row= mysqli_fetch_row($answsql);
				$flight_out->services[]=$row;	
			}
			$services_count_out=count($flight_out->services);
	//4.	
			// Prepare request for SAP ERPclass Item
	
			$req = new Request();
			
			// Set up params
			$terminal='T1'; // AIRPORT's terminal of departure
			$disc_type='ZK01'; //  Type of discount
			$disc_value=1;		// and it's value 
			$currency='';	// Currency in invoice
	
		
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
					if(array_key_exists($service_id,$discount_in))
					{
						$disc_value=$discount_in[$service_id];
					}
					else
					{
						$disc_value=0;
					}
					$qty=$flight_in->services[$it][1];
					if($sap_service_id[2]==3) $qty/=1000;
					$item1->MATERIAL=$sap_service_id[0];
					$item1->TARGET_QTY=$qty;//HERE WE FIX KILOS TO TONS NAVISION ISSUE
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
					
				if(array_key_exists($service_id,$discount_out))
					{
						$disc_value=$discount_out[$service_id];
					}
					else
					{
						$disc_value=0;
					}
				$qty_out=$flight_out->services[$k][1];
					if($sap_service_id[2]==3) $qty_out/=1000;     //ALSO FIXING NAVISION KILOS
				$item2->MATERIAL=$sap_service_id[0];
				$item2->TARGET_QTY=$qty_out;
				$item2->COND_TYPE=$disc_type;
				$item2->COND_VALUE=$disc_value;
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
		// Locate Sales Contract ID
		// Currently the contract is selected by the payer (bill-to)
			$client_id=$flight_out->bill_to;  
			$contractsql='SELECT id_SAP FROM contracts WHERE id_NAV="'.$client_id.'" AND isValid=1';	
			$answsql=mysqli_query($db_server,$contractsql);
				
			if(!$answsql) die("Database SELECT in contracts table failed: ".mysqli_error($db_server));	
			
			$client_contract= mysqli_fetch_row($answsql);
			$contract_id=$client_contract[0];
			if (isset($client_contract[0]))
			{	
				$req->ID_SALESCONTRACT = $contract_id;
			}
			else 
			{
				echo "No contract defined for Client ID: $client_id  FLIGHT $out_id CANCELLED<br/>";
				return 0;
			}
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
			$textsqlout='SELECT time_fact FROM flights WHERE id="'.$in_id.'" OR id="'.$out_id.'"';	
			$answsql2=mysqli_query($db_server,$textsqlout);	
			if(!$answsql2) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
			$flight_data_in= mysqli_fetch_row($answsql2);
				
				//SETTING UP outgoing Flight's Object
				
				$flight_in_time_fact=$flight_data_in[0];
				$in_H=(int)substr($flight_in_time_fact,0,2);
				$in_S=(int)substr($flight_in_time_fact,-2);
				$in_M=(int)substr($flight_in_time_fact,3,2);
				
				$flight_data_out= mysqli_fetch_row($answsql2);
				//SETTING UP outgoing Flight's Object
				
				$flight_out_time_fact=$flight_data_out[0];
				$out_H=(int)substr($flight_out_time_fact,0,2);
				$out_S=(int)substr($flight_out_time_fact,-2);
				$out_M=(int)substr($flight_out_time_fact,3,2);
				//echo " FLIGHT IN: $flight_in_time_fact  <br/> FLIGHT OUT: $flight_out_time_fact <br/>";
				//echo " HOURS: $in_H MIN: $in_M SEC: $in_S <br/>";
				//echo " HOURS: $out_H MIN: $out_M SEC: $out_S <br/>";
				/*
				var_dump($flight_in_time_fact);
				var_dump($flight_out_time_fact);
				echo "Difference is:".($flight_out_time_fact-$difference)."<br/>" ;
				var_dump($difference);*/
				//Difference
				$delta_S=$out_S-$in_S;
				
				
				
				// ALLOWANCE 3 HOURS 15 MINUTES
				//$out_H-=3;
				//$out_M-=15;
				
				if($delta_S>=0) $diff_S=$delta_S;
				else 
				{	
					$out_M-=1;
					$diff_S=60+$delta_S;
				}
				$delta_M=$out_M-$in_M;
				if($delta_M>=0) $diff_M=$delta_M;
				else 
				{	
					$out_H-=1;
					$diff_M=60+$delta_M;
				}
				$delta_H=$out_H-$in_H;
				if($delta_H>=0) $diff_H=$delta_H;
				else 
				{	
					
					$diff_H=24+$delta_H;
				}
				
				
				$diff_H-=3;
				$diff_M-=15;
			
				if (($diff_H<=0)&&($diff_M<=0)) return 0;
				else
				{
					//echo "PARKING TIME is:".$diff_H.":".$diff_M.":".$diff_S."<br/>";
					if($diff_M>30) $diff_H+=1;
					
					return $diff_H;
				}
			
	mysqli_close($db_server);
	
return $diff_H;
}	

?>