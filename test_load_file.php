<?php
include 'login_avia.php';


	
		$content="";
		$file_name='d:\\ozm.csv';
		$data_=array();
		$str_=array();
		$data_=file($file_name);
		
		
		//var_dump($data_);
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			$i=0;
			// CLEAN it FIRST
			$tr_sql='TRUNCATE services_transfer';
				$answsql=mysqli_query($db_server,$tr_sql);	
				if(!$answsql) die("TRUNCATE services_transfer table failed: ".mysqli_error($db_server));
			foreach( $data_ as $value)
			{
				$str_=explode(';',$value);
				$nav_id=$str_[0];
				$sap_id=$str_[1];
				$textsql='INSERT INTO services_transfer
						(id_NAV,id_SAP)
						VALUES("'.$nav_id.'","'.$sap_id.'")';
				$answsql=mysqli_query($db_server,$textsql);	
				if(!$answsql) die("Database INSERT INTO services_transfer table failed: ".mysqli_error($db_server));
				$i++;
			}
			echo "FINISHED! $i records inserted into table; <br/> ";
		/*
			
			
				
			
			$pair= mysqli_fetch_row($answsql);
			$in_id=$pair[0];
			$out_id=$pair[1];
		*/
			
					
	mysqli_close($db_server);

?>