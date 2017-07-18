<?php
ini_set("soap.wsdl_cache_enabled", "0");

$wsdlurl = 'https://api.direct.yandex.ru/live/v4/wsdl/';
$token = 'EQNxPFdYZJyE3e1Toi';
$locale = 'ru';
$login = 'f5820498b51c435ba0c682f2310cb168';

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
    'Login' => $login,
    'CampaignID' => 0,
    'Name' => 'New Campaign',
    'FIO' => 'Иван Иванов',
    'Strategy' => array(
        'StrategyName' => 'HighestPosition'
    ),
    'EmailNotification' => array(
        'MoneyWarningValue' => 20,
        'WarnPlaceInterval' => 60,
        'Email' => 'zaqo@yandex.ru'
    )
);

// Выполнение запроса к серверу API Директа
$result = $client->CreateOrUpdateCampaign($params);

// Вывод запроса и ответа
echo "Запрос:<pre>".htmlspecialchars($client->__getLastRequest()) ."</pre>";
echo "Ответ:<pre>".htmlspecialchars($client->__getLastResponse())."</pre>";

/*
// Вывод отладочной информации в случае возникновения ошибки
if (is_soap_fault($result)) { echo("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring}, detail: {$result->detail})"); }
*/
?>