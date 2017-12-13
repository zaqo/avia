<?php
// This is a script to update agent's personal data from the form
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 
	
	if(isset($_REQUEST['id'])) 			$id		= $_REQUEST['id'];
	if(isset($_REQUEST['nav_id'])) 		$nav_id	= $_REQUEST['nav_id'];
	if(isset($_REQUEST['name'])) 		$name	= $_REQUEST['name'];
	if(isset($_REQUEST['cl_id_SAP'])) 	$cl_id_SAP	= $_REQUEST['cl_id_SAP'];
	if(isset($_REQUEST['contract_id'])) $contract_id= $_REQUEST['contract_id'];
	if(isset($_REQUEST['isRus'])) 		$isRus	= $_REQUEST['isRus'];
	else $isRus=0;
	if(isset($_REQUEST['isBased'])) 	$isBased= $_REQUEST['isBased'];
	else $isBased=0;
	if(isset($_REQUEST['bundle'])) 		$bundle	= $_REQUEST['bundle'];
	if(isset($_REQUEST['template'])) 	$template	= $_REQUEST['template'];
	
	$newClient=0;
	
	//var_dump($_REQUEST);
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
		if (isset($id))
		{
			$textsql_client='UPDATE clients SET id_NAV="'.$nav_id.'",id_SAP="'.$cl_id_SAP.'",name="'.$name.'",isRusCarrier="'.$isRus.'" WHERE id="'.$id.'"';
			//$textsql_contract='UPDATE contracts SET id_SAP="'.$contract_id.'",isBased="'.$isBased.'" WHERE client_id="'.$id.'"';
		}
		else
		{
			$textsql_client='INSERT INTO clients
						(id_NAV,name,id_SAP,isRusCarrier)
						VALUES( "'.$nav_id.'","'.$name.'","'.$cl_id_SAP.'","'.$isRus.'")';
			$newClient=1;
			//$textsql_contract='';
		}
		//echo $textsql_client.$textsql_contract."<br/>";
	
		$answsql=mysqli_query($db_server,$textsql_client);
		if(!$answsql) die("Clients table UPDATE failed: ".mysqli_error($db_server));
		if($newClient) $id=$db_server->insert_id;
		//$answsql=mysqli_query($db_server,$textsql_contract);
		//if(!$answsql) die("Contracts table UPDATE failed: ".mysqli_error($db_server));
		
		  //=================================================//
		 //			BUNDLE UPDATE SECTION					//
		//-------------------------------------------------//
		if($newClient&&$bundle)
		{
				$textsql_make='INSERT INTO bundle_reg (client_id,bundle_id,isValid)
								VALUES ('.$id.','.$bundle.',1)'; 
					$answsql=mysqli_query($db_server,$textsql_make);
					if(!$answsql) die("INSERT INTO bundle_reg table failed: ".mysqli_error($db_server));
		}
		else
		{
			if ($bundle)
			{
				$textsql_bundle='SELECT id FROM bundle_reg 
							WHERE client_id='.$id.' AND isValid=1';
			//echo $textsql_bundle.'<br/>';
				$answsql=mysqli_query($db_server,$textsql_bundle);
				if(!$answsql) die("SELECT TO bundles table failed: ".mysqli_error($db_server));
				if ($answsql->num_rows)
				{
					$textsql_clear='UPDATE bundle_reg SET isValid=0 WHERE client_id='.$id; 
					$answsql=mysqli_query($db_server,$textsql_clear);
					if(!$answsql) die("UPDATE bundle_reg table failed: ".mysqli_error($db_server));
					
				
				}
					$textsql_make='INSERT INTO bundle_reg (client_id,bundle_id,isValid)
								VALUES ('.$id.','.$bundle.',1)'; 
					$answsql=mysqli_query($db_server,$textsql_make);
					if(!$answsql) die("INSERT INTO bundle_reg table failed: ".mysqli_error($db_server));
				//echo $textsql_make." :MAKE BUNDLE<br/>";		
			}
			else
			{
				$textsql_clear='UPDATE bundle_reg SET isValid=0 WHERE client_id='.$id; 
				$answsql=mysqli_query($db_server,$textsql_clear);
					if(!$answsql) die("UPDATE bundle_reg table failed: ".mysqli_error($db_server));
				//echo $textsql_clear." :CLEAR - NO BUNDLE <br/>";
			}
		}
		  //=================================================//
		 //			TEMPLATE UPDATE SECTION	    			//
		//-------------------------------------------------//
		
		if($newClient&&$template)
		{
				$textsql_make_tmp='INSERT INTO package_reg (client_id,package_id,isValid)
								VALUES ('.$id.','.$template.',1)'; 
				$answsql=mysqli_query($db_server,$textsql_make_tmp);
				if(!$answsql) die("INSERT INTO package_reg table failed: ".mysqli_error($db_server));
		}
		else
		{
		 if ($template)
		 {
			$textsql_tmp='SELECT id FROM package_reg 
							WHERE client_id='.$id.' AND isValid=1';
			//echo $textsql_tmp.'<br/>';
			$answsql=mysqli_query($db_server,$textsql_tmp);
			if(!$answsql) die("SELECT TO package_reg table failed: ".mysqli_error($db_server));
			if ($answsql->num_rows)
			{
				$tmp_row=mysqli_fetch_row($answsql);
				
					$textsql_clear_tmp='UPDATE package_reg SET isValid=0 WHERE client_id='.$id; 
					$answsql=mysqli_query($db_server,$textsql_clear_tmp);
					if(!$answsql) die("UPDATE package_reg table failed: ".mysqli_error($db_server));
					//echo $textsql_clear_tmp." :CLEAR FOR NEW TEMPLATE<br/>";
					
			}
			
				$textsql_make_tmp='INSERT INTO package_reg (client_id,package_id,isValid)
								VALUES ('.$id.','.$template.',1)'; 
				$answsql=mysqli_query($db_server,$textsql_make_tmp);
				if(!$answsql) die("INSERT INTO package_reg table failed: ".mysqli_error($db_server));
				//echo $textsql_make_tmp." :MAKE TEMPLATE<br/>";
		
		 }
		 else
		 {
			$textsql_clear_tmp='UPDATE package_reg SET isValid=0 WHERE client_id='.$id; 
				$answsql=mysqli_query($db_server,$textsql_clear_tmp);
				if(!$answsql) die("UPDATE package_reg table failed: ".mysqli_error($db_server));
				//echo $textsql_clear_tmp." :CLEAR - NO TEMPLATE <br/>";
		 }
		}
		  //=================================================//
		 //			CONTRACT UPDATE SECTION	    			//
		//-------------------------------------------------//
		
		if($newClient&&$contract_id)
		{
				$textsql_make_cnt='INSERT INTO contracts (client_id,id_SAP,isBased,isValid)
								VALUES ('.$id.','.$contract_id.','.$isBased.',1)'; 
				$answsql=mysqli_query($db_server,$textsql_make_cnt);
				if(!$answsql) die("INSERT INTO contracts table failed: ".mysqli_error($db_server));
		}
		else
		{
		 if ($contract_id)
		 {
			$textsql_contract='SELECT id,id_SAP,isBased FROM contracts 
							WHERE client_id='.$id.'  AND isValid=1';
			//echo $textsql_contract.'<br/>';
			$answsql=mysqli_query($db_server,$textsql_contract);
			if(!$answsql) die("SELECT TO contracts table failed: ".mysqli_error($db_server));
			if ($answsql->num_rows)
			{
				$cnt_row=mysqli_fetch_row($answsql);
				if (($cnt_row[1]!=$contract_id)||($cnt_row[2]!=$isBased))
				{
					$textsql_clear_cnt='UPDATE contracts SET isValid=0 WHERE id='.$cnt_row[0]; 
					$answsql=mysqli_query($db_server,$textsql_clear_cnt);
					if(!$answsql) die("UPDATE contracts table failed: ".mysqli_error($db_server));
					//echo $textsql_clear_cnt." :CLEAR FOR NEW <br/>";
					$textsql_make_cnt='INSERT INTO contracts (client_id,id_SAP,isBased,isValid)
								VALUES ('.$id.','.$contract_id.','.$isBased.',1)'; 
				$answsql=mysqli_query($db_server,$textsql_make_cnt);
				if(!$answsql) die("INSERT INTO contracts table failed: ".mysqli_error($db_server));
				}
				
			}
			else
			{
				$textsql_make_cnt='INSERT INTO contracts (client_id,id_SAP,isBased,isValid)
								VALUES ('.$id.','.$contract_id.','.$isBased.',1)'; 
				$answsql=mysqli_query($db_server,$textsql_make_cnt);
				if(!$answsql) die("INSERT INTO contracts table failed: ".mysqli_error($db_server));
				//echo $textsql_make_cnt." :MAKE CONTRACT <br/>";		
			}
		 }
		 else
		 {
			$textsql_clear_cnt='UPDATE contracts SET isValid=0 WHERE client_id='.$id; 
				$answsql=mysqli_query($db_server,$textsql_clear_cnt);
				if(!$answsql) die("UPDATE contracts table failed: ".mysqli_error($db_server));
				//echo $textsql_clear_cnt." :CLEAR - NO CONTGRACT ANY MORE <br/>";
		 }
		}
		echo '<script>history.go(-2);</script>';			
		
	
mysqli_close($db_server);
?>