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
			if($isGroup) $check_svs='SELECT services.id,services.id_NAV,services.description,units.description_rus,discounts_grp_reg.id 
								FROM services 
								LEFT JOIN units ON services.id_mu=units.id
								LEFT JOIN discounts_grp_reg ON ( services.id=discounts_grp_reg.service_id AND discounts_grp_reg.discount_id='.$disc_id.'  AND discounts_grp_reg.isValid)
								WHERE services.isValid ORDER BY services.id_NAV';
			else $check_svs='SELECT services.id,services.id_NAV,services.description,units.description_rus,discounts_ind_reg.id
								FROM services 
								LEFT JOIN units ON services.id_mu=units.id
								LEFT JOIN discounts_ind_reg ON ( services.id=discounts_ind_reg.service_id AND discounts_ind_reg.discount_id='.$disc_id.' AND discounts_ind_reg.isValid)
								WHERE services.isValid ORDER BY services.id_NAV';	
			$answsqlcheck=mysqli_query($db_server,$check_svs);
			if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		
		// Top of the table
		$content.= '<div class="container mt-2">
						<h4 class="mb-3">Выбор слуг для скидки</h4>';
		$content.= '<form id="form" method=post action=link_service.php class="needs-validation" novalidate>';
		$content.= '<div class="table">';
			$content.= '<table class="table table-striped table-sm ">';
				$content.= "<thead>";
					
					$content.= '<tr><th>&</th><th>№</th><th>Код</th><th>Название</th><th>Ед.изм.</th></tr>';
				$content.= "</thead>";
		$content.= "<tbody>";
		
		
		// Iterating through the array
		$counter=1;	
				
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$checked='';
				$rec_id=$row[0];
				$NAV_id=$row[1];
				$name=$row[2];
				$mu_desc=$row[3];
				if($row[4]) $checked='checked ';
				$content.="<tr><td><input type=\"checkbox\" name=\"to_export[]\" class=\"services\" value=\"$rec_id\" ".$checked.'/></td>';
				$content.= "<td>$counter</td>";
				$content.= "<td>$NAV_id</a></td>";
				$content.= "<td>$name</a></td>";
				$content.= "<td>$mu_desc</a></td>";
				//$content.= '<td><a href="localhost/avia/add_condition.php?id='.$rec_id.'">Добавить условие</a></td></tr>';
				
			$counter+=1;
			
		}
		$content.='<input type="hidden" name="isGroup" value="'.$isGroup.'"><input type="hidden" name="disc_id" value="'.$disc_id.'">';
		$content.= '<tr><td colspan="5" class="text-center"><button type="submit" class="btn btn-primary" >ВВОД</button></td></tr></form>';
		$content.= "</tbody>";
		$content.= '</table>';
		$content.= "</div>";
		$content.= "</div>";
	Show_page($content);
	mysqli_close($db_server);
	
?>
	