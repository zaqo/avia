<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT clients.id,clients.id_NAV,clients.name,clients.id_SAP,isRusCarrier,contracts.id_SAP,service_nick.nick,packages.name 
							FROM clients 
							LEFT JOIN contracts ON clients.id=contracts.client_id AND contracts.isValid
							LEFT JOIN bundle_reg ON clients.id=bundle_reg.client_id AND bundle_reg.isValid=1
							LEFT JOIN package_reg ON clients.id=package_reg.client_id AND package_reg.isValid=1
							LEFT JOIN packages ON packages.id=package_reg.package_id AND packages.isValid
							LEFT JOIN service_nick ON bundle_reg.bundle_id=service_nick.service_id 
							WHERE 1
							ORDER by clients.id ";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		
		$content.= '<h2>Перечень авиакомпаний</h2>';
		$content.= '<div class="table-responsive">';
		$content.= '<table class="table table-striped table-hover table-sm ml-3 mr-1 mt-1">';
		$content.= '<thead>';
		$content.= '<tr><th>ID</th><th>Название компании</th><th>Код NAV</th><th>Код SAP</th>
					<th>Контракт</th><th>Пакеты услуг</th><th>Шаблоны услуг</th><th></th>
					</tr>';
		$content.= '<tbody>';
		// Iterating through the array
		$counter=1;
		$rec_prev=0;
		$isFirst=1;
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$nav_id=$row[1];
				$name=$row[2];
				$id_SAP=$row[3];
				$isRus=$row[4];
				$contract_id=$row[5];
				$pack_nick=$row[6];
				$template=$row[7];
				if($rec_prev==$rec_id)
				{
					if($pack_nick) $content_pack.=', '.$pack_nick;
					//if($template) $content_templ.=', '.$template;
					$rec_prev=$rec_id;
				}
				else
				{
				
					if($isFirst) $isFirst=0;
					else 
					{	
						$content.='<td>'.$content_pack.'</td>';
						$content.='<td>'.$content_templ.'</td>';
						$content.= "<td><a href=\"edit_client.php?id=$rec_prev\">Редактировать</a></td></tr>";
					}
					$content_pack='';
					$content_templ='';
					$content.= "<td>$rec_id</td>";
					$content.= "<td><a href=\"show_client.php?id=$rec_id\">$name</a></td>";
					$content.= "<td>$nav_id</td>";
					$content.= "<td>$id_SAP</td>";
					$content.= "<td>$contract_id</td>";
					
					$content_pack=$pack_nick;
					$content_templ=$template;
					$rec_prev=$rec_id;
				}
				
			$counter+=1;
			
		}
		$content.='<td>'.$content_pack.'</td>';
		$content.='<td>'.$content_templ.'</td>';
		$content.= "<td><a href=\"edit_client.php?id=$rec_prev\">Редактировать</a></td></tr>";
		$content.= '</tbody>';
		$content.= '</table>';
		$content.= '</div>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	