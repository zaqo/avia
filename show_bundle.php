﻿<?php require_once 'login_avia.php';
// SHOWS CONTENT OF THE PACKAGE OF SERVICES
include ("header.php"); 
	
		if(isset($_REQUEST['id']))
		{
			$id		= $_REQUEST['id'];
			$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT bundle_content.service_id,services.description,services.id_NAV,clients.name,bundle_content.quantity
								FROM bundle_content
								LEFT JOIN services ON bundle_content.service_id=services.id
								LEFT JOIN bundle_reg ON bundle_content.bundle_id=bundle_reg.bundle_id
								LEFT JOIN clients ON bundle_reg.client_id=clients.id
									WHERE bundle_content.bundle_id=$id ORDER BY services.id_NAV";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into bundle_reg TABLE failed: ".mysqli_error($db_server));
		$row=mysqli_fetch_row($answsqlcheck);
		// Top of the table
		$counter=1;
		$client=$row[3];
		$qty=$row[4];
		$content.= "<table class='fullTab'><caption><b>Содержание пакета услуг № $id, клиент: $client</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Услуга</th><th>Количество</th><th>Описание</th></tr>';
		// Iterating through the array
		
		$desc=$row[1];
		$id_NAV=$row[2];
				
		$content.= "<tr><td>$counter</td>";
		$content.= "<td>$id_NAV</td><td>$qty</td><td>$desc</td>";
				
		$content.= '</tr>';
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$counter+=1;
				$desc=$row[1];
				$id_NAV=$row[2];
				$qty=$row[4];
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$id_NAV</td><td>$qty</td><td>$desc</td>";
				
				$content.= '</tr>';
				
			
			
		}
			$content.= '</table>';
			Show_page($content);
		mysqli_close($db_server);
		}
		else
			echo "ERROR: Package ID is not provoded! <\br>";
?>
	