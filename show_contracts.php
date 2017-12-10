<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT contracts.id,clients.name,contracts.id_SAP,contracts.isValid,date_booked,isBased
								FROM contracts
								LEFT JOIN clients ON clients.id=contracts.client_id
								WHERE 1";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Контракты SAP</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Компания</th><th>Контракт</th>
					<th>Действует</th><th>Базирование</th><th>Дата ред.</th></tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$name=$row[1];
				$sap_id=$row[2];
				$isvalid=$row[3];
				$date=$row[4];
				$isBased=$row[5];
				
				$content.= "<tr><td>$counter</td>";
				$content.= "<td><a href=\"edit_contract.php?id=$rec_id\">$name</a></td>";
				$content.= "<td>$sap_id</td>";
				
				
				if ($row[3])
					$content.= "<td>Да</td>";
				else
					$content.= "<td>Нет</td>";
				if ($row[5])
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
	