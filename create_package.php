﻿<?php 
// CREATES TEMPLATE OF SERVICES
require_once 'login_avia.php';
include ("header.php"); 	
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql='SELECT id,id_NAV FROM services WHERE 1 ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		// Services dropdown
		$services='<select name="val[]" id="val1" class="services" >';
		$services.='<option value=""> ... </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$services.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$services.='</select>';		
		// 
		// Constructs clients dropdown
		$check_clients='SELECT id,name FROM clients WHERE isValid=1';
					
					$answsqlcheck=mysqli_query($db_server,$check_clients);
					if(!$answsqlcheck) die("SELECT into clients TABLE failed: ".mysqli_error($db_server));
		$clients='<select name="client" id="client" >';
		$clients.='<option value=""> ... </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$clients.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$clients.='</select>';
		// Form begins
		$content.= '<form id="form" method=post action=update_package.php >
					<div id="add_field_area"><table id="myTab"><caption><b>Создаем пакет</b></caption>
					<tr><td colspan="2"><b>НАЗВАНИЕ:</b></td><td colspan="2"><input type="text" value="" name="pack_name" placeholder="Название пакета" /></td></tr>
					<tr><td colspan="2"><b>КЛИЕНТ:</b></td><td colspan="2">'.$clients.'</td></tr>
					<tr><th>Услуга</th><th>Везде</th><th>Вкл Аэропорты</th><th>Искл Аэропорты</th></tr>
					
					<div id="add1" class="add">
						<tr>
							<td>'.$services.'</td>
							<td><select name="to_all[]" id="all" class="services" >
							<option value=1>Да</option>
							<option value=0>Нет</option></select>
							</td>
							<td><input type="text" value="" name="including[]" placeholder="1,2,3"/></td>
							<td><input type="text" value="" name="excluding[]" placeholder="1,2,3"/></td>
						</tr>
					</div>
					<tbody id="tbody">
					<tr><td onclick="addsomeField();" class="addbutton" colspan="4">Add</td></tr>
					</tbody>
					<tr><td colspan="4"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></div></form>';
	
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	