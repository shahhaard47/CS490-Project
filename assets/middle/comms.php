<?php

/* This file is for any common communication between front and back that doesn't need middle involvement*/

// get front json request
$jsonrequest = file_get_contents('php://input');
$decoded = json_decode($jsonrequest, true);
$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$decoded["requestType"]."php";

// forward the request to back
$curl_opts = array(CURLOPT_POST => 1,
	CURLOPT_URL => $url,
	CURLOPT_POSTFIELDS => $jsonrequest,
	CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);

// send back the response to front
echo $result;

?>