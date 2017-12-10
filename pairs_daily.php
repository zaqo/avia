<?php
/*Start of EXPORT by PAIRS*/

include ("login_avia.php"); 
include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
?>
<html lang="ru">
	<head>
		<title>Выберите день</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		
		<link rel="stylesheet" type="text/css" href="/Agents/css/style.css" />
		<!--[if lt IE 9]> 
			<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<!--<script type="text/javascript" src="./js/jquery.js"></script>-->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	</head>
	<body>
	 

		<h1 align="center">Укажите Дату: </h1>
		
		<form id="form" method="post" action="pairs_by_day.php" >
			<table><tr>
			<th>День:</th><th>Месяц:</th><th>Год:</th></tr>
			<tr><td><div id="day"><p> 	
				<select name="day" id="day" class="day" >
				<option value="01">1</option>
				<option value="02">2</option>
				<option value="03">3</option>
				<option value="04">4</option>
				<option value="05">5</option>
				<option value="06">6</option>
				<option value="07">7</option>
				<option value="08">8</option>
				<option value="09">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>
				</select></p>
			
			</div>
		 	</td><td>
			<div id="month"><p> 	
				<select name="month" id="month" class="date" required>
				<option value="1">Январь</option>
				<option value="2">Февраль</option>
				<option value="3">Март</option>
				<option value="4">Апрель</option>
				<option value="5">Май</option>
				<option value="6">Июнь</option>
				<option value="7">Июль</option>
				<option value="8">Август</option>
				<option value="9">Сентябрь</option>
				<option value="10">Октябрь</option>
				<option value="11">Ноябрь</option>
				<option value="12">Декабрь</option>
				</select></p>
			
			</div>
			</td><td> <select name="year" id="year" class="date" required >
				<option disabled  value>---</option>
				<option value="2017">2017</option>
				<option value="2018">2018</option>
				</select>
			</td></tr>
			<tr><td colspan="3">
			<label><b>Авиакомпания:</b></label>
			<div id="Carrier"> 	
				<select name="carrier" id="carrier" class="carrier" >
				<option selected value="0">  -- все -- </option>
				<option value="FV">Россия</option>
				<option value="SU">Аэрофлот</option>
				<option value="DP">Победа</option>
				<option value="S7">S7</option>
				<option value="UT">UTair</option>
				<option value="U6">Уральские ав.</option>
				<option value="WZ">Red Wings</option>
				<option value="7R">РусЛайн</option>
				<option value="GH">Глобус</option>
				<option value="5N">Нордавиа</option>
				<option value="6W">Саратовские авиал.</option>
				<option value="N4">Северный ветер</option>
				<option value="D2">Северсталь</option>
				<option value="ZF">АЗУР Эйр</option>
				<option value="B2">Белавиа</option>
				<option value="NN">Вим-Авиа</option>
				<option value="I8">Ижавиа</option>
				<option value="KO">Комиавиатранс</option>
				<option value="VGV">Вологодское авиап.</option>
				<option value="КБ">Костромское авиап.</option>
				<option value="ЛП">Псков Авиа</option>
				<option value="R3">Якутия</option>
				<option value="YC">Ямал</option>
				<option value="BT">Air Baltic</option>
				<option value="AF">Air France</option>
				<option value="AZ">Alitalia</option>
				<option value="OS">Austrian</option>
				<option value="BA">British Airways</option>
				<option value="SN">Brussel Airlines</option>
				<option value="MU">CHINA EASTERN</option>
				<option value="OK">Czech Airlines</option>
				<option value="CY">CHARLIE</option>
				<option value="EK">Emirates</option>
				<option value="AY">Finnair</option>
				<option value="KL">K L M</option>
				<option value="LO">L O T</option>
				<option value="LH">Lufthansa</option>
				<option value="BJ">Nouvelair Tunisie</option>
				<option value="SK">S A S</option>
				<option value="LX">SWISS</option>
				<option value="RL">Royal Flight</option>
				<option value="TK">Turkish Airlines</option>
				<option value="VY">Vueling</option>
				<option value="J2">A Z A L</option>
				<option value="KC">Air Astana</option>
				<option value="A9">Georgian Airways</option>
				<option value="9U">Air Moldova</option>
				<option value="5F">Fly One</option>
				<option value="7J">Tajik Air</option>
				<option value="T5">TurkmenistanAir</option>
				<option value="HY">Uzbekistan Air</option>
				<option value="SZ">Somon Air</option>
				</select>
			</div></td>
			</tr>
			<tr><td colspan="3"><p><input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
			</table>
			<!---
			</p>
			
			-->
			<p><div id="errors"></div></p>	
			
		</form>
	</body>
</html>