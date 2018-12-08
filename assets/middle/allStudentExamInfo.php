<?php

require ("constructExamInfoFunction.php");

define("TESTING", false);

if (TESTING) {
    echo "Testing...\n";
    echo __FILE__."\n";
}

// define constants

$jsonrequest = file_get_contents('php://input');
$decoded = json_decode($jsonrequest, true);

if (TESTING) {
    $decoded = array("userID" => "jsnow");
    $jsonrequest = json_encode($decoded);
}

if ($decoded["userID"]) {
    $userid = $decoded["userID"];
} else {
    echo "(error) userID should be passed to view student exam info\n";
    exit();
}

$backfile = "allStudentExamInfo.php";
$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;

$curl_opts = array(CURLOPT_POST => 1,
    CURLOPT_URL => $url,
    CURLOPT_POSTFIELDS => $jsonrequest,
    CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);

if (TESTING) {
//    echo "Result:\n";
//    var_dump($result); exit();
}

$decoded = json_decode($result, true);

if (TESTING) {
//    var_dump($decoded);
//    exit();
}

$raw = $decoded["raw"];
if (TESTING) {
//    echo "RAW\n"; var_dump($raw); exit();
}

//var_dump($decoded);
//exit();
$return_array = constructExams($raw, $userid);

if (TESTING) {
    echo "RETURN ARRAY: \n"; var_dump($return_array);
    exit();
}

$encoded_return_array = json_encode($return_array);

//var_dump($return_array);
echo $encoded_return_array;










?>
