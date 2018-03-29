<?php require_once 'login_avia.php';
/*
		Books medical examinatoin results and produces printout
		
 by S. Pavlov (c) October 2017
*/
include ("header_tpl_doc.php"); 
	

	if(isset($_REQUEST['flight'])) 		$flight	= $_REQUEST['flight'];
	if(isset($_REQUEST['date'])) 		$date	= $_REQUEST['date'];
	if(isset($_REQUEST['qty'])) 		$quantity	= $_REQUEST['qty'];
	if(isset($_REQUEST['comment'])) 	$comment	= $_REQUEST['comment'];
	if(isset($_REQUEST['who'])) 		$doctor	= $_REQUEST['who'];
	//if(isset($_REQUEST['flight'])) 		$flight	= $_REQUEST['flight'];
	
		$content="";
	
		$today=date("d.m.y");;
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		// 1. RECORD THE SERVICE
				$transfer_mysql='INSERT INTO avia.medical_reg
								(flight,date,qty,who,comment,isValid) 
								VALUES
								("'.$flight.'","'.$date.'","'.$quantity.'","'.$doctor.'","'.$comment.'",1)'; //A0300462 HARDCODED NOW!!!
								
								$answsqlnext=mysqli_query($db_server,$transfer_mysql);
								
								if(!$answsqlnext) die("INSERT into TABLE failed: ".mysqli_error($db_server));
		
		// Top of the table
		$content.='<h2 align="center">СПРАВКА</h2>';
		$content.='<h3 align="center"> о прохождении предполетного досмотра</h3>';
		$content.="<div align=\"center\"> экипаж рейса: $flight <tab5>АК: </div><br/>";
		$content.= '<table class="fullTab">';
		
		$content.= '<tr><th rowspan="2">ДАТА</th><th colspan="2">Сведения по услугам</th><th rowspan="2">Примечание</th></tr>';
		
				
				$content.="<tr><td>Ед.изм.</td><td>Кол-во</td></tr>";
				//$content.="<tr><td>КЛИЕНТ:</td><td>$customer</td></tr>";
				$content.="<tr><td>1</td><td>2</td><td>3</td><td>4</td></tr>";
				$content.="<tr><td>$today</td><td>ЧЕЛ</td><td>$quantity</td><td>$comment</td></tr>";
				$content.= '</table>';
				$content.='<br/><br/><br/><div align="left"><tab4>ПОДПИСИ:</div><br/><br/>';
				$content.='<div align="center"><b>От: ООО ВВСС <tab6>От: ЭКИПАЖА</b></div><br/><br/>';
				$content.='<div align="center">_____________ <tab6>_____________<div><br/>';
				$content.='<div align="center">(подпись Ф.И.О.)<tab7>(подпись Ф.И.О.)</div><br/><br/>';
				
	Show_page($content);
	mysqli_close($db_server);
	
?>
	