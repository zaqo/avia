﻿<?php 
/* 
	
	BILLING PROCESS EXCEPTION REGISTRATION FORM
	TIED TO CLIENT
	
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
					$services_dd='"  required>';
					$services_dd.='<option value="" selected > -- выберите -- </option>';
					while ($row = mysqli_fetch_row( $answsqlcheck ))
					{	
						$selected='';
						$svs=$row[0];
						$desc=mb_strcut($row[2],0,40);
						$svs_desc=$row[1].' | '.$desc.'...';
						
						$services_dd.='<option value="'.$row[0].'" >'.$svs_desc.'</option>';
					}
					$services_dd.='</select>';	
				
		
			// Constructs CLIENTS dropdown
					$clients_mysql='SELECT id,id_NAV,name FROM clients WHERE isValid ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$clients_mysql);
					if(!$answsqlcheck) die("SELECT into clients TABLE failed: ".mysqli_error($db_server));
					
					$clients_dd='<select name="cl"  required>';
					$clients_dd.='<option value="" selected disabled> -- выберите -- </option>';
					while ($row = mysqli_fetch_row( $answsqlcheck ))
					{	
						$selected='';
						$cl_id=$row[0];
						$desc=mb_strcut($row[2],0,40);
						$cl_desc=$row[1].' | '.$desc.'...';
						
						$clients_dd.='<option value="'.$cl_id.'" >'.$cl_desc.'</option>';
					}
					$clients_dd.='</select>';		
		
		$content.= '<form id="form" method=post action=update_exception.php >
					<div id="add_field_area"><table class="myTab"><caption><b>Добавление клиента с особыми настройками</b></caption>
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
		
		
	Show_page($content);
	
	mysqli_close($db_server);

?>
	