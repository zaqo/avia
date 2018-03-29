<?php 
/*
CREATES CONDITION FOR APPLYING DISCOUNT

by S.Pavlov (c) 2017	
*/
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
		$params='<select name="param" id="val1" class="form-control" >';
		$params.='<option value=""> ... </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$params.='<option value="'.$row[0].'">'.$row[2].'</option>';
		$params.='</select>';		
		// 
		// Constructs conditions dropdown
		
		$conds='<select name="cond" id="conds" class="form-control">';
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
		
		$content.= '<div class="col-md-8 order-md-1 mt-2">
						<h4 class="mb-3"> Создаем условие</h4>';
		$content.= '<form id="form" method=post action=update_condition.php class="needs-validation" novalidate>';
		$content.='
					<div class="mb-3">
						<label for="name">Название</label>
							<input type="text" value="" name="cond_name" class="form-control" placeholder="Текст" />
							
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="param">Параметр</label>
							'.$params.'
					</div>
					<div class="mb-3">
						<label for="from">ОT:</label>
							<input type="number" value="" name="from" class="form-control" placeholder="Начальное значение" />
							
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="to">ДО:</label>
							<input type="number" value="" name="from" class="form-control" placeholder="Конечное значение" />
							
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="to">МНОЖЕСТВО:</label>
							<input type="" value="" name="enum" class="form-control" placeholder="Перечисление значений 11,12 ..." />
								<div class="invalid-feedback">
									Введите правильное значение идентификатора.
								</div>
					</div>
					<div class="mb-3">
						<label for="cond">Сравнение</label>
							'.$conds.'
					</div>
					 <hr class="mb-4">
						<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
					</form>';
		$content.='</div>';			
		
	
	Show_page($content);
	
	mysqli_close($db_server);
?>
	