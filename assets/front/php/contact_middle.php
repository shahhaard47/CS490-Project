<?php
/* Possible request types */
const LOGIN_RT = 'login',
GETQBANK_RT = 'getqbank',
CREATE_EXAM_RT = 'create_exam',
ADD_QUESTION_RT = 'add_question',
GET_AVAILABLE_EXAMS_RT = 'getAvailableExam',
SUBMIT_EXAM_RT = 'submit_exam',
REMOVE_QUESTION_FROM_BANK_RT = 'delete_question',
RELEASE_GRADE_RT = 'release_grade',
GRADE_EXAM_RT = 'gradeExam',
GET_ALL_CREATED_EXAMS = 'getAllCreatedExams';

$json = file_get_contents('php://input');
$json_decoded = json_decode($json);
//echo "json: ", $json; exit();

$url = '';
$req = $json_decoded->requestType;

if ($req == LOGIN_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/auth_login.php';
} elseif ($req == GETQBANK_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/request_questions.php';
} elseif ($req == CREATE_EXAM_RT) { //TODO
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == ADD_QUESTION_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == GET_AVAILABLE_EXAMS_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/getAvailableExam.php';
} elseif ($req == SUBMIT_EXAM_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == REMOVE_QUESTION_FROM_BANK_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == RELEASE_GRADE_RT) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == GET_ALL_CREATED_EXAMS) {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/comms.php';
} elseif ($req == 'allExamsToBeGraded') {
    $url = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/allExamsToBeGraded.php';
} elseif ($req == GRADE_EXAM_RT) {
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
