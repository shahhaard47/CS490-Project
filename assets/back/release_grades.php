<?php

$servername = "sql2.njit.edu";
$username = "ds547";
$password = "zrwEzyTq";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);

$release = file_get_contents('php://input'); 
$data = json_decode($release, true); 
$releaseInfo = array('userID' => $data['userID'], 'examID' => $data['examID']); 

$update_release = mysqli_query($conn, "UPDATE BETA_grades SET released=TRUE WHERE userID='".$releaseInfo['userID']."' AND examID='".$releaseInfo['examID']."'");
?>