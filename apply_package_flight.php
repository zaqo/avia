<?php
/*
Applies package (template of services) to the flight 
*/

function ApplyPackage($flightid)
{
//Applies package to the pair of flights
//INPUT: local flight ID
// Returns:
//  - 1 Ok
//	- 0 if package was already applied 
	include("login_avia.php");
	
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//  LOCATE flight data
			$textsql='SELECT id,id_NAV,date,flight,direction,plane_num,plane_type,
						plane_mow,airport,passengers_adults,passengers_kids,customer_id,
						bill_to_id,owner,time_fact,package_applied FROM  flights WHERE id="'.$flightid.'"';
				
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
				//SET UP Flight's Object
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
			
		
		//  1. LOCATE all packages relevant to the flight
			$clientsql='SELECT id FROM clients WHERE id_NAV="'.$flight->bill_to.'"';
			//echo $textsql.'<br/>';	
			$answsql0=mysqli_query($db_server,$clientsql);
				
			if(!$answsql0) die("Database SELECT in clients table failed: ".mysqli_error($db_server));
				//echo 'Package with:'.$answsql->num_rows.' rows<\br>';
			if(!$answsql0->num_rows) return 1; // No information about client
			$client_id=mysqli_fetch_row($answsql0);
			//PICKING UP PACKAGE BY CLIENT ID - NEED REFACTORING HERE!
			$textsql='SELECT id FROM packages WHERE client_id='.$client_id[0].' AND isValid=1';
			//echo $textsql.'<br/>';	
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT in packages table failed: ".mysqli_error($db_server));
				//echo 'Package with:'.$answsql->num_rows.' rows<\br>';
			if($answsql->num_rows)
			{
				while($package= mysqli_fetch_row($answsql))
				{	
			//	2. Process individual Package
				
					$sqlservices='SELECT id,service_id,scope FROM package_content 
								WHERE package_id='.$package[0].' AND isValid=1';
				//echo $sqlservices.'<br/>';
					$answsql1=mysqli_query($db_server,$sqlservices);
					while($cond= mysqli_fetch_row($answsql1))
					{
					// Get the quantity
						$service_id=$cond[1];
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
							case 2:  // based on passengers quantity
								if($mes_unit[1]) $quantity=$flight->passengers_kids;
								else $quantity=$flight->passengers_adults;
								break;
							case 3:  // based on plane max weight HERE COMES 1/1000
								$quantity=$flight->plane_mow;
							break;
							case 6:  // WATER - NOW IT"S ZEROED
								$quantity=0;
							case 8:  // TIME FOR OVERDRAFT - NOW IT"S ZEROED
								$quantity=0;
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
									("'.$flight->id_NAV.'","'.$service_nav.'",'.$quantity.')';
							//echo "<br/> ALL FLIGHTS ".$transfer_mysql."<br/>";
							$answsql=mysqli_query($db_server,$transfer_mysql);
							if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
						}	
						else              // apply to certain flights, depends on the airport
						{
							$pack_pos=$cond[0];
						 // Apply conditions
							$sqlairports='SELECT cond FROM package_conditions 
									WHERE airport_id='.$flight->airport.'
									AND package_position_id = '.$pack_pos.'
									AND isValid=1';
							$answairport=mysqli_query($db_server,$sqlairports);
							if(!$answairport) die("SELECT into package conditions TABLE failed: ".mysqli_error($db_server));
							$res_cond=mysqli_fetch_row($answairport);
							if($res_cond[0]) // Apply for this airport 
							{
								$transfer_mysql='INSERT INTO service_reg 
									(flight,service,quantity) 
									VALUES
									("'.$flight->id_NAV.'","'.$service_nav.'",'.$quantity.')';
								//echo "<br/>SPECIAL AIRPORTS FLIGHTS ".$transfer_mysql."<br/>";
								$answsql=mysqli_query($db_server,$transfer_mysql);
								if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
							}
						}
					}
				}
			}
		$finish_mysql="UPDATE  flights SET package_applied=1 WHERE id=$flightid";
		$answsql=mysqli_query($db_server,$finish_mysql);
		if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
	mysqli_close($db_server);			
	return 1;
}
?>