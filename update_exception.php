<?php
/* 
		
		CREATES NEW CLIENT RECORD FOR EXCEPTION SERVICES
		CALLED FROM: add_exception.php
by S.Pavlov (c) 2018
*/		
	include ("login_avia.php"); 

	if(isset($_REQUEST['id'])) $id		= $_REQUEST['id'];
	if(isset($_REQUEST['cl'])) $cl		= $_REQUEST['cl'];
	if(isset($_REQUEST['svs1'])) $svs1	= $_REQUEST['svs1'];
	if(isset($_REQUEST['svs2'])) $svs2	= $_REQUEST['svs2'];
	if(isset($_REQUEST['svs3'])) $svs3	= $_REQUEST['svs3'];
	
	$isValid= 0;
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		$check_sql='SELECT id FROM exc_process WHERE client_id="'.$cl.'"';
		$answsql=mysqli_query($db_server,$check_sql);
				if(!$answsql) die("Database: UPDATE exc_process TABLE failed: ".mysqli_error($db_server));		
		if (!$answsql->num_rows)		
		{
			if($svs1)
			{
				$takeoff_sql='INSERT INTO exc_process
							(client_id,service_id,sequence,isValid)
							VALUES( "'.$cl.'",'.$svs1.',1,1)';
				$answsql=mysqli_query($db_server,$takeoff_sql);
				if(!$answsql) die("Database: UPDATE exc_process TABLE failed: ".mysqli_error($db_server));
			}
			if($svs2)
			{
				$gh_adults_sql='INSERT INTO exc_process
						(client_id,service_id,sequence,isValid)
						VALUES( "'.$cl.'",'.$svs2.',4,1)';
				$answsql=mysqli_query($db_server,$gh_adults_sql);
				if(!$answsql) die("Database: UPDATE exc_process TABLE failed: ".mysqli_error($db_server));
				$last=$db_server->insert_id;

			}
			if($svs3&&$last)
			{
				$gh_kids_sql='INSERT INTO exc_default
						(exc_id,svs_kids_id)
						VALUES( "'.$last.'",'.$svs3.')';
				$answsql=mysqli_query($db_server,$gh_kids_sql);
				if(!$answsql) die("Database: UPDATE exc_default TABLE failed: ".mysqli_error($db_server));
			}
		}
						
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>