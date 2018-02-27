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
	if(isset($_REQUEST['mu'])) $mu		= $_REQUEST['mu'];
	if(isset($_REQUEST['desc'])) $desc	= $_REQUEST['desc'];

	//var_dump($_REQUEST);
	
	$isKid	= 0;
	$isValid= 0;
	if (isset($_REQUEST['Servicedata']))
	{
		$s_dat	= $_REQUEST['Servicedata'];
		foreach ($s_dat as $value)
		{
			if($value=='kid') $isKid		= 1;
			if($value=='valid') $isValid	= 1;
		}
	}
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
		if (isset($id))		
			$textsql='UPDATE services SET id_mu="'.$mu.'",id_NAV="'.$nav_id.'",id_SAP="'.$sap_id.'",isforKids="'.$isKid.'",description="'.$desc.'",isValid="'.$isValid.'" WHERE id="'.$id.'"';
		else
			$textsql='INSERT INTO services
						(id_mu,id_NAV,id_SAP,isforKids,isValid,description)
						VALUES( '.$mu.',"'.$nav_id.'",'.$sap_id.','.$isKid.',1,"'.$desc.'")';
		//echo $textsql;				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database UPDATE failed: ".mysqli_error($db_server));
					
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>