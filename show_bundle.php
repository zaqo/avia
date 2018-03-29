<?php require_once 'login_avia.php';
/* 
SHOWS CONTENT OF THE PACKAGE OF SERVICES

by S.Pavlov (c) 2017
*/
include ("header.php"); 
	
	if(isset($_REQUEST['id']))
	{
			$id		= $_REQUEST['id'];
			$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		// Get the service description
		$service_name="SELECT description,id_NAV
								FROM services
								WHERE id=$id ";
					
					$answsql=mysqli_query($db_server,$service_name);
					if(!$answsql) die("LOOKUP into services TABLE failed: ".mysqli_error($db_server));
		$row_srv=mysqli_fetch_row($answsql);
		$desc_srv=$row_srv[0];
		$srv_id_NAV=$row_srv[1];
		// Main bundle info section
			$check_in_mysql="SELECT services.description,services.id_NAV,bundle_content.quantity
								FROM bundle_content
								LEFT JOIN services ON bundle_content.service_id=services.id
									WHERE bundle_content.bundle_id=$id ORDER BY services.id_NAV";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into bundle_reg TABLE failed: ".mysqli_error($db_server));
		
		// Top of the table
		$counter=0;
		$content.= '<div class="container"><div class="col-md-4 order-md-2 mb-4 ">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted"> ПАКЕТ:</span>
            <span class="badge badge-secondary badge-pill">'.$srv_id_NAV.'</span>
          </h4>
          <ul class="list-group mb-3 align-self-center">
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Описание услуги</h6>
                <small class="text-muted">ID</small>
              </div>
              <span class="text-muted">Кол-во</span>
            </li>';
		
		// Iterating through the array
		
	
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$counter+=1;
				$desc=$row[0];
				$id_NAV=$row[1];
				$qty=$row[2];
				$content.= '<li class="list-group-item d-flex justify-content-between lh-condensed">
							<div>
								<h6 class="my-0">'.$desc.'</h6>
									<small class="text-muted">'.$id_NAV.'</small>
							</div>
								<span class="text-muted">'.$qty.'</span>
							</li>';
				
		}
			$content.= '</div></div>';
			Show_page($content);
		mysqli_close($db_server);
	}
		else
			echo "ERROR: Package ID is not provoded! <\br>";
?>
	