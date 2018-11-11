<?php

$decoded = array("questionID" => 55);
$jsonrequest = json_encode($decoded);

$backfile = "test.php";
	$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;
	$curl_opts = array(CURLOPT_POST => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $jsonrequest,
		CURLOPT_RETURNTRANSFER => 1);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_opts);
	$result = curl_exec($ch);
	$result = json_decode($result, true);
	var_dump($result);

?>