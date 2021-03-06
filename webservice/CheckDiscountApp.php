<?php
//THIS FILE WAS USED AS ORIGINAL OF FUNCTION
// WORKING COPY IS IN THE sapconnector.php

function CheckDiscountApp_DEFINITION($flight_out,$sid,$client_id,$time_fact,$isHelicopter,$result_discount)
{
/* 
	CHECKS APPLICABILITY OF DISCOUNT FOR THIS PAIR FOR PARTICULAR SERVICE 
	INPUT: 	
		$flight_out - FLIGHT OBJECT 
		$sid 		- ID of SERVICE
		$client_id	- ID of CLIENT
		$time_fact	- TIME OF ARRIVAL!
		$isHelicopter - HELICOPTER FLAG
	OUTPUT:  ARRAY of service id| discount value $result_discount
*/
	include 'login_avia.php';
	//$result_discount=array();
		$flightid=$flight_out->id;
		$airport=$flight_out->airport;
		$zone=$flight_out->airport_class; //DOMAIN
		$plane_id=$flight_out->plane_id;
		$plane_type=$flight_out->plane_type;
		$plane_mow=$flight_out->plane_mow;
		$passengers_adults=$flight_out->passengers_adults;	
		$passengers_kids=$flight_out->passengers_kids;
		$category=$flight_out->flight_cat;
		$flight_date=$flight_out->flight_date;
	
		
		//	1. GROUP discounts
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
												$enum_string=$cond_data[3];
												$comparison=$cond_data[4];
												$flag=0;
												switch($param)
												{
													case 1: // Is the destination domestic or foreign?
														//Now check the condition value and register the discount
															if($zone==2) $zone=1; // eliminating CIS!
															if($zone==$start_val) 
															$flag=1;
															
														break;
													
													case 2:  // Discount for the given aircraft number
														if(strcasecmp($start_val, $plane_id) == 0)
															$flag=1;
														break;
													
													case 3:  // based on plane type
														switch($comparison){
															case 0: // " = "
															if((int)$start_val==(int)$plane_type)
															{	
															//echo "FLAG IS SET! for PLANE TYPE $plane_type <br/>";
																$flag=1;
															}
															break;
															case 6: // " [ ... ] "
															if($enum_string)
															{	
															
																$values=explode(',',$enum_string);
																$total=count($values);
																if ($total)
																{
																	for($ind=0;$ind<$total;$ind++)
																	{
																		if($values[$ind]==$plane_type)
																		{
																			$flag=1;
																			echo "FLAG IS SET VIA ENUM! for PLANE TYPE $plane_type <br/>";
																		}
																	}
																}
															}
															break;
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
														{
															//echo "FLAG IS SET FOR HELICOPTER! <br/>";
															$flag=1;
														}
														break;	
													
													case 10:  // Time of arrival START_VAL, END_VAL must be time!!!
														if(($time_fact>=$start_val)||($time_fact<=$end_val))
														{
														echo "NIGHT: START FROM ->$start_val END BY ->$end_val ||| ACTUAL $time_fact <br/>";
															$flag=1;
														}
														break;
												
													default:
													echo "WARNING: Parameter for condition for a service: $service_id  does not exist! <br/>";
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
									
									// DISCOUNTS MULTIPLIED
									if($result_discount[$sid]) $result_discount[$sid]*=$disc_val;
									else $result_discount[$sid]=$disc_val;
								}
								//else echo "NO GROUP CONDITIONS DISCOVERED for THE SERVICE: $sid, SWITCHING TO INDIVIDUAL <br/>";
							}//end of processing individual discount
						}//end of processing group discounts
					
			// 2. INDIVIDUAL COMPANY discounts	
						
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
												$enum_string=$cond_data[3];
												$comparison=$cond_data[4];
												//echo "ENTERED PROCESSING CONDITIONS: param is  $param , start from: $start_val plane: $plane_type, VALUES: $enum_string <br/>";
												//echo "RESULT OF COMPARISON: ".strpos($start_val, $plane_type)."<br/>";
												$flag=0;
												switch($param)
												{
													case 1: // Is the destination domestic or foreign?
												
															if($zone==2) $zone=1; // eliminating CIS!
															if($zone==$start_val) 
																$flag=1;
														break;
													
													case 2:  // Discount for the given aircraft number
														if(strcasecmp($start_val, $plane_id) == 0)
															$flag=1;
														break;
													
													case 3:  // based on plane type
														switch($comparison){
															case 0: // " = "
															if((int)$start_val==(int)$plane_type)
															{	
															//echo "FLAG IS SET! for PLANE TYPE $plane_type <br/>";
																$flag=1;
															}
															break;
															case 6: // " [ ... ] "
															if($enum_string)
															{	
															
																$values=explode(',',$enum_string);
																$total=count($values);
																if ($total)
																{
																	for($ind=0;$ind<$total;$ind++)
																	{
																		if($values[$ind]==$plane_type)
																		{
																			$flag=1;
																			echo "FLAG IS SET VIA ENUM! for PLANE TYPE $plane_type <br/>";
																		}
																	}
																}
															}
															break;
														}
														break;
												
													case 4:  // based on plane MOW (not checking type of condition now)
														if(($start_val<=$plane_mow))
														{	
															$flag=1;
															//echo "Flag was set! Condition 4. <br/>";
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
														
														//echo "CHECKING OUT CATEGORY OF FLIGHT: $category <br/>";
														if($start_val==$category)
															$flag=1;
														break;		
												
													case 9:  // Helicopter
														if($isHelicopter)
															$flag=1;
														break;	
													
													case 10:  // Time of arrival START_VAL, END_VAL must be time!!!
														if(($time_fact>=$start_val)&&($time_fact<$end_val))
															$flag=1;
														break;	
												
													default:
													echo "WARNING: Parameter for condition for a service: $service_id  does not exist! <br/>";
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
								 // DISCOUNTS MULTIPLIED
									if($result_discount[$sid]) $result_discount[$sid]*=$disc_val;
									else $result_discount[$sid]=$disc_val;
								}
								//else echo "NO INDIVIDUAL DISCOUNTS APPLIED FOR THE SERVICE $sid: SWITCHING TO THE NEXT SERVICE<br/>";
							}//end of processing individual discount
						}
						//end of company discounts
	return 1;
}
?>