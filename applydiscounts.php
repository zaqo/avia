function ApplyDiscounts($flightid)
{
//Applies package to the flight
//INPUT: Navision flight ID
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
			$textsql='SELECT * FROM  flights WHERE id_NAV="'.$flightid.'"';
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data= mysqli_fetch_row($answsql);
				
				//Check out if package was already applied
				if($flight_data[17])
				{
					echo "WARNING: FLIGHT #".$flight_data[3]." PACKAGES ARE ALREADY APPLIED! -=EXITING=- <br/> ";
					return 0;
				}
				//SET UP Flight's Object
				$flight= new Flight();
				$flight->id=$flight_data[0];
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
			
		
		//  1. LOCATE all packages relevant to the flight
			$textsql='SELECT id FROM packages WHERE client_id="'.$flight->bill_to.'" AND isValid=1';
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
						$service_nav=$cond[1];
						$sqlgetservice='SELECT id_mu,isforKids FROM services 
									WHERE id_NAV="'.$service_nav.'"';
					//echo $sqlgetservice.'<br/>';
						$servicesql=mysqli_query($db_server,$sqlgetservice);
						if(!$servicesql) die(" SERVICE $service_nav could not be located in the services table: ".mysqli_error($db_server));			
					
						$mes_unit=mysqli_fetch_row($servicesql);
					//var_dump($mes_unit); 
						$quantity=0;
						$id_mu=$mes_unit[0];
						switch($id_mu)
						{
							case 1: // applies to a flight
								$quantity=1;
								break;
							case 2:  // based on passengers quantity
								if($mes_unit[1]) $quantity=$flight->passengers_kids;
								else $quantity=$flight->passengers_adults;
								break;
							case 3:  // based on plane max weight
								$quantity=$flight->plane_mow;
							break;
							default:
								echo "WARNING: Measurement unit for a service: $service_nav  is not defined! <br/>";
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