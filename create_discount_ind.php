<?php 
// CREATES NEW DISCOUNT
require_once 'login_avia.php';
include ("header.php"); 	
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql='SELECT id,id_NAV FROM services WHERE 1';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
		// Constructs services dropdown
		$services='<select name="val[]" id="val1" class="services" required>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$services.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$services.='</select>';	

		// Constructs clients dropdown
		$check_clients='SELECT id,name FROM clients WHERE name!="" AND isValid ORDER BY name' ;
					
					$answsqlcheck=mysqli_query($db_server,$check_clients);
					if(!$answsqlcheck) die("SELECT into clients TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$clients='<select name="client" id="client" class="form-control" required>';
		$clients.='<option disabled selected value> -- выберите компанию -- </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$clients.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$clients.='</select>';		
		
		$content.='<script src="/avia/js/calender.js" type="text/javascript"></script>';
$content.= '<div class="col-md-8 order-md-1 mt-2">
						<h4 class="mb-3"> Заводим скидку (клиент)</h4>';
		$content.= '<form id="form" method=post action=update_discount.php class="needs-validation" novalidate>';
		$content.='
					<div class="mb-3">
						<label for="name">Название</label>
							<textarea value="" name="name" class="form-control" required/></textarea>
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="client">Клиент</label>
							'.$clients.'
					</div>
					<div class="mb-3">
						<label for="val">Скидка</label>
							<input type="number" class="form-control" name="val" value="" min="-100" max="100" step="0.001" required/>
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="row">Действует
						<div class=" col mb-3">
							
							<label class="form-check-label" for="date_from"> С:</label>
							<input type="text" class="form-control" value="" name="from" onfocus="this.select();lcs(this)"
												onclick="event.cancelBubble=true;this.select();lcs(this)"/>
							
						</div>
						<div class="col mb-3">
						
							<label class="form-check-label" for="to">ПО:</label>
							<input type="text" class="form-control" value="" name="to" onfocus="this.select();lcs(this)"
												onclick="event.cancelBubble=true;this.select();lcs(this)"/>
						
						</div>
					</div>
					<div class="mb-3">
							
							<label class="form-check-label" for="priority">ПРИОРИТЕТ</label>
							<input type="number" value="" name="priority" value="0" min="1" max="9" step="1" />
							
					</div>
					
					 <hr class="mb-4">
						<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
					</form>';
		$content.='</div>';			
		
		
		
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	