<?php require_once 'login_avia.php';
//LINKING SERVICES TO THE DISCOUNT
include ("header.php"); 
	
	if(isset($_REQUEST['id'])) 		$flight	= $_REQUEST['id'];
	if(isset($_REQUEST['id_nav'])) 		$id_NAV	= $_REQUEST['id_nav'];
	if(isset($_REQUEST['customer'])) $customer	= $_REQUEST['customer'];
	
	
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		
		// Top of the table
		$content.= '<table class="fullTab"><caption><b>Регистрируем осмотр</b></caption><br>';
		$content.='<form id="form" method=post action=book_medical.php >';
		$content.= '<tr><th>Название</th><th>Значение</th></tr>';
		// Iterating through the array
				
				$content.="<tr><td>РЕЙС:</td><td>$flight</td></tr>";
				$content.="<tr><td>КЛИЕНТ:</td><td>$customer</td></tr>";
				$content.='<tr><td>КОЛИЧЕСТВО:</td><td><input type="number" value="" name="num" /></td></tr>';
				$content.='<tr><td>ПРИМЕЧАНИЕ:</td><td><input type="text" value="" name="comment" placeholder="Текст примечания"/></td></tr>';
				$content.='<tr><td>ВРАЧ:</td><td><input type="text" value="" name="doctor" placeholder="Табельный номер"/></td></tr>';		
				$content.= '<tr><td colspan="2"><input type="hidden" value="'.sanitizestring($customer).'" name="customer" /><input type="hidden" value="'.$id_NAV.'" name="id_nav" /><input type="submit" name="send" class="send" value="ВВОД"></td></tr></form>';
				$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	