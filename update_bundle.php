<?php
// This script is used to update service's settings from the form
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 
	
	if(isset($_REQUEST['id'])) $id		= $_REQUEST['id'];
	if(isset($_REQUEST['nav'])) $nav_id	= $_REQUEST['nav'];
	if(isset($_REQUEST['sap'])) $sap_id	= $_REQUEST['sap'];
	
	if(isset($_REQUEST['desc'])) $desc	= $_REQUEST['desc'];
	if(isset($_REQUEST['val'])) $svs	= $_REQUEST['val'];
	if(isset($_REQUEST['qt'])) $qty	= $_REQUEST['qt'];
	echo '<pre>';
	//var_dump($_REQUEST);
	//var_dump ($svs);
	echo '<pre>';
	
	$isKid	= 0;
	$isValid= 0;
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
		if (isset($id))		// ! DOEs NOT EXIST NOW!
			$textsql='UPDATE services SET id_NAV="'.$nav_id.'",id_SAP="'.$sap_id.'",description="'.$desc.'",isValid="'.$isValid.'" WHERE id='.$id;
		else
			$textsql='INSERT INTO services
						(id_mu,id_NAV,id_SAP,isValid,description,isBundle)
						VALUES( 1,"'.$nav_id.'","'.$sap_id.'",1,"'.$desc.'",1)';
		//echo $textsql;				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database UPDATE failed: ".mysqli_error($db_server));
		$svs_id=$db_server->insert_id;
		// RECORD SERVICES
		$cursor=0;
		foreach($svs as $value)
		{
			if($value)
			{
				$reg_bundle='INSERT INTO bundle_content
						(bundle_id,service_id,quantity)
						VALUES( "'.$svs_id.'","'.$value.'","'.$qty[$cursor].'")';
				$answsql=mysqli_query($db_server,$reg_bundle);
				if(!$answsql) die("UPDATE of bundle_reg table failed: ".mysqli_error($db_server));
			$cursor+=1;
			}	
		}
	echo '<script>history.go(-1);</script>';	
	
mysqli_close($db_server);
?>