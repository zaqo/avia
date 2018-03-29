<?php


time_over_parking_new(201);

function time_over_parking_new($pair_id)
{
/* 
	INPUT: 	PAIR ID
	OUTPUT: TIME IN HOURS, ROUNDED UP 
*/
include 'login_avia.php';

//include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$textsql='SELECT in_id,out_id FROM flight_pairs WHERE id="'.$pair_id.'"';
			
			$answsql=mysqli_query($db_server,$textsql);	
			if(!$answsql) die("Database SELECT TO flight_pairs table failed: ".mysqli_error($db_server));	
			
			$pair= mysqli_fetch_row($answsql);
			$in_id=$pair[0];
			$out_id=$pair[1];
			$textsqlout='SELECT id,time_fact,date FROM flights WHERE id="'.$in_id.'" OR id="'.$out_id.'"';	
			$answsql2=mysqli_query($db_server,$textsqlout);	
			if(!$answsql2) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
			$flight_data_in= mysqli_fetch_row($answsql2);
				
				//SETTING UP in and outgoing Flight's Object
				//IN CASE OUTGOING GOT INTO THE DATABASE EARLIER WE HAVE TO CHECK WHICH ONE WE GOT FIRST
				if($flight_data_in[0]==$in_id)
				{
					$flight_in_time_fact=$flight_data_in[1];
					$flight_in_date=$flight_data_in[2];
				}
				else
				{
					$flight_out_time_fact=$flight_data_in[1];
					$flight_out_date=$flight_data_in[2];
				}
				
				$flight_data_out= mysqli_fetch_row($answsql2);
				
				if($flight_data_out[0]==$out_id)
				{
					$flight_out_time_fact=$flight_data_out[1];
					$flight_out_date=$flight_data_out[2];
				}
				else
				{
					$flight_in_time_fact=$flight_data_out[1];
					$flight_in_date=$flight_data_out[2];
				}
				echo "FLIGHT IN $flight_in_date | $flight_in_time_fact <br/>";
				echo "FLIGHT OUT $flight_out_date | $flight_out_time_fact <br/>";
				$in_Y=(int)substr($flight_in_date,0,4);
				$out_Y=(int)substr($flight_out_date,0,4);
				
				$in_Mo=(int)substr($flight_in_date,5,2);
				$out_Mo=(int)substr($flight_out_date,5,2);
				
				
				$in_D=(int)substr($flight_in_date,-2);
				$out_D=(int)substr($flight_out_date,-2);

					
				$in_H=(int)substr($flight_in_time_fact,0,2);
				$in_S=(int)substr($flight_in_time_fact,-2);
				$in_M=(int)substr($flight_in_time_fact,3,2);
				$out_H=(int)substr($flight_out_time_fact,0,2);
				$out_S=(int)substr($flight_out_time_fact,-2);
				$out_M=(int)substr($flight_out_time_fact,3,2);
				echo "$in_H, $in_M, $in_S, $in_Mo, $in_D, $in_Y <br/>";
				echo "$out_H, $out_M, $out_S, $out_Mo, $out_D, $out_Y <br/>";
				$time_stamp_in=date('U',mktime($in_H, $in_M, $in_S, $in_Mo, $in_D, $in_Y));
				$time_stamp_out=date('U',mktime($out_H, $out_M, $out_S, $out_Mo, $out_D, $out_Y));
				$time_stamp_diff=$time_stamp_out-$time_stamp_in;
				echo "<p>" . ( $time_stamp_diff/3600) . "</p>";
				// CHECK IF DEPARTURE IS AFTER ARRIVAL AND INVERSE IF NOT
				$inverse_flag=0;
				if ($out_Y<$in_Y)	$inverse_flag=1;
				elseif($out_Mo<$in_Mo)	$inverse_flag=1;
				elseif(($out_D<$in_D)&&($out_Mo==$in_Mo))	$inverse_flag=1;
				elseif(($out_H<$in_H)&&($out_D==$in_D))	$inverse_flag=1;
				
				
				if ($inverse_flag)
				{
					echo "INVERSED ARR/DPT <br/>";
					$t_Y=$in_Y;$in_Y=$out_Y;$out_Y=$t_Y;
					$t_Mo=$in_Mo;$in_Mo=$out_Mo;$out_Mo=$t_Mo;
					$t_D=$in_D;$in_D=$out_D;$out_D=$t_D;
					$t_H=$in_H;$in_H=$out_H;$out_H=$t_H;
					$t_M=$in_M;$in_M=$out_M;$out_M=$t_M;
					$t_S=$in_S;$in_S=$out_S;$out_S=$t_S;
				}	
				//Difference
				$diff_days=0;
				if($out_D>$in_D)
				{
					if($out_Mo>$in_Mo) $diff_days=30;
					else $diff_days=$out_D-$in_D;
					$in_H-=24;
					$in_M-=60;
					$in_S-=60;
					echo "ATTENTION: CHANGE OF DATE - + $diff_days <br/>";
					if($out_Y>$in_Y)
						echo "ATTENTION: CHANGE OF YEAR!  <br/>";
				}
				
				
				$delta_S=$out_S-$in_S;
				
				if($delta_S>=0) $diff_S=$delta_S;
				else 
				{	
					$out_M-=1;
					if($out_M<0)
					{
						$out_M+=60;
						$out_H-=1;
					}
					$diff_S=60+$delta_S;
				}
				$delta_M=$out_M-$in_M;
				if($delta_M>=0) $diff_M=$delta_M;
				else 
				{	
					$diff_M=60+$delta_M;
					$out_H-=1;
				}
				 $diff_H=$out_H-$in_H;
				
				
				// ALLOWANCE 3 HOURS 15 MINUTES
				$diff_M-=15;
				if($diff_M<0)
					{
						$diff_M+=60;
						$diff_H-=1;
					}
				$diff_H-=3;
				if($diff_M>60)
				{
					$diff_M-=60;
					$diff_H+=1;
				}
			
				if (($diff_H<=0)&&($diff_M<=0)&&($diff_days==0)) $diff_H=0;
				else
				{
					echo "PARKING TIME is:".$diff_H.":".$diff_M.":".$diff_S."<br/>";
					if($diff_M>30) $diff_H+=1;
					
					$diff_H+=($diff_days-1)*24;
				}
			
	mysqli_close($db_server);
	
return $diff_H;
}	
?>