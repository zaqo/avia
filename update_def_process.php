<?php
/*
 Updater for the default billing process form
*/
 include ("login_avia.php"); 

//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
/*
 $in=$_REQUEST;
 echo "<pre>";
 var_dump($in);
 echo "</pre>";
*/	
$takeoff=array();
$ap_chrg=array();
$av_sec=array();
$gh=array();
	if(isset($_REQUEST['val'])) $takeoff	= $_REQUEST['val'];
	if(isset($_REQUEST['val_two'])) $ap_chrg= $_REQUEST['val_two'];
	if(isset($_REQUEST['val_three'])) $av_sec= $_REQUEST['val_three'];
	if(isset($_REQUEST['val_four'])) $gh= $_REQUEST['val_four'];
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
// 1. TAKE OFF		
	
		//a. CHECK if IT IS DIFFERENT
		
		$textsql='SELECT id FROM default_svs
						WHERE sequence=1 AND isValid AND service_id='.$takeoff[0];
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		//b. UPDATE
		if(!$answsql->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=1 AND isValid';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,isValid)
						VALUES(1,"'.$takeoff[0].'",1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
	
	//2. AIRPORT CHARGES	
	//a. CHECK if IT IS DIFFERENT
		
		$sql_apchrg_in_adult='SELECT id FROM default_svs
						WHERE sequence=2 AND direction=0 AND isAdult=1 AND isValid AND service_id='.$ap_chrg[0];
		$sql_apchrg_in_kid='SELECT id FROM default_svs
						WHERE sequence=2 AND direction=0 AND isAdult=0 AND isValid AND service_id='.$ap_chrg[1];
		$sql_apchrg_out_adult='SELECT id FROM default_svs
						WHERE sequence=2 AND direction=1 AND isAdult=1 AND isValid AND service_id='.$ap_chrg[2];
		$sql_apchrg_out_kid='SELECT id FROM default_svs
						WHERE sequence=2 AND direction=1 AND isAdult=0 AND isValid AND service_id='.$ap_chrg[3];
		$answsql_0=mysqli_query($db_server,$sql_apchrg_in_adult);
		if(!$answsql_0) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		$answsql_1=mysqli_query($db_server,$sql_apchrg_in_kid);
		if(!$answsql_1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		$answsql_2=mysqli_query($db_server,$sql_apchrg_out_adult);
		if(!$answsql_2) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		$answsql_3=mysqli_query($db_server,$sql_apchrg_out_kid);
		if(!$answsql_3) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		//b. UPDATE
		// 1. IN ADULT
		if(!$answsql_0->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=2 AND isValid AND direction=0 AND isAdult=1';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,direction,isAdult,isValid)
						VALUES(2,"'.$ap_chrg[0].'",0,1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
		// 2. IN KIDS
		if(!$answsql_1->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=2 AND isValid AND direction=0 AND isAdult=0';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,direction,isAdult,isValid)
						VALUES(2,"'.$ap_chrg[1].'",0,0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
	// 3. OUT ADULT
		if(!$answsql_2->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=2 AND isValid AND direction=1 AND isAdult=1';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,direction,isAdult,isValid)
						VALUES(2,"'.$ap_chrg[2].'",1,1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
	// 4. OUT KIDS
		if(!$answsql_0->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=2 AND isValid AND direction=1 AND isAdult=0';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,direction,isAdult,isValid)
						VALUES(2,"'.$ap_chrg[3].'",1,0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
	
	// 3. AVIATION SEC
		
	//a. CHECK if IT IS DIFFERENT
		
		$sql_aviasec_rus='SELECT id FROM default_svs
						WHERE sequence=3 AND isRus=1 AND isValid AND service_id='.$av_sec[0];
		$sql_aviasec_for='SELECT id FROM default_svs
						WHERE sequence=3 AND isRus=0 AND isValid AND service_id='.$av_sec[1];
		
		$answsql_10=mysqli_query($db_server,$sql_aviasec_rus);
		if(!$answsql_10) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		$answsql_11=mysqli_query($db_server,$sql_aviasec_for);
		if(!$answsql_11) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		
		//b. UPDATE
		// 1. AVIA SECURITY RUSSIAN
		if(!$answsql_10->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=3 AND isValid AND isRus=1';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,isRus,isValid)
						VALUES(3,"'.$av_sec[0].'",1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
		// 2. AVIATION SECURITY FOREIGN
		if(!$answsql_11->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=3 AND isValid AND isRus=0';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,isRus,isValid)
						VALUES(3,"'.$av_sec[1].'",0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
	
	// END OF AVIATION SECURITY
	// 4. GROUND HANDLING
		
	//a. CHECK if IT IS DIFFERENT
		
		$sql_gh_adult='SELECT id FROM default_svs
						WHERE sequence=4 AND isAdult=1 AND isValid AND service_id='.$gh[0];
		$sql_gh_kid='SELECT id FROM default_svs
						WHERE sequence=4 AND isAdult=0 AND isValid AND service_id='.$gh[1];
		
		$answsql_20=mysqli_query($db_server,$sql_gh_adult);
		if(!$answsql_20) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		$answsql_21=mysqli_query($db_server,$sql_gh_kid);
		if(!$answsql_21) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));
		
		
		//b. UPDATE
		// 1. GH ADULT
		if(!$answsql_20->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=4 AND isValid AND isAdult=1';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,isAdult,isValid)
						VALUES(4,"'.$gh[0].'",1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
		// 2. GH KIDS
		if(!$answsql_21->num_rows) 
		{
			$check_sql='SELECT id FROM default_svs
						WHERE sequence=4 AND isValid AND isAdult=0';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE default_svs
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF default_svs table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO default_svs
						(sequence,service_id,isAdult,isValid)
						VALUES(4,"'.$gh[1].'",0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO default_svs table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE INSERTED <br/>";
		}
		//else
		//echo "NO CHANGES: SERVICE STAYS THE SAME <br/>";
	
	// END OF GROUND HANDLING
	//echo $textsql.'<br/>';				
		
	//echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);

?>