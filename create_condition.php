<?php 
// CREATES CONDITION FOR APPLYING DISCOUNT
require_once 'login_avia.php';
include ("header.php"); 	
		$content="";
		$params="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql='SELECT * FROM params WHERE 1 ORDER BY id';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into params TABLE failed: ".mysqli_error($db_server));
		// Params dropdown
		$params='<select name="param" id="val1" class="params" >';
		$params.='<option value=""> ... </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$params.='<option value="'.$row[0].'">'.$row[2].'</option>';
		$params.='</select>';		
		// 
		// Constructs conditions dropdown
		
		$conds='<select name="cond" id="conds" >';
		$conds.='<option value=""> ... </option>';
		$conds.='<option value=0> = </option>';
		$conds.='<option value=1> < </option>';
		$conds.='<option value=2> <= </option>';
		$conds.='<option value=3> > </option>';
		$conds.='<option value=4> >= </option>';
		$conds.='<option value=5> > < </option>';
		$conds.='<option value=6> [ ] </option>';
		$conds.='<option value=7> ][ </option>';
		$conds.='</select>';
		// Form begins
		$content.= '<form id="form" method=post action=update_condition.php >
					<table id="myTab"><caption><b>Создаем условие</b></caption>
					<tr><th></th><th></th></tr>
					<tr><td><b>НАЗВАНИЕ:</b></td><td><input type="text" value="" name="cond_name" placeholder="Название условия" /></td></tr>
					<tr><td><b>ПАРАМЕТР:</b></td><td>'.$params.'</td></tr>
					<tr><td><b>ОТ:</b></td><td><input type="number" value="" name="from" placeholder="Начальное значение" /></td></tr>
					<tr><td><b>ДО:</b></td><td><input type="number" value="" name="to" placeholder="Конечное значение" /></td></tr>
					<tr><td><b>МНОЖЕСТВО:</b></td><td><input type="" value="" name="enum" placeholder="Перечисление значений 11,12 ..." /></td></tr>
					<tr><td><b>СРАВНЕНИЕ:</b></td><td>'.$conds.'</td></tr>
					<tr><td colspan="2"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
	
	Show_page($content);
	
	mysqli_close($db_server);
?>
	