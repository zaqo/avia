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
		$services='<select name="val[]" id="val1" class="services" >';
		$services.='<option value=""> ... </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$services.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$services.='</select>';		
		$message='hi';
		// Top of the table	
		
		// Form begins
		$content.= '<form id="form" method=post action=update_bundle.php >
					<div id="add_field_area"><table id="myTab" class="myTab"><caption><b>Создаем пакет</b></caption>
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
					</table></form><br/>';
		
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	