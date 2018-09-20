<?php
$credentials = array(
    'user' => $_POST['user'],
    'pass' => $_POST['pass']
);
$json = json_encode($credentials);
$curl_opts = array(
    CURLOPT_POST => 1,
    CURLOPT_URL => 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/index.php',
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
