<?php
/* 

		UPDATE LIST OF AIRPORTS IN EXCEPTIONS
		INPUTS:
			id		-	identificator of exception
			airports - 	number of service ( we have four)
			
	by S.Pavlov (c) 2018
*/
include ("login_avia.php"); 

 
	
if(isset($_REQUEST['id'])) 			
{
	
	$id		= $_REQUEST['id'];
	if(isset($_REQUEST['airports'])) 	$apt	= $_REQUEST['airports'];

	
	//var_dump($_REQUEST);
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		$airports=explode(",", $apt);

		  //=================================================//
		 //					 UPDATE SECTION					//
		//-------------------------------------------------//
		
			$clear_sql='UPDATE exc_conditions SET isValid=0 WHERE exc_id="'.$id.'"';
					
			$answsql_cl=mysqli_query($db_server,$clear_sql);
			if(!$answsql_cl) die("UPDATE exc_conditions table failed: ".mysqli_error($db_server));
			//echo "CLEANED: <br/>";
			
		foreach ($airports as $value)
		{
		  
		  //=================================================//
		 //					 INSERT SECTION					//
		//-------------------------------------------------//

				$insert_sql='INSERT INTO exc_conditions (exc_id,airport_id,isValid) VALUES ("'.$id.'","'.$value.'",1)';
	
				$answsql_in=mysqli_query($db_server,$insert_sql);
				if(!$answsql_in) die("INSERT exc_conditions table failed: ".mysqli_error($db_server));
				//echo "INSERTED: $value <br/>";
		}
}
else echo "ERROR: NO EXCEPTION's ID PROVIDED";
echo '<script>history.go(-2);</script>';			
		
	
mysqli_close($db_server);
?>