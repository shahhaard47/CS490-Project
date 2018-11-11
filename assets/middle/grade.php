<?php

/*
1. get student response from database
2. write student response to file
3. get correct response from database
4. write correct response to file
5. get testcases from database
6. compare student's to correct response's on individual testcases
*/

/*
GRADING NOTES AND ASSUMPTIONS
- assuming that there isn't a comment line before function title that has the actual function name
	- #def functionName()
*/

function constructFunctionCalls($functionName, $testcases) {
	$functioncalls = array();
	foreach ($testcases as $case) {
		$tc = array();
		// echo "case: $case\n";
		$call = "";
		$call = "$functionName(";
		// $case = explode(",", $case);
		$case = preg_split('/(,|;)/', $case, -1, PREG_SPLIT_NO_EMPTY);
		// var_dump($case[count($case)-1]);
		// echo "$functionName(";
		if (count($case) == 1) {
			$call .= ")";
			// echo ")";
		}
		else {
			for ($i = 0; $i < count($case)-2; $i++) {
				$a = explode(" ", $case[$i]);
				$call .= "$a[0]('$a[1]')".", ";
				// echo "$case[$i], ";
			}
			$a = explode(" ", $case[$i]);
			$call .= "$a[0]('$a[1]')".")";
			// echo "$case[$i])";
		}
		// echo $call."\n";
		array_push($tc, $call);
		array_push($tc, $case[count($case)-1]);
		// var_dump($tc);
		array_push($functioncalls, $tc);
	}
	// var_dump($functioncalls);
	return $functioncalls;
}

/*
@arg $testCasesPassFail = [1, 0, 0 ...]
@arg $otherComments = ["WRONG_HEADER", "NOT_RETURNING", "CONSTRAINT_FORLOOP", ..., "CONSTRAINT_WHILELOOP"]
*/
function constructCommentsAndPoints($maxPoints, $testCases, $testCasesPassFail, $otherComments) {
	$finalComments = array();
	$individualTCpoints = $maxPoints / count($testCases);
	$passedTC = 0;
	$totalPoints = $maxPoints;
	for ($i = 0; $i < count($testCases); $i++) {
		if ($testCasesPassFail[$i] == 0) {
			$currentTC = explode(";", $testCases[$i]);
			$TCnumber = $i + 1;
			$tmp = "Failed testcase$TCnumber: "."input(".$currentTC[0]."), expected_output(".$currentTC[1].")\t[-".round($individualTCpoints, 2)." points]";
			array_push($finalComments, $tmp);
			$totalPoints -= $individualTCpoints;
		}
		else {
			$passedTC++;
		}
	}
	$TCsummary = "Testcases passed: ".$passedTC."/".count($testCases);
	array_unshift($finalComments, $TCsummary);

	// otherComments
	$deductCustomPoints = $deduction = 2.0;
	foreach ($otherComments as $c) {
		if ($totalPoints < $deductCustomPoints) {
			$deduction = 0;
			$totalPoints = 0;
		}

		switch ($c) {
			case "WRONG_HEADER":
				$tmp = "Used incorrect function header.\t[-".$deductCustomPoints."]";
				array_push($finalComments, $tmp);
				break;
			case "NOT_RETURNING":
				$tmp = "Function is not returning anything.\t[-".$deductCustomPoints."]";
				array_push($finalComments, $tmp);
				break;
			case "CONSTRAINT_WHILELOOP":
				$tmp = "Function is not using while loop.\t[-".$deductCustomPoints."]";
				array_push($finalComments, $tmp);
				break;
			case "CONSTRAINT_FORLOOP":
				$tmp = "Function is not using for loop.\t[-".$deductCustomPoints."]";
				array_push($finalComments, $tmp);
				break;
			case "CONSTRAINT_RECURSION":
				$tmp = "Function is not using recursion.\t[-".$deductCustomPoints."]";
				array_push($finalComments, $tmp);
				break;
		}
		$totalPoints -= $deduction;
	}

	$totalPoints = round($totalPoints, 2);
	$tmp = "Final score: ".$totalPoints."/".$maxPoints;
	array_push($finalComments, $tmp);

	$final_package = array("qScore" => $totalPoints,
							"testCasesPassFail" => $testCasesPassFail,
							"comments" => $finalComments
							);
	// var_dump($final_package);

	return $final_package;
}

function gradeQuestion($question_data) {
	$student_filename = 'tmppy/student.py';
	$test_file = 'tmppy/test.py';
	//*	1. sample student response
	$student_response = $question_data["student_response"];
	$function_name = $question_data["function_name"];

	$comments = array(); // strings of comments

	// check if function title in named properly
	$lines = explode("\n", $student_response);
	// echo "BEFORE: \n$student_response\n";
	for ($i = 0; $i < count($lines); $i++) {
		$line = $lines[$i];
		$def_pos = strpos($line, "def");
		// echo "defpos ";
		// var_dump($def_pos);
		if ($def_pos !== false) {
			$l_par = strpos($line, "(");
			// FIXME: what to do when l_par is false
			if ($l_par === false) { continue; }
			$func_title = substr($line, $def_pos, $l_par - $def_pos);
			$tmp = explode(" ", $func_title);

			$student_function_name = $tmp[count($tmp) - 1];
			if ($student_function_name != $function_name){
				// replace
				$lines[$i] = str_replace("$student_function_name", "$function_name", $line);
				array_push($comments, "WRONG_HEADER");
			}
			break; // func header is good
		}
	}
	$student_response = implode("\n", $lines);
	// echo "AFTER: \n$student_response\n";

	// check for "return"
	$return_pos = strpos($student_response, "return");
	if ($return_pos === false) {
		array_push($comments, "NOT_RETURNING");
	}

	// FIXME: check for restraints 

	//*	2. write student_response to py file
	$file = fopen($student_filename, 'w');
	if ($file) {
		fwrite($file, $student_response);
		fclose($file);
	}

	//*	5. get testcases from database
	$test_cases = $question_data["test_cases"];
	$functioncalls = constructFunctionCalls($function_name, $test_cases);
	// echo var_dump($functioncalls)."\n";

	//*	6. compare student's to correct response's on individual testcases
	$TCresults = array(); // size should be count($functioncalls) RETURNING
	$TCtotal = count($functioncalls);
	foreach ($functioncalls as $call) {
		$callll = $call[0]; $correct_response = $call[1];
		$correct_response = explode(" ", $correct_response);
		// get students output
		$text = "from student import *\n";
		$text .= "response = $callll\n";
		$text .= "correct = $correct_response[0]('$correct_response[1]')\n";
		$text .= "if (response == correct):\n";
		$text .= "\tprint('output is correct')\n";
		$text .= "else:\n";
		$text .= "\tprint('wrong')\n";

		$file = fopen($test_file, 'w');
		if ($file){
			fwrite($file, $text);
			fclose($file);
		}
		$command = escapeshellcmd("python $test_file");
		$student_output = shell_exec($command);

		// compare outputs
		$outputpos = strpos($student_output, "output is correct");
		if ($outputpos !== false){
			array_push($TCresults, 1);
		}
		else {
			array_push($TCresults, 0);
		}
	}

	if (count($TCresults) == $TCtotal) { // just a sanity to make sure everything went well
		$maxPoints = $question_data["points"];
		$rtn_package = constructCommentsAndPoints($maxPoints, $test_cases, $TCresults, $comments);
		return $rtn_package;
	}
	else {
		// something went wrong 
		echo "(middle): error while grading. Testcase and function calls mismatch. Terminating.";
		exit();
	}
}

/* returns 2D array of dims (questionID, 2)
example:
$final_grades=  [
					[questionID, score, [testcase1pass, testcase2fail,...]],
					[1, 17, [1, 0, 1, 1]],
					[3, 3, [1, 1, 0, 0]]
				]
*/
function gradeAll($grading_data) {
	$final_grades = array();
	foreach ($grading_data as $question_data) {
		$pkg = gradeQuestion($question_data);
		$pkg["questionID"] = (int)$question_data["questionID"];
		array_push($final_grades, $pkg);
	}
	return $final_grades;
}

// Main stuff

//* get front's grading request 							F -> M
$jsonrequest = file_get_contents('php://input');
// extract examID and userID
$decoded = json_decode($jsonrequest, true);

//* testing without front input
// $decoded = array("examID" => 49, "userID" => "mscott");
// $decoded = array("examID" => 46, "userID" => "jsnow");
$jsonrequest = json_encode($decoded);

$examID = $decoded["examID"];
$userID = $decoded["userID"];

if ($decoded["examID"] && $decoded["userID"]) {
	//* forward the request to back 						M -> B
	$backfile = "get_grading_info.php";
	$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;
	$curl_opts = array(CURLOPT_POST => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $jsonrequest,
		CURLOPT_RETURNTRANSFER => 1);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_opts);
	$result = curl_exec($ch); // should be json				B -> M

	//* extract the grading data from $result
	$grading_data = json_decode($result, true);

	//check that the database is still up
	if ($decoded["conn"] && $decoded["conn"] == false) {
		echo $result;
		exit();
	}

	// echo "Grading data received from debbie\n";
	// var_dump($grading_data); exit();

	//* perform grading
	$grades = gradeAll($grading_data); 

	// check if grading worked
	if (count($grades) != count($grading_data)) {
		// error occured since num_rows of both are not the same
		echo "(middle) Error: while grading could not autograde\n";
		exit();
	}

	//* send grades to back 								M -> B
	// 	package
	$grades_pack = array("userID" => $userID,
						"examID" => $examID,
						"scores" => $grades);

	// var_dump($grades_pack);
	$grades_encoded = json_encode($grades_pack);
	//	send
	$backfile = "update_grade.php";
	$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;
	$curl_opts = array(CURLOPT_POST => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $grades_encoded,
		CURLOPT_RETURNTRANSFER => 1);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_opts);
	$result = curl_exec($ch); // should be string			B -> M

	//* check update status and report back to front 		M -> F
	if (strpos($result, "error") === false) { // SUCCESS
		// send the grades to front
		// echo true;
		echo $grades_encoded;
	}
	else {
		// unsuccessful grading or updating report error to front
		echo "(back)".$result."\n";
	}
}
exit(); // everthing after this is test data and test scripts

// sample $grades_output_decoded
// {"userID":"mscott","examID":49,"scores":[{"questionID":45,"qScore":25,"testcases":[1,0]},{"questionID":48,"qScore":0,"testcases":[0,0]}]}
/** @noinspection PhpUnreachableStatementInspection */
$grades_output_decoded = array("userID" => "mscott",
								"examID" => 49,
								"scores" => array(array("questionID" => 45,
														"qScore" => 25,
														"testcases" => array(1, 0)),
												array("questionID" => 48,
														"qScore" => 0,
														"testcases" => array(0, 0))
											)
							);

/*--------------------------------------------------------------------------------------*/
//SAMPLE DATA AND TESTING
/*--------------------------------------------------------------------------------------*/
$grading_data = array(array(
						"questionID" => 3,
						"points" => 10,
						"function_name" => "roundNum",
						"student_response" => "def roundnum(num):\n\tnum=round(num)\n\treturn (3)",
						"test_cases" => array("float 3.14159;int 3", "float 2.7183;int 3", "float 1.5;int 2")),
					array("questionID" => 1,
						"points" => 20,
						"function_name" => "printMe",
						"student_response" => "def printMee(name, num):\n\tprint(name*num)\n\treturn (name*num)",
						"test_cases" => array("str MAC,int 5;str MACMACMACMACMAC", "str CHEESE,int 2;str CHEESECHEESE")));

// $question_data = $grading_data[1];
// $score = gradeQuestion($question_data);
// echo "Score: $score\n";

$grades = gradeAll($grading_data);
echo "GradingData:\n";
// var_dump($grading_data);
echo "-----------Final-----------\n";
var_dump($grades);
exit();


?>