<?php
function SAP_connector($params)
{

	include("login_avia.php");
	ini_set("soap.wsdl_cache_enabled", "0");

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
		$result = $client->ZsdOrderAviCrud($params);
	}
	catch(SoapFault $fault)
	{
	// <xmp> tag displays xml output in html
		echo 'Request : <br/><xmp>',
		$client->__getLastRequest(),
		'</xmp><br/><br/> Error Message : <br/>',
		$fault->getMessage();
	} 
	
	//обработчик ответа
	//var_dump($result);
	$order=SAP_response_handler($result);
	
	/*
	// Вывод запроса и ответа
	echo "Запрос:<pre>".htmlspecialchars($client->__getLastRequest()) ."</pre>";
	echo "Ответ:<pre>".htmlspecialchars($client->__getLastResponse())."</pre>";
	*/
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
	
	echo '<table><tr><th>PARAMETER</th><th>VALUE</th></tr>';
	foreach($Return2->Return2->item as $result)
	{
	//$result=$Return2->Return2->item[2]->Type;
		$message=$result->Message;
			$contract=$result->MessageV1;
			$position=$result->MessageV2;
			$date=$result->MessageV3;
			$number=$result->Number;
			$system=$result->System;
		echo "<tr><td colspan=\"2\" ><hr color=\"black\" ></td></tr>";		
		if ($result->Type=='E')
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
		}
	}
	echo '</table>';
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
				//SET UP Flight's Object
				$flight->id_NAV=$flight_data[1];
				$flight->flight_date=$flight_data[2];
				$flight->flight_num=$flight_data[3];
				$flight->direction=$flight_data[4];
				$flight->plane_id=$flight_data[7];
				$flight->plane_type=$flight_data[8];
				$flight->plane_mow=$flight_data[9];
				$flight->airport=$flight_data[10];
				$flight->passengers_adults=$flight_data[11];
				$flight->passengers_kids=$flight_data[12];
				$flight->customer=$flight_data[13];
				$flight->bill_to=$flight_data[14];
				$flight->plane_owner=$flight_data[15];
			
		// Bill Date is now a date of flight
			$billdate=$flight_data[2];
		
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
			echo "SERVICE ID: $service_id |--> SAP ID: $services_map[$service_id]<br/>  ";
			if (isset($services_map[$service_id]))
			{	
				echo "GOT IT! <br/>";
				$item1->Material=$services_map[$flight->services[$it][0]];
				$item1->TargetQty=$flight->services[$it][1];
				$item1->PurchNoS=$flight->id_NAV;
				$item1->PoDatS=$flight->flight_date; // Also date of flight
				$item1->PoMethS='AODB';
				if($flight->direction)
					$item1->SalesDist='1';
				else
					$item1->SalesDist='0';
			}
			else 
			{	
				echo "No such service: $service_id <br/> ";
				return 0;
			}
			//Item List section
			
			$items->item[$it] = $item1;
		}
	$req->SalesItemsIn = $items;
	
		// Client ID is now only via Sales Contract ID
			$client_id=$flight->customer;
			echo "CLIENT ID: $client_id <br/>  ";
			if (isset($clients_map[$client_id]))
			{	
				echo "GOT IT! <br/>";
				$req->IdSalescontract = $clients_map[$client_id];
			}
			else 
			{
				echo "No such client $client_id <br/>";
				return 0;
			}
		
		
	// General request section
			$req->Servicemode = 'SO_C'; 		// CREATE
			$req->IdSalesorder = '';
			$req->Billdate = $billdate; 		// it is set earlier
			$req->IdPlaneowner = '15000010'; 	// To be completed !!!
			$req->Return2 = '';
			
			//var_dump($req);
			
			$sdorder_num=SAP_connector($req);
			
			if($sdorder_num)
			echo "SUCCESS: order # $sdorder_num created! <br/>";
			
			return $sdorder_num;
}
?>