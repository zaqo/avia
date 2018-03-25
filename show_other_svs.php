<?php 
/*

		THIS SCRIPT LISTS CUTE DCS & ALL OTHER SERVICES APPLIED BY DEFAULT TO A FLIGHT FOR A GIVEN CLIENT 

*/
require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql='SELECT services.id_NAV,services.description,clients.name,other_svs.date,other_svs.id
								FROM other_svs
								LEFT JOIN services ON services.id=other_svs.service_id
								LEFT JOIN clients ON clients.id=other_svs.client_id
								WHERE other_svs.isValid ORDER BY clients.id';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into other_svs TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= '<h2 class="mt-2 ml-2">Дополнительные услуги в рейсе</h2>';
		$content.= '<div class="table mt-2 ml-2 w-75">';
		$content.= '<table class="table table-striped table-hover table-sm ml-1" onload="JavaScript:AutoRefresh(5000)">';
		$content.= '<thead class="">';
		$content.= '<tr><th>№ </th><th>Компания</th><th>Название услуги</th><th>Дата</th><th></th></tr></thead>';
		$content.= "<tbody>";
		
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
	
				$desc=$row[0].' | '.$row[1];
				$client=$row[2];
				$date=$row[3];
				$exc_id=$row[4];
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$client</td>";
				$content.= "<td>$desc</td>";
		        $content.= "<td>$date</td>";
				$content.= '<td><a href="delete_osvs.php?id='.$exc_id.'"><img src="/avia/css/delete.png" alt="Delete" title="Удалить" ></a></td>';
				$content.= '</tr>';	
			$counter+=1;
			
		}
		$content.= '</tbody>';
		$content.= '</table>';
		
		$content.= '<div class="d-flex justify-content-center">';
		$content.= '<a href="add_osvs.php" class="btn btn-primary btn-lg active justify-content-center" role="button" aria-pressed="true">Добавить</a>';
		$content.= '</div>';
		$content.= '</div>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	