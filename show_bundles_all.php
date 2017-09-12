<?php require_once 'login_avia.php';
// LIST ALL BUNDLES IN THE SYSTEM
include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT id,id_NAV,id_SAP,isValid,date_booked,description
								FROM services
								WHERE isBundle=1 ORDER BY id_NAV";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Пакеты услуг</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Описание</th><th>Код NAV</th><th>Код SAP</th>
					<th>Действует</th><th>Дата</th></tr>';
		
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$nav_id=$row[1];
				$sap_id=$row[2];
				$isvalid=$row[3];
				$date=$row[4];
				$desc=$row[5];
				
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$desc</td>";
				$content.= "<td><a href=\"show_bundle.php?id=$rec_id\">$nav_id</a></td>";
				$content.= "<td>$sap_id</td>";
				
				if ($row[3])
					$content.= "<td>Да</td>";
				else
					$content.= "<td>-</td>";
				
		        $content.= "<td>$date</td>";
				
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	