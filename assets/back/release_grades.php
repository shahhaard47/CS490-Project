<?php
//update released column in grades table so that the exam scores can be released to the student



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json request from Haard (middle) to update released
$release = file_get_contents('php://input'); //get JSON request data to update released
$data = json_decode($release, true); //decode JSON request data to update released
$releaseInfo = array('userID' => $data['userID'], 'examID' => $data['examID']); //store JSON request data to update released
//$releaseInfo = array('userID' => 'mscott', 'examID' => 6); //TEST



$update_release = mysqli_query($conn, "UPDATE BETA_grades SET released=TRUE WHERE userID='".$releaseInfo['userID']."' AND examID='".$releaseInfo['examID']."'");



?>