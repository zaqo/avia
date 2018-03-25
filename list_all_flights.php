<?php 
	/*
	   PRODUCES A LIST OF NUMBER OF FLIGHTS BY DAY
		March 2018 (c) by S. Pavlov
	*/
	require_once 'login_avia.php';
	
	include ("header.php"); 
	
		
		
		if (isset($_REQUEST['offset'])) $offset = $_REQUEST['offset'];	
		else $offset=0;
		$input_date=date("dmY");
		$day   = substr($input_date,0,2);
		$month = substr($input_date,2,2);
		$year  = substr($input_date,4,4);
		$input_d=array($year,$month,$day);
	
		$date_=mktime(0,0,0,$month,$day,$year);
	
		$date=date("Y-m-d", $date_);
	
		$content='';
		//----------------------------------------
		// Top of the table
		
		$content.= '<div class="container ml-5 mt-2">';
		$content.= "<h4>  Импортировано рейсов  </h4><hr>";
		//$content.= '<form id="form" method=post action="update_erp_pairs.php"  >';
		$content.= '<ul class="list-group w-50">';
		//$content.= '';
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1">Перечень</h5>
							
						</div>
					</li>';
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// 1. Check if there is a pair and make a record 
		
			$textsql='SELECT COUNT(*),date
						FROM  flights
						GROUP BY DATE';
		
		
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
		$num=0;
		while( $row = mysqli_fetch_row($answsql) ) 
		{
			//a. cut the prefix
			
			$day   = substr($row[1],8,2);
			$month = substr($row[1],5,2);
			$year  = substr($row[1],0,4);
			$content.='<li class="list-group-item flex-column align-items-start" >
							<div class="d-flex w-100 justify-content-between">
		
							<h5 class="mb-1">
							<a href="list_daily_flights.php?day='.$day.'&month='.$month.'&year='.$year.'">'.$day.' / '
									.$month.' / '.$year.'</a>
									</h5><span  class="my_arrow">&#x21E2</span> <span class="text-muted"><h5>'.$row[0].'</h5>
									</span>
									</div>
									</li>';
					
		}
		
		
		//$content.= '<button type="submit" class="btn btn-primary mb-2">ВВОД</button></form>';
		$content.= '</div>';
	Show_page($content);
	mysqli_close($db_server);
?>
	