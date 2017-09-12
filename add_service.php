<?php require_once 'login_avia.php';
//LINKING SERVICES TO THE DISCOUNT
include ("header.php"); 
	
	if(isset($_REQUEST['id'])) $disc_id	= $_REQUEST['id'];
	if(isset($_REQUEST['isGroup'])) $isGroup= $_REQUEST['isGroup'];
	
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// Prepare list of services
			$check_individual='SELECT services.id,services.id_NAV,services.description,units.description_rus 
								FROM services 
								LEFT JOIN units ON services.id_mu=units.id
								WHERE isValid=1 ORDER BY services.id_NAV';
					
			$answsqlcheck=mysqli_query($db_server,$check_individual);
			if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		
		// Top of the table
		$content.= '<table><caption><b>Перечень услуг</b></caption><br>';
		$content.='<form id="form" method=post action=link_service.php >';
		$content.= '<tr><th>&</th><th>№</th><th>Код</th><th>Название</th><th>Ед.изм.</th></tr>';
		// Iterating through the array
		$counter=1;	
				
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				
				$rec_id=$row[0];
				$NAV_id=$row[1];
				$name=$row[2];
				$mu_desc=$row[3];
				
				$content.="<tr><td><input type=\"checkbox\" name=\"to_export[]\" class=\"services\" value=\"$rec_id\" /></td>";
				$content.= "<td>$counter</td>";
				$content.= "<td>$NAV_id</a></td>";
				$content.= "<td>$name</a></td>";
				$content.= "<td>$mu_desc</a></td>";
				//$content.= '<td><a href="localhost/avia/add_condition.php?id='.$rec_id.'">Добавить условие</a></td></tr>';
				
			$counter+=1;
			
		}
		$content.='<input type="hidden" name="isGroup" value="'.$isGroup.'"><input type="hidden" name="disc_id" value="'.$disc_id.'">';
		$content.= '<tr><td colspan="5"><input type="submit" name="send" class="send" value="ВВОД"></td></tr></form>';
		$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	