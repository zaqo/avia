<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT id,description_rus,description_en
								FROM units
								WHERE 1";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Ед.Измерения</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Название (rus)</th><th>Название (en)</th>
					</tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$name_rus=$row[1];
				$name_en=$row[2];
				
				
				$content.= "<tr><td><a href=\"edit_mu.php?id=$rec_id\">$counter</td>";
				$content.= "<td>$name_rus</a></td>";
				$content.= "<td>$name_en</td>";
				
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	