<?php 
// CREATES TEMPLATE OF SERVICES
require_once 'login_avia.php';?>
<html>
		
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="/avia/css/style.css" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript">

		function addMyField () {
			var telnum = parseInt($('#add_field_area').find('div.add:last').attr('id').slice(3))+1;
			var $content=$("select#val1").html();
			$('div#add_field_area').find('div.add:last').append('<div id="field'+telnum+'"><hr><tr><div id="add'+telnum+'" class="add"><label> №'+telnum+
			'</label><select name="val'+telnum+'" id="val" onblur="writeFieldsValues();" >'+$content+
			'</select></div></tr><tr><div class="deletebutton" onclick="deleteField('+telnum+');"></div></tr></div>');
		}
		
		function deleteField (id) {
			$('div#field'+id).remove();
		}

		function writeFieldsValues () {
			var str = [];
			var tel = '';
			for(var i = 0; i<$("select#val").length; i++) {
			tel = $($("select#val")[i]).val();
				if (tel !== '') {
					str.push($($("input#values")[i]).val());
				}
			}
			$("input#values").val(str.join("|"));
		}
		</script>		
		<title>ВВОД Агентов</title>
	</head>
	<body>
<?php
//include ("header.php"); 	
include ("functions.php"); 	
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql='SELECT id,id_NAV FROM services WHERE 1';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$services='<select name="val1" id="val1" class="services" >';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$services.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$services.='</select>';		
		?>
		<form id="form" method=post action=update_package.php >
					<div id="add_field_area">
					<table><caption><b>Создаем пакет</b></caption>
					<tr><td colspan="6"><b>НАЗВАНИЕ:</b><input type="text" value="" name="pack_name" /></td></tr>
					<tr><th>№</th><th>Услуга</th><th>Везде</th><th>Вкл Аэропорты</th><th>Искл Аэропорты</th><th></th></tr>
					<tr><td>1</td>
					<td><div id="add1">
					<?php echo $services;?>
					</div></td>
					<td><input type="checkbox" name="Servicedata[]" class="name" value="all" /></td>
					<td><input type="text" value="" name="including" placeholder="1,2,3"/></td>
					<td><input type="text" value="" name="excluding" placeholder="1,2,3"/></td>
					
					</tr>
					<tr><td onclick="alert();" class="addbutton" td colspan="6">Add</td></tr>
					<tr><td colspan="6"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></div></form>';
		</body>
		
	<?php
	
	mysqli_close($db_server);
	
?>
	