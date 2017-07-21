<?php require_once 'login_avia.php';

include ("header.php"); 
	
		$id= $_REQUEST['id'];
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT id,id_NAV,id_SAP,isforKids,isValid,date_booked,id_mu
								FROM services
								WHERE id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$row = mysqli_fetch_row( $answsqlcheck );
		
				$rec_id=$row[0];
				$nav_id=$row[1];
				$sap_id=$row[2];
				$iskids=$row[3];
				$isvalid=$row[4];
				$date=$row[5];
				$mu=$row[6];
				$status.= '';
				
				if ($row[3])
					$status.= 'checked';
				
				if ($row[4])
					$status.= 'checked';
				
		$content.= '<form id="form" method=post action=update_service.php >
					<table><caption><b>Карточка услуги</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>Код NAV:</td><td><input type="text" value="'.$nav_id.'" name="nav" /></td></tr>
					<tr><td>Код SAP:</td><td><input type="text" value="'.$sap_id.'" name="sap" /></td></tr>
					<tr><td>Ед.изм:</td><td>
						<select name="mu" id="mu" class="mu" >
							<option value="0"> </option>
							<option value="1">Рейс</option>
							<option value="2">Пассажир</option>
							<option value="3">Тонна</option>
						</select>
					</td></tr></th>
					<tr><td>Детский:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="kid" '.$status.'/></td></tr></th>
					<tr><td>Действует:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="valid" '.$status.'/></td></tr></th>
					<tr><td colspan="2"><p><input type="hidden" value="'.$id.'" name="id">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	