<?php require_once 'login_avia.php';
// SHOWS CONTENT OF THE FLIGHT
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
		
			$check_in_mysql="SELECT * FROM flights
							WHERE id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into flights TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$row = mysqli_fetch_row( $answsqlcheck );
		$content.= '<div class="container"><div class="card" style="width: 18rem;">
						<div class="card-header">
							Данные по рейсу # '.$row[3].'
						</div>';
		$content.= '<ul class="list-group list-group-flush">';
		
		// Iterating through the array
		
				if ($row[11]) $pass_adults=$row[11];
				else $pass_adults='-';
				if ($row[12]) $pass_kids=$row[12];
				else $pass_kids='-';
				$content.= '<li class="list-group-item">ID: '.$row[1].'</li>';
				$content.= '<li class="list-group-item">Дата: '.$row[2].'</li>';
				$content.= '<li class="list-group-item">Номер рейса: '.$row[3].'</li>';
				if($row[4]) $dir='ВЫЛЕТ';
				else $dir='ПРИЛЕТ';
				$content.= '<li class="list-group-item">Направление: '.$dir.'</li>';
				$content.= '<li class="list-group-item">Бортовой Номер: '.$row[7].'</li>';
				$content.= '<li class="list-group-item">Тип ВС: '.$row[21].'</li>';
				
				$content.= '<li class="list-group-item">Макс.Взл.Масса: '.number_format($row[9], 0, ',', ' ').' кг.</li>';
				if($row[6]) $heli='ДА';
				else $heli='НЕТ';
				$content.= '<li class="list-group-item">Вертолет: '.$heli.'</li>';
				$content.= '<li class="list-group-item">Аэропорт: '.$row[10].'</li>';
				$content.= '<li class="list-group-item">Пассажиры ВЗР: <span class="badge badge-primary badge-pill">'.$pass_adults.'</span></li>';
				$content.= '<li class="list-group-item">Пассажиры ДЕТИ:<span class="badge badge-primary badge-pill"> '.$pass_kids.'</span></li>';
				$content.= '<li class="list-group-item">Владелец ВС: '.$row[15].'</li>';
				$content.= '<li class="list-group-item">Категория полета: '.$row[19].'</li>';
				$content.= '<li class="list-group-item">Время факт.: '.$row[20].'</li>';
				$content.= '<li class="list-group-item">Импортировано: <small>'.$row[22].'</small></li>';
			$content.= '</li>';
			$content.= '</ul>';
			$content.= '</div></div>';
			Show_page($content);
		mysqli_close($db_server);
		}
		else
			echo "ERROR: Package ID is not provoded! <\br>";
?>
	