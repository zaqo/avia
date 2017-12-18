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
		
		// Get the service description
		$service_name="SELECT services.description
								FROM services
								WHERE id=$id ";
					
					$answsql=mysqli_query($db_server,$service_name);
					if(!$answsql) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		$row_srv=mysqli_fetch_row($answsql);
		$desc_srv=$row_srv[0];
		
		// Main bundle info section
			$check_in_mysql="SELECT services.description,services.id_NAV,bundle_content.quantity
								FROM bundle_content
								LEFT JOIN services ON bundle_content.service_id=services.id
									WHERE bundle_content.bundle_id=$id ORDER BY services.id_NAV";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into bundle_reg TABLE failed: ".mysqli_error($db_server));
		
		// Top of the table
		$counter=0;
		
		$content.= "<table class='fullTab'><caption><b>Содержание пакета услуг: </b><br/>$desc_srv</caption><br/>";
		$content.= '<tr><th>№ </th><th>Услуга</th><th>Количество</th><th>Описание</th></tr>';
		// Iterating through the array
		
	
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$counter+=1;
				$desc=$row[0];
				$id_NAV=$row[1];
				$qty=$row[2];
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
	