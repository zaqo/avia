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
					$services_dd_head='<select name="svs';
					$services_dd='" class="custom-select d-block w-100" required>';
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
						<h4 class="mb-3 ml-5"> Добавление особых условий для авиакомпании</h4>';
		$content.= '<form id="form" method=post action="update_exception.php" class="needs-validation" novalidate/>';
		$content.='
					<div class="mb-3 mt-5">
						<label for="cl">Компания</label>
							'.$clients_dd.'
					</div>
					<div class="mb-3 mt-2">
						<label for="svs">ВЗЛЕТ / ПОСАДКА</label>
							'.$services_dd_head.'1'.$services_dd.'
					</div>
					 <hr class="mb-4 ">
					 <div class="mb-3 mt-2">
						<label for="svs">Наземное обслуживание (ВЗР)</label>
							'.$services_dd_head.'2'.$services_dd.'
					</div>
					 <hr class="mb-4 ">
					 <div class="mb-3 mt-2">
						<label for="svs">Наземное обслуживание (ДЕТ)</label>
							'.$services_dd_head.'3'.$services_dd.'
					</div>
					 <hr class="mb-4 ">
					<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
				</form>';
		$content.= '</div>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);

?>
	