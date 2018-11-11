<?php

function constructQuestions(&$rawArr) {
	// $outputarr = array();
	// foreach ($rawArr as $que) {
	for ($a = 0; $a < count($rawArr); $a++) {
		$que = $rawArr[$a];
		$funcName = $rawArr[$a]["functionName"];
		$params = $rawArr[$a]["params"]; // array

		$outparams = "";
		if (count($params) == 1) {
			$ptmp = $params[0];
			$outparams = "parameter <$ptmp>";
		}
		elseif (count($params) == 2) {
			$ptmp0 = $params[0]; $ptmp1 = $params[1];
			$outparams = "parameters <$ptmp0> and <$ptmp1>";
		}
		else {
			$outparams .= "parameters ";
			for ($i = 0; $i < count($params)-2; $i++) {
				$outparams .= "<$params[$i]>, ";
			}
			$outparams .= "<$params[$i]> and "; $i++; 
			$outparams .= "<$params[$i]>";
		}
		$does = $rawArr[$a]["functionDescription"];
		$prints = $rawArr[$a]["output"];
		$tmp = "Write a function named \"$funcName\" that takes $outparams, $does and prints $prints.";

		// add constructed question attribute
		$rawArr[$a]["constructed"] = $tmp;
	}
}

$jsonrequest = file_get_contents('php://input');
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
// var_dump($decoded);

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
	echo "(middle) error: could not extract \"questions\" attribute from incoming json";
	exit();
}












?>