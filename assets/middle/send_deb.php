<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://web.njit.edu/~ds547/back.php');

curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, 'user='.'testuser'.'&pass='.'password');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($ch);

echo $result;

