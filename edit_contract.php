 <?php require_once 'login_avia.php';

include ("header.php"); 
	
		$id= $_REQUEST['id'];
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT contracts.id,clients.id_NAV,contracts.id_SAP,contracts.isValid
								FROM contracts
								LEFT JOIN clients ON clients.id=contracts.client_id
								WHERE contracts.id=$id";
					
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
		$content.= '<div class="col-md-8 order-md-1">
						<h4 class="mb-3">Карточка контракта</h4>';
		$content.= '<form id="form" method="post" action="update_contract.php" class="needs-validation" novalidate>
					
					<div class="mb-3">
						<label for="id_NAV">Код NAV </label>
							<input type="text" class="form-control" id="nav" name="nav" value="'.$nav_id.'" placeholder="A0000000">
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="id_SAP">Код SAP</label>
							<input type="text" class="form-control" id="sap" name="sap" value="'.$sap_id.'" placeholder="900000000">
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
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
	