<?php
// This script is used to create discount settings from the form
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 $in=$_REQUEST;
 echo "<pre>";
 //var_dump($in);
 echo "</pre>";
	
	if(isset($_REQUEST['name'])) $name	= $_REQUEST['name'];
	if(isset($_REQUEST['client'])) $client_id= $_REQUEST['client'];
	if(isset($_REQUEST['val'])) $disc_val= $_REQUEST['val'];
	if(isset($_REQUEST['from'])) $date_fr= $_REQUEST['from'];
	if(isset($_REQUEST['to'])) $date_to= $_REQUEST['to'];
	if(isset($_REQUEST['priority'])) $prio	= $_REQUEST['priority'];
	if(isset($_REQUEST['isGroup'])) $isGroup	= $_REQUEST['isGroup'];
	else $isGroup=0;
	if(isset($_REQUEST['group_id'])) $group	= $_REQUEST['group_id'];
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		$date_fr=substr($date_fr,-4).substr($date_fr,3,2).substr($date_fr,0,2);
		$date_to=substr($date_to,-4).substr($date_to,3,2).substr($date_to,0,2);
// 1. Create discount		
		if($isGroup)
			$textsql='INSERT INTO discounts_group
						(name,group_id,discount_val,valid_from,valid_to,priority,isValid)
						VALUES("'.$name.'","'.$group.'","'.$disc_val.'","'.$date_fr.'",
						"'.$date_to.'","'.$prio.'",1)';
		else
			$textsql='INSERT INTO discounts_individual
						(name,client_id,discount_val,valid_from,valid_to,priority,isValid)
						VALUES("'.$name.'",'.$client_id.',"'.$disc_val.'","'.$date_fr.'",
						"'.$date_to.'","'.$prio.'",1)';
		//echo $textsql.'<br/>';				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Insert INTO discounts_individual table failed: ".mysqli_error($db_server));

	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>