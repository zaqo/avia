<?php require_once 'login_avia.php';

include ("header.php"); 
	
		if(isset($_REQUEST['id']))
		{
			$id		= $_REQUEST['id'];
			$content="";
			$content_list="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT date_set,composition,name_rus,from_val,to_val,enum_of_values,discount_conditions.condition_id,discounts_individual.name
							FROM discount_ind_content 
							LEFT JOIN discount_conditions ON discount_ind_content.condition_id=discount_conditions.id 
							LEFT JOIN discounts_individual ON discounts_individual.id=discount_ind_content.discount_id
							WHERE discount_id=$id AND discount_conditions.isValid=1 AND discount_ind_content.isValid=1
							ORDER BY sequence";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into packages TABLE failed: ".mysqli_error($db_server));
		// Top of the conditions table
		
		$content.= '<div class="container">';
		$content.= '<div class="row">';
			$content.= '<div class="col-sm-6">';
				$content.= '<div class="card mt-5" style="width: 18rem;">
						<div class="card-header">
							Скидка: # '.$id.'
						</div>';
					$content.= '<ul class="list-group list-group-flush">';
		
		
		//$content.= '<tr><th>№ </th><th>Название</th><th>От:</th><th>До:</th><th>Перечисление</th><th>Условие</th><th>Дата</th></tr>';
		// Iterating through the array
		$counter=1;
		
			while( $row = mysqli_fetch_row( $answsqlcheck ))  
			{ 
				$date_set=$row[0];
				$composition=$row[1];
				$name_rus=$row[2];
				$to='';
				$from='';
			
				if($row[3]) $from=$row[3];
				if($row[4]) $to=$row[4];
				
				$enum=$row[5];
				$cond=$row[6];
				$disc_name=$row[7];
				// 
				$cond_str='';	
				switch($cond)
				{
					case 0:
						$cond_str='=';
						break;
					case 1:
						$cond_str='<';
						break;
					case 2:
						$cond_str='=<';
						break;
					case 3:
						$cond_str='>';
						break;
					case 4:
						$cond_str='>=';
						break;
					case 5:
						$cond_str='><';
						break;
					case 6:
						$cond_str='[...]';
						break;
					case 7:
						$cond_str='...][...';
						break;
				}
					
				//$content.= '<li class="list-group-item">';
					//$content.= '<span>'.$counter.'</span>';
					$content_list.= '<li class="list-group-item">Условие: '.$name_rus.'</li>';
					$content_list.= '<li class="list-group-item">Значение: ';
					if($from) 
					{	
						$content_list.=$from;
						if($to) ' - '.$to;
					}
					else if($enum)
					$content_list.=$enum;
					$content_list.='</li>';
	
					$content_list.= '<li class="list-group-item">Сравнение: '.$cond_str.'</li>';
					$content_list.= '<li class="list-group-item">Дата: '.$date_set.'</li>';
					
					$counter+=1;
			}
			$content.= '<li class="list-group-item active"> Название: '.$disc_name.'</li>';
			$content.= $content_list;
			$content.= '</ul>';
			$content.= '</div>';
			$content.= '</div>';
			$content.= '<div class="col-sm-6">';
			// Top of the services table
				$content.= '<div class="card mt-5" style="width: 18rem;">
						<div class="card-header">
							Действие Скидки: # '.$id.'
						</div>';
				$content.= '<ul class="list-group list-group-flush">';
		
		
		$check_services="SELECT discounts_ind_reg.id,services.id_NAV,services.description 
							FROM discounts_ind_reg 
							LEFT JOIN services 
							ON discounts_ind_reg.service_id=services.id 
							WHERE discount_id=$id AND discounts_ind_reg.isValid=1";
					
					$answsql2=mysqli_query($db_server,$check_services);
					if(!$answsql2) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		
		// Iterating through the array
		$counter=1;
		
			while( $row_serv = mysqli_fetch_row( $answsql2 ))  
			{ 
				$id_NAV=$row_serv[1];
				
				$name_serv_rus=$row_serv[2];
				
					
				$content.= '<li class="list-group-item">'.$counter.'. ';
				$content.= "<span>$id_NAV</span> ";
				$content.= " <span>$name_serv_rus</span>";
				
				
				$content.= '</li>';
				
				$counter+=1;
			
			}
			$content.= '</ul>';
			$content.= '</div>';
			$content.= '</div>';
			$content.= '</div>';
			//And now SHOW
			Show_page($content);
		mysqli_close($db_server);
		}
		else
			echo "ERROR: Package ID is not provoded! <\br>";
?>
	