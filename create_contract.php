<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
					
		$content.= '<form id="form" method=post action=update_contract.php >
					<table><caption><b> Создаем контракт</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>Код клиента NAV:</td><td><input type="text" value="" name="nav" /></td></tr>
					<tr><td>Контракт SAP:</td><td><input type="text" value="" name="sap" /></td></tr>
					<tr><td>Действует:</td><td><input type="checkbox" name="Servicedata[]" class="name" value="valid" '.$status.'/></td></tr>
					<tr><td colspan="2"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	
	
	
?>
	