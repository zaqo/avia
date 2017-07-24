 <?php require_once 'login_avia.php';

include ("header.php"); 
	
		$id= $_REQUEST['id'];
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT id,id_NAV,id_SAP,isValid
								FROM contracts
								WHERE id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into contracts TABLE failed: ".mysqli_error($db_server));
		$row = mysqli_fetch_row( $answsqlcheck );
		
				$rec_id=$row[0];
				$nav_id=$row[1];
				$sap_id=$row[2];
				$isvalid=$row[3];
				
				$status_valid= '';
				
				if ($row[3])
					$status_valid.= 'checked';
		 
		// Top of the table
		
				
		$content.= '<form id="form" method=post action=update_contract.php >
					<table><caption><b>Карточка контракта</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>Код NAV:</td><td><input type="text" value="'.$nav_id.'" name="nav" /></td></tr>
					<tr><td>Код SAP:</td><td><input type="text" value="'.$sap_id.'" name="sap" /></td></tr>
					<tr><td>Действует:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="valid" '.$status_valid.'/></td></tr>
					<tr><td colspan="2"><p><input type="hidden" value="'.$id.'" name="id">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	