<?php
ini_set("soap.wsdl_cache_enabled", "0");

$wsdlurl = 'https://devlt.berlogic.de/Partner/Avia/v3?wsdl';
$pass = 'KG=bR5C8zrW2!';
$locale = 'ru';
$login = 'pulkovoairport';

// Инициализация SOAP-клиента
$client = new SoapClient($wsdlurl,
    array(
        'trace'=> true,
        'exceptions' => false,
        'encoding' => 'UTF-8'
    )
);
 
// Формирование заголовков SOAP-запроса
$client->__setSoapHeaders(
   array(
      new SoapHeader('API', 'token', $token, false),
      new SoapHeader('API', 'locale', $locale, false)
   )
);

// Входные данные запроса
$params = array(
    'agentCode' => $login,
    'agencyCode' => $login,
    'salesPoint' => $login
);

// Выполнение запроса к серверу API tickets
$result = $client->CreateOrUpdateCampaign($params);

// Вывод запроса и ответа
echo "Запрос:<pre>".htmlspecialchars($client->__getLastRequest()) ."</pre>";
echo "Ответ:<pre>".htmlspecialchars($client->__getLastResponse())."</pre>";

/*
// Вывод отладочной информации в случае возникновения ошибки
if (is_soap_fault($result)) { echo("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring}, detail: {$result->detail})"); }
*/
?>