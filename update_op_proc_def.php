<?php
/*
 Updater for the default billing process form - OPERATORS' FLIGHTS
*/
 include ("login_avia.php"); 

//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";

 $in=$_REQUEST;
 //echo "<pre>";
	//var_dump($in);
 //echo "</pre>";
	
$takeoff=array();

	if(isset($_REQUEST['val'])) $takeoff	= $_REQUEST['val'];

		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
// 1. TAKE OFF		
	
		//a. TERMINAL
		
		$textsql='SELECT id FROM process
						WHERE sequence=1 AND isValid AND terminal AND service_id='.$takeoff[0];
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		//  UPDATE
		if(!$answsql->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=1 AND terminal AND isValid';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,terminal,isValid)
						VALUES(1,"'.$takeoff[0].'",1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE #1 INSERTED ".$takeoff[0]."<br/>";
		}
		//else
			//echo "NO CHANGES: SERVICE #1 STAYS THE SAME <br/>";
	
	//b. TERMINAL
		
		$textsql='SELECT id FROM process
						WHERE sequence=1 AND isValid AND parking=6 AND service_id='.$takeoff[1];
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		//  UPDATE
		if(!$answsql->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=1 AND parking=6 AND isValid';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,parking,isValid)
						VALUES(1,"'.$takeoff[1].'","6",1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE #2 INSERTED ".$takeoff[1]."<br/>";
		}
		//else
			//echo "NO CHANGES: SERVICE #2 STAYS THE SAME <br/>";
	//2. AIRPORT CHARGES	
	//a. CHECK if IT IS DIFFERENT
		
		$sql_apchrg_in_adult='SELECT id FROM process
						WHERE sequence=2 AND direction=0 AND isAdult=1 AND isValid AND service_id='.$takeoff[2];
		$sql_apchrg_in_kid='SELECT id FROM process
						WHERE sequence=2 AND direction=0 AND isAdult=0 AND isValid AND service_id='.$takeoff[3];
		$sql_apchrg_out_adult='SELECT id FROM process
						WHERE sequence=2 AND direction=1 AND isAdult=1 AND isValid AND service_id='.$takeoff[4];
		$sql_apchrg_out_kid='SELECT id FROM process
						WHERE sequence=2 AND direction=1 AND isAdult=0 AND isValid AND service_id='.$takeoff[5];
		$answsql_0=mysqli_query($db_server,$sql_apchrg_in_adult);
		if(!$answsql_0) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		$answsql_1=mysqli_query($db_server,$sql_apchrg_in_kid);
		if(!$answsql_1) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		$answsql_2=mysqli_query($db_server,$sql_apchrg_out_adult);
		if(!$answsql_2) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		$answsql_3=mysqli_query($db_server,$sql_apchrg_out_kid);
		if(!$answsql_3) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		//b. UPDATE
		// 1. IN ADULT
		if(!$answsql_0->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=2 AND isValid AND NOT direction AND isAdult';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,direction,isAdult,isValid)
						VALUES(2,"'.$takeoff[2].'",0,1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE #3 INSERTED ".$takeoff[2]."<br/>";
		}
		//else
			//echo "NO CHANGES: SERVICE #3 STAYS THE SAME <br/>";
		// 2. IN KIDS
		if(!$answsql_1->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=2 AND isValid AND NOT direction AND NOT isAdult';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM default_svs table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,direction,isAdult,isValid)
						VALUES(2,"'.$takeoff[3].'",0,0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE #4 INSERTED ".$takeoff[3]."<br/>";
		}
		//else
			//echo "NO CHANGES: SERVICE #4 STAYS THE SAME <br/>";
	// 3. OUT ADULT
		if(!$answsql_2->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=2 AND isValid AND direction AND isAdult';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,direction,isAdult,isValid)
						VALUES(2,"'.$takeoff[4].'",1,1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE #5 INSERTED ".$takeoff[4]."<br/>";
		}
		//else
			//echo "NO CHANGES: SERVICE #5 STAYS THE SAME <br/>";
	// 4. OUT KIDS
		if(!$answsql_3->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=2 AND isValid AND direction AND NOT isAdult';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,direction,isAdult,isValid)
						VALUES(2,"'.$takeoff[5].'",1,0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE #6 INSERTED ".$takeoff[5]."<br/>";
		}
		//else
			//echo "NO CHANGES: SERVICE #6 STAYS THE SAME <br/>";
	
	// 3. AVIATION SEC
		
	//a. CHECK if IT IS DIFFERENT
		
		$sql_sec_rus_pass='SELECT id FROM process
						WHERE sequence=3 AND isRus=1 AND NOT isCargo AND havePAX AND isValid AND service_id='.$takeoff[6];
		$sql_sec_rus_cargo='SELECT id FROM process
						WHERE sequence=3 AND isRus=1 AND isCargo AND NOT havePAX AND isValid AND service_id='.$takeoff[7];
		$sql_sec_for_no='SELECT id FROM process
						WHERE sequence=3 AND NOT isRus AND NOT isCargo AND NOT havePAX AND isValid AND service_id='.$takeoff[8];
		$sql_sec_for_cargo='SELECT id FROM process
						WHERE sequence=3 AND NOT isRus AND isCargo AND NOT havePAX AND isValid AND service_id='.$takeoff[9];
		$sql_sec_for_pass='SELECT id FROM process
						WHERE sequence=3 AND NOT isRus AND NOT isCargo AND havePAX AND isValid AND service_id='.$takeoff[10];
						
		$answsql_10=mysqli_query($db_server,$sql_sec_rus_pass);
			if(!$answsql_10) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		$answsql_11=mysqli_query($db_server,$sql_sec_rus_cargo);
			if(!$answsql_11) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		$answsql_12=mysqli_query($db_server,$sql_sec_for_no);
		if(!$answsql_12) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		$answsql_13=mysqli_query($db_server,$sql_sec_for_cargo);
			if(!$answsql_13) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		$answsql_14=mysqli_query($db_server,$sql_sec_for_pass);
			if(!$answsql_14) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		//b. UPDATE
		// 1. AVIA SECURITY RUSSIAN
		if(!$answsql_10->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=3 AND isRus=1 AND NOT isCargo AND havePAX AND isValid';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,isRus,isCargo,havePAX,isValid)
						VALUES(3,"'.$takeoff[6].'",1,0,1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			  //echo "NEW RECORD: SERVICE #7 INSERTED ".$takeoff[6]."<br/>";
		}
		//else
			//	echo "NO CHANGES: SERVICE #7 STAYS THE SAME <br/>";
		// 2. AVIATION SECURITY FOREIGN
		if(!$answsql_11->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=3 AND isRus AND isCargo AND NOT havePAX AND isValid';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,isRus,isCargo,havePAX,isValid)
						VALUES(3,"'.$takeoff[7].'",1,1,0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			  //echo "NEW RECORD: SERVICE #8 INSERTED ".$takeoff[7]."<br/>";
		}
		//else
			//	echo "NO CHANGES: SERVICE #8 STAYS THE SAME <br/>";
	// FOREIGN NO 
	if(!$answsql_12->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=3 AND NOT isRus AND NOT isCargo AND NOT havePAX AND isValid';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,isRus,isCargo,havePAX,isValid)
						VALUES(3,"'.$takeoff[8].'",0,0,0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			  //echo "NEW RECORD: SERVICE #9 INSERTED ".$takeoff[8]."<br/>";
		}
		//else
			//	echo "NO CHANGES: SERVICE #9 STAYS THE SAME <br/>";
	// FOREIGN CARGO
	if(!$answsql_13->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=3 AND NOT isRus AND isCargo AND NOT havePAX AND isValid';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,isRus,isCargo,havePAX,isValid)
						VALUES(3,"'.$takeoff[9].'",0,1,0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			  //echo "NEW RECORD: SERVICE #10 INSERTED ".$takeoff[9]."<br/>";
		}
		//else
			//	echo "NO CHANGES: SERVICE #10 STAYS THE SAME <br/>";
	if(!$answsql_14->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=3 AND NOT isRus AND NOT isCargo AND havePAX AND isValid';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,isRus,isCargo,havePAX,isValid)
						VALUES(3,"'.$takeoff[10].'",0,0,1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			  //echo "NEW RECORD: SERVICE #11 INSERTED ".$takeoff[10]."<br/>";
		}
		//else
			//	echo "NO CHANGES: SERVICE #11 STAYS THE SAME <br/>";
	// END OF AVIATION SECURITY
	// 4. GROUND HANDLING
		
	//a. CHECK if IT IS DIFFERENT
		
		$sql_gh_adult='SELECT id FROM process
						WHERE sequence=4 AND isAdult AND isValid AND service_id='.$takeoff[11];
		$sql_gh_kid='SELECT id FROM process
						WHERE sequence=4 AND NOT isAdult AND isValid AND service_id='.$takeoff[12];
		
		$answsql_20=mysqli_query($db_server,$sql_gh_adult);
		if(!$answsql_20) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		$answsql_21=mysqli_query($db_server,$sql_gh_kid);
		if(!$answsql_21) die("SELECT FROM process table failed: ".mysqli_error($db_server));
		
		
		//b. UPDATE
		// 1. GH ADULT
		if(!$answsql_20->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=4 AND isValid AND isAdult';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,isAdult,isValid)
						VALUES(4,"'.$takeoff[11].'",1,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE #12 INSERTED ".$takeoff[11]."<br/>";
		}
		//else
			//echo "NO CHANGES: SERVICE #12 STAYS THE SAME <br/>";
		// 2. GH KIDS
		if(!$answsql_21->num_rows) 
		{
			$check_sql='SELECT id FROM process
						WHERE sequence=4 AND isValid AND NOT isAdult';
			$answsql1=mysqli_query($db_server,$check_sql);
			if(!$answsql1) die("SELECT FROM process table failed: ".mysqli_error($db_server));	
			if($answsql1->num_rows)
			{
				while($row=mysqli_fetch_row($answsql1))
				{
					$update_sql='UPDATE process
								SET isValid=0 WHERE id='.$row[0];
					$answsql2=mysqli_query($db_server,$update_sql);
					if(!$answsql2) die("UPDATE OF process table failed: ".mysqli_error($db_server));
				}
			}
			$insert_sql='INSERT INTO process
						(sequence,service_id,isAdult,isValid)
						VALUES(4,"'.$takeoff[12].'",0,1)';
			$answsql3=mysqli_query($db_server,$insert_sql);
			if(!$answsql3) die("INSERT INTO process table failed: ".mysqli_error($db_server));
			//echo "NEW RECORD: SERVICE #13 INSERTED ".$takeoff[12]."<br/>";
		}
		//else
			//echo "NO CHANGES: SERVICE #13 STAYS THE SAME <br/>";
	
	// END OF GROUND HANDLING
	//echo $textsql.'<br/>';				
		
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);

?>