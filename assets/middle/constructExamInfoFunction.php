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

// convert gradedComments to tabular format aka 2d array
//function convertToTabularArray($gradedComments) {
//    if (count($gradedComments) < 2) { // sanity check (don't have 1 or 0 line comments)
//        if (TESTING) {
//            echo "(middle) count(gradedComments) < 2 is true\n";
//        }
//        return;
//    }
//
//    $gradedCommentsArray = array();
//
//    $summary = array();
//    $first = $gradedComments[0];
//
//    $normalCase = strpos($first, "Testcases");
//    if ($normalCase !== false) {
//
//    }
//
//
//    if (TESTING) {
//        echo "GRADED COMMENTS:\n"; var_dump($gradedComments);
//        exit();
//    }
//}

function constructExams($raw, $userID=0){
    $detailed_info = array();
    $user_exam_ids = array();

    $uniqueExams = 0;

    foreach ($raw as $r_elem){
        $examid = $r_elem["examID"];
        if ($userID) {
            $userid = $userID;
        } else {
            $userid = $r_elem["userID"];
        }

        $examScore                  = $r_elem["examScore"];
        $qid                        = $r_elem["questionID"];
        $qpoints                    = $r_elem["points"];
        $originalStudentResponse    = $r_elem["studentResponse"];
        $studentResponse            = $r_elem["modResponse"];
        $funcName                   = $r_elem["functionName"];
        $params                     = $r_elem["parameters"];
        $does                       = $r_elem["does"];
        $returns                    = $r_elem["prints"];
        $testCases                  = $r_elem["testCases"];
        $testCasesPassFail          = $r_elem["testCasesPassFail"];
        // new stuff
        $constraints                = $r_elem["constraints"];
        $topic                      = $r_elem["topic"];
        $gradedComments             = $r_elem["gradedComments"];
//        $gradedCommentsArray        = convertToTabularArray($gradedComments);
        $instructorComments         = $r_elem["instructorComments"];
        $constructed = constructQuestion($funcName, $params, $does, $returns);
        $questioninfo_arr = array(
            "questionID"		    => $qid,
            "points" 			    => $qpoints,
            "maxPoints"             => $r_elem["totalPoints"],
            "constructed"		    => $constructed,
            "originalResponse"      => $originalStudentResponse,
            "studentResponse"	    => $studentResponse,
            "testCases"			    => $testCases,
            "testCasesPassFail"     => $testCasesPassFail,
            "constraints"		    => $constraints,
            "topic"				    => $topic,
            "gradedComments" 	    => $gradedComments,
            "instructorComments"    => $instructorComments
//            "gradedCommentsArray"   => $gradedCommentsArray
        );
        if ($detailed_info["$userid"]["$examid"]){ // same student same exam NEW question
            array_push($detailed_info["$userid"]["$examid"]["examQuestions"], $questioninfo_arr);
        }
        else { // NEW user or NEW exam or NEW both
            array_push($user_exam_ids, array($userid, $examid));
            $uniqueExams++;
            $detailed_info["$userid"]["$examid"] = array(
                "otherInfo" => array(
                    array("userID"      , $userid),
                    array("examID"      , $examid),
                    array("overallScore", $examScore),
                    array("examName"    , $r_elem["examName"]),
                    array("released"    , $r_elem["released"])
                ),
                "examQuestions" => array($questioninfo_arr)
            );
        }
    }

    $returnExamsArray = array();
    foreach ($user_exam_ids as $elem) {
        $userID = $elem[0];
        $examID = $elem[1];

        $examInfo = $detailed_info["$userID"]["$examID"];
        $otherInfo = $examInfo["otherInfo"];

        $tmp = array();
        foreach ($otherInfo as $info) {
            $k = $info[0];
            $tmp["$k"] = $info[1];
        }
        $tmp["examQuestions"] = $examInfo["examQuestions"];

        array_push($returnExamsArray, $tmp);
    }
/*
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


    }*/

    return $returnExamsArray;
}

?>