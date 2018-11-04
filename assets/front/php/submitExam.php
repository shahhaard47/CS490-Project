<?php
$json = file_get_contents('php://input');

$url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/grade.php';
//echo json_decode($json);

$curl_opts = array(
    CURLOPT_POST => 1,
    CURLOPT_URL => $url,
    CURLOPT_POSTFIELDS => $json,
    CURLOPT_RETURNTRANSFER => 1

);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);

if ($error_msg = curl_error($ch)) {
    echo $error_msg;
}

echo $result;

curl_close($ch);


//$json = json_decode(file_get_contents('php://input'));
//$json = (file_get_contents('php://input'));
//$url = '';
//echo $json;
//$req = $json['requestType'];
//if ($req == 'login') {
//    echo 'login';
//    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/auth_login.php';
//} elseif ($req == 'getqbank') {
//    echo 'qbank';
//    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/request_question.php';
//}
