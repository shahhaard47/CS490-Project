<?php

function constructQuestion($funcName, $params, $does, $returns) {
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

function constructExams($raw, $exams, $userID=0){
    $detailed_info = array();
    $user_exam_ids = array();

    foreach ($raw as $r_elem){
        $examid = $r_elem["examID"];
        if ($userID) {
            $userid = $userID;
        } else {
            $userid = $r_elem["userID"];
        }

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

    //  get all exams in "Exam"
    $all_exams_ids = array();
    //  var_dump($exams); exit();
    foreach ($exams as $examinfo){
        array_push($all_exams_ids, $examinfo["examID"]);
    }

    // combine the two $user_exam_ids and $detailed_info
    $return_array = array();
    foreach ($user_exam_ids as $elem) {
        $userid = $elem[0];
        $examid = $elem[1];

        $foundidx = array_search($examid, $all_exams_ids);
        $examTitle = $exams[$foundidx]["examName"];

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

    return $return_array;
}

?>