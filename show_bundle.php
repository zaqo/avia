<?php require_once 'login_avia.php';
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
		
			$check_in_mysql="SELECT bundle_reg.service_id,services.description,services.id_NAV FROM bundle_reg
								LEFT JOIN services ON bundle_reg.service_id=services.id
									WHERE bundle_id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into bundle_reg TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Содержание пакета услуг № $id</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Услуга</th><th>Описание</th></tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$desc=$row[1];
				$id_NAV=$row[2];
				
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$id_NAV</td><td>$desc</td>";
				
				$content.= '</tr>';
				
			$counter+=1;
			
		}
			$content.= '</table>';
			Show_page($content);
		mysqli_close($db_server);
		}
		else
			echo "ERROR: Package ID is not provoded! <\br>";
?>
	