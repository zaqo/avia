<?php 
/* 
	
	BILLING PROCESS EXCEPTION REGISTRATION FORM
	TIED TO a CLIENT
	
*/
require_once 'login_avia.php';
include ("header.php"); 	
		
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
				// Constructs services dropdown
					$check_in_mysql='SELECT id,id_NAV,description FROM services WHERE isValid ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
					$services_dd='<select name="svs" class="custom-select d-block w-100" required>';
					$services_dd.='<option value="" selected disabled> -- укажите услугу -- </option>';
					while ($row = mysqli_fetch_row( $answsqlcheck ))
					{	
						$selected='';
						$svs=$row[0];
						$nav=$row[1];
						$desc=$row[2];
						if( strlen($desc)>80)
						{
							$desc=mb_strcut($desc,0,80);
							$svs_desc=$nav.' | '.$desc.'...';
						}
						else
						{
							$svs_desc=$nav.' | '.str_pad($desc,80);
						}
						
						$services_dd.='<option value="'.$row[0].'" >'.$svs_desc.'</option>';
					}
					$services_dd.='</select>';	
				
		
			// Constructs CLIENTS dropdown
					$clients_mysql='SELECT id,id_NAV,name FROM clients WHERE isValid ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$clients_mysql);
					if(!$answsqlcheck) die("SELECT into clients TABLE failed: ".mysqli_error($db_server));
					
					$clients_dd='<select name="cl" class="custom-select d-block w-100"  required>';
					$clients_dd.='<option value="" selected disabled> -- укажите компанию -- </option>';
					while ($row = mysqli_fetch_row( $answsqlcheck ))
					{	
						$selected='';
						$cl_id=$row[0];
						$svs=$row[0];
						$nav=$row[1];
						$desc=$row[2];
						if( strlen($desc)>80)
						{
							$desc=mb_strcut($desc,0,80);
							$cl_desc=$nav.' | '.$desc.'...';
						}
						else
						{
							$cl_desc=$nav.' | '.str_pad($desc,80);
						}
						
						$clients_dd.='<option value="'.$cl_id.'" >'.$cl_desc.'</option>';
					}
					$clients_dd.='</select>';		
		
		$content.= '<div class="col-md-8 order-md-1 mt-5 ml-2">
						<h4 class="mb-3"> Добавление особых условий для авиакомпании</h4>';
		$content.= '<form id="form" method=post action="update_exception.php" class="needs-validation" novalidate/>';
		$content.='
					<div class="mb-3 mt-5">
						<label for="cl">Компания</label>
							'.$clients_dd.'
					</div>
					<div class="mb-3 mt-2">
						<label for="svs">Услуга</label>
							'.$services_dd.'
					</div>
					 <hr class="mb-4 ">
					<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
				</form>';
		$content.= '</div>';
		
		/*
		$content.= '<form id="form" method=post action= >
					<div id="add_field_area"><table class="myTab"><caption><b>Добавление </b></caption>
					<tr><th class="col90">ПАРАМЕТР</th><th class="col300">ЗНАЧЕНИЕ</th></tr>
					
					<tr><td>КОМПАНИЯ</td><td>'.$clients_dd.'</td></tr>
					<tr><td colspan="2"> ВЗЛЕТ / ПОСАДКА</td></tr>
					<tr><td>УСЛУГА</td><td>'.$services_dd_head.'1'.$services_dd.'</td></tr>
					<tr><td colspan="2"> НАЗЕМНОЕ ОБСЛУЖИВАНИЕ</td></tr>
					<tr><td>ВЗР</td><td>'.$services_dd_head.'2'.$services_dd.'</td></tr>
					<tr><td>ДЕТ</td><td>'.$services_dd_head.'3'.$services_dd.'</td></tr>
					<tr><td colspan="2">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></div></form>';
		*/
		
	Show_page($content);
	
	mysqli_close($db_server);

?>
	