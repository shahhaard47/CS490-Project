<?php

$decoded = array("examID" => 61, "userID" => "mscott", "requestType" => "gradeExam");
//$decoded = array("examID" => 61, "userID" => "jsnow");
$jsonrequest = json_encode($decoded);

//$jsonrequest = file_get_contents('php://input');
$url = "./grade.php";
$curl_opts = array(CURLOPT_POST => 1,
    CURLOPT_URL => $url,
    CURLOPT_POSTFIELDS => $jsonrequest,
    CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
session_write_close();
$result = curl_exec($ch);

echo $result;













?>