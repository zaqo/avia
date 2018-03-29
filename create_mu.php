 <?php 
 /*
	Creates unit of measurement record
	by S.Pavlov (c) 2017
 */
 require_once 'login_avia.php';

include ("header.php"); 
	
		//$id= $_REQUEST['id'];
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		/*
			$check_in_mysql="SELECT *
								FROM units
								WHERE id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into units TABLE failed: ".mysqli_error($db_server));
		$row = mysqli_fetch_row( $answsqlcheck );
		
				//$rec_id=$row[0];
				$name_rus=$row[1];
				$name_en=$row[2];
			*/	
		// Top of the table
		
				
		$content.= '<form id="form" method=post action=update_mu.php >
					<table><caption><b>Единица измерения</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>Название (rus):</td><td><input type="text" value="" name="name_rus" /></td></tr>
					<tr><td>Название (en):</td><td><input type="text" value="" name="name_en" /></td></tr>
					<tr><td colspan="2"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	