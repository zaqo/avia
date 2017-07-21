<?php

// This script is for transferring data from the array to the mySQL table

include ("login_avia.php"); 
 	
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
				$textsql='';
				foreach($services_map as $key=>$value)
				{
					
					$transfer_mysql='INSERT INTO services (id_NAV,id_SAP) 
								VALUES("'.$key.'","'.$value.'")';
					echo $transfer_mysql."<br/>";
					$answsql=mysqli_query($db_server,$transfer_mysql);
					if(!$answsql) die("INSERT into services TABLE failed: ".mysqli_error($db_server));
					
					
				}
				//echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>