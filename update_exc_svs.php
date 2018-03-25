<?php
/* 

		UPDATE SERVICE ID IN EXCEPTIONS
		INPUTS:
			id	-	identificator of exception
			num - 	number of service ( we have four)
			val - 	id of service, for setting the default

*/
include ("login_avia.php"); 

 
	
	if(isset($_REQUEST['id'])) 			$id		= $_REQUEST['id'];
	if(isset($_REQUEST['num'])) 		$num	= $_REQUEST['num'];
	if(isset($_REQUEST['val'])) 		$svs	= $_REQUEST['val'];
	
	
	//var_dump($_REQUEST);
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		  //=================================================//
		 //					 UPDATE SECTION					//
		//-------------------------------------------------//
		
		switch($num)
		{
				case 1:
					$textsql='UPDATE exc_process SET service_id='.$svs.' WHERE id='.$id; 
					break;
				case 2:
					$textsql='UPDATE exc_default SET svs_kids_id='.$svs.' WHERE exc_id='.$id; 
					break;
				case 3:
					$textsql='UPDATE exc_default SET exc_svs_id='.$svs.' WHERE exc_id='.$id;
					break;
				case 4:
					$textsql='UPDATE exc_default SET exc_svs_kids_id='.$svs.' WHERE exc_id='.$id;
					break;
				default:
					echo "ERROR: WRONG SERVICE POSITION ID IN THE INPUT! <br/>";
					break;
		}
					$answsql=mysqli_query($db_server,$textsql);
					if(!$answsql) die("UPDATE package_reg table failed: ".mysqli_error($db_server));
					//echo $textsql_clear_tmp." :CLEAR FOR NEW TEMPLATE<br/>";
					
		
		echo '<script>history.go(-2);</script>';			
		
	
mysqli_close($db_server);
?>