<?php

require_once ('autograder.php');
require_once('testGetQuestions.php');

/*--------------------------------------------------------------------------------------*/
//SAMPLE DATA AND TESTING
/*--------------------------------------------------------------------------------------*/
/** @noinspection PhpUnreachableStatementInspection */
$grading_data = array(
    array(
        "questionID" => 65,
        "points" => 30,
        "function_name" => "combineSorted",
        "student_response" => "def combineSorte(lst1, lst2):\n\tnewlst = lst1 + lst2\n\tnewlst.sort()\n\treturn newlst",
        "test_cases" => array("int [1,2,3,4,5],int [6,7,8,9,10];int [1,2,3,4,5,6,7,8,9,10]", "int [-2,-1,5,6],int [1,2,3,4];int [-2,-1,1,2,3,4,5,6]")
    ),
    array(
        "questionID" => 62,
        "points" => 20,
        "function_name" => "initialVowel",
        "student_response" => "def initialVowels(text):\n\twords=text.split(\" \")\n\tvowels=[]\n\tfor word in words:\n\t\tif('aeiou'.find(word[0])!=-1):\n\t\t\tvowels.append(word)\n\treturn vowels",
        "test_cases" => array(
            "str It was in this apartment also that there stood against the western wall a gigantic clock of ebony;str [It,in,apartment,also,against,a,of,ebony]",
            "str It was many and many a year ago in a kingdom by the sea that a maiden there lived whom you may know by the name of Annabel Lee and this maiden she lived with no other thought than to love and be loved by me;str [It,and,a,ago,in,a,a,of,Annabel,and,other,and]",
            "str It's impossible to go through life unscathed Nor should you want to By the hurts we accumulate we measure both our follies and our accomplishments;str [It's,impossible,unscathed,accumulate,our,and,our,accomplishments]",
            "str My mom always said life was like a box of chocolates You never know what you're gonna get;str [always,a,of]",
            "str str Sally sells seashells by the seashore;str []"
            )
    )/*,
    array(
        "questionID" => 1,
        "points" => 20,
        "function_name" => "printMe",
        "student_response" => "def printMee(name, num):\n\tprint(name*num)\n\treturn (name*num)",
        "test_cases" => array("str MAC,int 5;str MACMACMACMACMAC", "str CHEESE,int 2;str CHEESECHEESE")
    ),
    array(
        "questionID" => 3,
        "points" => 10,
        "function_name" => "roundNum",
        "student_response" => "def roundnum(num):\n\tnum=round(num)\n\treturn (3)",
        "test_cases" => array("float 3.14159;int 3", "float 2.7183;int 3", "float 1.5;int 2")
    )*/
); // not using this rn


$allQuestionBank = getQuestionsReadyToGrade("55");

$grades = gradeAll($allQuestionBank);
echo "-----------Grading test Data-----------\n";
var_dump($grades);
exit();





?>