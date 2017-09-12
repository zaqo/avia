<?php
// This script is used to update discount settings from the form
// by linking discount to a set of services
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 $in=$_REQUEST;
 echo "<pre>";
	//var_dump($in);
 echo "</pre>";
	
	if(isset($_REQUEST['to_export'])) $srv_array	= $_REQUEST['to_export'];
	if(isset($_REQUEST['isGroup'])) $isGroup= $_REQUEST['isGroup'];
	if(isset($_REQUEST['disc_id'])) $disc_id= $_REQUEST['disc_id'];
	$disc_table='';
	if($isGroup) $disc_table='discounts_grp_reg';
	else $disc_table='discounts_ind_reg';
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
// 1. Fix conditions into discount
	foreach($srv_array as $key)
	{
		$textsql='INSERT INTO '.$disc_table.'
						 (discount_id,service_id)
						VALUES('.$disc_id.','.$key.')';
		//echo $textsql.'<br/>';				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Insert INTO $disc_table table failed: ".mysqli_error($db_server));
	}
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>