<?php
//update grade information for a specific student for a specific exam in the rawExamData table and in the grades table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json request from Haard (middle) to update questionScore
$rawGrades = file_get_contents('php://input'); //get JSON request data to update questionScore
$data = json_decode($rawGrades, true); //decode JSON request data to update questionScore
$scoreInfo = array('userID' => $data['userID'], 'examID' => $data['examID'], 'score' => $data['score']); //store JSON request data to update questionScore

$updateScore = mysqli_query($conn, "UPDATE BETA_grades SET examScore='".$scoreInfo['score']."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."'");

?>