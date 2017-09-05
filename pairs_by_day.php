<?php 
	/*
	For a given day
	1. Combines pairs of flights. Make records in the flight_pairs table
	
	next after it update_erp_pairs.php
	
	2. Applies packages
	
	3. Applies discounts
	
	4. Exports to SAP ERP
	
	*/
	
	require_once 'login_avia.php';
	set_time_limit(0);
	include ("header.php"); 
	
	
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
		$content.= '<table><caption><b>Данные о рейсах</b></caption><br>
					<form id="form" method=post action=update_erp_pairs.php >
					<tr><th>*</th><th>№</th><th>ID->|</th><th>id NAV</th><th>ID|-></th><th>id NAV</th>
					</tr>';
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// 1. Check if there is a pair and make a record 
		
		$textsql='SELECT id,id_NAV,linked_to
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
					$pair_id=$row_pair[0];
					$flight_id=$row_pair[1];
					$customer=$row_pair[2];
				//c. Check if we have a record on it already	
					$sqlfindrec="SELECT id
								FROM  flight_pairs 
								WHERE in_id=$in_id";
				
					$answsql2=mysqli_query($db_server,$sqlfindrec);
					if(!$answsql2) die("Database SELECT TO flight_pairs table failed: ".mysqli_error($db_server));
					$rec_id=mysqli_fetch_row($answsql2);
					if($answsql2->num_rows)
						$position=$rec_id[0];
					
					else//NO RECORDS - LET's INSERT
					{	
						$sqlrecordpair='INSERT
							INTO  flight_pairs
							(in_id,out_id)
							VALUES
							('.$in_id.','.$pair_id.')';
					
						$answsql3=mysqli_query($db_server,$sqlrecordpair);
						$position=$db_server->insert_id;
					
						if(!$answsql3) die("Database INSERT TO flight_pairs table failed: ".mysqli_error($db_server));
					}
					//HERE CHECKBOXES FOR FLIGHTS
						$content.="<tr>";
						$content.= "<td><input type=\"checkbox\" name=\"to_export[]\" class=\"flights\" value=\"$position\" /></td>";//
						$content.= "<td><a href=\"check_services_mysql.php?id=$nav_id\">$num</a></td>";
						$content.="<td>$flight_id</td><td>$customer</td><td>$pair_id</td><td>$nav_pair_id</td></tr>";
				}
				
			}
			else 
				echo 'Wrong ID for the associated flight for '.$row[0].'! Found:'.$pair_id.'<br/>';
		}
		
		
		$content.= '<tr><td colspan="17"><input type="submit" name="send" class="send" value="ВВОД"></td></tr></form></table>';
	Show_page($content);
	mysqli_close($db_server);
?>
	