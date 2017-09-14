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
	
		$day   = $_POST['day'];
		$month = $_POST['month'];
		$year  = $_POST['year'];
				
		$input_d=array($year,$month,$day);
	
		$date_=mktime(0,0,0,$month,$day,$year);
	
		$date=date("Y-m-d", $date_);
	
		$content='';
		//----------------------------------------
		// Top of the table
		$content.= "<b>  Пары рейсов за:</b> $date <hr>";
		$content.= '<table class="myTab"><caption><b>Данные о рейсах</b></caption><br>
					<form id="form" method=post action=update_erp_pairs.php >
					<col class="col1"><col class="col1"><col class="col2"><col class="col2"><col class="col3">
					<tr><td><input type="checkbox" id="flights" onclick="checkIt();" checked/></td><th>№</th><th>FLIGHT->|</th><th>FLIGHT|-></th><th>КЛИЕНТ</th>
					</tr>';
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// 1. Check if there is a pair and make a record 
		
		$textsql='SELECT id,id_NAV,linked_to,flight
						FROM  flights WHERE date="'.$date.'" AND direction=0 AND sent_to_SAP IS NULL';
				
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
			if(!$nav_pair_id) echo "WARNING: FLIHT ID of linked flight is shorter than expected! - $nav_pair_id <br/>";
			if(strlen($nav_pair_id)==7)
			{
			//b. Look for the pair
				$sqlfindpair='SELECT id,flight,owner
							FROM  flights 
							WHERE id_NAV="'.$nav_pair_id.'" 
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
						$content.="<tr>";
						$content.= "<td><input type=\"checkbox\" name=\"to_export[]\" class=\"flights\" value=\"$position\" checked/></td>";//
						$content.= "<td>$num</td>";
						$content.= "<td><a href=\"show_flight.php?id=$in_id\">$flight_num</a></td>";
						$content.= "<td><a href=\"show_flight.php?id=$out_id\">$flight_num_pair</a></td>";
						$content.="<td>$customer</td></tr>";
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
						$content.="<tr>";
						$content.= "<td><input type=\"checkbox\" name=\"to_export[]\" class=\"flights\" value=\"$position\" checked/></td>";//
						$content.= "<td>$num</td>";
						$content.= "<td><a href=\"show_flight.php?id=$in_id\">$flight_num</a></td>";
						$content.= "<td><a href=\"show_flight.php?id=$out_id\">$flight_num_pair</a></td>";
						$content.="<td>$customer</td></tr>";
					
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
		
		
		$content.= '<tr><td colspan="17"><input type="submit" name="send" class="send" value="ВВОД"></td></tr></form></table>';
	Show_page($content);
	mysqli_close($db_server);
?>
	