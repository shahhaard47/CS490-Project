<?php

require_once ("constructExamInfoFunction.php");

define("TESTING", false);
if (TESTING) {
	echo "Testing...\n";
	echo __FILE__."\n";
}

// get front json request // WON'T need
// $jsonrequest = file_get_contents('php://input');
$backfile = "allExamsToBeGraded.php";
// $backfile = "test.php";
$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;

$curl_opts = array(CURLOPT_POST => 1,
	CURLOPT_URL => $url,
	CURLOPT_POSTFIELDS => "",
	CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);

if (TESTING) {
//	echo "Result:\n"; var_dump($result);
//	exit();
}

$examsdata = json_decode($result, true);
// echo "DATA DECODEd\n";
if (TESTING) {
//	var_dump($examsdata); exit();
}
// var_dump($examsdata); exit();

if ($examsdata["conn"] && $examsdata["conn"] == false) {
	echo $result;
	exit();
}
//var_dump($examsdata);
$raw = $examsdata["raw"];
//var_dump($raw); exit();

$return_array = constructExams($raw, $exams);

if (TESTING) {
//    echo "Final output: \n"; var_dump($return_array);
//    exit();
}

$encoded_return_array = json_encode($return_array);

// var_dump($return_array);
echo $encoded_return_array;












?>

