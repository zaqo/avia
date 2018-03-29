<?php 
	/*
	Makes a list of all outgoing flights in the system
	with a link to a form for medical services
	by S.Pavlov (c) 2017
	*/
	require_once 'login_avia.php';
	
	set_time_limit(0);
	include ("header.php"); 
	
	
		$content='';
		//----------------------------------------
		// Top of the table
		$content.= "<b>  Список рейсов по вылету:</b>  <hr>";
		$content.= '<table><caption><b>Данные о рейсах</b></caption><br>
					<tr><th>№</th><th>ДАТА</th><th>РЕЙС</th><th>КЛИЕНТ</th></tr>';
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// 1. List flights 
		
		$textsql='SELECT flight,date,owner,id,id_NAV
						FROM  flights WHERE  direction=1 AND done_medical IS NULL';
				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
		$num=1;
		while( $row = mysqli_fetch_row($answsql) ) 
		{
			
			
			$flight=$row[0];
			$date_rec=substr($row[1],-2).'/'.substr($row[1],5,2).'/'.substr($row[1],2,2);
			$customer=$row[2];
			$id=$row[3];
			$id_NAV=$row[4];
			
						$content.="<tr>";
						$content.= "<td>$num</td>";
						$content.= "<td>$date_rec</a></td>";
						$content.= "<td><a href=\"form_medical.php?id=$id\">$flight</a></td>";
						$content.="<td>$customer</td></tr>";
						$num+=1;
		}
		$content.="</table>";
			
	Show_page($content);
	mysqli_close($db_server);
?>
	