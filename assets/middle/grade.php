<?php

/*
1. get student response from database
2. write student response to file
3. get correct response from database
4. write correct response to file
5. get testcases from database
6. compare student's to correct response's on individual testcases
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

function gradeQuestion($question_data) {
	$student_filename = 'tmppy/student.py';
	$test_file = 'tmppy/test.py';
	//*	1. sample student response
	$student_response = $question_data["student_response"];
	//*	2. write student_response to py file
	$file = fopen($student_filename, 'w');
	if ($file) {
		fwrite($file, $student_response);
		fclose($file);
	}
	//*	5. get testcases from database
	$function_name = $question_data["function_name"];
	$test_cases = $question_data["test_cases"];
	$functioncalls = constructFunctionCalls($function_name, $test_cases);
	// echo var_dump($functioncalls)."\n";

	//*	6. compare student's to correct response's on individual testcases
	$TCresults = array(); // size should be count($functioncalls)
	$TCtotal = count($functioncalls);
	foreach ($functioncalls as $call) {
		$callll = $call[0]; $correct_response = $call[1];
		$correct_response = explode(" ", $correct_response);
		// get students output
		$text = "from student import *\n";
		$text .= "response = $callll\n";
		$text .= "correct = $correct_response[0]('$correct_response[1]')\n";
		$text .= "if (response == correct):\n";
		$text .= "\tprint('correct')\n";
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
		if ($student_output == "correct\n"){
			array_push($TCresults, 1);
		}
		else {
			array_push($TCresults, 0);
		}
	}

	if (count($TCresults) == $TCtotal) {
		return $TCresults;
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
		$tcs = gradeQuestion($question_data);
		$correct = array_sum($tcs);
		$ratio = $correct / count($tcs);
		$points = $question_data["points"];
		$score = $points * $ratio;
		$score = round($score);
		$tmpQuestion = array(	"questionID" => (int)$question_data["questionID"],
								"qScore" => (int)$score,
								"testcases" => $tcs);
		array_push($final_grades, $tmpQuestion);
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
						"questionID" => 1,
						"points" => 20,
						"function_name" => "printMe",
						"student_response" => "def printMe(name, num):\n\treturn (name*num)",
						"test_cases" => array("str MAC,int 5;str MACMACMACMACMAC", "str CHEESE,int 2;str CHEESECHEESE")
							),
					array(
						"questionID" => 3,
						"points" => 10,
						"function_name" => "roundNum",
						"student_response" => "def roundNum(num):\n\tnum=round(num)\n\treturn (3)",
						"test_cases" => array("float 3.14159;int 3", "float 2.7183;int 3", "float 1.5;int 2")
						)
					);

// $question_data = $grading_data[1];
// $score = gradeQuestion($question_data);
// echo "Score: $score\n";

$grades = gradeAll($grading_data);
echo "GradingData:\n";
var_dump($grading_data);
echo "-----------Final-----------\n";
var_dump($grades);
exit();

/* same as gradeAll uncomment if that breaks and needs debugging with the following code that WORKS
	$qidx = 0;
	//*	1. sample student response
	// $student_response =<<<END
	// def printMe(name, num):
	// 	for i in range(num):
	// 		print(name, i+1)
	// END;
	$student_response = $grading_data[0]["student_response"];

	//* 3. correct response
	// $correct_response =<<<END
	// def printMe(name, num):
	// 	for i in range(num):
	// 		print(name, i+1)
	// END;
	$correct_response = $grading_data[0]["correct_response"];

	$student_filename = 'tmppy/student.py';
	$correct_filename = 'tmppy/correct.py';
	$test_file = 'tmppy/test.py';

	// so something like the following
	$functionName = $grading_data[$qidx]["function_name"];
	$testcases = $grading_data[$qidx]["test_cases"]; // get array of test_cases
	// echo var_dump($testcases)."\n";

	//*	2. write student_response to py file
	$file = fopen($student_filename, 'w');
	if ($file) {
		fwrite($file, $student_response);
		fclose($file);
	}

	//*	4. write correct_response to py file
	$file = fopen($correct_filename, 'w');
	if ($file) {
		fwrite($file, $correct_response);
		fclose($file);
	}

	//*	5. get testcases from database
	$functioncalls = constructFunctionCalls($functionName, $testcases);

	//*	6. compare student's to correct response's on individual testcases
	foreach ($functioncalls as $call) {
		// get students output
		$text = "from student import *\n";
		$text .= "$call\n";
		$file = fopen($test_file, 'w');
		if ($file){
			fwrite($file, $text);
			fclose($file);
		}
		$command = escapeshellcmd("python $test_file");
		$student_output = shell_exec($command);
		print("Student output\n$student_output");

		$text = "from correct import *\n";
		$text .= "$call\n";
		$file = fopen($test_file, 'w');
		if ($file){
			fwrite($file, $text);
			fclose($file);
		}
		$command = escapeshellcmd("python ".$test_file);
		$correct_output = shell_exec($command);
		print("Correct output\n$correct_output");

		// compare outputs
		if ($student_output == $correct_output)
			echo "MATCHED\n";
		else
			echo "NO MATCH\n";
	}
*/
/* uses system() to run python and also allows you to get return value (might be useful if checking for returning function)
	//*run the py file
	// Outputs all the result of shellcommand "ls", and returns
	// the last output line into $last_line. Stores the return value
	// of the shell command in $retval.
	$last_line = system('python tmppy/tmp.py', $retval);
	// Printing additional info
	echo "lastline: $last_line\n";
	echo "retval: $retval\n";
*/


?>