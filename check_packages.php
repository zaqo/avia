<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT id,name,client_id,isValid,date_booked FROM packages
									WHERE 1";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into packages TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Шаблоны услуг по рейсу</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Название</th><th>Код клиента</th><th>Действует</th><th>Дата</th></tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$name=$row[1];
				$client=$row[2];
				$date=$row[4];
				$content.= "<tr><td>$counter</td>";
				$content.= "<td><a href=\"edit_package.php?id=$rec_id\">$name</a></td>";
				$content.= "<td>$client</td>";
				if ($row[3])
					$content.= "<td>Да</td>";
				else
					$content.= "<td>Нет</td>";
		$content.= "<td>$date</td>";
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	