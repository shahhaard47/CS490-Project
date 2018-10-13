<?php

// returns array of questions encoded in json format
/*
constructedQuestions = {
	"queArr" : array("Write a function named 'add' that takes parameters <num1> and <num2>, adds the 				two numbers and prints out the sum.",
					"Write a function named "subtract" that takes parameters <num1> and <num2>, subtracts the second number from first number and prints out the result")
};
input param -> array
returns -> array
*/
function constructQuestion($rawArr) {
	$outputarr = array();
	foreach ($rawArr as $que) {
		$funcName = $que["functionName"];
		$params = $que["params"]; // array
		$outparams = "";
		if (count($params) == 1) {
			$outparams = "parameter <$params[0]>";
		}
		elseif (count($params) == 2) {
			$outparams = "parameters <$params[0]> and <$params[1]>";
		}
		else {
			$outparams += "parameters ";
			for ($i = 0; $i < count($params)-2; $i++) {
				$outparams += "<$params[$i]>, ";
			}
			$outparams += "<$params[$i]> and "; $i++; 
			$outparams += "<$params[$i]>";
		}
		$does = $que["does"];
		$prints = $que["prints"];
		$tmp = "Write a function named \"$funcName\" that takes $outparams, $does and prints $prints.";
		// echo "$tmp\n";
		array_push($outputarr, $tmp);
	}
	return $outputarr;
}
// wrapper for constructQuestion just takes in json and returns json
function jsonConstructQuestion($queJSON){
	$decJSON = json_decode($queJSON, true);
	$result = constructQuestion($decJSON["raw"]);
	$questionsArr = array("questions" => $result);
	$questionJSON = json_encode($questionsArr);
	return $questionJSON;
}

// receive request from front
$jsonrequest = file_get_contents('php://input');

// send request to back
//*	curl send to debbie
$curl_opts = array(CURLOPT_POST => 1,
	CURLOPT_URL => 'https://web.njit.edu/~ds547/CS490-Project/assets/back/back_login.php',
	CURLOPT_POSTFIELDS => $jsonrequest,
	CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);
// ideally
// $ques_sendback = jsonConstructQuestion($result);
// echo $ques_sendback;

// testing
$sample = array(
	"raw" => array(
		array("functionName" => "add", 
			"params" => array("num1", "num2"), 
			"does" => "adds two numbers",
			"prints" => "the sum"),
		array("functionName" => "subtract",
			"params" => array("num1", "num2"),
			"does" => "subtracts second number from first number",
			"prints" => "the result")
	)
);
$encodedSample = json_encode($sample);

$jsonEncodedQues = jsonConstructQuestion($encodedSample);
$decoded = json_decode($jsonEncodedQues, true);
// echo $decoded["questions"][0]."\n";
foreach ($decoded["questions"] as $que) {
	echo "$que\n";
}

?>