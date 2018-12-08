<?php
/*
GRADING NOTES AND ASSUMPTIONS
- assuming that there isn't a comment line before function title that has the actual function name
	- #def functionName()
*/

if (!defined("TESTING")) {
    define("TESTING", false);
}

if (TESTING) {
    echo "Testing... ";
    echo __FILE__."\n";
}

define ("TEST_FILE", "tmppy/test.py");

define ("WRONG_FUNCTION_NAME", "header");
define ("CONSTRAINT_WHILELOOP", "while");
define ("CONSTRAINT_FORLOOP", "for");
define ("CONSTRAINT_RECURSION", "recursion");

// future checks
define ("INCORRECT_PARAMS_NAMES", "params");
define ("MISSING_COLON", "colon");

// checks that result in ungradable behaviour
define ("FUNCTION_HEADER_NOT_FOUND", "Function header not found.");
define ("NOT_RETURNING", "Return statement not found");
define ("INCORRECT_PARAMS", "Incorrect number of parameters");

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
        $inp = str_replace(",",":", $inp); //FIXME: tmp did this for some functionality when i was doing lists
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
//        if (TESTING) {echo "ORIGINAL TC:\n"; var_dump($case);}
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

//        if (TESTING) { echo "CONSTRUCTED TC:\n"; var_dump($tc); exit(); }

        array_push($functioncalls, $tc);
    }
//    if (TESTING) {
//        echo "FUNCTION CALLS: \n"; var_dump($functioncalls); exit();
//    }
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
            default:
                if (TESTING) { echo "(middle) encounter unhandled comment/constraint.\n";}
                continue;
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

//    if (TESTING) {var_dump($final_package);}

    return $final_package;
}

function constructUngradeableComments($maxPoints, $errorCodes) {

    $finalComments = array();
    $totalPoints = 0;
    array_push($finalComments, "Question could not be auto-graded because of following reasons:");
    $finalComments = array_merge($finalComments, $errorCodes);
    array_push($finalComments, "Final score: ".$totalPoints."/".$maxPoints);

    $final_package = array(
        "qScore" => $totalPoints,
        "testCasesPassFail" => "",
        "comments" => $finalComments
    );

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


// implied that $line is not a comment
function checkColonError($line) {
    $line = trim($line);
    $first = explode(" ", $line)[0];
    $first = preg_split('/( |\(|\:)/', $line, -1, PREG_SPLIT_NO_EMPTY);
//    if (TESTING) {echo "SPLIT CHECK: "; var_dump($first);}
    $first = $first[0];

    if ($first == "def" || $first == "if" || $first == "else" || $first == "elif" || $first == "for" || $first == "while") {
        // check for trailing colon
        if (substr($line, -1) != ':') {
            return true;
        }
    }
    return false; // no colon error
}

// return $parsed():
    // "newStudentResponse" => "def fun...",
    // "errorCodes"         => array(),
    // "gradable"           => true | false
define ("PARSEKEY_STUDENT_RESPONSE", "studentResponse");
define ("PARSEKEY_MODRESPONSE", "modResponse");
define ("PARSEKEY_ERROR_CODES", "errorCodes");
define ("PARSEKEY_GRADABLE", "gradable");
define ("PARSEKEY_STUDENT_FUNCTION_NAME", "studentFunctionName")   ;

function parseResponse($question_data) {
    $parsed = array(
        PARSEKEY_STUDENT_RESPONSE       => $question_data["student_response"],
        PARSEKEY_MODRESPONSE            => $question_data["student_response"],
        PARSEKEY_ERROR_CODES            => array(),
        PARSEKEY_GRADABLE               => true,
        PARSEKEY_STUDENT_FUNCTION_NAME  => null // functiontitle student gives right or wrong (for recursion checking)
    );
    $correctFunctionName = $question_data["function_name"];

//    if (TESTING) {echo "BEFORE: \n".$parsed["studentResponse"]."\n"; }

    // traverse through studentResponse line by line
    $lines = explode("\n", $parsed[PARSEKEY_STUDENT_RESPONSE]);
    $mlines = explode("\n", $parsed[PARSEKEY_MODRESPONSE]);
    $functionTitleFound = false;
    $returnFound = false;
    for ($i = 0; $i < count($lines); $i++) {
//        $line = $lines[$i];
//        $mline = $mlines[$i];

        if (checkComment($lines[$i])) { continue; }

//        if (TESTING) {echo "LINE: "; var_dump($line);}

        if (checkColonError($lines[$i])) { // true if colon missing
            $lines[$i] = rtrim($lines[$i]).":";
//            $lines[$i] = $line;
            $mlines[$i] = rtrim($mlines[$i])."<mark>:</mark>";
//            $mlines[$i] = $mline;
            $parsed[PARSEKEY_STUDENT_RESPONSE] = implode("\n", $lines);
            $parsed[PARSEKEY_MODRESPONSE] = implode("\n", $mlines);
        }

        $defPos = strpos($lines[$i], "def");
        if ($defPos !== false){
            $leftPar = strpos($lines[$i], "(");
            if ($leftPar !== false) {
                $functionTitleFound = true;

                //* check function title
                $funcTitle = substr($lines[$i], $defPos, $leftPar - $defPos);
//                $tmp = explode(" ", $funcTitle);
//                $student_function_name = $tmp[count($tmp) - 1];
                $studentFunctionName = end(explode(" ", $funcTitle));
                if ($studentFunctionName != $correctFunctionName){
                    // replace
                    $lines[$i] = str_replace($studentFunctionName, $correctFunctionName, $lines[$i]);
                    $mlines[$i] = str_replace($studentFunctionName, "<mark>".$correctFunctionName."</mark>",
                        $mlines[$i]);
                    array_push($parsed[PARSEKEY_ERROR_CODES], WRONG_FUNCTION_NAME);
                    $parsed[PARSEKEY_STUDENT_RESPONSE] = implode("\n", $lines);
                    $parsed[PARSEKEY_MODRESPONSE] = implode("\n", $mlines);
                    $parsed[PARSEKEY_STUDENT_FUNCTION_NAME] = $studentFunctionName;
                }

                //* FIXME: check for number of parameters
                // check for parameters (assuming colon is present at the end)
                // note: still uses old $line to get parameters (so that $leftPar matches)
                $leftPar = strpos($lines[$i], "("); // look for left par again (it might be changed during replacing question
                $parameters = substr($lines[$i], $leftPar+1, -2);

                $numParams = count(explode(",", $parameters));

                $idealNumParams = count($question_data["params"]); // NOTE: this did not work because "parameters" was not being returned from back
//                $tc1params = explode(";", $question_data["test_cases"][0]);
//                $idealNumParams = count(explode(",", $tc1params[0]));

                if (TESTING) {
//                    echo "PARAM INFO:\n";
//                    echo "params: "; var_dump($parameters);
//                    echo "num params: "; var_dump($numParams);
//                    echo "ideal params: "; var_dump($tc1params[0]); // this is for when i was using test_cases to get params
//                    echo "ideal num params: "; var_dump($idealNumParams);
//                    exit();
                }

                if ($numParams != $idealNumParams) {
                    array_push($parsed[PARSEKEY_ERROR_CODES], INCORRECT_PARAMS);
                    $parsed[PARSEKEY_GRADABLE] = false;
                }
//                if (TESTING) {
//                    echo "numParams: $numParams\n";
//                    echo "ideal: $idealNumParams\n"; exit();
//                }
            }
            else {
                array_push($parsed[PARSEKEY_ERROR_CODES], INVALID_SYNTAX_CANNOT_PARSE);
                $parsed[PARSEKEY_GRADABLE] = false;
            }
        }
        else {
            $tmp = ltrim($lines[$i]);
            $tmp = explode(" ", $tmp);
            if ($tmp[0] == "return") {
                $returnFound = true;
            }
        }

    }
//    if (TESTING) {echo "AFTER: \n".$parsed["studentResponse"]."\n"; }

    // check whether function header was found
    if ($functionTitleFound == false) {
        array_push($parsed[PARSEKEY_ERROR_CODES], FUNCTION_HEADER_NOT_FOUND);
        $parsed[PARSEKEY_GRADABLE] = false;
    }

    // check whether function returns
    if ($returnFound == false) {
        array_push($parsed[PARSEKEY_ERROR_CODES], NOT_RETURNING);
        $parsed[PARSEKEY_GRADABLE] = false;
    }

    // passing $parsed as reference
    checkConstraints($parsed, $question_data["constraints"], $correctFunctionName);

    return $parsed;
}

function extractError($programOutputString) {
    $student_output = explode("\n", $programOutputString);
    $outputString = "";
    $errorDetail = "";
    if (count($student_output) > 1) {
        $last = $student_output[count($student_output)-1];
        $errorTag = explode(":", $last)[0];
        for ($i = 0; $i < count($student_output); $i++) {
            $errorMarker = strpos($student_output[$i], "^");
            if ($errorMarker !== false) {
                $errorDetail = substr($student_output[$i-1], 0, $errorMarker)."<mark>".substr($student_output[$i-1], $errorMarker, 1). "</mark>".substr($student_output[$i-1], $errorMarker+1);
            }
        }
        if ($errorDetail) { $outputString = $errorTag." --> ".$errorDetail; }
        else { $outputString = $errorTag; }
    }
    else {
        $outputString = rtrim($student_output);
    }
    return $outputString;
}

function runTestCases($functionCalls, $studentResponse) {
    $TCresults = array(); // size should be count($functioncalls) RETURNING
    foreach ($functionCalls as $call) {
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
        $text = $studentResponse."\n";
        $text .= "response = $callll\n";
        $text .= "correct = $type($expectedValue)\n";
//        if (TESTING) {
//            $text .= "print('TEST check response:', response)\n";
//            $text .= "print('TEST check correct:', correct)\n";
//        }
        $text .= "if (response == correct):\n";
        $text .= "\tprint('output is correct')\n";
        $text .= "else:\n";
        $text .= "\tprint(response)";

        $file = fopen(TEST_FILE, 'w');
        if ($file){
            fwrite($file, $text);
            fclose($file);
        }
        elseif (TESTING) {
            echo "COULD NOT OPEN FILE\n";
        }
        $command = escapeshellcmd("python ".TEST_FILE);
//        if (TESTING) { var_dump($command); }
//        $student_output = shell_exec($command);
        $student_output = "";
        exec("$command 2>&1", $student_output);
        if (TESTING) {
//            echo "raw STUDENT OUT: \n"; var_dump($student_output);
//            exit();
        }
        $student_output = implode("\n", $student_output);

        // compare outputs
        $outputpos = strpos($student_output, "output is correct");
        if ($outputpos !== false){
            array_push($TCresults, 1);
        }
        else {
//            $studentOutputLines = explode("\n", $student_output);
//            if (count($studentOutputLines) > 1) { // means an error probably occured
//                $lastLine = $studentOutputLines[count($studentOutputLines) - 1];
//                $outputString = explode(":", $lastLine)[0];
//            } else {
//                $outputString = rtrim($student_output);
//            }

            $outputString = extractError($student_output);

            array_push($TCresults, $outputString);
        }
    }
//    if (TESTING) exit();

    $TCtotal = count($functionCalls);
    if (count($TCresults) == $TCtotal) {
        return $TCresults;
    } else {
        return false;
    }
}

function gradeQuestion($question_data) {
//    $student_filename = 'tmppy/student.py';
//    $test_file = 'tmppy/test.py'; // don't need
    $maxPoints = $question_data["points"];

    $parsedData = parseResponse($question_data);
    if (TESTING) {
//        echo "Q DATA:\n"; var_dump($question_data); exit();
//        echo "PARSED DATA:\n"; var_dump($parsedData); exit();
    }

    $function_name = $question_data["function_name"];
    $student_response = $parsedData["studentResponse"];
    $errorCodes = $parsedData["errorCodes"];


    if (!$parsedData["gradable"]) {
        if (TESTING) {echo "NOT GRADABLE...\n";}
        $rtn_package = constructUngradeableComments($maxPoints, $errorCodes);
    }
    else {
        //* get testcases from database
        $test_cases = $question_data["test_cases"];
        $functioncalls = constructFunctionCalls($function_name, $test_cases);

        $TCresults = runTestCases($functioncalls, $student_response);

        if ($TCresults) { // just a sanity to make sure everything went well
            $rtn_package = constructCommentsAndPoints($maxPoints, $test_cases, $TCresults, $errorCodes);
        }
        else {
            // something went wrong
            echo "(middle): error while grading. Testcase and function calls mismatch. Terminating.";
            exit();
        }
    }

    $rtn_package[PARSEKEY_MODRESPONSE] = $parsedData[PARSEKEY_MODRESPONSE];
//    if (TESTING) {
//        echo $rtn_package[PARSEKEY_NEW_STUDENT_RESPONSE]."\n"; exit();
//    }
//    if (TESTING) {
//        echo "graded:\n";
//        var_dump($rtn_package);
//        exit();
//    }

    return $rtn_package;
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