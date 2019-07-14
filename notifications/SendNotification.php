<?php

/**
 * Created by PhpStorm.
 * User: comsol
 * Date: 13-Jul-19
 * Time: 6:17 PM
 */

include_once '../notifications/Notification.php';
class SendNotification
{
    function sendToTopic($title, $message, $topic, $action, $actionDestination, $shop_id, $shop_name){
        $notification = new Notification();
        //$title = 'test';
        //$message = 'this is test message';
        $imageUrl = '';


        if ($actionDestination == '') {
            $action = '';
        }
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setImage($imageUrl);
        $notification->setAction($action);
        $notification->setActionDestination($actionDestination);
        $notification->setShopId($shop_id);
        $notification->setShopName($shop_name);

        $firebase_token = 'aaadsd';
        $firebase_api = 'AAAAhGDHEv8:APA91bFm8qhvB-96rE-rejAluemlBtMLdhd8-O4A_ZfEbhEG3Hc-NgzONY380FRlKmbww9IEJts1WItw3FCuKMTeDX75iZu38qqthd82GU5YBVAD5Eh8BX-RZh6E03PXlmq30_Ib4-N7';

        $requestData = $notification->getNotificatin();

        $fields = array(
            'to' => '/topics/' . $topic,
            'data' => $requestData,
        );

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

    }

}