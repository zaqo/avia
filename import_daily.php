<?php
include ("login_avia.php"); 
include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
?>
<html lang="ru">
	
	 
<script src="/avia/js/calender.js" type="text/javascript"></script>

	<div class="container mt-5">
		<h1 >Импорт рейсов из AODB: </h1>
		<form class="form-inline mt-5" id="inlineForm" method="post" action="import_flights.php">
			<label  for="inlineFormInput">Укажите Дату:</label>
				
				<input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInput" value="" name="from" onfocus="this.select();lcs(this)"
												onclick="event.cancelBubble=true;this.select();lcs(this)" required/>
			<button type="submit" class="btn btn-primary mb-2">ВВОД</button>
		
		</form>
	</div>
</html>