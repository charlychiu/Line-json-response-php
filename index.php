<?php
header('Content-type: application/json;charset=utf-8');
$LINE_ACCESS_TOKEN = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $http_request_body = file_get_contents('php://input');
    $data_body = json_decode($http_request_body, true);
    // file_put_contents('log.txt', $http_request_body);

    foreach ($data_body['events'] as $event) {
        if ($event['type'] == 'message') {
            $reply_token = $event['replyToken'];
            $msg = $event['message']['text'];

            $json = file_get_contents("./line-json/" . $msg . ".json");
            $json_valid = json_decode($json, true);

            $tmp_array = array();
            foreach ($json_valid as $_j) {
                $tmp_array[] = $_j;
            }

            $payload = [
                'replyToken' => $reply_token,
                'messages' => $tmp_array,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/reply');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $LINE_ACCESS_TOKEN,
            ]);
            $Result = curl_exec($ch);
            curl_close($ch);
        }
    }
}
