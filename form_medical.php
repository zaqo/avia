<?php require_once 'login_avia.php';
//LINKING SERVICES TO THE DISCOUNT
include ("header.php"); 
	
	if(isset($_REQUEST['id'])) 		$id	= $_REQUEST['id'];
	
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			$textsql='SELECT flight,date,owner,id_NAV
						FROM  flights WHERE  id='.$id.' AND done_medical IS NULL';
				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
		$row = mysqli_fetch_row($answsql);
		$flight=$row[0];
		$date=$row[1];
		$customer=$row[2];
		$id_NAV=$row[3];
		$date_show=substr($date,-2).'-'.substr($date,5,2).'-'.substr($date,2,2);
		
		// Top of the table
		$content.= '<table class="fullTab"><caption><b>Регистрируем осмотр</b></caption><br>';
		$content.='<form id="form" method=post action=book_medical.php >';
		$content.= '<tr><th>Название</th><th>Значение</th></tr>';
		// Iterating through the array
				
				$content.="<tr><td>РЕЙС:</td><td>$flight</td></tr>";
				$content.="<tr><td>ДАТА:</td><td>$date_show</td></tr>";
				$content.="<tr><td>КЛИЕНТ:</td><td>$customer</td></tr>";
				$content.='<tr><td>КОЛИЧЕСТВО:</td><td><input type="number" value="" name="num" /></td></tr>';
				$content.='<tr><td>ПРИМЕЧАНИЕ:</td><td><input type="text" value="" name="comment" placeholder="Текст примечания"/></td></tr>';
				$content.='<tr><td>ВРАЧ:</td><td><input type="text" value="" name="doctor" placeholder="Табельный номер"/></td></tr>';		
				$content.= '<tr><td colspan="2"><input type="hidden" value="'.sanitizestring($flight).'" name="flight" />
									<input type="hidden" value="'.sanitizestring($customer).'" name="customer" />
									<input type="hidden" value="'.$id_NAV.'" name="id_NAV" />
									<input type="submit" name="send" class="send" value="ВВОД"></td></tr></form>';
				$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	