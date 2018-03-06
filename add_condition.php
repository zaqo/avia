<?php require_once 'login_avia.php';
//LISTING ALL CONDITIONS IN THE SYSTEM
include ("header.php"); 
	
	if(isset($_REQUEST['id'])) $disc_id	= $_REQUEST['id']; // ID OF THE RELEVANT DISCOUNT
	if(isset($_REQUEST['isGroup'])) $isGroup= $_REQUEST['isGroup'];
	
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// Prepare list of conditions
		if ($isGroup)
			$check_dsc='SELECT discount_conditions.id,name_rus,param_id,from_val,to_val,enum_of_values,discount_conditions.condition_id,
						discount_grp_content.id, discounts_group.name 
						FROM discount_conditions 
						LEFT JOIN discount_grp_content 
						ON (discount_conditions.id=discount_grp_content.condition_id AND discount_grp_content.isValid=1
						AND discount_grp_content.discount_id='.$disc_id.')			
						LEFT JOIN discounts_group ON discounts_group.id='.$disc_id.'
						WHERE discount_conditions.isValid=1 ORDER BY discount_conditions.id';
		else
			$check_dsc='SELECT discount_conditions.id,name_rus,param_id,from_val,to_val,enum_of_values,discount_conditions.condition_id,
						discount_ind_content.id, discounts_individual.name 
						FROM discount_conditions 
						LEFT JOIN discount_ind_content 
						ON (discount_conditions.id=discount_ind_content.condition_id AND discount_ind_content.isValid=1
						AND discount_ind_content.discount_id='.$disc_id.')			
						LEFT JOIN discounts_individual ON discounts_individual.id='.$disc_id.'
						WHERE discount_conditions.isValid=1 ORDER BY discount_conditions.id';
					
			$answsqlcheck=mysqli_query($db_server,$check_dsc);
			if(!$answsqlcheck) die("LOOKUP into discounts_individual TABLE failed: ".mysqli_error($db_server));

		
		// Top of the table
		
		$content.= '<div class="container mt-2">
						<h4 class="mb-3">Выбор условия для скидки</h4>';
		$content.= '<form id="form" method=post action=link_discount.php class="needs-validation" novalidate>';
		$content.= '<div class="table">';
			$content.= '<table class="table table-striped table-sm ">';
				$content.= "<thead>";
					$content.= '<tr><th>&</th><th>№</th><th>Название</th><th>Параметр</th><th>От:</th><th>До:</th><th>Перечисление:</th><th>Операция:</th></tr>';
		$content.= "</thead>";
		$content.= "<tbody>";
		//$content.= '<tr><th>&</th><th>№</th><th>Название</th><th>Параметр</th><th>Значение,от:</th><th>Значение, до:</th><th>Перечисление:</th><th>Сравнение</th></tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$val_to='';
				$val_from='';
				$sel='';
				$rec_id=$row[0];
				$name=$row[1];
				$param=$row[2];
				if($row[3]) $val_from=$row[3];
				if($row[4]) $val_to=$row[4];
				
				$val_enum=$row[5];
				$condition=$row[6];
				
				if($row[7]) $sel='checked';
				// Visualize condition
				$cond_char='';
				switch ($condition)
				{
					case 0:
						$cond_char='=';
						break;
					case 1:
						$cond_char='<';
						break;
					case 2:
						$cond_char='<=';
						break;
					case 3:
						$cond_char='>';
						break;
					case 4:
						$cond_char='>=';
						break;
					case 5:
						$cond_char='><';
						break;
					case 6:
						$cond_char='[]';
						break;
					case 7:
						$cond_char='][';
						break;
					default:
						echo 'WARNING: CONDITION IS NOT DEFINED! <br/>';	
				}
				// Visualize the param
				// Prepare list of params
				$check_params="SELECT name_rus 
								FROM params 
								WHERE id=$param";
					
				$paramcheck=mysqli_query($db_server,$check_params);
				if(!$paramcheck) die("LOOKUP into params TABLE failed: ".mysqli_error($db_server));
				$param_name = mysqli_fetch_row( $paramcheck );
				$content.='<tr><td><input type="checkbox" name="to_export[]" class="" value="'.$rec_id.'"  '.$sel.'/></td>';
				$content.= "<td>$counter</td>";
				$content.= "<td>$name</a></td>";
				$content.= "<td>".$param_name[0]."</td><td>$val_from</td><td>$val_to</td><td>$val_enum</td>";
				$content.= "<td>$cond_char</td>";
				//$content.= '<td><a href="localhost/avia/add_condition.php?id='.$rec_id.'">Добавить условие</a></td></tr>';
			$disc_name=$row[8];
			$counter+=1;
			
		}
		$content.='<input type="hidden" name="isGroup" value="'.$isGroup.'"><input type="hidden" name="disc_id" value="'.$disc_id.'">';
		$content.= '<tr><td colspan="8" class="text-center"><button type="submit" class="btn btn-primary" >ВВОД</button></td></tr></form>';
		$content.= "</tbody>";
		$content.= '</table>';
		$content.= "</div>";
		$content.= "</div>";
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	