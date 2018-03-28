 <?php require_once 'login_avia.php';
/*
	FORM TO EDIT SERVICE's MASTER
	by S.Pavlov March 2018

*/
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
		
		$mu_dropdown='<select name="mu" id="mu" class="custom-select">';
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
		
		/*		
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
		*/
		$content.= '<div class="col-md-8 order-md-1 mt-2 ">
						<h4 class="mb-3 ml-5">Редактирование услуги</h4>';
		$content.= '<form id="form" method="post" action="update_service.php" class="needs-validation" novalidate>
					
					<div class="mb-3">
						<label for="id_NAV">Код NAV </label>
							<input type="text" class="form-control" id="nav" name="nav" value="'.$nav_id.'">
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="id_SAP">Код SAP</label>
							<input type="text" class="form-control" id="sap" name="sap"  value="'.$sap_id.'">
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="id_mu">Ед.измерения</label>
							'.$mu_dropdown.'
					</div>
					<div class="mb-3">
						<label for="desc">Описание</label>
							<textarea rows="5" cols="45" class="form-control" id="desc" name="desc">'.$desc.'</textarea>
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<div class="form-check">
							<input  type="checkbox" id="Kids" name="Servicedata[]" class="form-check-input" value="kid" '.$status_kid.'/>
							<label class="form-check-label" for="Kids">Для детей</label>
						</div>
					</div>
					<div class="mb-3">
						<div class="form-check">
							<input  type="checkbox" id="Valid" name="Servicedata[]" class="form-check-input" value="valid" '.$status_valid.'/>
							<label class="form-check-label" for="Valid">Действует</label>
						</div>
					</div>
					
					 <hr class="mb-4">
						<input type="hidden" value="'.$id.'" name="id">
						<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
					</form>';
		
		$content.= '</div>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	