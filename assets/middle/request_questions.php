<?php

// returns array of questions encoded in json format
/*
questionJSON = {
	"queArr" : array(array(question, difficulty, points))
}
constructedQuestions = {
	"queArr" : array(array("Write a function named 'add' that takes parameters <num1> and <num2>, adds the 				two numbers and prints out the sum.", 'h', 20),
					"Write a function named "subtract" that takes parameters <num1> and <num2>, subtracts the second number from first number and prints out the result"),
};
input param -> array
returns -> array
*/

function constructQuestions(&$rawArr) {
	// $outputarr = array();
	// foreach ($rawArr as $que) {
	for ($a = 0; $a < count($rawArr); $a++) {
		$que = $rawArr[$a];
		$funcName = $rawArr[$a]["functionName"];
		$params = $rawArr[$a]["params"]; // array
		$outparams = "";
		if (count($params) == 1) {
			$outparams = "parameter <$params[0]>";
		}
		elseif (count($params) == 2) {
			$outparams = "parameters <$params[0]> and <$params[1]>";
		}
		else {
			$outparams .= "parameters ";
			for ($i = 0; $i < count($params)-2; $i++) {
				$outparams .= "<$params[$i]>, ";
			}
			$outparams .= "<$params[$i]> and "; $i++; 
			$outparams .= "<$params[$i]>";
		}
		$does = $rawArr[$a]["does"];
		$prints = $rawArr[$a]["prints"];
		$tmp = "Write a function named \"$funcName\" that takes $outparams, $does and returns $prints.";
		// add constructed question attribute
		$rawArr[$a]["constructed"] = $tmp;
	}
	// return $outputarr;
}
// wrapper for constructQuestion just takes in json and returns json
function jsonConstructQuestions(&$queJSON){
	$decJSON = json_decode($queJSON, true);

	constructQuestions($decJSON["raw"]);
	// var_dump($decJSON["raw"]); exit();
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
