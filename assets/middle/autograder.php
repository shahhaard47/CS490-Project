<?php

/*
GRADING NOTES AND ASSUMPTIONS
- assuming that there isn't a comment line before function title that has the actual function name
	- #def functionName()
*/

function listifyOutput($output) {
    $lbr_pos = strpos($output, "[");
    if ($lbr_pos === false) {
        // not a list
        return $output;
    }
    else { // found list
        $tmpSplit = preg_split('/("|,|\[|\])/', $output, -1, PREG_SPLIT_NO_EMPTY);
        $type = array_shift($tmpSplit);
        for ($i = 0; $i < count($tmpSplit); $i++) {
            $tmpSplit[$i] = "$type(\"$tmpSplit[$i]\")";
        }
        $output = implode(", ", $tmpSplit);
        $output = "list [$output]";
    }
    return $output;
}

function listifyInput($inp) {
    $lbr_pos = strpos($inp, "[");
    if ($lbr_pos === false) {
        $inp = str_replace(",",":", $inp);
        return $inp;
    }
    else {
        // replace ',' between params with ':'
        $outside = true;
        for ($i = 0; $i < strlen($inp); $i++) {
            if ($outside == true && $inp[$i] == ',') {
                $inp[$i] = ":"; continue;
            }
            if ($inp[$i] == '[') {
                $outside = false; continue;
            }
            if ($inp[$i] == ']') {
                $outside = true; continue;
            }
        }
        $inps = explode(":", $inp);
        for ($i = 0; $i < count($inps); $i++) {
            $inps[$i] = listifyOutput($inps[$i]);
        }
        $inp = implode(":", $inps);
//        var_dump($inp);
    }
    return $inp;
}

function constructFunctionCalls($functionName, $testcases) {
    $functioncalls = array();
    foreach ($testcases as $case) {
        $tc = array();
        $call = "";
        $call = "$functionName(";
//        $case = preg_split('/(,|;)/', $case, -1, PREG_SPLIT_NO_EMPTY);

        $tmpCaseSplit = explode(";", $case);
        $caseInput = $tmpCaseSplit[0];
        $caseInput = listifyInput($caseInput);
        $inputParams = explode(":", $caseInput);

//        echo "COUNT TMPCASESPLIT: ".count($tmpCaseSplit)."\n";
        $expectedOutput = $tmpCaseSplit[1]; // assuming size 2
        $expectedOutput = listifyOutput($expectedOutput);

        for ($i = 0; $i < count($inputParams)-1; $i++) {
            $tmp = explode(" ", $inputParams[$i]);
            $type = array_shift($tmp); // pop first element
            $param = implode(" ", $tmp);
            if ($type != 'list') {
                $param = "\"$param\"";
            }
            $call .= "$type($param), ";
        }
        $tmp = explode(" ", $inputParams[$i]);
        $type = array_shift($tmp); // pop first element
        $param = implode(" ", $tmp);
        if ($type != 'list') {
            $param = "\"$param\"";
        }
        $call .= "$type($param))";

        array_push($tc, $call);
        // if output is list
        array_push($tc, $expectedOutput);
        // var_dump($tc);
        array_push($functioncalls, $tc);
    }

//    var_dump($functioncalls); exit();
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

    $constraints = explode(" ", $question_data["constraints"]);
    foreach ($constraints as $constraint) {
        switch ($constraint) {
            case "for":
                // check for CONSTRAINT_FORLOOP
                $for_pos = strpos($student_response, "for");
                if ($for_pos === false) {
                    array_push($comments, "CONSTRAINT_FORLOOP");
                }
                break;

            case "while":
                // check for CONSTRAINT_WHILELOOP
                $while_pos = strpos($student_response, "while");
                if ($while_pos === false) {
                    array_push($comments, "CONSTRAINT_WHILELOOP");
                }
                break;

            case "recursion":
                // check for CONSTRAINT RECURSION
                $functionNameCount = substr_count($student_response, $function_name);
                if ($functionNameCount < 2) {
                    array_push($comments, "CONSTRAINT_RECURSION");
                }
                break;
        }
    }
    //*	2. write student_response to py file
    $file = fopen($student_filename, 'w');
    if ($file) {
        fwrite($file, $student_response);
        fclose($file);
    }

    //*	5. get testcases from database
    $test_cases = $question_data["test_cases"];
    $functioncalls = constructFunctionCalls($function_name, $test_cases);

    //*	6. compare student's to correct response's on individual testcases
    $TCresults = array(); // size should be count($functioncalls) RETURNING
    $TCtotal = count($functioncalls);
    foreach ($functioncalls as $call) {
        $callll = $call[0]; $correct_response = $call[1];
        $correct_response = explode(" ", $correct_response);
        $type = array_shift($correct_response);
        $returnValue = implode(" ", $correct_response);

        if ($type != "list") { // if == list don't add QUOTES around returnValue
            $returnValue = "\"$returnValue\"";
        }

//        $cmd = shell_exec(escapeshellcmd("touch littifworks.txt"));
//        var_dump($cmd);
//        exit();


        // get students output
//        $text = "#!/usr/bin/env python\n";
        /*$text .= "from student import *\n";
        $text .= "response = $callll\n";
        $text .= "correct = $type($returnValue)\n";
        $text .= "if (response == correct):\n";
        $text .= "\tprint('output is correct')\n";
        $text .= "else:\n";
        $text .= "\tprint(response)\n";*/
        $text = "print('YOOOOLLLLLOOOOO')\n";
//        $cmd = shell_exec(escapeshellcmd("python -c \"".$text."\""));

        echo "TESTING TEXT FILE\n";
        var_dump($text);

//        $cmd = shell_exec(escapeshellcmd("chmod 777 tmppy/*"));
//        $cmd = shell_exec(escapeshellcmd(""));
//        var_dump($cmd);


        $file = fopen($test_file, 'w');
        $check = fopen($_SERVER['DOCUMENT_ROOT'].'test.txt','w');
        if ($check) {
            echo "FUCK YES\n";
        }
        else {
            echo "FUCK YOU\n";
        }
        exit();

        if ($file){
            echo "OPENED SUCCessfully\n";
            fwrite($file, $text);
            fclose($file);
        }
        else {
            echo "(middle) grading could NOT open grading file.\n";
        }

        $command = escapeshellcmd("python ".$test_file);
        echo "COMMAND\n";
        var_dump($command);
        $student_output = shell_exec($command);

        echo "TEST OUTPUT\n";
        var_dump($student_output); exit();

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
//        var_dump($rtn_package); exit();
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