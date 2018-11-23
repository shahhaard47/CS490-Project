<?php

function loginToDatabase() {

}

function getTestQuestionInGradingFormat($questionID) {
    $decoded = array("questionID" => $questionID);
    $jsonrequest = json_encode($decoded);

    $backfile = "test.php";
    $url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;
    $curl_opts = array(CURLOPT_POST => 1,
        CURLOPT_URL => $url,
        CURLOPT_POSTFIELDS => $jsonrequest,
        CURLOPT_RETURNTRANSFER => 1);
    $ch = curl_init();
    curl_setopt_array($ch, $curl_opts);
    $result = curl_exec($ch);
    $result = json_decode($result, true);
    return $result;
}

/*
 * Information needed for grading data:
 * - questionID
 * - points = 10
 * - function_name
 * - student_response
 * - test_cases
 * - topic
 * - constraints
 */

function getAllQuestions() {
    $questionsEncoded = file_get_contents("./info_files/BETA_questionBank.json");
    $questions = json_decode($questionsEncoded, true);
    return $questions;
}

function getQuestion($id) {
    $all = getAllQuestions();
    foreach ($all as $question) {
        if (array_search($id, $question)) {
            return array($question);
        };
    }
    echo "Could not find questionID ".$id."\n";
    exit(1);
}


function is_json($str) {
    return json_decode($str) != null;
}

function getAllSolutions() {
    $solutionsEncoded = file_get_contents("./info_files/solutionsALL-nov14.json");
    $solutions = json_decode($solutionsEncoded, true);
    return $solutions;
}

function combineQuestionsAndSolutions($whichQuestion="all") {
    if ($whichQuestion == 'all') {
        $questions = getAllQuestions();
    }
    else {
        $questionID = $whichQuestion;
        $questions = getQuestion($questionID);
    }
    $solutions = getAllSolutions();
    $customPoints = 10;
    $rtnArray = array();

    // insert solutions in questions
    for ($i = 0; $i < count($questions); $i++) {
//        var_dump($questions[$i]);

        // change functionName to function_name
        $questions[$i]["function_name"] = $questions[$i]["functionName"];
        unset($questions[$i]["functionName"]);
        // explode testCases to test_cases with ":"
        $questions[$i]["test_cases"] = explode(":", $questions[$i]["testCases"]);
        unset($questions[$i]["testCases"]);

        $funcName = $questions[$i]["function_name"];
//        echo "solution: ".$solutions[$funcName]."\n";
        if ($solutions["$funcName"]) {
            $questions[$i]["student_response"] = $solutions["$funcName"];
            $questions[$i]["points"] = $customPoints;
            array_push($rtnArray, $questions[$i]);
        }
    }
    return $rtnArray;
}

function getQuestionsReadyToGrade($whichQuestion) {
    return combineQuestionsAndSolutions($whichQuestion);
}

//$tmp = getQuestionsReadyToGrade();
//var_dump($tmp);

/*function getAllSolutions() {
    $all_solutions_qbank = file('../back/questionBank_allSolutions.txt');
    $state = 'before_func'; // in_func | after_func | in_sol
    $i = 0;
    foreach ($all_solutions_qbank as $line) {
        echo strlen($line)."\n";

        if ($i++ > 10) {
            break;
        }
    }
}*/

//$allquestions = getAllQuestions();
//$decoded = json_decode($allquestions);
//var_dump($decoded[0]);


$allSolutions = array(
    "cube" => "def cube(num):
	return (num**3)",
    "isSubstring" => "def isSubstring(str1,str2):
	if(str1 in str2):
		return True
	else:
		return False",
    "initialVowels" => "def initialVowels(text):
	count=0
	for word in text.split(' '):
		if(word[0] in 'aeiouAEIOU'):
			count=count+1
	return count",
    "mathOperations" => "def mathOperations(operation,int1,int2):
	if(operation=='-'):
		return(int1-int2)
	elif(operation=='+'):
		return(int1+int2)
	elif(operation=='*'):
		return(int1*int2)
	elif(operation=='/'):
		return(int1/int2)
	elif(operation=='%'):
		return(int1%int2)
	else:
		return 0",
    "fibonacci" => "def fibonacci(num):
	if(num==0):
		return '0'
	elif(num==1):
		return '0'
	elif(num==2):
		return '0,1'
	else:
		nums=['0','1']
		for i in range(num-2):
			nextNum=int(nums[i])+int(nums[i+1])
			nums.append(str(nextNum))
	return ','.join(nums)",
    "backwards" => "def backwards(word):
	revWord=''
	for char in word:
		revWord=char+revWord
	return revWord",
    "combineSorted" => "def combineSorted(list1,list2):
	list3=list1+list2
	list3.sort()
	return list3",
    "doubleIt" => "def doubleIt(num):
	return (num*2)",
    "lessThanX" => "def lessThanX(listOfNums,x):
	nums=[]
	for num in listOfNums:
		if(num<x):
			nums.append(num)
	return nums",
    "listOverlap" => "def listOverlap(listOfNumsA,listOfNumsB):
	overlap=[]
	for num in listOfNumsA:
		if(num in listOfNumsB and num not in overlap):
			overlap.append(num)
	for num in listOfNumsB:
		if(num in listOfNumsA and num not in overlap):
			overlap.append(num)
	return overlap",
    "isPal" => "def isPal(word):
	revWord=word[::-1]
	print(word+' '+revWord)
	if(revWord==word):
		return True
	else:
		return False",
    "caesarEncrypt" => "def caesarEncrypt(text):
	alpha='abcdefghijklmnopqrstuvwxyz'
	APLPHA=alpha.upper()
	encryptedText=''
	for char in text:
		if(char.isupper()):
			index=ALPHA.find(char)
			encryptedText=encryptedText+ALPHA[index+3]
		else:
			index=alpha.find(char)
			encryptedText=encryptedText+alpha[index+3]
	return encryptedText",
    "inRange" => "def inRange(testNum,floor,ceiling):
	if(testNum>floor and testNum<ceiling):
		return True
	else:
		return False",
    "containsLetter" => "def containsLetter(char,text):
	words=[]
	for word in text.split(' '):
		if(char in word):
			words.append(word)
	return words",
    "wordCount" => "def wordCount(maxWordLen,text):
	count=0
	for word in text.split(' '):
		if(len(word)<=maxWordLen):
			count=count+1
	return count",
    "generateInitials" => "def generateInitials(fullName):
	initials=''
	for name in fullName.split(' '):
		initials=initials+name[0]+'.'
	return initials",
    "isAMinHeap" => "def isAMinHeap(A):
	j=len(A)-1
	if((j+1)==1 or (j+1)==0):
		return True
	k=int((j-1)/2)
	if(A[j]>A[k]):
		return(isAMinHeap(A[0:j]))
	else:
		return False",
    "allOdds" => "def allOdds(n):
	i=0
	nums=[]
	while(i<=n):
		if(i % 2 != 0):
			nums.append(i)
		i=i+1
	return nums",
    "factorial" => "def factorial(num):
	val=1
	for i in range(1,num+1):
		val=val*i
	return val"
);
//$allSolutionsEncoded = json_encode($allSolutions);

//echo $allSolutionsEncoded;

// write solutions to solutionsALL-nov14.json
/*$solutions_file = "solutionsALL-nov14.json";
$solutions_file = fopen($solutions_file, "w");
if ($solutions_file) {
    fwrite($solutions_file, $allSolutionsEncoded);
    fclose($solutions_file);
}*/

?>