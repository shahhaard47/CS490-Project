<?php
//echo exec('ps -up '.getmypid());
#exit();
//ini_set('display_errors',1); error_reporting(E_ALL);
/*
1. get student response from database
2. write student response to file
3. get correct response from database
4. write correct response to file
5. get testcases from database
6. compare student's to correct response's on individual testcases
*/

include ('autograder.php');



//* get front's grading request 							F -> M
$jsonrequest = file_get_contents('php://input');
// extract examID and userID
$decoded = json_decode($jsonrequest, true);

//echo "REQUEST: $jsonrequest\n";
//* testing without front input
//$decoded = array("examID" => 72, "userID" => "mscott", "requestType" => "gradeExam");

//$decoded = array("examID" => 80, "userID" => "jsnow"); $jsonrequest = json_encode($decoded);

$examID = $decoded["examID"];
$userID = $decoded["userID"];

if ($decoded["examID"] && $decoded["userID"]) {
	//* forward the request to back 						M -> B
	$backfile = "get_grading_info.php";
	$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;
	$curl_opts = array(CURLOPT_POST => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $jsonrequest,
		CURLOPT_RETURNTRANSFER => 1);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_opts);
	$result = curl_exec($ch); // should be json				B -> M

	//* extract the grading data from $result
	$grading_data = json_decode($result, true);

//    echo "GRADING DATA\n"; var_dump($grading_data);

	//check that the database is still up
	if ($decoded["conn"] && $decoded["conn"] == false) {
		echo "(back)".$result;
		exit();
	}

	// echo "Grading data received from debbie\n";
//    var_dump($grading_data); exit();

	//* perform grading
//    var_dump($grading_data); exit();
	$grades = gradeAll($grading_data);

	// check if grading worked
	if (count($grades) != count($grading_data)) {
		// error occured since num_rows of both are not the same
		echo "(middle) Error: while grading could not autograde\n";
		exit();
	}

	//* send grades to back 								M -> B
	// 	package
	$grades_pack = array("userID" => $userID,
						"examID" => $examID,
						"scores" => $grades);

//	var_dump($grades_pack);
	$grades_encoded = json_encode($grades_pack);
	//	send
	$backfile = "update_grade.php";
	$url = "https://web.njit.edu/~ds547/CS490-Project/assets/back/".$backfile;
	$curl_opts = array(CURLOPT_POST => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $grades_encoded,
		CURLOPT_RETURNTRANSFER => 1);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_opts);
	$result = curl_exec($ch); // should be string			B -> M
//    echo $result;

	//* check update status and report back to front 		M -> F
    $result = json_decode($result, true);
	if ($result["query"] == false) { // SUCCESS
		// send the grades to front
		// echo true;
		echo $grades_encoded;
	}
	else {
		// unsuccessful grading or updating report error to front
		echo "(back)".$result."\n";
	}
}
exit();


?>
