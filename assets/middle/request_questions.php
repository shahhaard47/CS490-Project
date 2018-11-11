<?php

require_once ('constructQuestionsInPlace.php');

// wrapper for constructQuestion just takes in json and returns json
function jsonConstructQuestions(&$queJSON){
	$decJSON = json_decode($queJSON, true);

	constructQuestions($decJSON["raw"]);
//	 var_dump($decJSON["raw"]); exit();
	$questionJSON = json_encode($decJSON);
	return $questionJSON;
}

// receive request from front
$jsonrequest = file_get_contents('php://input');
// send request to back
//*	curl send to debbie
$curl_opts = array(CURLOPT_POST => 1,
	CURLOPT_URL => 'https://web.njit.edu/~ds547/CS490-Project/assets/back/back_questionBank.php',
	CURLOPT_POSTFIELDS => $jsonrequest,
	CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);
// echo $result;
$decoded = json_decode($result, true);

// var_dump($decoded);
if ($decoded["conn"] && $decoded["conn"] == false) {
	echo $result;
	exit();
}

// ideally
$ques_sendback = jsonConstructQuestions($result);
echo $ques_sendback;

?>
