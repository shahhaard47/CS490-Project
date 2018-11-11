<?php

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

function gradeAll($grading_data) {
    $final_grades = array();
    foreach ($grading_data as $question_data) {
        $pkg = gradeQuestion($question_data);
        $pkg["questionID"] = (int)$question_data["questionID"];
        array_push($final_grades, $pkg);
    }
    return $final_grades;
}

?>