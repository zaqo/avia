<?php //login_avia.php
	
	//mySQL section
	$db_hostname= 'localhost';
	$db_database= 'avia';
	$db_username= 'php';
	$db_password= '12345';
	
	//SQL server
	$db_mssql='172.16.90.39';
	$db_mssql_dbname='NCG_PRODUCTION';
	$db_mssql_user='php';
	$db_mssql_pass='12345sql';
	$serverName = '172.16.90.39'; //serverName\instanceName
	$tableRoute='dbo.[NCG$Route]';
	$tableRouteDetail='dbo.[[NCG$AODB Route Detail]]';
	$connectionInfo = array( "Database"=>'NCG_PRODUCTION', "UID"=>"php", "PWD"=>"12345sql");
	$systems=array();
	
	//SAP web service
	$SAP_username= 'PHP_SALES';
	$SAP_password= 'Service5#';
	//DO NOT FORGET TO CHANGE SAP's URL - must be A101!!!
	//$wsdlurl = "http://SRVR-186.local.newpulkovo.ru:8002/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zsd_order_avi_crud/001/zsd_order_avi_crud/zsd_order_avi_crud?sap-client=001";
    //EP $wsdlurl='http://srvr-186.local.newpulkovo.ru:8002/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zsd_order_avi_crud2/001/zsd_order_avi_crud2/zsd_order_avi_crud2?sap-client=001';
	$wsdlurl='http://srvr-185.local.newpulkovo.ru:8000/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zsd_order_zavi_crud/110/zsd_order_zavi_crud/zsd_order_zavi_crud?sap-client=110';
	
	
		//Services Navision to SAP mapping section
		$services_map=array (
								'A0200191'=>'900000114',
								'A0300030'=>'900000115',
								'A0300036'=>'900000116',
								'A0300043'=>'900000117',
								'A0300073'=>'900000118',
								'A0300076'=>'900000119',
								'A0300077'=>'900000136',
								'A0300487'=>'900000121',
								'A0300491'=>'900000137',
								'A0300499'=>'900000123',
								'A0300503'=>'900000124',
								'A0300509'=>'900000125',
								'A0300512'=>'900000138',
								'P0300017'=>'900000139',
								'P0300390'=>'900000128',
								'P0300420'=>'900000129',
								'P0300421'=>'900000130',
								'P0300422'=>'900000131',
								'P0300431'=>'900000132',
								'P0300450'=>'900000198',
								'P0300463'=>'900000134',
								'P0300464'=>'900000135',
								'P0300449'=>'900000141',
								'P0500371'=>'900000142',
								'P0300361'=>'900000143',
								'P0300382'=>'900000144',
								'N0900465'=>'900000145',
								'N0900466'=>'900000146',
								'N1000413'=>'900000147',
								'A0100200'=>'900000148',
								//'A0100201'=>'900000149',
								//'A0100224'=>'900000150',
								'A0100225'=>'900000151',
								'P0900354'=>'900000152',
								'P0900356'=>'900000153',
								'N0800533'=>'900000154',
								'N0800282'=>'900000155',
								'N0800560'=>'900000156',
								'P0900359'=>'900000157',
								'A0100183'=>'900000158',
								'A0100186'=>'900000159',
								//'A0100187'=>'900000160',
								'A0100188'=>'900000161',
								'A0100190'=>'900000162',
								'A0100194'=>'900000163',
								'A0100195'=>'900000164',
								'A0100196'=>'900000165',
								'A0100197'=>'900000166',
								'A0100198'=>'900000167',
								'A0100199'=>'900000168',
								'A0100205'=>'900000169',
								'A0100208'=>'900000170',
								'A0100214'=>'900000171',
								'N0900416'=>'900000172',
								'N0900479'=>'900000173',
								'N0900480'=>'900000174',
								'A0100189'=>'900000175',
								'A0100191'=>'900000176',
								'A0100202'=>'900000177',
								'A0100203'=>'900000178',
								'A0100207'=>'900000179',
								'A0100215'=>'900000180',
								'A0100216'=>'900000181',
								'A0100217'=>'900000182',
								'A0100218'=>'900000183',
								'A0100219'=>'900000184',
								'A0100220'=>'900000185',
								'A0100221'=>'900000186',
								'A0100222'=>'900000187',
								'A0100228'=>'900000188',
								'N0900417'=>'900000189',
								'N0900418'=>'900000190',
								'N0900419'=>'900000191',
								'N0900420'=>'900000192',
								'A0300009'=>'900000193',	
								'A0300040'=>'900000199',
								'A0300009'=>'900000200',
								'A0300003'=>'900000201',
								'A0300078'=>'900000202',
								'A0300079'=>'900000203',
								'A0300458'=>'900000204',
								'A0300489'=>'900000205',
								'A0300517'=>'900000206'
							);
		$clients_map=array (
								'К04472' => '40000034',
								'К07814' => '40000022',
								'К01974' => '40000023',
								'К01754' => '40000024',
								'К01722' => '40000025',
								'К02734' => '40000026',
								'К06444' => '40000027',
								'К02711' => '40000028',
								'К09991' => '40000029',
								'К07645' => '40000030',
								'К03304' => '40000031',
								'К04529' => '40000032',
								'К08887' => '40000033'
							);
	
	
?>