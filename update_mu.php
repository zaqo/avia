<?php
// This is a script to update agent's personal data from the form
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 
	
	if(isset($_REQUEST['id'])) $id		= $_REQUEST['id'];
	if(isset($_REQUEST['name_rus'])) $name_rus	= $_REQUEST['name_rus'];
	if(isset($_REQUEST['name_en'])) $name_en	= $_REQUEST['name_en'];
	
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
		if (isset($id))		
			$textsql='UPDATE units SET description_rus="'.$name_rus.'",description_en="'.$name_en.'" WHERE id="'.$id.'"';
		else
			$textsql='INSERT INTO units
						(description_rus,description_en)
						VALUES( "'.$name_rus.'","'.$name_en.'")';
		echo $textsql;				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Units table UPDATE failed: ".mysqli_error($db_server));
					
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>