<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
					
		$content.= '<form id="form" method=post action=update_contract.php >
					<table><caption><b>Карточка контракта</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>Код клиента NAV:</td><td><input type="text" value="" name="nav" /></td></tr>
					<tr><td>Контракт SAP:</td><td><input type="text" value="" name="sap" /></td></tr>
					<tr><td>Действует:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="valid" '.$status.'/></td></tr>
					<tr><td colspan="2"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	