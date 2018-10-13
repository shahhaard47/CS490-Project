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
		$call = "";
		$call = "$functionName(";
		// echo "$functionName(";
		if (count($case) == 0) {
			$call .= ")";
			// echo ")";
		}
		else {
			for ($i = 0; $i < count($case)-1; $i++) {
				$call .= "\"$case[$i]\"".", ";
				// echo "$case[$i], ";
			}
			$call .= "\"$case[$i]\"".")";
			// echo "$case[$i])";
		}
		array_push($functioncalls, $call);
	}
	return $functioncalls;
}

//* SAMPLE DATA
//*	1. sample student response
$student_response =<<<END
def printMe(name, num):
	for i in range(num):
		print(name, i+1)
END;
//* 3. correct response
$correct_response =<<<END
def printMe(name, num):
	for i in range(num):
		print(name, i+1)
END;
$student_filename = 'tmppy/student.py';
$correct_filename = 'tmppy/correct.py';
$test_file = 'tmppy/test.py';

$functionName = "printMe";
$testcases = array(array("Haard", "5"), 
					array("Mac", "7"),
					array("CHEESE", "2"));

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
	$student_output = shell_exec($command);
	print("Correct output\n$student_output");
}


// run the py file
// Outputs all the result of shellcommand "ls", and returns
// the last output line into $last_line. Stores the return value
// of the shell command in $retval.
// $last_line = system('python tmppy/tmp.py', $retval);
// // Printing additional info
// echo "lastline: $last_line\n";
// echo "retval: $retval\n";



//*	then run instructors code 





?>