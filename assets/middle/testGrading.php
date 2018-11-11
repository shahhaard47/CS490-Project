<?php

require_once ('autograder.php');

/*--------------------------------------------------------------------------------------*/
//SAMPLE DATA AND TESTING
/*--------------------------------------------------------------------------------------*/
/** @noinspection PhpUnreachableStatementInspection */
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
// var_dump($grading_data);
echo "-----------Grading test Data-----------\n";
var_dump($grades);
exit();





?>