<?php
// This script is used excluseviley to load data in the sytem tables
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
include ("customers.php");//array with map of Navision and SAP ERP sustomer records 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 

 
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
		//$textsql='UPDATE services SET id_mu="'.$mu.'",id_NAV="'.$nav_id.'",id_SAP="'.$sap_id.'",isforKids="'.$isKid.'",isValid="'.$isValid.'" WHERE id="'.$id.'"';
		$i=0;
		foreach ($customers as $key=>$value)
		{
			$textsql='INSERT INTO clients
						(id_NAV,id_SAP)
						VALUES( "'.$key.'","'.$value.'")';
			echo $textsql;				
			$answsql=mysqli_query($db_server,$textsql);
			if(!$answsql) die("Database UPDATE failed: ".mysqli_error($db_server));
			$i+=1;
		}			
	echo "DONE! $i RECORDS INSERTED<\br>";//'<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>