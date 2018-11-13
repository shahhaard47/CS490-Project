<?php

require_once('constructQuestionsInPlace.php');

$jsonrequest = file_get_contents('php://input');

//$decjson = array("userID" => "mscott");
//$jsonrequest = json_encode($decjson);

$backfile = "getAvailableExam.php";
$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;
$curl_opts = array(CURLOPT_POST => 1,
	CURLOPT_URL => $url,
	CURLOPT_POSTFIELDS => $jsonrequest,
	CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);
// var_dump($result);

$decoded = json_decode($result, true);
//var_dump($decoded);

// check connection
if ($decoded["conn"] && $decoded["conn"] == false) {
	echo $result;
	exit();
}

if ($decoded["questions"]) {
	$questions = $decoded["questions"];

	constructQuestions($questions); // passing as reference so it changes $questions

	$decoded["questions"] = $questions;
	$encoded = json_encode($decoded);
	echo $encoded;
}
else {
    $decoded = array("error" => "no available exams");
    $encoded = json_encode($decoded);
	echo $encoded;
	exit();
}












?>