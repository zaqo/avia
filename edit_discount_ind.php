<?php 
// EDITS INDIVIDUAL DISCOUNT
require_once 'login_avia.php';
include ("header.php"); 	
		$id= $_REQUEST['id'];
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//GET DISCOUNT DATA
			$check_ind_discount='SELECT discounts_individual.name,client_id,discount_val,clients.name,valid_from,valid_to
					FROM discounts_individual 
					LEFT JOIN clients ON client_id=clients.id
					WHERE discounts_individual.id='.$id;
					
					$answsql_disc=mysqli_query($db_server,$check_ind_discount);
					if(!$answsql_disc) die("SELECT into discounts_individual TABLE failed: ".mysqli_error($db_server));
				$row_d = mysqli_fetch_row( $answsql_disc );
				$name=$row_d[0];
				$client_id=$row_d[1];
				$client_name=$row_d[3];
				$disc_val=$row_d[2];
				$valid_f=$row_d[4];
				$valid_t=$row_d[5];
				$valid_from=substr($valid_f,-2).substr($valid_f,4,3).'-20'.substr($valid_f,2,2);
		$valid_to=substr($valid_t,-2).substr($valid_t,4,3).'-20'.substr($valid_t,2,2);
		// Constructs services dropdown
		$check_in_mysql='SELECT id,id_NAV FROM services WHERE 1';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
		$services='<select name="val[]" id="val1" class="services" required>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$services.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$services.='</select>';	

		// Constructs clients dropdown
		$selected='';
		$check_clients='SELECT id,name FROM clients WHERE name!="" AND isValid ORDER BY name' ;
					
					$answsqlcheck=mysqli_query($db_server,$check_clients);
					if(!$answsqlcheck) die("SELECT into clients TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$clients='<select name="client" id="client" required>';
		$clients.='<option disabled value> -- выберите компанию -- </option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		{
			if($row[0]==$client_id) $selected='selected';
			$clients.='<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
			$selected='';
		}
		$clients.='</select>';		
		
		$content.='<script src="/avia/js/calender.js" type="text/javascript">
	    </script>';	
		$content.= '<form id="form" method=post action=update_discount.php >
					<div id="add_field_area"><table class="fullTab"><caption><b>Редактируем скидку на клиента</b></caption>
					<tr><th></th><th></th></tr>
					<tr><td><b>НАЗВАНИЕ:</b></td><td><textarea value="" name="name" required/>'.$name.'</textarea></td></tr>
					<tr><td><b>КЛИЕНТ:</b></td><td>'.$clients.'</td></tr>
					<tr><td><b>СКИДКА (%):</b></td><td><input type="number" name="val" value="'.$disc_val.'" min="-100" max="100" step="0.001" required/></td></tr>
					<tr><td><b>C:</b></td><td><input type="text" class="date_input" value="'.$valid_from.'" name="from" onfocus="this.select();lcs(this)"
												onclick="event.cancelBubble=true;this.select();lcs(this)"/></td></tr>
					<tr><td><b>ПО:</b></td><td><input type="text" class="date_input" value="'.$valid_to.'" name="to" onfocus="this.select();lcs(this)"
												onclick="event.cancelBubble=true;this.select();lcs(this)"/></td></tr>
					<tr><td><b>ПРИОРИТЕТ:</b></td><td><input type="number" value="" name="priority" value="0" min="1" max="9" step="1" /></td></tr>
					<tr><td colspan="2"><input type="hidden" name="id" value="'.$id.'">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></div></form>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	