<?php 
/* 
	SHOWS PROCESS SETTINGS FOR OUT OF THE GENERAL RULE CLIENTS
*/
require_once 'login_avia.php';
include ("header.php"); 	
		
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//GET ALL DATA
	
			$select_exc='SELECT exc_process.id,clients.id,clients.name,sequence,hasConditions,services.description,services.id_NAV,
								t1.description,t1.id_NAV,t2.description,t2.id_NAV,t3.description,t3.id_NAV,
								services.id,t1.id,t2.id,t3.id
							FROM exc_process 
							LEFT JOIN services ON service_id=services.id
							LEFT JOIN clients ON client_id=clients.id
							LEFT JOIN exc_default ON exc_default.exc_id=exc_process.id
							LEFT JOIN services AS t1 ON exc_default.svs_kids_id=t1.id
							LEFT JOIN services AS t2 ON exc_default.exc_svs_id=t2.id
							LEFT JOIN services AS t3 ON exc_default.exc_svs_kids_id=t3.id
							WHERE exc_process.isValid
							ORDER BY clients.id, sequence';
					
					$answsql=mysqli_query($db_server,$select_exc);
					if(!$answsql) die("SELECT into exc_process TABLE failed: ".mysqli_error($db_server));

// Top of the table
		$content.= '<h2 class="mt-2 ml-2">Исключения в расчете цены рейса</h2>';
		$content.= '<div class="table mt-2 ml-2 w-75">';
		$content.= '<table class="table  table-hover table-sm ml-1" onload="JavaScript:AutoRefresh(5000)">';
		$content.= '<thead class="">';
		$content.= '<tr><th></th><th></th><th></th></thead>';
		$content.= "<tbody>";
		
		//$content.= '<table class="myTab"><caption><b> в расчете цены для рейса</b></caption>
		//			<tr><th class="col80"></th><th class="col500"></th><th class="col1"></th></tr>';		
		
		$client_last=0;
		$step_last=0;
		while ($row = mysqli_fetch_row( $answsql))
		{	
			$exc_id=$row[0];
			$client_id=$row[1];
			$client=$row[2];
			$seq=$row[3];
			$condFlag=$row[4];
			$svs_1= $row[5];
			$svs_1_nav= $row[6];
			$svs_2= $row[7];
			$svs_2_nav= $row[8];
			$svs_3= $row[9];
			$svs_3_nav= $row[10];
			$svs_4= $row[11];
			$svs_4_nav= $row[12];
			$svs_1_id= $row[13];
			$svs_2_id= $row[14];
			$svs_3_id= $row[15];
			$svs_4_id= $row[16];
			
			if($client_last!=$client_id) //CLIENTS TOP
			{
				if($client_last) $content.= '<tr><td colspan="3" ></td></tr>';
				$content.= '<tr><td colspan="2"><h4>'.$client.'</h4></td><td ><a href="delete_exc_cl.php?id='.$client_id.'" ><img src="/avia/css/delete.png" alt="Delete" title="Удалить" ></a></td></tr>';
			}
			if(($step_last!=$seq)||($client_last!=$client_id))	// STEP TOP
			{
				if($step_last) $content.= '<tr><td colspan="3" ></td></tr>';
				$content.= '<tr><td colspan="2" >'.$steps[$seq-1].'</td><td ><a href="delete_exc.php?id='.$exc_id.'" ><img src="/avia/css/delete.png" alt="Delete" title="Удалить" ></a></td></tr>';
				
			}
			if($svs_1)
				$content.= '<tr><td>'.$svs_1_nav.'</td><td class="tab_normal">'.$svs_1.'</td><td><a href="edit_exc.php?id='.$exc_id.'&svs=1&svs_id='.$svs_1_id.'" ><img src="/avia/src/pencil.png" alt="Edit" title="Изменить" ></a></td></tr>';
			if($svs_2)
				$content.= '<tr><td>'.$svs_2_nav.'</td><td class="tab_normal">'.$svs_2.'</td><td><a href="edit_exc.php?id='.$exc_id.'&svs=2&svs_id='.$svs_2_id.'" ><img src="/avia/src/pencil.png" alt="Edit" title="Изменить" ></a></td></tr>';
			if($svs_3&&$condFlag)
			{
				$cond_sql='SELECT airports.name_rus FROM exc_conditions
							LEFT JOIN airports ON airports.id=exc_conditions.airport_id
							WHERE	exc_conditions.isValid AND exc_conditions.exc_id='.$exc_id;
				$answsql_airport=mysqli_query($db_server,$cond_sql);
					if(!$answsql_airport) die("SELECT into exc_conditions TABLE failed: ".mysqli_error($db_server));
				$airports='';
				while ($row1 = mysqli_fetch_row( $answsql_airport))
					$airports.=$row1[0].", ";
				$airports=substr($airports,0,-2);
				$content.= '<tr><td colspan="3">ДЛЯ НАПРАВЛЕНИЙ:</td></tr>';
				$content.= '<tr><td colspan="2">'.$airports.'</td><td><a href="edit_exc.php?id='.$exc_id.'&svs=0" ><img src="/avia/src/pencil.png" alt="Edit" title="Изменить" ></a></td></tr>';
				$content.= '<tr><td>'.$svs_3_nav.'</td><td class="tab_normal">'.$svs_3.'</td><td><a href="edit_exc.php?id='.$exc_id.'&svs=3&svs_id='.$svs_3_id.'" ><img src="/avia/src/pencil.png" alt="Edit" title="Изменить" ></a></td></tr>';
				$content.= '<tr><td>'.$svs_4_nav.'</td><td class="tab_normal">'.$svs_4.'</td><td><a href="edit_exc.php?id='.$exc_id.'&svs=4&svs_id='.$svs_4_id.'" ><img src="/avia/src/pencil.png" alt="Edit" title="Изменить" ></a></td></tr>';
			}
			$client_last=$client_id;
			$step_last=$seq;
		}
		
		
		$content.= '</tbody>';
		$content.= '</table>';
		$content.= '<div class="d-flex justify-content-center">';
		$content.= '<a href="add_exception.php" class="btn btn-primary btn-lg active justify-content-center" role="button" aria-pressed="true">Добавить</a>';
		$content.= '</div>';
	$content.= '</div>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);

?>
	