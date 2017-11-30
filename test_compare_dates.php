<?php


$res=time_over_parking_new(200);
echo "PARKING TIME IN HOURS: $res<br/>";
$res-=3.25;
//$res=ceil($res);
echo "PARKING TIME ROUNDED UP: $res<br/>";

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
				$res=( $time_stamp_diff/3600);//IN HOURS
				//echo "<p>" . ( $time_stamp_diff/3600) . "</p>";
					
	mysqli_close($db_server);
	
return $res;
}	
?>