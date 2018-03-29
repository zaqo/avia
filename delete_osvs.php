<?php
/*
		This script DELETES EXCEPTION IN FLIGHT BILLING PROCESS 
		by S.Pavlov (c) 2018
*/
include ("login_avia.php"); 

 
	if(isset($_REQUEST['id'])) $id	= $_REQUEST['id'];
	
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
// FIX  package		
		$textsql='UPDATE other_svs SET
						isValid=0
						WHERE id='.$id;
						
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("UPDATE of other_svs table failed: ".mysqli_error($db_server));

	
	echo '<script>history.go(-1);</script>';	
	
mysqli_close($db_server);
?>