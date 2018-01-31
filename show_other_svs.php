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
		$content.= '<table class="myTab"><caption><b>Дополнительные услуги в рейсе</b></caption><br>';
		$content.= '<tr><th class="col1">№ </th><th class="col120">Компания</th><th class="col300">Название услуги</th><th class="col90">Дата</th><th class="col1"></th></tr>';
		
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
		$content.= '</table>';
		$content.= '<div class="center"><a href="add_osvs.php><img src="/avia/src/red_plus_small.png" alt="ADD" title="Добавить" ></a></div>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	