<?php
/*
*		Applies bundle (set of services) to a pair of flights
*			Bundle is different from package (template) in a sense that
*				a. it appears in invoice as a single string
*				b. if we find services included in the bundle, we cancel them (isValid=0, not send it to ERP
*		! IT IS PROCESSING ALL FLIGHTS, ALSO FOR OPERATORS 
*		 INPUT: pair ID
* 		 Returns:
*		  	- 1 Ok
*			- 0 if bundle was already applied 
*/

function ApplyBundle($rec_id,$fp)
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
				fwrite($fp,"WARNING: No flights found for a given ID in flight_pairs \r\n");
				return 0;
			}	
			$pair_data= mysqli_fetch_row($answsql_pre);
			$in_=$pair_data[0];
			$out_=$pair_data[1];
			$sent_flag=$pair_data[2];
			if($sent_flag) 
			{
				fwrite($fp,"FLIGHT WAS PROCESSED: EXITING! \r\n");
				return 0;
			}
		
		//  LOCATE CUSTOMER in the IN flight data
			$textsql='SELECT flights.id_NAV, clients.id,airport,aircrafts.air_class,clients.isOperator
						FROM  flights
						LEFT JOIN clients ON flights.bill_to_id=clients.id_NAV 
						LEFT JOIN aircrafts ON flights.plane_num=aircrafts.reg_num
						 WHERE flights.id="'.$in_.'"';
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data= mysqli_fetch_row($answsql);
				$flight_id_in=$flight_data[0];
				$customer=$flight_data[1];
				$airport=$flight_data[2];
				$airplane_cl=$flight_data[3];
				$isOperator=$flight_data[4];
				//echo "CUSTOMER ID: $customer <br/>";
				
			
		//  LOCATE OUT flight data
			$textsql='SELECT flights.id_NAV, clients.id,airport,aircrafts.air_class,clients.isRusCarrier,flights.flight_type
						FROM  flights
						LEFT JOIN clients ON flights.owner_id=clients.id_NAV 
						LEFT JOIN aircrafts ON flights.plane_num=aircrafts.reg_num
						 WHERE flights.id="'.$out_.'"';
				
			$answsql_out=mysqli_query($db_server,$textsql);
				
			if(!$answsql_out) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data_out= mysqli_fetch_row($answsql_out);
				$flight_id_out=$flight_data_out[0];
				$owner_id=$flight_data_out[1];
				$airport_out=$flight_data_out[2];
				$airplane_cl_out=$flight_data_out[3];
				$isRus=$flight_data_out[4];
				$fl_type=$flight_data_out[5];
				if($airplane_cl_out!=$airplane_cl) fwrite($fp,"WARNING: DIFFERENT CLASSES OF AIRCRAFT IN THE SAME ROUTE: $flight_id_out \r\n");
			
		//  0. IF IT IS AN OPERATOR's FLIGHT
		
		if($isOperator)
		{
				if(!$isRus)
				{
					if($fl_type==1) $isCargo=1;
					else $isCargo=0;
				fwrite($fp,"OPERATOR LOOP: FOREIGN CLIENT (function ApplyBundle) \r\n");	
					// SELECT APPROPRIATE BUNDLE
					$bundle_sql='SELECT bundle_id,services.id_NAV
						FROM  bundle_reg_op
						LEFT JOIN services on bundle_reg_op.bundle_id=services.id
						WHERE isCargo="'.$isCargo.'"';
				
			$answsql_bundle=mysqli_query($db_server,$bundle_sql);
				
			if(!$answsql_bundle) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
				$op_bundle= mysqli_fetch_row($answsql_bundle);
				$op_bundle_id		=$op_bundle[0];
				$op_bundle_id_NAV	=$op_bundle[1];
						$ret=ApplyBundleExe($rec_id,$flight_id_in,$flight_id_out,$op_bundle_id,$op_bundle_id_NAV);
						if ($ret) 
							fwrite($fp, "BUNDLE was applied SUCCESSFULLY! \r\n");
						else
							fwrite($fp, "ERROR: BUNDLE APPLICATION ABORTED \r\n");
						
					
				}
		}
		else
		{
			
	
		//  1. LOCATE all relevant BUNDLES  
		
			$b_sql='SELECT bundle_id,services.id_NAV, class, airports 
						FROM bundle_reg 
						LEFT JOIN services ON bundle_reg.bundle_id=services.id 
						WHERE client_id="'.$customer.'" AND (class="'.$airplane_cl_out.'" OR class IS NULL) AND (airports LIKE "%'.$airport_out.'%" OR airports IS NULL) AND bundle_reg.isValid';
			//echo $textsql.'<br/>';	
			$answsql0=mysqli_query($db_server,$b_sql);
				
			if(!$answsql0) die("Database SELECT in clients table failed: ".mysqli_error($db_server));
				//echo 'Package with:'.$answsql->num_rows.' rows<\br>';
			if(!$answsql0->num_rows)
			{
				fwrite($fp,"WARNING: NO BUNDLES FOR ClientID: $customer! \r\n");
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
								WHERE (flight='.$flight_id_in.' OR flight='.$flight_id_out.') AND service="'.$row_cond[0].'" AND isValid';
				
								$answsql4=mysqli_query($db_server,$checkservices);
								if(!$answsql4) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));
								$row_cond_exe= mysqli_fetch_row($answsql4);
								if($answsql4->num_rows)	$cond_worked=1;
							}
							// INFLUENCE QUANTITY
							
					}
						$sqlservices='SELECT id,quantity FROM service_reg 
								WHERE (flight='.$flight_id_in.' OR flight='.$flight_id_out.' ) AND service="'.$service_id_NAV.'" AND isValid';
			
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
				else fwrite($fp,"WARNING: EMPTY BUNDLE!");
			}	
			$finish_mysql="UPDATE  flight_pairs SET bundle_applied=1 WHERE id=$rec_id";
			$answsql=mysqli_query($db_server,$finish_mysql);
			if(!$answsql) die("UPDATE flight_pairs TABLE failed: ".mysqli_error($db_server));
		}
	mysqli_close($db_server);			
	return 1;
}
function ApplyBundleExe($rec_id,$flight_id_in,$flight_id_out,$bundle_id,$bundle_id_NAV)
{
 
	include("login_avia.php");
	
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
						
			
			//RECORD BUNDLE IN THE SERVICE REG
			
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
						
							$answsql=mysqli_query($db_server,$transfer_mysql);
							if(!$answsql) die("INSERT into service_reg TABLE failed:".mysqli_error($db_server));
			//PICKING UP BUNDLE CONTENT
		
			$textsql='SELECT services.id_NAV,bundle_content.quantity,bundle_content.hasCondition,bundle_content.id 
						FROM bundle_content 
						LEFT JOIN services ON bundle_content.service_id=services.id
						WHERE bundle_id='.$bundle_id.' AND bundle_content.isValid=1';
				
			$answsql=mysqli_query($db_server,$textsql);
			$num_rows=$answsql->num_rows;	
			if(!$answsql) die("Database SELECT in bundle_content table failed: ".mysqli_error($db_server));
		
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
								WHERE (flight='.$flight_id_in.' OR flight='.$flight_id_out.') AND service="'.$row_cond[0].'" AND isValid';
				
								$answsql4=mysqli_query($db_server,$checkservices);
								if(!$answsql4) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));
								$row_cond_exe= mysqli_fetch_row($answsql4);
								if($answsql4->num_rows)	$cond_worked=1;
							}
							// INFLUENCE QUANTITY
							
					}
					$sqlservices='SELECT id,quantity FROM service_reg 
								WHERE (flight='.$flight_id_in.' OR flight='.$flight_id_out.' ) AND service="'.$service_id_NAV.'" AND isValid';
			
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
			else fwrite($fp,"WARNING: EMPTY BUNDLE!");
		
		$finish_mysql="UPDATE  flight_pairs SET bundle_applied=1 WHERE id=$rec_id";
		$answsql=mysqli_query($db_server,$finish_mysql);
		if(!$answsql) die("UPDATE flight_pairs TABLE failed: ".mysqli_error($db_server));
		
	mysqli_close($db_server);	
	return 1;
}
?>