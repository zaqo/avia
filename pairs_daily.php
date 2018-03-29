<?php
/*
	Starting page for SAP ERP export process
	by S.Pavlov (c) 2017
*/
include ("login_avia.php"); 
include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
?>
<html lang="ru">
	
	 
<script src="/avia/js/calender.js" type="text/javascript"></script>

	<div class="container mt-5">
		<h1 >Экспорт рейсов в ERP: </h1>
		<form class="form mt-5" id="inlineForm" method="post" action="pairs_by_day.php">
			<div class="form-group">
			<label for="inlineFormInput">Укажите Дату:</label>
				
				<input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInput" value="" name="from" onfocus="this.select();lcs(this)"
												onclick="event.cancelBubble=true;this.select();lcs(this)" required/>
			</div>	
			<div id="form-group"> 	
					<label for="carrier">Авиакомпания:</label>
					<select name="carrier" id="carrier" class="form-control  mb-2 mr-sm-2">
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
						<option value="A3">Aegean</option>
						<option value="BT">Air Baltic</option>
						<option value="AF">Air France</option>
						<option value="AZ">Alitalia</option>
						<option value="OS">Austrian</option>
						<option value="BA">British Airways</option>
						<option value="SN">Brussel Airlines</option>
						<option value="MU">CHINA EASTERN</option>
						<option value="CZ">CHINA Southern</option>
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
			</div>
			
			<button type="submit" class="btn btn-primary mt-2">ВВОД</button>
		
		</form>
	</div>
</html>