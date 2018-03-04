<?php require_once 'login_avia.php';

include ("header.php"); 

		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT id,name
								FROM clients
								WHERE isValid ORDER BY name";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into clients TABLE failed: ".mysqli_error($db_server));
		$cl_dd='<select name="id" id="cl" class="custom-select d-block w-100" required>';
		$cl_dd.='<option value="" selected disabled>-- выберите --</option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		{
			if($row[1])
			$cl_dd.='<option value="'.$row[0].'">'.$row[1].'</option>';
		}
		$cl_dd.='</select>';	
		
		$content="";
					
		
		$content.= '<div class="col-md-8 order-md-1">
						<h4 class="mb-3">Новый контракт</h4>';
		$content.= '<form id="form" method="post" action="update_contract.php" class="needs-validation" >
					
					<div class="mb-3">
						<label for="Client">Компания </label>
							'.$cl_dd.'
					</div>
					
					
					<div class="mb-3">
						<label for="id_SAP">Код SAP</label>
							<input type="text" class="form-control" id="sap" name="sap" placeholder="900000000" required>
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					
					<div class="mb-3">
						<div class="form-check">
							<input  type="checkbox" id="Valid" name="Servicedata[]" class="form-check-input" value="valid" required checked/>
							<label class="form-check-label" for="Valid">Действует</label>
						</div>
					</div>
					<div class="mb-3">
						<div class="form-check">
							<input  type="checkbox" id="Base" name="Servicedata[]" class="form-check-input" value="base" required />
							<label class="form-check-label" for="Base">Базирование</label>
						</div>
					</div>
					 <hr class="mb-4">	
						<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
					</form>';		
			$content.= '</div>';
		
	Show_page($content);
	
	
	
?>
	