<?php
/*
		This script DELETES EXCEPTION IN FLIGHT BILLING PROCESS 
*/
include ("login_avia.php"); 

 
	if(isset($_REQUEST['id'])) $id	= $_REQUEST['id'];
	
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
// FIX  package		
		$textsql='UPDATE exc_process SET
						isValid=0
						WHERE id='.$id;
						
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("UPDATE of exc_process table failed: ".mysqli_error($db_server));

	
	echo '<script>history.go(-1);</script>';	
	
mysqli_close($db_server);
?>