<?php
/* 
	Execute linking given discount with a condition
by S.Pavlov (c) 2017
*/
	include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 $in=$_REQUEST;
 echo "<pre>";
 //var_dump($in);
 echo "</pre>";
	
	if(isset($_REQUEST['to_export'])) $cond_array	= $_REQUEST['to_export'];
	if(isset($_REQUEST['isGroup'])) $isGroup= $_REQUEST['isGroup'];
	if(isset($_REQUEST['disc_id'])) $disc_id= $_REQUEST['disc_id'];
	$disc_table='';
	if($isGroup) $disc_table='discount_grp_content';
	else $disc_table='discount_ind_content';
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
	//1.First clean old	
	$textsql_clear='UPDATE '.$disc_table.' SET isValid=0 WHERE discount_id='.$disc_id; 
				$answsql=mysqli_query($db_server,$textsql_clear);
				if(!$answsql) die("UPDATE '.$disc_table.' table failed: ".mysqli_error($db_server));
		
	// 2. Fix conditions into discount
	foreach($cond_array as $key)
	{
		$textsql='INSERT INTO '.$disc_table.'
						 (discount_id,condition_id,composition,sequence)
						VALUES('.$disc_id.','.$key.',0,1)';
		//echo $textsql.'<br/>';				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Insert INTO discounts_***_content table failed: ".mysqli_error($db_server));
	}
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>