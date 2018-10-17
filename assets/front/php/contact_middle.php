<?php
/* Possible request types */
const LOGIN_RT = 'login',
GETQBANK_RT = 'getqbank',
CREATE_EXAM_RT = 'create_exam',
ADD_QUESTION_RT = 'add_question',
GET_AVAILABLE_EXAMS_RT = 'getAvailableExams',
SUBMIT_EXAM_RT = 'submit_exam';

$json = file_get_contents('php://input');
$json_decoded = json_decode($json);

$url = '';
$req = $json_decoded->requestType;

if ($req == LOGIN_RT) {
//    echo 'login';
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/auth_login.php';
} elseif ($req == GETQBANK_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/request_question.php';
} elseif ($req == CREATE_EXAM_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == ADD_QUESTION_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == GET_AVAILABLE_EXAMS_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/getallexams.php';
} elseif ($req == SUBMIT_EXAM_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == 'allExamsToBeGraded') {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == 'allExamsToBeGraded') {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == 'releaseGrade') {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/grade.php';
}


$curl_opts = array(
    CURLOPT_POST => 1,
    CURLOPT_URL => $url,
    CURLOPT_POSTFIELDS => $json,
    CURLOPT_RETURNTRANSFER => 1

);
$ch = curl_init();
curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);

if ($error_msg = curl_error($ch)) {
    echo $error_msg;
}

echo $result;

curl_close($ch);
