<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql='SELECT id,description_rus FROM units WHERE 1';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into units TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$mu_dropdown='<select name="mu" id="mu" class="custom-select d-block w-100" >';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$mu_dropdown.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$mu_dropdown.='</select>';		
		//var_dump($mu_dropdown);		
		$content.= '<div class="col-md-8 order-md-1">
						<h4 class="mb-3">Новая услуга</h4>';
		$content.= '<form id="form" method="post" action="update_service.php" class="needs-validation" novalidate>
					
					<div class="mb-3">
						<label for="id_NAV">Код NAV </label>
							<input type="text" class="form-control" id="nav" name="nav" placeholder="A0000000">
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="id_SAP">Код SAP</label>
							<input type="text" class="form-control" id="sap" name="sap" placeholder="900000000">
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
							<textarea rows="5" cols="45" class="form-control" id="desc" name="desc" placeholder="Информация об услуге" ></textarea>
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="id_mu">Для детей</label>
							<input type="checkbox" name="Servicedata[]" class="form-control" value="kid" '.$status.'/>
					</div>
					<input type="hidden" name="Servicedata[]" value="valid" />
					
					 <hr class="mb-4">
						<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
					</form>';
		
		$content.= '</div>';
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	