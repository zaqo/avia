﻿<?php 
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
		$check_clients='SELECT id,name FROM clients WHERE name!="" AND isValid';
					
					$answsqlcheck=mysqli_query($db_server,$check_clients);
					if(!$answsqlcheck) die("SELECT into clients TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$clients='<select name="client" id="client" required>';
		$clients.='<option disabled selected value> -- выберите компанию -- </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$clients.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$clients.='</select>';		
		
		$content.='<script src="/avia/js/calender.js" type="text/javascript">
	    </script>';	
		$content.= '<form id="form" method=post action=update_discount.php >
					<div id="add_field_area"><table id="myTab"><caption><b>Создаем скидку на клиента</b></caption>
					<tr><th></th><th></th></tr>
					<tr><td><b>НАЗВАНИЕ:</b></td><td><input type="text" value="" name="name" required/></td></tr>
					<tr><td><b>ГРУППА:</b></td><td><select name="group_id" required/>
						<option value=""> - </option><option value="0"> ВСЕ </option><option value="1"> RUS </option><option value="2"> INT </option>
					</select></td></tr>
					<tr><td><b>СКИДКА (%):</b></td><td><input type="number" value="" name="val" value="0" min="-100" max="100" step="0.001" required/></td></tr>
					<tr><td><b>C:</b></td><td><input type="text" class="date_input" value="" name="from" onfocus="this.select();lcs(this)"
												onclick="event.cancelBubble=true;this.select();lcs(this)"/></td></tr>
					<tr><td><b>ПО:</b></td><td><input type="text" class="date_input" value="" name="to" onfocus="this.select();lcs(this)"
												onclick="event.cancelBubble=true;this.select();lcs(this)"/></td></tr>
					<tr><td><b>ПРИОРИТЕТ:</b></td><td><input type="number" value="" name="priority" value="0" min="1" max="9" step="1" /></td></tr>
					<tr><td colspan="2"><input type="hidden" name="isGroup" value="1">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></div></form>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	