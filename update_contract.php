<?php
// This is a script to update agent's personal data from the form
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 
	
	if(isset($_REQUEST['id'])) $id		= $_REQUEST['id'];
	
	if(isset($_REQUEST['sap'])) $sap_id	= $_REQUEST['sap'];
	
	$isValid= 0;
	$isBase	= 0;
	if (isset($_REQUEST['Servicedata']))
	{
		$s_dat	= $_REQUEST['Servicedata'];
		foreach ($s_dat as $value)
		{
			if($value=='valid') $isValid	= 1;
			if($value=='base') $isBase	= 1;
		}
	}
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
		
			$textsql='INSERT INTO contracts
						(client_id,id_SAP,isValid,isBased)
						VALUES("'.$id.'","'.$sap_id.'","'.$isValid.'","'.$isBase.'")';
		//echo $textsql;				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database UPDATE failed: ".mysqli_error($db_server));
					
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>