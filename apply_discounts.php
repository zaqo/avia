<?php
function ApplyDiscounts($flightid)
{
//Applies discounts to the flight's services
//Most probably I will need only fraction of this code directly incorporated into flight's processing procedure
//INPUT: local flight ID
// Returns:
//  - Array (service,discount) Ok
//	- 0 failed
	include("login_avia.php");
	
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			//  LOCATE flight data
			$textsql="SELECT flights.id,flights.id_NAV,date,flight,direction,plane_num,plane_type,plane_mow,airport,passengers_adults,passengers_kids,
						category,isHelicopter,time_fact,clients.id FROM  flights 
						LEFT JOIN clients ON clients.id_NAV=flights.bill_to_id
						WHERE flights.id=$flightid";
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data= mysqli_fetch_row($answsql);
				
				
				//SET UP Flight's Object
				//$flight= new Flight();
				//$flight->id=$flight_data[0];
				$id_NAV=$flight_data[1];
				$flight_date=$flight_data[2];
				$flight_num=$flight_data[3];
				$direction=$flight_data[4];
				$plane_id=$flight_data[5];
				$plane_type=$flight_data[6];
				$plane_mow=$flight_data[7];
				$airport=$flight_data[8];
				$passengers_adults=$flight_data[9];
				$passengers_kids=$flight_data[10];
				$category=$flight_data[11];
				$isHelicopter=$flight_data[12];
				$time_fact=$flight_data[13];
				$client_id=$flight_data[14];
				//$flight->plane_owner=$flight_data[15];
				$result_discount=array();
		
		//  1. LOCATE all services relevant to the flight
			$textsql='SELECT service FROM service_reg WHERE flight="'.$id_NAV.'"';
			//echo $textsql.'<br/>';	
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));
				//echo 'Flight has got:'.$answsql->num_rows.' services<br/>';
			if($answsql->num_rows)
			{
				while($service= mysqli_fetch_row($answsql))
				{	
			//	2. Process individual service
				
					//a.get service id
					$srv_nav_id=$service[0];
					echo "SERVICE: $srv_nav_id <br/>";
					$sqlserviceid='SELECT id 
									FROM services 
									WHERE id_NAV="'.$srv_nav_id.'"';
					//echo $sqlserviceid.'group <br/>';
					$answsql1=mysqli_query($db_server,$sqlserviceid);
					
					if ($answsql1)
					{	
						$service_id= mysqli_fetch_row($answsql1);
						$sid=$service_id[0];
				//	2.1 - process group discounts
						$sqlservices='SELECT discount_id,discounts_group.discount_val 
									FROM discounts_grp_reg 
									LEFT JOIN discounts_group on discounts_group.id = discounts_grp_reg.discount_id 
									WHERE service_id="'.$sid.'" AND discounts_group.isValid=1 AND discounts_group.valid_from<="'.$flight_date.'" AND discounts_group.valid_to>="'.$flight_date.'"';//If we need zero as unlimited in valid_to, add it after additional OR here
				
						//echo '1. '.$sqlservices.'group <br/>';
						$answsql2=mysqli_query($db_server,$sqlservices);
						//var_dump($answsql2);
						if($answsql2->num_rows)
						{	
			// Process  discount from the list		
							while($discount= mysqli_fetch_row($answsql2))
							{
			// Get conditions for applicability of discount
								$disc_id=$discount[0];
								$disc_val=$discount[1];
								$flag=0;
								//echo "2. Discounts are: $disc_id, $disc_val <br/>";
								$sqlgetconditions="SELECT condition_id,composition FROM discount_grp_content 
												WHERE discount_id=$disc_id ORDER BY sequence";
								//echo '3. '.$sqlgetconditions.' group conditions<br/>';
								$answsql3=mysqli_query($db_server,$sqlgetconditions);
								if($answsql3->num_rows) 
								{
					//Process individual condition
									while ($condition=mysqli_fetch_row($answsql3))
									{	
										$cond_id=$condition[0];
										$cond_comp=$condition[1];
										$sqlgetconditiondata="SELECT param_id,from_val,to_val,enum_of_values,condition_id FROM discount_conditions 
												WHERE id=$cond_id AND isValid=1";
										//echo '4.'.$sqlgetconditiondata.' inside condition <br/>';
										$answsql4=mysqli_query($db_server,$sqlgetconditiondata);
										if($answsql4)
										{	
											
											$cond_data=mysqli_fetch_row($answsql4);
											//echo "5. Analyzing condition: ".$cond_data[0]."<br/>";
											if($cond_data)
											{
					// Process applicability of condition!
												$param=$cond_data[0];
												$start_val=$cond_data[1];
												$end_val=$cond_data[2];
												$flag=0;
												switch($param)
												{
													case 1: // Is the destination domestic or foreign?
														$sqlcheckdestination="SELECT domain FROM airports 
																WHERE id=$airport";
														//echo '6. '.$sqlgetconditiondata.'<br/>';
														$answsql5=mysqli_query($db_server,$sqlcheckdestination);
														if(!$answsql5) die("Database SELECT into airportd table failed: ".mysqli_error($db_server));
														$zone=mysqli_fetch_row($answsql5);
														if($zone)
														{
														//Now check the condition value and register the discount
															$dest_zone=$zone[0];
															if($zone[0]==2) $dest_zone=1; // eliminating CIS!
															if($dest_zone==$start_val) 
															$flag=1;
														}	
														break;
													
													case 2:  // Discount for the given aircraft number
														if(strcasecmp($start_val, $plane_id) == 0)
															$flag=1;
														break;
													
													case 3:  // based on plane type
														if((int)$start_val==(int)$plane_type)
														{	
															//echo "FLAG IS SET! for PLANE TYPE $plane_type <br/>";
															$flag=1;
														}
														break;
												
													case 4:  // based on plane MOW (not checking type of condition now)
														if(($start_val<=$plane_mow)&&($end_val>=$plane_mow))
														{	
															$flag=1;
															//echo "Flag was set! PLANE MOW = $plane_mow <br/>";
														}
														break;
													
													case 5:  // based on destination (no support for diapazone yet)
														if($start_val==$airport)
															$flag=1;
														break;	

													case 6:  // PAX only if above the limit
														if($start_val<=$passengers_adults)
															$flag=1;
														break;	
													
													case 7:  // PAX only if above the limit
														if($start_val<=$passengers_kids)
															$flag=1;
														break;	
													
													case 8:  // category is equal to
														if($start_val==$category)
															$flag=1;
														break;		
												
													case 9:  // Helicopter
														if($isHelicopter)
															$flag=1;
														break;	
													
													case 10:  // Time of arrival START_VAL, END_VAL must be time!!!
														if(($time_fact>=$start_val)&&($time_fact<=$end_val))
															$flag=1;
														break;
												
													default:
													echo "WARNING: Paremeter for condition for a service: $service_id  does not exist! <br/>";
												}
											}
										}
										if(($flag==0)&&($cond_comp==1)) break; // If we have combination of conditions and condition with AND is FALSE discount is not appplied
									}
								}
								
							// make a record of it
								if($flag)
								{
									$textsql='INSERT INTO discounts_journal
										(flight_id,service_id,discount_id,isGroup,condition_id,value)
										VALUES( '.$flightid.','.$sid.','.$disc_id.',1,'.$cond_id.','.$disc_val.')';
									//echo 'FINISH grp.'.$textsql.'<br/>';				
									$answsql6=mysqli_query($db_server,$textsql);
									if(!$answsql6) die("Insert INTO discounts_journal table failed: ".mysqli_error($db_server));
									$result_discount[$sid]=$disc_val;
								}
								else echo "NO GROUP CONDITIONS DISCOVERED for THE SERVICE: $sid, SWITCHING TO INDIVIDUAL <br/>";
							}//end of processing individual discount
						}//end of processing group discounts
					
					// 2.2 Process company discounts	
						
						$sqlservices='SELECT discount_id,discounts_individual.discount_val 
									FROM discounts_ind_reg 
									LEFT JOIN discounts_individual on discounts_individual.id = discounts_ind_reg.discount_id 
									WHERE service_id="'.$sid.'" 
										AND discounts_individual.client_id="'.$client_id.'"
										AND discounts_individual.isValid=1 
										AND discounts_individual.valid_from<="'.$flight_date.'" 
										AND (discounts_individual.valid_to>="'.$flight_date.'" OR discounts_individual.valid_to="0000-00-00")';//If we need zero as unlimited in valid_to, add it after additional OR here
				
						//echo "WARNING: $sqlservices <br/>";
						$answsql2=mysqli_query($db_server,$sqlservices);
						//var_dump($answsql2);
						if($answsql2->num_rows)
						{	
			// Process individual discount from the list		
							
							while($discount= mysqli_fetch_row($answsql2))
							{
			// Get conditions for applicability of discount
								$disc_id=$discount[0];
								$disc_val=$discount[1];
								$flag=0;
								echo "ENTERED PROCESSING INDIVIDUAL DISCOUNT $disc_id , $disc_val % <br/>";
								//echo "2 ind. Discounts are: $disc_id, $disc_val <br/>";
								$sqlgetconditions="SELECT condition_id,composition FROM discount_ind_content 
												WHERE discount_id=$disc_id ORDER BY sequence";
								//echo "3 ind. ".$sqlgetconditions.' individual<br/>';
								$answsql3=mysqli_query($db_server,$sqlgetconditions);
								if($answsql3->num_rows) 
								{
					//Process individual condition
									while ($condition=mysqli_fetch_row($answsql3))
									{	
										$cond_id=$condition[0];
										$cond_comp=$condition[1];
										$sqlgetconditiondata="SELECT param_id,from_val,to_val,enum_of_values,condition_id FROM discount_conditions 
												WHERE id=$cond_id AND isValid=1";
										//echo "4 ind. LOOKING FOR CONDITIONS".$sqlgetconditiondata.'individual <br/>';
										$answsql4=mysqli_query($db_server,$sqlgetconditiondata);
										if($answsql4->num_rows)
										{	
											$cond_data=mysqli_fetch_row($answsql4);
											if($cond_data)
											{
					// Process applicability of condition!
												
												$param=$cond_data[0];
												$start_val=$cond_data[1];
												$end_val=$cond_data[2];
												echo "ENTERED PROCESSING CONDITIONS: param is  $param , start from: $start_val plane: $plane_type<br/>";
												echo "RESULT OF COMPARISON: ".strpos($start_val, $plane_type)."<br/>";
												$flag=0;
												switch($param)
												{
													case 1: // Is the destination domestic or foreign?
														$sqlcheckdestination="SELECT domain FROM airports 
																WHERE id=$airport";
														//echo "5 ind. ".$sqlcheckdestination.' individual <br/>';
														$answsql5=mysqli_query($db_server,$sqlcheckdestination);
														if(!$answsql5) die("Database SELECT into airportd table failed: ".mysqli_error($db_server));
														$zone=mysqli_fetch_row($answsql5);
														if($zone)
														{
														//Now check the condition value and register the discount
															$dest_zone=$zone[0];
															if($zone[0]==2) $dest_zone=1; // eliminating CIS!
															if($dest_zone==$start_val) 
															$flag=1;
														}	
														break;
													
													case 2:  // Discount for the given aircraft number
														if(strcasecmp($start_val, $plane_id) == 0)
															$flag=1;
														break;
													
													case 3:  // based on plane type
														if((int)$start_val==(int)$plane_type)
														{
															//echo "CATCHED PLANE TYPE COND: planetype= $plane_type <br/>";
															$flag=1;
														}
														break;
												
													case 4:  // based on plane MOW (not checking type of condition now)
														if(($start_val<=$plane_mow))
															{	
															$flag=1;
															echo "Flag was set! Condition 4. <br/>";
														}
														break;
													
													case 5:  // based on destination (no support for diapazone yet)
														if($start_val==$airport)
															$flag=1;
														break;	

													case 6:  // PAX only if above the limit
														if($start_val<=$passengers_adults)
															$flag=1;
														break;	
													
													case 7:  // PAX only if above the limit
														if($start_val<=$passengers_kids)
															$flag=1;
														break;	
													
													case 8:  // category is equal to
														if($start_val==$category)
															$flag=1;
														break;		
												
													case 9:  // Helicopter
														if($isHelicopter)
															$flag=1;
														break;	
													
													case 10:  // Time of arrival START_VAL, END_VAL must be time!!!
														if(($time_fact>=$start_val)&&($time_fact<=$end_val))
															$flag=1;
														break;	
												
													default:
													echo "WARNING: Paremeter for condition for a service: $service_id  does not exist! <br/>";
												}
											}
										}
										if(($flag==0)&&($cond_comp==1)) break; // If we have combination of conditions and condition with AND is FALSE discount is not appplied
									}
								}
							// make a record of it
								if($flag)
								{
								 $textsql='INSERT INTO discounts_journal
									(flight_id,service_id,discount_id,isGroup,condition_id,value)
									VALUES( '.$flightid.','.$sid.','.$disc_id.',0,'.$cond_id.','.$disc_val.')';
								   //echo "FINISH ind. ".$textsql.'  JOURNAL individual<br/>';				
							 	 $answsql6=mysqli_query($db_server,$textsql);
								 if(!$answsql6) die("Insert INTO discounts_journal table failed: ".mysqli_error($db_server));
								 $result_discount[$sid]=$disc_val;
								}
								else echo "NO INDIVIDUAL DISCOUNTS APPLIED FOR THE SERVICE $sid: SWITCHING TO THE NEXT SERVICE<br/>";
							}//end of processing individual discount
						}
						//end of company discounts
					} 		
				
				}//end of processing individual service
			}//end of general if
			
		
	mysqli_close($db_server);			
	return $result_discount;
}

?>