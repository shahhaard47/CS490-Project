<?php
/*
GRADING NOTES AND ASSUMPTIONS
- assuming that there isn't a comment line before function title that has the actual function name
	- #def functionName()
*/

define("TESTING", false);

if (TESTING) {
    echo "Testing...\n";
    echo __FILE__."\n";
}

define ("WRONG_HEADER", "header");
define ("NOT_RETURNING", "return");
define ("CONSTRAINT_WHILELOOP", "while");
define ("CONSTRAINT_FORLOOP", "for");
define ("CONSTRAINT_RECURSION", "recursion");
// future checks
define ("INCORRECT_PARAMS", "params");
define ("MISSING_COLON", "colon");


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
        $inp = str_replace(",",":", $inp); //FIXME: tmp did this for some functionality when iwas doing lists
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
//    echo "TESTCASES\n";var_dump($testcases);
    foreach ($testcases as $case) {
        $tc = array();
        $call = "";
        $call = "$functionName(";
//        $case = preg_split('/(,|;)/', $case, -1, PREG_SPLIT_NO_EMPTY);
        $tmpCaseSplit = explode(";", $case);

        if (count($tmpCaseSplit) != 2) {
            // assuming size 2 everywhere (make sure its size 2)
        }

        $caseInput = $tmpCaseSplit[0]; $caseInput = trim($caseInput);
//        echo "TRIMMED:"; var_dump($caseInput);
        $caseInput = listifyInput($caseInput);
//        echo "LISTIFIED:"; var_dump($caseInput);
        $inputParams = explode(":", $caseInput);
//        var_dump($inputParams);

        for ($i = 0; $i < count($inputParams)-1; $i++) {
            $inputParams[$i] = trim($inputParams[$i]);
            $tmp = explode(" ", $inputParams[$i]);
            $type = array_shift($tmp); // pop first element
            $param = implode(" ", $tmp);
//            if ($type != 'list') {
//                $param = "\"$param\"";
//            }
            if ($type == 'list' || $type == 'bool') {
                $param = "$param";
            } else {
                $param = "\"$param\"";
            }
            $call .= "$type($param), ";
        }
        $inputParams[$i] = trim($inputParams[$i]);
        $tmp = explode(" ", $inputParams[$i]);
//        var_dump($tmp);
        $type = array_shift($tmp); // pop first element
        $param = implode(" ", $tmp);
        if ($type == 'list' || $type == 'bool') {
            $param = "$param";
        } else {
            $param = "\"$param\"";
        }
        $call .= "$type($param))";

        array_push($tc, $call);
        // if output is list

        $expectedOutput = $tmpCaseSplit[1]; // assuming size 2
        $expectedOutput = listifyOutput($expectedOutput);
//        var_dump($expectedOutput);
        array_push($tc, $expectedOutput);
        // var_dump($tc);
        array_push($functioncalls, $tc);
    }
//    echo "FUNCTIONCALLS: ".$functionName."\n";
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
        if ($testCasesPassFail[$i] != 1) {
            $studentOutput = $testCasesPassFail[$i];
            $currentTC = explode(";", $testCases[$i]);
            $TCnumber = $i + 1;
            $tmp = "Failed testcase$TCnumber: "."input(".$currentTC[0]."), expected_output(".$currentTC[1]."), student_output(".$studentOutput.")\t[-".round($individualTCpoints, 2)." points]";
            array_push($finalComments, $tmp);
            $totalPoints -= $individualTCpoints;

            $testCasesPassFail[$i] = 0; // change back to 0 to be sure nothing breaks
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
            case WRONG_HEADER:
                $tmp = "Used incorrect function header.\t[-".$deductCustomPoints."]"; break;
            case NOT_RETURNING:
                $tmp = "Function is not returning anything.\t[-".$deductCustomPoints."]"; break;
            case CONSTRAINT_WHILELOOP:
                $tmp = "Function is not using while loop.\t[-".$deductCustomPoints."]"; break;
            case CONSTRAINT_FORLOOP:
                $tmp = "Function is not using for loop.\t[-".$deductCustomPoints."]"; break;
            case CONSTRAINT_RECURSION:
                $tmp = "Function is not using recursion.\t[-".$deductCustomPoints."]"; break;
            case INCORRECT_PARAMS:
                $tmp = "Used incorrect parameter name/s\t[-".$deductCustomPoints."]"; break;
        }
        array_push($finalComments, $tmp);
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

$flag_wrongHeader = "WRONG_HEADER";

function gradeQuestion($question_data) {
//    $student_filename = 'tmppy/student.py';
    $test_file = 'tmppy/test.py';
    //*	1. sample student response
    $student_response = $question_data["student_response"];
    $function_name = $question_data["function_name"];
    $comments = array(); // strings of comments
    // check if function title in named properly
    $lines = explode("\n", $student_response);
//    if (TESTING) {echo "BEFORE: \n$student_response\n";}
    $functionHeaderFound = false;

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        $def_pos = strpos($line, "def");
//        if(TESTING) {echo "defpos "; var_dump($def_pos);}
        if ($def_pos !== false) {
            $l_par = strpos($line, "(");

            if ($l_par === false) { continue; }

            $functionHeaderFound = true;

            $func_title = substr($line, $def_pos, $l_par - $def_pos);
            $tmp = explode(" ", $func_title);
            $student_function_name = $tmp[count($tmp) - 1];
            if ($student_function_name != $function_name){
                // replace
                $lines[$i] = str_replace("$student_function_name", "$function_name", $line);
                array_push($comments, WRONG_HEADER);
            }
            break; // func header is good
        }
    }
    $student_response = implode("\n", $lines);

    if ($functionHeaderFound == false) {
//        echo "DID NOT FIND\n"; exit();
    }


//    echo "AFTER: \n$student_response\n";
//    exit();
    // check for "return"
    $return_pos = strpos($student_response, "return");
    if ($return_pos === false) {
        array_push($comments, NOT_RETURNING);
    }
    // FIXME: check for restraints
    $constraints = explode(" ", $question_data["constraints"]);
    foreach ($constraints as $constraint) {
        switch ($constraint) {
            case "for":
                // check for CONSTRAINT_FORLOOP
                $for_pos = strpos($student_response, "for");
                if ($for_pos === false) {
                    array_push($comments, CONSTRAINT_FORLOOP);
                }
                break;
            case "while":
                // check for CONSTRAINT_WHILELOOP
                $while_pos = strpos($student_response, "while");
                if ($while_pos === false) {
                    array_push($comments, CONSTRAINT_WHILELOOP);
                }
                break;
            case "recursion":
                // check for CONSTRAINT RECURSION
                $functionNameCount = substr_count($student_response, $function_name);
                if ($functionNameCount < 2) {
                    array_push($comments, CONSTRAINT_RECURSION);
                }
                break;
        }
    }
    //*	2. write student_response to py file
//    $file = fopen($student_filename, 'w');
//    if ($file) {
//        fwrite($file, $student_response);
//        fclose($file);
//        echo "WROTE STUDENT FILE\n";
//    }
    //*	5. get testcases from database
    $test_cases = $question_data["test_cases"];
    $functioncalls = constructFunctionCalls($function_name, $test_cases);
//    var_dump($functioncalls); exit();
    //*	6. compare student's to correct response's on individual testcases
    $TCresults = array(); // size should be count($functioncalls) RETURNING
    $TCtotal = count($functioncalls);
    foreach ($functioncalls as $call) {
        $callll = $call[0]; $correct_response = $call[1];
        $correct_response = explode(" ", $correct_response);
        $type = array_shift($correct_response);
        $expectedValue = implode(" ", $correct_response);
//        if ($type != "list") { // if == list don't add QUOTES around expectedValue
//            $expectedValue = "\"$expectedValue\"";
//        }
        if ($type == "bool" || $type == 'list') {
            // don't need to do anything
            $expectedValue = "$expectedValue"; // this doesn't do anything but makes the logic looks symmetric
        }
        else {
            $expectedValue = "\"$expectedValue\"";
        }
        // get students output
//        $text = "from student import *\n";
        $text = $student_response."\n";
        $text .= "response = $callll\n";
        $text .= "correct = $type($expectedValue)\n";
        $text .= "if (response == correct):\n";
        $text .= "\tprint('output is correct')\n";
        $text .= "else:\n";
        $text .= "\tprint(response)";

//        echo "TEST FILE\n";
//        echo $text."\n";

        $file = fopen($test_file, 'w');
        if ($file){
//            echo "OPENED FILE SUCCESSFULLY\n";
            fwrite($file, $text);
            fclose($file);
//            echo "WROTE TEST FILE\n";
        }
        else {
//            echo "COULD NOT OPEN FILE\n";
        }
        $command = escapeshellcmd("python $test_file");
        $student_output = shell_exec($command);

//        echo "RAN TEST FILE... Student output:";
//        var_dump($student_output);
        // compare outputs
        $outputpos = strpos($student_output, "output is correct");
        if ($outputpos !== false){
            array_push($TCresults, 1);
        }
        else {
            // attach the output here (extract from $student_output string between 'Oi' and 'Of')
//            $startPos = strpos($student_output, 'Oi');
//            if ($startPos !== false) {
//                $startPos += 2; // length of 'Oi' = 2
//                $endPos = strpos($student_output, 'Of');
//                $lengthOfOutput = $endPos - $startPos;
//                $outputString = substr($student_output, $startPos, $lengthOfOutput);
//            }
            if ($student_output) {
                $outputString = $student_output;
            }
            else {
                $outputString = "RUNTIME_ERROR";
            }
            array_push($TCresults, $outputString);
        }
    }
//    exit();
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
//    echo $final_grades;
    return $final_grades;
}
?>