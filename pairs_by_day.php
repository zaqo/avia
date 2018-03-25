<?php 
	/*
	For a given day
	1. Combines pairs of flights. Make records in the flight_pairs table
	
	next when it calls update_erp_pairs.php
	
	2. Applies packages
	
	3. Applies discounts
	
	4. Exports to SAP ERP
	
	*/
	require_once 'login_avia.php';
	include_once ("apply_discounts.php");
	include_once ("apply_package.php");
	include("/webservice/sapconnector.php");
	set_time_limit(0);
	include ("header.php"); 
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
	
		$input_date=$_REQUEST['from'];
		$day   = substr($input_date,0,2);
		$month = substr($input_date,3,2);
		$year  = substr($input_date,6,4);
		
		if (isset($_REQUEST['carrier'])) $carrier = $_REQUEST['carrier'];	
		else $carrier=0;
		
		$input_d=array($year,$month,$day);
	
		$date_=mktime(0,0,0,$month,$day,$year);
	
		$date=date("Y-m-d", $date_);
	
		$content='';
		//----------------------------------------
		// Top of the table
		$content.= "<b>  Пары рейсов за:</b> $date <hr>";
		$content.= '<div class="container ml-5">';
		$content.= '<form id="form" method=post action="update_erp_pairs.php"  >';
		$content.= '<ul class="list-group">';
		//$content.= '';
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1">Перечень</h5>
							<small><input type="checkbox" id="flights" onclick="checkIt();" checked/></small>
						</div>
					</li>';
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// 1. Check if there is a pair and make a record 
		if($carrier)
			$textsql='SELECT flights.id,id_NAV,linked_to,flight,time_fact,airports.name_rus
						FROM  flights
						LEFT JOIN airports ON flights.airport=airports.id 
						WHERE date="'.$date.'" 
						AND direction=0 AND sent_to_SAP IS NULL 
						AND flight LIKE "'.$carrier.'%"';
		else
		 $textsql='SELECT flights.id,id_NAV,linked_to,flight,time_fact,airports.name_rus
					FROM  flights
					LEFT JOIN airports ON flights.airport=airports.id
					WHERE date="'.$date.'" AND direction=0 AND sent_to_SAP IS NULL';
				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
		$num=0;
		while( $row = mysqli_fetch_row($answsql) ) 
		{
			//a. cut the prefix
			$in_id=$row[0];
			$nav_id=$row[1];
			$nav_pair_id=substr($row[2],-7);
			$flight_num=$row[3];
			$time_fact=substr($row[4],0,5);
			$airport_in=$row[5];
			if(!$nav_pair_id) echo "WARNING: FLIHT ID of linked flight is shorter than expected! - $nav_pair_id <br/>";
			if(strlen($nav_pair_id)==7)
			{
			//b. Look for the pair
				$sqlfindpair='SELECT flights.id,flights.flight,flights.owner,flights.time_fact,clients.id,clients.hasLogo,airports.name_rus
							FROM  flights 
							LEFT JOIN clients ON flights.owner_id=clients.id_NAV
							LEFT JOIN airports ON flights.airport=airports.id
							WHERE flights.id_NAV="'.$nav_pair_id.'" 
							AND sent_to_SAP IS NULL';
				
				$answsql1=mysqli_query($db_server,$sqlfindpair);
				if(!$answsql1) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));
				if($answsql1->num_rows)
				{
					$num+=1;
					$row_pair = mysqli_fetch_row($answsql1);
					$out_id=$row_pair[0];
					$flight_num_pair=$row_pair[1];
					$customer=$row_pair[2];
					$time_fact_pair=substr($row_pair[3],0,5);
					//LOGO PREPARATION SECTION
					$client_id=$row_pair[4];
					$hasLogo=$row_pair[5];
					$airport_out=$row_pair[6];
					if($hasLogo)
						$logo_filename=$client_id.'.png';
					else
						$logo_filename='default_logo.png';
				//c. Check if we have a record on it already	
					$sqlfindrec="SELECT id,sent_to_SAP
								FROM  flight_pairs 
								WHERE in_id=$in_id";
				
					$answsql2=mysqli_query($db_server,$sqlfindrec);
					if(!$answsql2) die("Database SELECT TO flight_pairs table failed: ".mysqli_error($db_server));
					$rec_id=mysqli_fetch_row($answsql2);
					if(($answsql2->num_rows)&&(!$rec_id[1]))
					{
						$position=$rec_id[0];
						//HERE CHECKBOXES FOR FLIGHTS
						//$content.='<li class="list-group-item">';
						//$content.= "<input type=\"checkbox\" name=\"to_export[]\" class=\"flights\" value=\"$position\" checked/>";//
						//$content.= "$num";
						//$content.= "<a href=\"show_flight.php?id=$in_id\">$flight_num</a>$time_fact";
						//$content.= "<a href=\"show_flight.php?id=$out_id\">$flight_num_pair</a>$time_fact_pair";
						//$content.="$customer</li>";
						$content.='<li class="list-group-item flex-column align-items-start" >
									<div class="d-flex w-100 justify-content-between">
									<div class="collapse" id="Toggle'.$num.'">
										<div class="bg-light p-2">
											<span class="text-muted"><small> приб.: '.$time_fact.'</small></span>
						
											<span class="text-muted"><small>отпр.: '.$time_fact_pair.'</small></span>
										</div>
									</div>
									 <nav class="navbar navbar-light bg-light">
										<button class="btn btn-outline-success" type="button" data-toggle="collapse" data-target="#Toggle'.$num.'" aria-controls="Toggle'.$num.'" aria-expanded="false" aria-label="Info">t</button>
									</nav>
									
										<h5 class="mb-1">
										<a href="show_flight.php?id='.$in_id.'&from='.$input_date.'&carrier='.$carrier.'">'.$flight_num.'</a> <span class="text-muted"><small>'.$airport_in.'</small></span>
											</h5><span  class="my_arrow">&#x21E2</span>
										<h5 class="mb-1"> <a href="show_flight.php?id='.$out_id.'&from='.$input_date.'&carrier='.$carrier.'">'.$flight_num_pair.'</a> <span class="text-muted"><small>'.$airport_out.'</small></span></h5> 
										<span class="mb-1">	<img src="/avia/src/'.$logo_filename.'" alt="Company Logo"></span>
											
										<small><input type="checkbox" name="to_export[]" class="flights" value="'.$position.'" checked/></small>
									
									</div>
									</li>';
					}
					elseif(!$answsql2->num_rows) 
					{	
						
						$sqlrecordpair='INSERT
							INTO  flight_pairs
							(in_id,out_id)
							VALUES
							('.$in_id.','.$out_id.')';
					
						$answsql3=mysqli_query($db_server,$sqlrecordpair);
						$position=$db_server->insert_id;
					
						if(!$answsql3) die("Database INSERT TO flight_pairs table failed: ".mysqli_error($db_server));
					//HERE CHECKBOXES FOR FLIGHTS
						
						$content.='<li class="list-group-item flex-column align-items-start" >
									<div class="d-flex w-100 justify-content-between">
										<h5 class="mb-1">
										<a href="show_flight.php?id='.$in_id.'&from='.$input_date.'&carrier='.$carrier.'">'.$flight_num.'</a> <small>'.$time_fact.'</small>
											&#x21E2 <a href="show_flight.php?id='.$out_id.'&from='.$input_date.'&carrier='.$carrier.'">'.$flight_num_pair.'</a> <small>'.$time_fact_pair.' </small> 
											<img src="/avia/src/'.$logo_filename.'" alt="Company Logo">
											</h5>
										<small><input type="checkbox" name="to_export[]" class="flights" value="'.$position.'" checked/></small>
									</div>
									</li>';
					
					}
					else 
					{
						echo "NO variants for flight# $in_id <br/>";
						$num--;
					}
					// UPDATE FLIGHTS, NOW THEY HAVE BEEN PROCESSED, SO sent_to_SAP=1
					/*
					//HERE CHECKBOXES FOR FLIGHTS
						$content.="<tr>";
						$content.= "<td><input type=\"checkbox\" name=\"to_export[]\" class=\"flights\" value=\"$position\" checked/></td>";//
						$content.= "<td>$num</td>";
						$content.= "<td><a href=\"show_flight.php?id=$in_id\">$flight_num</a></td>";
						$content.= "<td><a href=\"show_flight.php?id=$out_id\">$flight_num_pair</a></td>";
						$content.="<td>$customer</td></tr>";
						*/
				}
				
			}
			else 
				echo 'Wrong ID for the associated flight for '.$row[0].'! Found:'.$pair_id.'<br/>';
		}
		
		
		$content.= '<button type="submit" class="btn btn-primary mb-2">ВВОД</button></form>';
		$content.= '</div>';
	Show_page($content);
	mysqli_close($db_server);
?>
	