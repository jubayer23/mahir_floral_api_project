<?php
/**
 * Created by PhpStorm.
 * User: jubayer
 * Date: 7/4/2019
 * Time: 5:34 PM
 */


//require_once __DIR__ . '/Notification.php';
include_once '../notifications/Notification.php';
$notification = new Notification();

$title = 'test';
$message = 'this is test message';
$imageUrl = '';
$action = '';

$actionDestination = '';

if ($actionDestination == '') {
    $action = '';
}
$notification->setTitle($title);
$notification->setMessage($message);
$notification->setImage($imageUrl);
$notification->setAction($action);
$notification->setActionDestination($actionDestination);

$firebase_token = $_POST['firebase_token'];
$firebase_api = 'AAAAhGDHEv8:APA91bFm8qhvB-96rE-rejAluemlBtMLdhd8-O4A_ZfEbhEG3Hc-NgzONY380FRlKmbww9IEJts1WItw3FCuKMTeDX75iZu38qqthd82GU5YBVAD5Eh8BX-RZh6E03PXlmq30_Ib4-N7';

//$topic = $_POST['topic'];

$requestData = $notification->getNotificatin();

/*if ($_POST['send_to'] == 'topic') {
    $fields = array(
        'to' => '/topics/' . $topic,
        'data' => $requestData,
    );

} else {*/

    $fields = array(
        'to' => $firebase_token,
        'data' => $requestData,
    );
//}


// Set POST variables
$url = 'https://fcm.googleapis.com/fcm/send';

$headers = array(
    'Authorization: key=' . $firebase_api,
    'Content-Type: application/json'
);

// Open connection
$ch = curl_init();

// Set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Disabling SSL Certificate support temporarily
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

// Execute post
$result = curl_exec($ch);
if ($result === FALSE) {
    die('Curl failed: ' . curl_error($ch));
}

// Close connection
curl_close($ch);

echo '<h2>Result</h2><hr/><h3>Request </h3><p><pre>';
echo json_encode($fields, JSON_PRETTY_PRINT);
echo '</pre></p><h3>Response </h3><p><pre>';
echo $result;
echo '</pre></p>';

?>