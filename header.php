<?php // header.php
	session_start();
	?>
	<html lang="ru">
		<head>
			<script src="/avia/js/OSC.js"></script>
			<script src="/avia/js/menu.js"></script>
			<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		
			<link rel="stylesheet" type="text/css" href="/avia/css/style.css" />
			<!--[if lt IE 9]> 
			<script type="text/javascript" src="./js/html5.js"></script>
			<![endif]-->
			<!--<script type="text/javascript" src="./js/jquery.js"></script>-->
			<script src="./js/jquery.min.js"></script>
<?php
	include 'functions.php';
	
	if (isset($user))
	{
		unset($user);
	}
	$userstr = '';
	if (isset($_SESSION['user']))
	{
		$user = $_SESSION['user'];
		$loggedin = TRUE;
		$status = $_SESSION['status'];
		$userstr = " ($user)";
	}
	else $loggedin = TRUE; //FALSE;
	echo "<title>Avia</title>".
	"</head><body>";
	$status=0; // Delete it later on
	if ($loggedin)
	{
		if($status==0) //full access
		{
			echo "<div class='dropdown'>
				<button onclick='myFunction()' class='dropbtn'>Меню</button>
				<div id=\"myDropdown\" class=\"dropdown-content\">
				<a href=\"export_daily.php\">Экспорт рейсов в SAP ERP</a>
				<a href=\"logout.php\">Выйти из системы</a>
				</div>
			</div>
			";//<div class=\"userid\">Вы вошли в систему как: $userstr</div>
		}
		/*
		elseif($status==1)  //Shift watchers
		{
			echo "<div class='dropdown'>
				<button onclick='myFunction()' class='dropbtn'>Меню</button>
				<div id=\"myDropdown\" class=\"dropdown-content\">
				<a href=\"start_mssql_guest.php\">График на сегодня</a>
				<a href=\"start_mssql_yesterday_guest.php\">Отчет: ВЧЕРА</a>
				<a href=\"start_mssql_daybeforeyesterday_guest.php\">Отчет:ПОЗАВЧЕРА</a>
				<a href=\"pers_rec_show.php\">Данные сотрудника</a>
				<a href=\"logout.php\">Выйти из системы</a>
				</div>
			</div>
			<div class=\"userid\">Вы вошли в систему как: $userstr</div>";
		}
		elseif($status==2) //Shift leaders
		{
			echo "<div class='dropdown'>
				<button onclick='myFunction()' class='dropbtn'>Меню</button>
				<div id=\"myDropdown\" class=\"dropdown-content\">
				<a href=\"start_mssql.php\">График на сегодня</a>
				<a href=\"start_mssql_yesterday.php\">Отчет: ВЧЕРА</a>
				<a href=\"start_mssql_daybeforeyesterday.php\">Отчет:ПОЗАВЧЕРА</a>
				<a href=\"pers_rec_show.php\">Данные сотрудника</a>
				<a href=\"search_by_flight.php\">Поиск по рейсу</a>
				<a href=\"logout.php\">Выйти из системы</a>
				</div>
			</div>
			<div class=\"userid\">Вы вошли в систему как: $userstr</div>";
		}*/
	}
	/*
	else
	{
		echo "<div class=\"dropdown\">
		<button onclick=\"myFunction()\" class=\"dropbtn\">Меню</button>
		<div id=\"myDropdown\" class=\"dropdown-content\">
			<a href='login.php'>Вход в систему</a>
		</div>
		</div>";
// Для просмотра этой страницы нужно войти на сайт
	} */
?></body></html>