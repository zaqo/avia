<?php
/*
			MIXED BAG COLLECTION OF FUNCTIONS
			(C) Sergey Pavlov 2018			
*/
function toggle_gen($number,$key,$chk)
{
	/* GENETRATES TOGGLE SWITCHES FOR THE PAGE
	INPUT:
			$number		-		integer, position on the page
			$key		-		type of selector, 0 - gender, 1 - direction, 2 - RUS/FOREIGN, 3 - PASS / CARGO, 4 - NO / HAVE PASSENGERS
			$chk		-		current value,  0 - first, 1 - second
	OUTPUT:
			html of radiobutton
	
	
	*/
	$checked='checked';
	
	switch($key)
	{
		case 1:
			$legend_0='ПРИБ';
			$legend_1='ОТПР';
			$name='dir'.$number;
			break;
		case 2:
			$legend_0='ЗАР';
			$legend_1='РОС';
			$name='dom'.$number;
			break;
		case 3:
			$legend_0='НЕТ';
			$legend_1='ГРУЗ';
			$name='cargo'.$number;
			break;
		case 4:
			$legend_0='НЕТ';
			$legend_1='ПАСС';
			$name='pass'.$number;
			break;
		default:
			$legend_0='ДЕТ';
			$legend_1='ВЗР';
			$name='gender'.$number;
			break;
	}
	if(!$chk)
	{
		$first=$checked;
		$second='';
	}
	else
	{
		$second=$checked;
		$first='';
	}
	
	
return ' <div class="switch-field">
							<input type="radio" id="left'.$key.$number.'" name="'.$name.'" value="yes" '.$first.' disabled/>
							<label for="left'.$key.$number.'">'.$legend_0.'</label>
							<input type="radio" id="right'.$key.$number.'" name="'.$name.'" value="no" '.$second.' disabled/>
							<label for="right'.$key.$number.'">'.$legend_1.'</label>
					</div>';
}
?>