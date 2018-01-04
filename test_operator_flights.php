<?php
include 'login_avia.php';
include '/webservice/sapconnector.php';

	class Flight
	{
			public $id;						
			public $id_NAV;
			public $flight_date;
			public $time_fact;
			public $flight_num;
			public $flight_type;
			public $flight_cat;
			public $direction;
			public $plane_id;
			public $plane_type;
			public $plane_class;
			public $plane_mow;
			public $airport;
			public $airport_class;
			public $passengers_adults;
			public $passengers_kids;
			public $customer;
			public $bill_to;
			public $plane_owner;
			public $services;
			public $parked_at;
			public $terminal;
			public $is_operator;
	}	
		$content="";
		$flight_out= new Flight();
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		/*
			$textsql='SELECT in_id,out_id FROM flight_pairs WHERE id="'.$pair_id.'"';
			
			$answsql=mysqli_query($db_server,$textsql);	
			if(!$answsql) die("Database SELECT TO flight_pairs table failed: ".mysqli_error($db_server));	
			
			$pair= mysqli_fetch_row($answsql);
			$in_id=$pair[0];
			$out_id=$pair[1];
		*/
				$in_id=1;
			$textsqlout="SELECT flights.id_NAV,flights.date,flight,direction,plane_num,flight_type,plane_type,plane_mow,
						passengers_adults,passengers_kids,customer_id,bill_to_id,owner,category,time_fact,airport,
						isHelicopter,terminal,parkedAt,isOperator
							FROM flights WHERE id=$in_id";	
			$answsql2=mysqli_query($db_server,$textsqlout);	
			if(!$answsql2) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			$flight_data_out= mysqli_fetch_row($answsql2);
				
				//SETTING UP outgoing Flight's Object
				$flight_out->id=$in_id;
				$flight_out->id_NAV=$flight_data_out[0];
				$flight_out->flight_date=$flight_data_out[1];
				$flight_out->flight_num=$flight_data_out[2];
				$flight_out->direction=$flight_data_out[3];
				$flight_out->plane_id=$flight_data_out[4];
				$flight_out->flight_type=$flight_data_out[5];
				$flight_out->plane_type=$flight_data_out[6];
				$flight_out->plane_mow=$flight_data_out[7];
				$flight_out->passengers_adults=$flight_data_out[8];
				$flight_out->passengers_kids=$flight_data_out[9];
				$flight_out->customer=$flight_data_out[10];
				$flight_out->bill_to=$flight_data_out[11];
				$flight_out->plane_owner=$flight_data_out[12];
				$flight_out->flight_cat=$flight_data_out[13];
				$flight_out->time_fact=$flight_data_out[14];
				$airport_out=$flight_data_out[15];
				$heli_flag=$flight_data_out[16];
				$flight_out->terminal=$flight_data_out[17];
				$flight_out->parked_at=$flight_data_out[18];
				$flight_out->is_operator=$flight_data_out[19];
				//echo "MOW: ".$flight_data_out[7]."HELI: $heli_flag || IS OPERATOR FLAG:".$flight_out->is_operator."<br/>";
				// SZV EXCLUDE SECTION
				if ($flight_out->passengers_adults||$flight_out->passengers_kids) $SZV_out_flag=1;
				else $SZV_out_flag=0;
			// Locate Airport IATA code
			$aportsql='SELECT code,domain FROM airports WHERE id="'.$airport_out.'"';	
			$answsql=mysqli_query($db_server,$aportsql);
			if(!$answsql) die("Database SELECT in airports table failed: ".mysqli_error($db_server));	
	
			$aport_out= mysqli_fetch_row($answsql);
			if(isset($aport_out[0])) 
			{	
				if ($aport_out[1]) $airport_location=1;//ABROAD, FIX CIS ISSUE
				else $airport_location=0; //HOME
				$flight_out->airport=$aport_out[0];
				$destination_zone=$airport_location;  // <-- TAKEN BY THE DEPARTURE AIRPORT
				$flight_out->airport_class=$airport_location;
			}
			else 
				echo "ERROR: Airport CODE COULD NOT BE LOCATED!!! <br/>";
		
			// LOCATE CLASS OF AIRCRAFT
			// KEEP IN MIND aircrats TABLE NEEDS to be UPDATED regularly
			$aircraftsql='SELECT air_class,made_in_rus FROM aircrafts WHERE reg_num="'.$flight_out->plane_id.'"';	
			$answsql_air=mysqli_query($db_server,$aircraftsql);
				
			if(!$answsql_air) die("Database SELECT in aircrafts table failed: ".mysqli_error($db_server));	
			$made_in_rus=0;
			$aircraft= mysqli_fetch_row($answsql_air);
			if(isset($aircraft[0])) 
			{
				$flight_out->plane_class=$aircraft[0];
				if(isset($aircraft[1])) $made_in_rus=$aircraft[1];
			}
			else 
				echo "ERROR: Aircraft record COULD NOT BE LOCATED!!! <br/>";
			$client_geo=1;
			$id= OperatorFlight($flight_out,$client_geo,$made_in_rus);
					
	mysqli_close($db_server);

?>