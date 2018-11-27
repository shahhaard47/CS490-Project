<?php
/*
GRADING NOTES AND ASSUMPTIONS
- assuming that there isn't a comment line before function title that has the actual function name
	- #def functionName()
*/

if (!defined("TESTING")) {
    define("TESTING", true);
}

if (TESTING) {
    echo "Testing...\n";
    echo __FILE__."\n";
}

define ("WRONG_FUNCTION_NAME", "header");
define ("CONSTRAINT_WHILELOOP", "while");
define ("CONSTRAINT_FORLOOP", "for");
define ("CONSTRAINT_RECURSION", "recursion");

// future checks
define ("INCORRECT_PARAMS_NAMES", "params");
define ("MISSING_COLON", "colon");

define ("FUNCTION_HEADER_NOT_FOUND", "no_header");
define ("NOT_RETURNING", "return");
define ("INCORRECT_PARAMS", "incorrect_parameters");

// all unexpected behaviors
define ("INVALID_SYNTAX_CANNOT_PARSE", "cannot_parse");

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
            $tmp = "Failed testcase$TCnumber: ";
            $tmp .= "input(".$currentTC[0]."), expected_output(".$currentTC[1];
            $tmp .= "), student_output(".$studentOutput.")\t[-";
            $tmp .= round($individualTCpoints, 2)." points]";
            array_push($finalComments, $tmp);
            $totalPoints -= $individualTCpoints;

            $testCasesPassFail[$i] = 0; // change back to 0 to be sure nothing breaks
        }
        else {
            ///
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
            case WRONG_FUNCTION_NAME:
                $tmp = "Used incorrect function name.\t[-".$deductCustomPoints."]"; break;
            case NOT_RETURNING:
                $tmp = "Function is not returning anything.\t[-".$deductCustomPoints."]"; break;
            case CONSTRAINT_WHILELOOP:
                $tmp = "Function is not using while loop.\t[-".$deductCustomPoints."]"; break;
            case CONSTRAINT_FORLOOP:
                $tmp = "Function is not using for loop.\t[-".$deductCustomPoints."]"; break;
            case CONSTRAINT_RECURSION:
                $tmp = "Function is not using recursion.\t[-".$deductCustomPoints."]"; break;
            case INCORRECT_PARAMS_NAMES:
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

    if (TESTING) {var_dump($final_package);}

    return $final_package;
}

//* Check for constraints
//  returns: updated $student_response

// returns  true if comment or empty line, false otherwise
function checkComment($line) {
    $trimmed = ltrim($line);
    if ($trimmed) { // not empty
        if ($trimmed[0] == '#') {
            return true;
        } else {
            return false;
        }
    }
    return true;
}

function checkConstraints(&$parsed, $constraints, $correctFunctionName) {
    if (!$parsed["gradable"]) {
        return;
    }

//    $constraints = explode(" ", $question_data["constraints"]);
    $studentResponse = $parsed["studentResponse"];
    $studentFunctionName = $parsed["studentFunctionName"];
    $constraintsSplit = explode(" ", $constraints);
    foreach ($constraintsSplit as $constraint) {
        // FIXME: check for constraints by looping line by line to make sure comments don't influence the check
        switch ($constraint) {
            case "for":
                // check for CONSTRAINT_FORLOOP
                $for_pos = strpos($studentResponse, "for");
                if ($for_pos === false) {
                    array_push($parsed["errorCodes"], CONSTRAINT_FORLOOP);
                }
                break;
            case "while":
                // check for CONSTRAINT_WHILELOOP
                $while_pos = strpos($studentResponse, "while");
                if ($while_pos === false) {
                    array_push($parsed["errorCodes"], CONSTRAINT_WHILELOOP);
                }
                break;
            case "recursion":
                // check for CONSTRAINT RECURSION
                if (!$studentFunctionName) {
                    $functionNameCount = substr_count($studentResponse, $correctFunctionName);
                    if ($functionNameCount < 2) {
                        array_push($parsed["errorCodes"], CONSTRAINT_RECURSION);
                    }
                }
                else {
                    // FIXME: deal with this (changed function header to correct title but recursion implemented with incorrect function title
                }
                break;
        }
    }
}

// return $parsed():
    // "newStudentResponse" => "def fun...",
    // "errorCodes"         => array(),
    // "gradable"           => true | false
function parseResponse($question_data) {
    $parsed = array(
        "studentResponse"       => $question_data["student_response"],
        "newStudentResponse"    => false,
        "errorCodes"            => array(),
        "gradable"              => true,
        "studentFunctionName"   => null // functiontitle student gives right or wrong (for recursion checking)
    );
    $correctFunctionName = $question_data["function_name"];

    $errorCodes = array();
    if (TESTING) {echo "BEFORE: \n".$parsed["studentResponse"]."\n"; }

    // traverse through studentResponse line by line
    $lines = explode("\n", $parsed["studentResponse"]);
    $functionTitleFound = false;
    $returnFound = false;
    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];

        if (checkComment($line)) { continue; }

        //* FIXME: check colon (need to figure out a way to store which line/s are missing it)

        $defPos = strpos($line, "def");
        if ($defPos !== false){
            $leftPar = strpos($line, "(");
            if ($leftPar !== false) {
                $functionTitleFound = true;

                //* check function title
                $funcTitle = substr($line, $defPos, $leftPar - $defPos);
//                $tmp = explode(" ", $funcTitle);
//                $student_function_name = $tmp[count($tmp) - 1];
                $studentFunctionName = end(explode(" ", $funcTitle));
                if ($studentFunctionName != $correctFunctionName){
                    // replace
                    $lines[$i] = str_replace($studentFunctionName, $correctFunctionName, $line);
                    array_push($errorCodes, WRONG_FUNCTION_NAME);
                    $parsed["studentResponse"] = implode("\n", $lines);
                    $parsed["newStudentResponse"] = true;
                    $parsed["studentFunctionName"] = $studentFunctionName;
                }

                //* FIXME: check for number of parameters

            }
            else {
                array_push($errorCodes, INVALID_SYNTAX_CANNOT_PARSE);
                $parsed["gradable"] = false;
            }
        }
        else {
            $tmp = ltrim($line);
            $tmp = explode(" ", $tmp);
            if ($tmp[0] == "return") {
                $returnFound = true;
            }
        }
    }
    if (TESTING) {echo "AFTER: \n".$parsed["studentResponse"]."\n"; }

    // check whether function header was found
    if ($functionTitleFound == false) {
        array_push($errorCodes, FUNCTION_HEADER_NOT_FOUND);
        $parsed["gradable"] = false;
    }

    // check whether function returns
    if ($returnFound == false) {
        array_push($errorCodes, NOT_RETURNING);
        $parsed["gradable"] = false;
    }

    // passing $parsed as reference
    checkConstraints($parsed, $question_data["constraints"], $correctFunctionName);

    return $parsed;
}

function constructUngradableComments($maxPoints, $errorCodes) {

}

function gradeQuestion($question_data) {
//    $student_filename = 'tmppy/student.py';
    $test_file = 'tmppy/test.py';
    /*$CANNOT_GRADE = false;
    //*	1. sample student response
    $student_response = $question_data["student_response"];
    $function_name = $question_data["function_name"];
    $errorCodes = array(); // strings of comments
    // check if function title in named properly
    $lines = explode("\n", $student_response);
    if (TESTING) {echo "BEFORE: \n$student_response\n";}
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
                array_push($errorCodes, WRONG_FUNCTION_NAME);
            }
            break; // func header is good
        }
    }
    $student_response = implode("\n", $lines);

    if ($functionHeaderFound == false) {
//        echo "DID NOT FIND\n"; exit();
        array_push($errorCodes, FUNCTION_HEADER_NOT_FOUND);
        $CANNOT_GRADE = true;
    }
    if (TESTING) {echo "AFTER: \n$student_response\n";}

    // check for "return"
    $return_pos = strpos($student_response, "return");
    if ($return_pos === false) {
        array_push($errorCodes, NOT_RETURNING);
        $CANNOT_GRADE = true;
    }

    //  check for restraints
    $constraints = explode(" ", $question_data["constraints"]);
    foreach ($constraints as $constraint) {
        switch ($constraint) {
            case "for":
                // check for CONSTRAINT_FORLOOP
                $for_pos = strpos($student_response, "for");
                if ($for_pos === false) {
                    array_push($errorCodes, CONSTRAINT_FORLOOP);
                }
                break;
            case "while":
                // check for CONSTRAINT_WHILELOOP
                $while_pos = strpos($student_response, "while");
                if ($while_pos === false) {
                    array_push($errorCodes, CONSTRAINT_WHILELOOP);
                }
                break;
            case "recursion":
                // check for CONSTRAINT RECURSION
                $functionNameCount = substr_count($student_response, $function_name);
                if ($functionNameCount < 2) {
                    array_push($errorCodes, CONSTRAINT_RECURSION);
                }
                break;
        }
    }*/

    $parsedData = parseResponse($question_data);

    /* FIXME - CONTINUE NOTE
        - then add more parsingPlanned (check for number of parameters, semicolon,
            (make sure recursion check always works)
        - then FIRST make middle files for anything front uses to submit comments using 'comms.php'
            - once that is done do this (so that i can worry about delimiters and control
                                                the translation of messages)
                - work with Emad to send me 2D arrays of information
                - then work with Debbie for her to accept "strings to store and send back strings only"
    */
    $function_name = $question_data["function_name"];
    $student_response = $parsedData["studentResponse"];
    $errorCodes = $parsedData["errorCodes"];

    if (!$parsedData["gradable"]) {
        echo "NOT GRADABLE...\n";


    }

    //* get testcases from database
    $test_cases = $question_data["test_cases"];
    $functioncalls = constructFunctionCalls($function_name, $test_cases);

    $TCresults = array(); // size should be count($functioncalls) RETURNING
    foreach ($functioncalls as $call) {
        $callll = $call[0]; $correct_response = $call[1];
        $correct_response = explode(" ", $correct_response);
        $type = array_shift($correct_response);
        $expectedValue = implode(" ", $correct_response);

        if ($type == "bool" || $type == 'list') {
            // don't need to do anything
            $expectedValue = "$expectedValue"; // this doesn't do anything but makes the logic looks symmetric
        }
        else {
            $expectedValue = "\"$expectedValue\"";
        }

        // check student_response
        $text = $student_response."\n";
        $text .= "response = $callll\n";
        $text .= "correct = $type($expectedValue)\n";
        $text .= "if (response == correct):\n";
        $text .= "\tprint('output is correct')\n";
        $text .= "else:\n";
        $text .= "\tprint(response)";

        $file = fopen($test_file, 'w');
        if ($file){
            fwrite($file, $text);
            fclose($file);
        }
        elseif (TESTING) {
            echo "COULD NOT OPEN FILE\n";
        }
        $command = escapeshellcmd("python $test_file");
        $student_output = shell_exec($command);

        // compare outputs
        $outputpos = strpos($student_output, "output is correct");
        if ($outputpos !== false){
            array_push($TCresults, 1);
        }
        else {
            if ($student_output) {
                // remove any newline characters from end
                $outputString = rtrim($student_output);
            }
            else {
                // FIXME: show what the error was
                $outputString = "RUNTIME_ERROR";
            }
            array_push($TCresults, $outputString);
        }
    }

    $TCtotal = count($functioncalls);
    if (count($TCresults) == $TCtotal) { // just a sanity to make sure everything went well
        $maxPoints = $question_data["points"];
        $rtn_package = constructCommentsAndPoints($maxPoints, $test_cases, $TCresults, $errorCodes);
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