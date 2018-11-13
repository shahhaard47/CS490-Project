<?php

//require_once ("allExamsToBeGraded.php");

function constructQuestion($funcName, $params, $does, $returns) {
    // $outputarr = array();
    // foreach ($rawArr as $que) {
    $outparams = "";
    if (count($params) == 1) {
        $outparams = "parameter <$params[0]>";
    }
    elseif (count($params) == 2) {
        $outparams = "parameters <$params[0]> and <$params[1]>";
    }
    else {
        $outparams .= "parameters ";
        for ($i = 0; $i < count($params)-2; $i++) {
            $outparams .= "<$params[$i]>, ";
        }
        $outparams .= "<$params[$i]> and "; $i++;
        $outparams .= "<$params[$i]>";
    }

    $constructed = "Write a function named \"$funcName\" that takes $outparams, $does and returns $returns.";
    return $constructed;
}

function constructExams($raw, $exams, $userid){
    $detailed_info = array();
    $user_exam_ids = array();

//    var_dump($raw); exit();
    foreach ($raw as $r_elem){
//        $userid = $r_elem["userID"];
        $examid = $r_elem["examID"];

        $examScore= $r_elem["examScore"];
        $qid = $r_elem["questionID"];
        $qpoints = $r_elem["points"];
        $studentResponse = $r_elem["studentResponse"];
        $funcName = $r_elem["functionName"];
        $params = $r_elem["parameters"];
        $does = $r_elem["does"];
        $returns = $r_elem["prints"];
        $testCases = $r_elem["testCases"];
        $testCasesPassFail = $r_elem["testCasesPassFail"];
        // new stuff
        $constraints = $r_elem["constraints"];
        $topic = $r_elem["topic"];
        $gradedComments = $r_elem["gradedComments"];
        $constructed = constructQuestion($funcName, $params, $does, $returns);
        $questioninfo_arr = array(	"questionID"		=> $qid,
            "points" 			=> $qpoints,
            "constructed"		=> $constructed,
            "studentResponse"	=> $studentResponse,
            "testCases"			=> $testCases,
            "testCasesPassFail" => $testCasesPassFail,
            "constraints"		=> $constraints,
            "topic"				=> $topic,
            "gradedComments" 	=> $gradedComments,
            "instructorComments" => $r_elem["instructorComments"]
        );
        if ($detailed_info["$userid"]["$examid"]){ // same student same exam NEW question
            array_push($detailed_info["$userid"]["$examid"][1], $questioninfo_arr);
        }
        else { // NEW user or NEW exam or NEW both
            array_push($user_exam_ids, array($userid, $examid));
            $detailed_info["$userid"]["$examid"] = array($examScore, array($questioninfo_arr));
        }
    }

// var_dump($user_exam_ids);
// var_dump($detailed_info);

//get all exams in "Exam"
    $all_exams_ids = array();
//    var_dump($exams); exit();
    foreach ($exams as $examinfo){
        array_push($all_exams_ids, $examinfo["examID"]);
    }

// combine the two $user_exam_ids and $detailed_info
    $return_array = array();
    foreach ($user_exam_ids as $elem) {
//        var_dump($elem); exit();

        $userid = $elem[0];
        $examid = $elem[1];

        // get examtitle
        $foundidx = array_search($examid, $all_exams_ids);
//        var_dump($foundidx);
//        $foundidx = 0;
        $examTitle = $exams[$foundidx]["examName"];
//        var_dump($examTitle); exit();

        $overallScore = $detailed_info["$userid"]["$examid"][0];
        $examQuestions = $detailed_info["$userid"]["$examid"][1];
        array_push($return_array, array(
            "userID" => $userid,
            "examID" => $examid,
            "examName" => $examTitle,
            "overallScore" => $overallScore,
            "examQuestions" => $examQuestions
        ));
    }
//    var_dump($return_array); exit();
    return $return_array;
}

$jsonrequest = file_get_contents('php://input');
$decoded = json_decode($jsonrequest, true);

//$decoded = array("userID" => "jsnow");
//$jsonrequest = json_encode($decoded);

if ($decoded["userID"]) {
    $userid = $decoded["userID"];
} else {
    echo "(error) userID should be passed to view student exam info\n";
    exit();
}


$backfile = "allStudentExamInfo.php";
$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;

$curl_opts = array(CURLOPT_POST => 1,
    CURLOPT_URL => $url,
    CURLOPT_POSTFIELDS => $jsonrequest,
    CURLOPT_RETURNTRANSFER => 1);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);

$decoded = json_decode($result, true);

//var_dump($decoded); exit();

$raw = $decoded["raw"];
//var_dump($raw); exit();

$exams = $decoded["exams"];

//var_dump($decoded);
//exit();
$return_array = constructExams($raw, $exams, $userid);

//var_dump($return_array); exit();

$encoded_return_array = json_encode($return_array);

// var_dump($return_array);
echo $encoded_return_array;










?>
