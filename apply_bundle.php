<?php
/*
*		Applies bundle (set of services) to a pair of flights
*			Bundle is different from package (template) in a sense that
*				a. it appears in invoice as a single string
*				b. if we find services included in the bundle, we cancel them (isValid=0, not send it to ERP 
*		 INPUT: pair ID
* 		 Returns:
*		  	- 1 Ok
*			- 0 if package was already applied 
*/

function ApplyBundle($rec_id)
{

 
	include("login_avia.php");
	
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//  LOCATE data for the pair
			$textsql_pre="SELECT in_id,out_id,sent_to_SAP FROM  flight_pairs WHERE id=$rec_id";
				
			$answsql_pre=mysqli_query($db_server,$textsql_pre);
				
			if(!$answsql_pre) die("Database SELECT TO flight_pairs table failed: ".mysqli_error($db_server));	
			if (!$answsql_pre->num_rows)
			{
				echo "WARNING: No flights found for a given ID in flight_pairs <br/>";
				return 0;
			}	
			$pair_data= mysqli_fetch_row($answsql_pre);
			$in_=$pair_data[0];
			$out_=$pair_data[1];
			$sent_flag=$pair_data[2];
			if($sent_flag) 
			{
				echo "FLIGHT WAS PROCESSED: EXITING!";
				return 0;
			}
		
		//  LOCATE CUSTOMER in the IN flight data
			$textsql='SELECT flights.id_NAV, clients.id 
						FROM  flights
						LEFT JOIN clients ON flights.bill_to_id=clients.id_NAV 
						 WHERE flights.id="'.$in_.'"';
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data= mysqli_fetch_row($answsql);
				$flight_id_in=$flight_data[0];
				$customer=$flight_data[1];
				echo "CUSTOMER ID: $customer <br/>";
				
			
		//  LOCATE OUT flight data
			$textsql='SELECT id_NAV FROM  flights WHERE id="'.$out_.'"';
				
			$answsql_out=mysqli_query($db_server,$textsql);
				
			if(!$answsql_out) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data_out= mysqli_fetch_row($answsql_out);
				$flight_id_out=$flight_data_out[0];
				
			
		//  1. LOCATE all relevant BUNDLES  
		
			$b_sql='SELECT bundle_id,services.id_NAV FROM bundle_reg 
						LEFT JOIN services ON bundle_reg.bundle_id=services.id
						WHERE client_id="'.$customer.'"';
			//echo $textsql.'<br/>';	
			$answsql0=mysqli_query($db_server,$b_sql);
				
			if(!$answsql0) die("Database SELECT in clients table failed: ".mysqli_error($db_server));
				//echo 'Package with:'.$answsql->num_rows.' rows<\br>';
			if(!$answsql0->num_rows)
			{
				echo "WARNING: NO BUNDLES FOR ClientID: $customer! <br/>";
				return 0; // No information about client
			}
		while($b_row=mysqli_fetch_row($answsql0))
		{	
			//RECORD BUNDLE IN THE SERVICE REG
			
			$bundle_id=$b_row[0];
			$bundle_id_NAV=$b_row[1];
			//echo "LOCATED BUNDLE  ID: $bundle_id <br/>";
			//  CLEAN OLD RECORD
						$clean_mysql='DELETE FROM service_reg 
									WHERE
									service="'.$bundle_id_NAV.'" AND 
									flight= "'.$flight_id_out.'"';
								
						$answsqlnext=mysqli_query($db_server,$clean_mysql);
								
						if(!$answsqlnext) die("DELETE in service_reg TABLE failed: ".mysqli_error($db_server));
			
			// MAKE A NEW ONE
			$transfer_mysql='INSERT INTO service_reg 
									(flight,service,quantity) 
									VALUES
									("'.$flight_id_out.'","'.$bundle_id_NAV.'","1")';//BUNDLE IS APPLIED TO THE OUTGOING
				//echo $transfer_mysql.'<br/>';			
							$answsql=mysqli_query($db_server,$transfer_mysql);
							if(!$answsql) die("INSERT into service_reg TABLE failed:".mysqli_error($db_server));
			//PICKING UP BUNDLE CONTENT
		
			$textsql='SELECT services.id_NAV,bundle_content.quantity,bundle_content.hasCondition,bundle_content.id 
						FROM bundle_content 
						LEFT JOIN services ON bundle_content.service_id=services.id
						WHERE bundle_id='.$bundle_id.' AND bundle_content.isValid=1';
			//echo $textsql.'<br/>';	
			$answsql=mysqli_query($db_server,$textsql);
			$num_rows=$answsql->num_rows;	
			if(!$answsql) die("Database SELECT in bundle_content table failed: ".mysqli_error($db_server));
				//echo 'BUNDLE with:'.$num_rows.' rows<\br>';
		
			if($num_rows)
			{
				for($l=0;$l<$num_rows;$l++)
				{
					$row= mysqli_fetch_row($answsql);
			//	2. Process individual SERVICE
					$service_id_NAV=$row[0];
					$qty=$row[1];
					$cond_flag=$row[2];
					$position_id=$row[3];
					$cond_worked=1;
					if($cond_flag)
					{
							$cond_worked=0;
							
							// a. PULL CONDITION
							$conditionsql='SELECT services.id_NAV
									FROM bundle_content 
									LEFT JOIN bundle_cond ON bundle_content.id=bundle_cond.bundle_content_id
									LEFT JOIN services ON bundle_cond.service_id=services.id
									WHERE bundle_content.id='.$position_id;
				
							$answsql3=mysqli_query($db_server,$conditionsql);
							if(!$answsql3) die("Database SELECT in bundle_content table failed: ".mysqli_error($db_server));
							
							if($answsql3->num_rows)	
							{
							// b. CHECK IF THE SERVICE IN CONDITION WAS TAKEN FOR THE FLIGHT
								$row_cond= mysqli_fetch_row($answsql3);
								$checkservices='SELECT id,quantity FROM service_reg 
								WHERE (flight='.$flight_id_in.' OR flight='.$flight_id_out.') AND service="'.$row_cond[0].'"';
				
								$answsql4=mysqli_query($db_server,$checkservices);
								if(!$answsql4) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));
								$row_cond_exe= mysqli_fetch_row($answsql4);
								if($answsql4->num_rows)	$cond_worked=1;
							}
							// INFLUENCE QUANTITY
							
					}
					$sqlservices='SELECT id,quantity FROM service_reg 
								WHERE (flight='.$flight_id_in.' OR flight='.$flight_id_out.' ) AND service="'.$service_id_NAV.'"';
			
					$answsql1=mysqli_query($db_server,$sqlservices);
					$num_svs=$answsql1->num_rows;	
					if(($num_svs)&&($cond_worked))
					{
						
						$qty_svs=0;
						while($row_svs= mysqli_fetch_row($answsql1))
						{
								$register_id=$row_svs[0];
								$qty_svs=$row_svs[1];
								if($qty>=$qty_svs)
								{
									//DISABLE THIS RECORD
									$disable_svs="UPDATE service_reg SET isValid=0 WHERE id=$register_id";
									$answsql2=mysqli_query($db_server,$disable_svs);
									if(!$answsql2) die("UPDATE service_reg TABLE failed: ".mysqli_error($db_server));
									$qty-=$qty_svs;
								}
								else
								{//UPDATE THIS RECORD
									$qty_svs-=$qty;
									$qty=0;
									$adjust_svs="UPDATE service_reg SET quantity=$qty_svs WHERE id=$register_id";
									$answsql3=mysqli_query($db_server,$adjust_svs);
									if(!$answsql3) die("UPDATE service_reg TABLE failed: ".mysqli_error($db_server));
								}
						}
					}
					
				}
			}
			else echo "WARNING: EMPTY BUNDLE!";
		}	
		$finish_mysql="UPDATE  flight_pairs SET bundle_applied=1 WHERE id=$rec_id";
		$answsql=mysqli_query($db_server,$finish_mysql);
		if(!$answsql) die("UPDATE flight_pairs TABLE failed: ".mysqli_error($db_server));
		
	mysqli_close($db_server);			
	return 1;
}
?>