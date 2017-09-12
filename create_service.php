<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql='SELECT id,description_rus FROM units WHERE 1';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into units TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$mu_dropdown='<select name="mu" id="mu" class="mu" >';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$mu_dropdown.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$mu_dropdown.='</select>';		
		//var_dump($mu_dropdown);		
		$content.= '<form id="form" method=post action=update_service.php >
					<table><caption><b>Карточка услуги</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>Код NAV:</td><td><input type="text" value="" name="nav" /></td></tr>
					<tr><td>Код SAP:</td><td><input type="text" value="" name="sap" /></td></tr>
					<tr><td>Ед.изм:</td><td>'.$mu_dropdown.'</td></tr>
					<tr><td>Описание:</td><td><textarea rows="5" cols="45" name="desc" placeholder="Описание" ></textarea></td></tr>
					<tr><td>Детский:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="kid" '.$status.'/></td></tr></th>
					<tr><td>Действует:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="valid" '.$status.'/></td></tr></th>
					<tr><td colspan="2"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	