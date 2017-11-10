<?php // header.php
	session_start();
	?>
	<html lang="ru">
		<head>
			<script src="/re/js/OSC.js"></script>
			<script src="/re/js/menu.js"></script>
			<script src="/re/js/re_input.js" defer></script>
			<script src="/re/js/form_submit.js"></script>
			<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		
			<link rel="stylesheet" type="text/css" href="/re/css/style_w3.css" />
			<link rel="stylesheet" href="/re/css/w3.css">
			<!--[if lt IE 9]> 
			<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
			<script type="text/javascript" src="./js/jquery.js"></script>
			<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
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
	echo "<title>Медицина</title>".
	"</head><body>";
	$status=0; // Delete it later on
	if ($loggedin)
	{
		/*if($status==0) //full access
		{
			echo "<div class='dropdown'>
				<button onclick='myFunction()' class='dropbtn'>Меню</button>
				<div id=\"myDropdown\" class=\"dropdown-content\">
				<a href=\"select_tenant.php\">Выбор арендатора</a>
				<a href=\"contract_form.php\">Новый контракт</a>
				<a href=\"select_month.php\">Создать счета за месяц</a>
				<a href=\"logout.php\">Выйти из системы</a>
				</div>
			</div><hr>
			";//<div class=\"userid\">Вы вошли в систему как: $userstr</div>
		}
		
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
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<style>
body,h1,h2,h3,h4,h5 {font-family: "Poppins", sans-serif}
body {font-size:16px;}
.w3-half img{margin-bottom:-6px;margin-top:16px;opacity:0.8;cursor:pointer}
.w3-half img:hover{opacity:1}
</style>


<!-- Sidebar/menu -->
<nav class="w3-sidebar w3-grey w3-collapse w3-top w3-large w3-padding" style="z-index:3;width:300px;font-weight:bold;" id="mySidebar"><br>
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-button w3-hide-large w3-display-topleft" style="width:100%;font-size:22px">Close Menu</a>
  <div class="w3-container">
    <h3 class="w3-padding-64"><b>MEDICAL<br>invoicing</b></h3>
  </div>
  <div class="w3-bar-block">
    <a href="/re/select_client.php" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white">Авиакомпании</a> 
    <a href="/re/select_other.php" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white">Прочие клиенты</a> 
    
  </div>
</nav>

<!-- Top menu on small screens -->
<header class="w3-container w3-top w3-hide-large w3-red w3-xlarge w3-padding">
  <a href="javascript:void(0)" class="w3-button w3-red w3-margin-right" onclick="w3_open()">☰</a>
  <span>ПУЛКОВО</span>
  </header>
