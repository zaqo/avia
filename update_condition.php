<?php
// This script is used to update package (template of services) settings from the form
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 $in=$_REQUEST;
 echo "<pre>";
	var_dump($in);
 echo "</pre>";
	
	if(isset($_REQUEST['cond_name'])) $name	= $_REQUEST['cond_name'];
	else return 0;
	if(isset($_REQUEST['cond'])) $cond_id= $_REQUEST['cond'];
	else return 0;
	if(isset($_REQUEST['from'])) $from	= $_REQUEST['from'];
	
	if(isset($_REQUEST['to'])) $to= $_REQUEST['to'];
		
	if(isset($_REQUEST['enum'])) $enum	= $_REQUEST['enum'];
		
	if(isset($_REQUEST['param'])) $param_id	= $_REQUEST['param'];
		else return 0;
	
	
	switch($cond_id)
	{
		case 6:
			if ((!isset($_REQUEST['to']))&&(!isset($_REQUEST['enum']))) return 0;
			break;
	
		case 7:
			if ((!isset($_REQUEST['to']))&&(!isset($_REQUEST['enum']))) return 0;
			break;
		
		default:
			break;
	}
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
// 1.create package		
		$textsql='INSERT INTO discount_conditions
						(name_rus,param_id,from_val,to_val,enum_of_values,condition_id,isValid)
						VALUES ("'.$name.'",'.$param_id.',"'.$from.'","'.$to.'","'.$enum.'",'.$cond_id.',1)';

		echo $textsql;
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Insert INTO discount_conditions table failed: ".mysqli_error($db_server));

		
	
	//echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>