﻿<?php require_once 'login_avia.php';
/*
 LIST ALL BUNDLES IN THE SYSTEM
by S.Pavlov (c) 2017
 */
 include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT DISTINCT services.id,id_NAV,id_SAP,bundle_reg.class,date_booked,description,bundle_reg.airports
								FROM services
								LEFT JOIN bundle_reg ON (services.id=bundle_reg.bundle_id AND bundle_reg.isValid)
								WHERE isBundle AND services.isValid ORDER BY id_NAV";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= '<div class="container mt-2">';
		$content.= '<h2>Пакеты (все)</h2>';
		$content.= '<div class="table">';
		$content.= '<table class="table table-striped table-sm ">';
		$content.= "<thead>";
		$content.= '<tr><th>№ </th><th>Описание</th><th>ID</th><th>Код NAV</th><th>Код SAP</th>
					<th>Класс ВС</th><th>Аэропорты</th><th>Дата</th></tr>';
		$content.= "<tbody>";
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$nav_id=$row[1];
				$sap_id=$row[2];
				$class=$row[3];
				$date=$row[4];
				$desc=$row[5];
				$airports=$row[6];
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$desc</td>";
				$content.= "<td>$rec_id</td>";
				$content.= "<td><a href=\"show_bundle.php?id=$rec_id\">$nav_id</a></td>";
				$content.= "<td>$sap_id</td>";
				$content.= "<td>$class</td>";
				$content.= "<td>$airports</td>";
		        $content.= "<td>$date</td>";
				$content.= '</tr>';	
			$counter+=1;
			
		}
		$content.= '</tbody>';
		$content.= '</table>';
		$content.= '</div>';
		$content.= '</div>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	