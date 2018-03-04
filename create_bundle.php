<?php require_once 'login_avia.php';

include ("header.php"); 
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			

		// Prepare services dropdown
			$check_in_mysql='SELECT id,id_NAV FROM services WHERE 1 ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		// Services dropdown
		$services='<select name="val[]" id="val1" class="custom-select d-block w-100" >';
		$services.='<option value=""> ... </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$services.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$services.='</select>';		
		$message='hi';
		// Top of the table	
		$content.= '<div  class="col-md-8 order-md-1">
						<h4 class="mb-3">Новый пакет</h4>';
		$content.= '<form id="form" method=post action=update_bundle.php class="needs-validation" novalidate>';
		
		// FORM CONTENT
		$content.='
					<div class="mb-3">
						<label for="pack_name">Название</label>
							<input type="text" id="pack_name" class="form-control" value="" name="pack_name" placeholder="Название пакета" />
							
								<div class="invalid-feedback">
									Введите корректно текст названия.
								</div>
					</div>
					
					<div class="mb-3">
						<label for="id_SAP">Идентификатор SAP</label>
							<input type="text" class="form-control" value="" id="id_SAP" name="sap" placeholder="25000000" />
					</div>
					<div class="mb-3">
						<label for="id_nav">Идентификатор NAV</label>
							<input type="nav" id="id_nav" value="" class="form-control" name="nav"  placeholder="A0000000"/>
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						
						<label for="desc">Описание</label>
							<textarea id="desc" rows="5" cols="45" name="desc"  class="form-control" placeholder="Назначение пакета" ></textarea>
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div id="add_field_area" class="mb-3">
						
						<div id="add1" class="row add">
								
								<div class="input-group-prepend">
									<div class="input-group-text">#1</div>
								</div>
								<div class="col">
									<input type="number" name="qt[]" class="form-control">
								</div>
								
								<div class="col">
								'.$services.'
								</div>
						</div>
					</div>
					<div class="mb-3" >
					<div class="addbutton" onclick="addMyDiv();">+</div>
					</div>
					 <hr class="mb-4">
						<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
					</form>';
		$content.= '</div>';
		/*
		$content.= '<form id="form" method=post action= >
					<div id="add_field_area"><table id="myTab" class="fullTab"><caption><b>Создаем пакет</b></caption>
					<tr><th></th></tr>
					<tr><td><b>НАЗВАНИЕ:</b><input type="text" value="" name="pack_name" placeholder="Название пакета" /></td></tr>
					<tr><td>Код NAV:<input type="text" value="" name="nav" /></td></tr>
					<tr><td>Код SAP:<input type="text" value="" name="sap" /></td></tr>
			
					<tr><td>Описание:<textarea rows="5" cols="45" name="desc" placeholder="Назначение пакета" ></textarea></td></tr>
					
					<tr><th >ВКЛЮЧАЕТ УСЛУГИ:</th></tr>
					<tr><tbody id="tbody">
					<div id="add1" class="add">
							<td>#1:'.$services.'</td>
					</div>
					</tbody></tr>
					
					<tr><td onclick="addMyRow();" class="addbutton" colspan="4">Add</td></tr>
					
					</div>
					<tr><td colspan="4"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form><br/>';*/
		
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	