<?php

function constructQuestion(&$rawArr) {
	// $outputarr = array();
	// foreach ($rawArr as $que) {
	for ($a = 0; $a < count($rawArr); $a++) {
		$que = $rawArr[$a];
		$funcName = $rawArr[$a]["functionName"];
		$params = $rawArr[$a]["parameters"]; // array
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
		$does = $rawArr[$a]["functionDescription"];
		$prints = $rawArr[$a]["output"];
		$tmp = "Write a function named \"$funcName\" that takes $outparams, $does and prints $prints.";
		// add constructed question attribute
		$rawArr[$a]["constructed"] = $tmp;

		// $tmparr = array($tmp, $rawArr[$a]["difficulty"], $rawArr[$a]["points"], $rawArr[$a]["questionID"]);
		// echo "$tmp\n";
		// array_push($outputarr, $tmparr);
	}
}
	// return $outputarr;

	//*	curl send to debbie
$curl_opts = array(CURLOPT_POST => 1,
	CURLOPT_URL => 'https://web.njit.edu/~ds547/CS490-Project/assets/back/getAvailableExams.php',
	CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);

$decoded = json_decode($result, true);
constructQuestion($decoded);
// foreach ($decoded as $exam) {
// 	echo "------------------------------------------------------\n";
// 	var_dump($exam);
// }
$encoded = json_encode($decoded);
echo $encoded;

?>
