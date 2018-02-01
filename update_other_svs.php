<?php
/* 

		UPDATE OF OTHER_SVS TABLE: ADD RECORD
		CALLED BY add_osvs.php
*/
		include ("login_avia.php"); 

	
	if(isset($_REQUEST['cl'])) $cl		= $_REQUEST['cl'];
	
	if(isset($_REQUEST['svs'])) $svs	= $_REQUEST['svs'];
	echo '<pre>';
	//var_dump($_REQUEST);
	//var_dump ($svs);
	echo '<pre>';
	
	
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		$find_sql='SELECT id FROM other_svs WHERE client_id="'.$cl.'" AND service_id="'.$svs.'"';	
		$answsql=mysqli_query($db_server,$find_sql);
		if(!$answsql) die("Database SELECT to other_svs table failed: ".mysqli_error($db_server));
		
		if (!$answsql->num_rows)		// ! DOEs NOT EXIST NOW!
			
			$textsql='INSERT INTO other_svs
						(client_id,service_id,isValid)
						VALUES("'.$cl.'","'.$svs.'",1)';
		//echo $textsql;				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database UPDATE failed: ".mysqli_error($db_server));
		
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>