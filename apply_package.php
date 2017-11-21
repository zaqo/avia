<?php
/*
Applies package (template of services) to the flight 
*/

function ApplyPackage($rec_id)
{
//Applies package to the pair of flights
//INPUT: local flight ID
// Returns:
//  - 1 Ok
//	- 0 if package was already applied 
	include("login_avia.php");
	
		set_time_limit(0);
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
		
		//  LOCATE IN flight data
			$textsql='SELECT id,id_NAV,date,flight,direction,plane_num,plane_type,
						plane_mow,airport,passengers_adults,passengers_kids,customer_id,
						bill_to_id,owner,time_fact,package_applied FROM  flights WHERE id="'.$in_.'"';
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data= mysqli_fetch_row($answsql);
				$flight_num=$flight_data[3];
				$pack_flag=$flight_data[15];
				//Check out if package was already applied
				
				if($pack_flag)
				{
					echo "WARNING: FLIGHT #".$flight_num." PACKAGES HAVE BEEN ALREADY APPLIED! -=EXITING=- <br/> ";
					return 0;
				}
				
				//SET UP IN Flight's Object
				$flight= new Flight();
				$flight->id=$flight_data[0];
				$flight->id_NAV=$flight_data[1];
				$flight->flight_date=$flight_data[2];
				$flight->flight_num=$flight_num;
				$flight->direction=$flight_data[4];
				$flight->plane_id=$flight_data[5];
				$flight->plane_type=$flight_data[6];
				$flight->plane_mow=$flight_data[7];
				$flight->airport=$flight_data[8];
				$flight->passengers_adults=$flight_data[9];
				$flight->passengers_kids=$flight_data[10];
				$flight->customer=$flight_data[11];
				$flight->bill_to=$flight_data[12];
				$flight->plane_owner=$flight_data[13];
				$flight->time_fact=$flight_data[14];
			
		//  LOCATE OUT flight data
			$textsql='SELECT id,id_NAV,date,flight,direction,plane_num,plane_type,
						plane_mow,airport,passengers_adults,passengers_kids,customer_id,
						bill_to_id,owner,time_fact,package_applied FROM  flights WHERE id="'.$out_.'"';
				
			$answsql_out=mysqli_query($db_server,$textsql);
				
			if(!$answsql_out) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data_out= mysqli_fetch_row($answsql_out);
				$flight_num=$flight_data_out[3];
				$pack_flag=$flight_data_out[15];
				//Check out if package was already applied
				
				if($pack_flag)
				{
					echo "WARNING: FLIGHT #".$flight_num." PACKAGES HAVE BEEN ALREADY APPLIED! -=EXITING=- <br/> ";
					return 0;
				}
				
		//SET UP OUT Flight's Object
				$flight_out= new Flight();
				$flight_out->id=$flight_data_out[0];
				$flight_out->id_NAV=$flight_data_out[1];
				$flight_out->flight_date=$flight_data_out[2];
				$flight_out->flight_num=$flight_num;
				$flight_out->direction=$flight_data_out[4];
				$flight_out->plane_id=$flight_data_out[5];
				$flight_out->plane_type=$flight_data_out[6];
				$flight_out->plane_mow=$flight_data_out[7];
				$flight_out->airport=$flight_data_out[8];
				$flight_out->passengers_adults=$flight_data_out[9];
				$flight_out->passengers_kids=$flight_data_out[10];
				$flight_out->customer=$flight_data_out[11];
				$flight_out->bill_to=$flight_data_out[12];
				$flight_out->plane_owner=$flight_data_out[13];
				$flight_out->time_fact=$flight_data_out[14];
			
		//  1. LOCATE all packages relevant to the flight
			$clientsql='SELECT id FROM clients WHERE id_NAV="'.$flight->bill_to.'"';
			//echo $textsql.'<br/>';	
			$answsql0=mysqli_query($db_server,$clientsql);
				
			if(!$answsql0) die("Database SELECT in clients table failed: ".mysqli_error($db_server));
				//echo 'Package with:'.$answsql->num_rows.' rows<\br>';
			if(!$answsql0->num_rows)
			{
				echo "WARNING: NO INFO ABOUT Client! <br/>";
				return 0; // No information about client
			}
			$client_id=mysqli_fetch_row($answsql0);
			//PICKING UP PACKAGE BY CLIENT ID
			$textsql='SELECT package_id FROM package_reg WHERE client_id="'.$client_id[0].'"';
			//echo $textsql.'<br/>';	
			$answsql=mysqli_query($db_server,$textsql);
			$num_rows=$answsql->num_rows;	
			if(!$answsql) die("Database SELECT in package_reg table failed: ".mysqli_error($db_server));
				//echo 'Package with:'.$num_rows.' rows<\br>';
				
			if($num_rows)
			{
				for($l=0;$l<$num_rows;$l++)
				{
					$package= mysqli_fetch_row($answsql);
			//	2. Process individual Package
				//var_dump($package);
					$sqlservices='SELECT id,service_id,scope,direction FROM package_content 
								WHERE package_id='.$package[0].' AND isValid=1';
				//echo $sqlservices.'<br/>';
					$answsql1=mysqli_query($db_server,$sqlservices);
					$passengers=0;
					$passengers_kids=0;
					$plane_mow=0;
					while($cond= mysqli_fetch_row($answsql1))
					{
					// Get the quantity
						$service_id=$cond[1];
						$dir=$cond[3];
						if($dir)
						{
							$passengers=$flight_out->passengers_adults;
							$passengers_kids=$flight_out->passengers_kids;
							$plane_mow=$flight_out->plane_mow;
							$flight_id=$flight_out->id_NAV;
							$airport=$flight_out->airport;
						}
						else
						{
							$passengers=$flight->passengers_adults;
							$passengers_kids=$flight->passengers_kids;
							$plane_mow=$flight->plane_mow;
							$flight_id=$flight->id_NAV;
							$airport=$flight->airport;
						}
						$sqlgetservice='SELECT id_mu,isforKids,id_NAV FROM services 
									WHERE id='.$service_id;
					//echo $sqlgetservice.'<br/>';
						$servicesql=mysqli_query($db_server,$sqlgetservice);
						if(!$servicesql) die(" SERVICE $service_id could not be located in the services table: ".mysqli_error($db_server));			
					
						$mes_unit=mysqli_fetch_row($servicesql);
					//var_dump($mes_unit); 
						$quantity=0;
						$id_mu=$mes_unit[0];
						$service_nav=$mes_unit[2];
						switch($id_mu)
						{
							case 1: // applies to a flight
								$quantity=1;
								break;
							case 2:  // based on passengers quantity - depending on kids or adults
								if($mes_unit[1]) $quantity=$passengers_kids;
								else $quantity=$passengers;
								break;
							case 3:  // based on plane max weight HERE COMES 1/1000
								$quantity=$plane_mow;
							break;
							case 6:  // WATER - NOW IT"S ZEROED
								$quantity=0;
							case 7:  // TIME FOR PARKING OVERDRAFT IN HOURS - NOW IT"S ZEROED
								$quantity=0;
							break;
							case 8:  // TIME FOR PARKING OVERDRAFT IN DAYS - NOW IT"S ZEROED
								$quantity=0;
							break;
							case 9:  // based on TOTAL passengers quantity - independing on kids or adults
								$quantity=$passengers_kids+$passengers;
								
								break;
							default:
								echo "WARNING: Measurement unit for a service: $service_id  is not defined! <br/>";
								$quantity=0;
						}
						if(!$servicesql) die("Database SERVICE $service_nav could not be located: ".mysqli_error($db_server));			
					
						if ($cond[2]==0) //applicable to all flights
						{
							$transfer_mysql='INSERT INTO service_reg 
									(flight,service,quantity) 
									VALUES
									("'.$flight_id.'","'.$service_nav.'","'.$quantity.'")';
							//echo "<br/> ALL FLIGHTS ".$transfer_mysql."<br/>";
							$answsql=mysqli_query($db_server,$transfer_mysql);
							if(!$answsql) die("INSERT into TABLE failed: #1".mysqli_error($db_server));
						}	
						else              // apply to certain flights, depends on the airport
						{
							$pack_pos=$cond[0];
						 // Apply conditions
						 // CHECK IF CONDITION IS INCLUSIVE OR EXCLUSIVE
						 $sqlairports='SELECT cond,airport_id FROM package_conditions 
									WHERE  package_position_id = "'.$pack_pos.'"
									AND isValid=1';
							$answairport=mysqli_query($db_server,$sqlairports);
							if(!$answairport) die("SELECT into package conditions TABLE failed: ".mysqli_error($db_server));
							$airport_flag=0;
							$res_cond=mysqli_fetch_row($answairport);
							$include_flag=$res_cond[0];
							if($res_cond[1]==$airport)
								$airport_flag=1;
							else
							{
								while( $res_cond=mysqli_fetch_row($answairport))
								{
									if($res_cond[1]==$airport)
										$airport_flag=1;
								}
							}
							if(($airport_flag&&$include_flag)||(!$airport_flag&&!$include_flag))
							{
									$transfer_mysql='INSERT INTO service_reg 
										(flight,service,quantity) 
										VALUES
									("'.$flight_id.'","'.$service_nav.'","'.$quantity.'")';
									//echo "<br/>SPECIAL AIRPORTS CONDITION ".$transfer_mysql."<br/>";
									$answsql=mysqli_query($db_server,$transfer_mysql);
									if(!$answsql) die("INSERT into TABLE failed: #2".mysqli_error($db_server));
							}	
						}
					}
				}
			}
		
		$finish_mysql="UPDATE  flights SET package_applied=1 WHERE id=$in_";
		$answsql=mysqli_query($db_server,$finish_mysql);
		if(!$answsql) die("UPDATE flights TABLE failed: ".mysqli_error($db_server));
		$finish_mysql="UPDATE  flights SET package_applied=1 WHERE id=$out_";
		$answsql=mysqli_query($db_server,$finish_mysql);
		if(!$answsql) die("UPDATE flights TABLE failed: ".mysqli_error($db_server));
		
	mysqli_close($db_server);			
	return 1;
}
?>