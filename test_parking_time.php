<?php 
echo time_over(202);

function time_over( $pair_id)
{
require_once 'login_avia.php';

include ("header.php"); 
	
		
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
			$textsqlout='SELECT id,time_fact FROM flights WHERE id="'.$in_id.'" OR id="'.$out_id.'"';	
			$answsql2=mysqli_query($db_server,$textsqlout);	
			if(!$answsql2) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
			$flight_data_in= mysqli_fetch_row($answsql2);
				
				//SETTING UP outgoing Flight's Object
				
				if($flight_data_in[0]==$in_id)
					$flight_in_time_fact=$flight_data_in[1];
	
				else
					$flight_out_time_fact=$flight_data_in[1];
				$flight_data_out= mysqli_fetch_row($answsql2);
				
				if($flight_data_out[0]==$out_id)
					$flight_out_time_fact=$flight_data_out[1];
		
				else
					$flight_in_time_fact=$flight_data_out[1];
				
				$in_H=(int)substr($flight_in_time_fact,0,2);
				$in_S=(int)substr($flight_in_time_fact,-2);
				$in_M=(int)substr($flight_in_time_fact,3,2);
				
				
				//SETTING UP outgoing Flight's Object
				
				//$flight_out_time_fact=$flight_data_out[0];
				$out_H=(int)substr($flight_out_time_fact,0,2);
				$out_S=(int)substr($flight_out_time_fact,-2);
				$out_M=(int)substr($flight_out_time_fact,3,2);
				echo " FLIGHT IN: $flight_in_time_fact  <br/> FLIGHT OUT: $flight_out_time_fact <br/>";
				echo " HOURS: $in_H MIN: $in_M SEC: $in_S <br/>";
				echo " HOURS: $out_H MIN: $out_M SEC: $out_S <br/>";
				/*
				var_dump($flight_in_time_fact);
				var_dump($flight_out_time_fact);
				echo "Difference is:".($flight_out_time_fact-$difference)."<br/>" ;
				var_dump($difference);*/
				//Difference
				$delta_S=$out_S-$in_S;
				
				
				
				// ALLOWANCE 3 HOURS 15 MINUTES
				//$out_H-=3;
				//$out_M-=15;
				
				if($delta_S>=0) $diff_S=$delta_S;
				else 
				{	
					$out_M-=1;
					$diff_S=60+$delta_S;
				}
				$delta_M=$out_M-$in_M;
				if($delta_M>=0) $diff_M=$delta_M;
				else 
				{	
					$out_H-=1;
					$diff_M=60+$delta_M;
				}
				$delta_H=$out_H-$in_H;
				if($delta_H>=0) $diff_H=$delta_H;
				else 
				{	
					
					$diff_H=24+$delta_H;
				}
				
				
				$diff_H-=3;
				$diff_M-=15;
			
				if (($diff_H<=0)&&($diff_M<=0)) return 0;
				else
				{
					echo "PARKING TIME is:".$diff_H.":".$diff_M.":".$diff_S."<br/>";
					if($diff_M>30) $diff_H+=1;
					
					return $diff_H;
				}
			
	mysqli_close($db_server);
	
return $diff_H;
}	
?>
	