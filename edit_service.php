 <?php require_once 'login_avia.php';

include ("header.php"); 
	
		$id= $_REQUEST['id'];
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT id,id_NAV,id_SAP,isforKids,isValid,date_booked,id_mu,description
								FROM services
								WHERE id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		$row = mysqli_fetch_row( $answsqlcheck );
		
				$rec_id=$row[0];
				$nav_id=$row[1];
				$sap_id=$row[2];
				$iskids=$row[3];
				$isvalid=$row[4];
				$date=$row[5];
				$mu=$row[6];
				$desc=$row[7];
				$status_kid= '';
				$status_valid= '';
				
				if ($row[3])
					$status_kid.= 'checked';
				
				if ($row[4])
					$status_valid.= 'checked';
		// Prepare Mes Units 
		$check_in_mysql='SELECT id,description_rus FROM units WHERE 1';
					
					$answsql_mu=mysqli_query($db_server,$check_in_mysql);
					if(!$answsql_mu) die("SELECT into units TABLE failed: ".mysqli_error($db_server));
		
		$mu_dropdown='<select name="mu" id="mu" class="mu" >';
		$mu_dropdown.='<option value="0">  </option>';
		
		while ($row_d = mysqli_fetch_row( $answsql_mu ))
		{
			$sel='';
			if($row_d[0]==$mu)
				$sel='selected';
			$mu_dropdown.='<option value="'.$row_d[0].'" '.$sel.'>'.$row_d[1].'</option>';
		}
		$mu_dropdown.='</select>';	
		
		// Top of the table
		
				
		$content.= '<form id="form" method=post action=update_service.php >
					<table><caption><b>Карточка услуги</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>Код NAV:</td><td><input type="text" value="'.$nav_id.'" name="nav" /></td></tr>
					<tr><td>Код SAP:</td><td><input type="text" value="'.$sap_id.'" name="sap" /></td></tr>
					<tr><td>Ед.изм:</td><td>'.$mu_dropdown.'</td></tr>
					<tr><td>Описание:</td><td><textarea rows="5" cols="45" name="desc" >'.$desc.'</textarea></td></tr>
					<tr><td>Для детей:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="kid" '.$status_kid.'/></td></tr>
					<tr><td>Действует:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="valid" '.$status_valid.'/></td></tr>
					<tr><td colspan="2"><p><input type="hidden" value="'.$id.'" name="id">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	