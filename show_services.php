<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT services.id,id_NAV,id_SAP,isforKids,isValid,date_booked,units.description_rus,description
								FROM services
								LEFT JOIN units ON services.id_mu=units.id
								WHERE 1 ORDER BY id_NAV";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= '<h2>Перечень услуг</h2>';
		$content.= '<div class="table-responsive">';
		$content.= '<table class="table table-striped table-sm ml-1">';
		$content.= "<thead>";
		$content.= '<tr><th>№ </th><th>Услуга</th><th>Код NAV</th><th>Код SAP</th><th>Ед.изм</th>
					<th>Для детей</th><th>Действует</th></tr></thead>';
		$content.= "<tbody>";
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$nav_id=$row[1];
				$sap_id=$row[2];
				$iskids=$row[3];
				$isvalid=$row[4];
				$date=$row[5];
				$mu=$row[6];
				$desc=$row[7];
				
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$desc</td>";
				$content.= "<td><a href=\"edit_service.php?id=$rec_id\">$nav_id</a></td>";
				$content.= "<td>$sap_id</td>";
				$content.= "<td>".$mu."</td>";
				
				if ($row[3])
					$content.= "<td>Да</td>";
				else
					$content.= "<td>-</td>";
				
				if ($row[4])
					$content.= "<td>Да</td>";
				else
					$content.= "<td>-</td>";
				
		//$content.= "<td>$date</td>";
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</tbody>';
		$content.= '</table>';
		$content.= '</div>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	