<?php // header.php
	session_start();
	?>
	<html lang="ru">
		<head>
			<script src="/avia/js/OSC.js"></script>
			<script src="/avia/js/menu.js"></script>
			<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		<link rel="stylesheet" href="/avia/css/jquery.minical.plain.css" type="text/css">
			<link rel="stylesheet" type="text/css" href="/avia/css/style.css" />
			<!--[if lt IE 9]> 
			<script type="text/javascript" src="./js/html5.js"></script>
			<![endif]-->
			<!--<script type="text/javascript" src="./js/jquery.js"></script>-->
			<script src="/avia/js/jquery-3.1.1.js"></script>
			<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
			
			<script type="text/javascript" src="/avia/js/jquery.minical.plain.js"></script>
			<script src="/avia/js/myFunctions.js"></script>
<?php
	include_once 'functions.php';
	
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
				<button onclick='myFunction()' class='dropbtn'>Загрузка</button>
				<div id=\"myDropdown\" class=\"dropdown-content\">
				<a href=\"import_daily.php\">Импорт <-| NAV</a>
				<a href=\"pairs_daily.php\">Экспорт |-> SAP ERP</a>
				</div>
			</div>
			";
			echo "<div class='dropdown'>
				<button onclick='myFunction2()' class='dropbtn'>Услуги</button>
				<div id=\"myDropdown2\" class=\"dropdown-content\">
				<a href=\"show_services.php\">Услуги</a>
				<a href=\"create_service.php\">Создать Услугу</a>
				<a href=\"check_packages.php\">Пакеты услуг</a>
				<a href=\"create_package.php\">Создать пакет</a>
				</div>
			</div>
			";//<div class=\"userid\">Вы вошли в систему как: $userstr</div>
			echo "<div class='dropdown'>
				<button onclick='myFunction3()' class='dropbtn'>Скидки</button>
				<div id=\"myDropdown3\" class=\"dropdown-content\">
				<a href=\"show_discounts_all.php\">Скидки</a>
				<a href=\"create_discount_ind.php\">Создать Скидку</a>
				</div>
			</div>
			";
			echo "<div class='dropdown'>
				<button onclick='myFunction4()' class='dropbtn'>Клиенты</button>
				<div id=\"myDropdown4\" class=\"dropdown-content\">
				<a href=\"show_contracts.php\">Контракты</a>
				<a href=\"create_contract.php\">Создать Контракт</a>
				<a href=\"show_contracts.php\">Клиенты</a>
				<a href=\"create_contract.php\">Создать Клиента</a>
				</div>
			</div>
			";
			echo "<div class='dropdown'>
				<button onclick='myFunction5()' class='dropbtn'>Прочее</button>
				<div id=\"myDropdown5\" class=\"dropdown-content\">
				<a href=\"show_mus.php\">Единицы измерения</a>
				<a href=\"create_mu.php\">Добавить ед.изм.</a>
				</div>
			</div>
			";
			echo '<hr>';
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
?>