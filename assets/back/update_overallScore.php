<?php
$servername = "sql2.njit.edu";
$username = "ds547";
$password = "OVzSWetym";
$databaseName = "ds547";
$conn = new mysqli($servername, $username, $password, $databaseName);
if ($conn->connect_error) 
{
    $myObj->conn=false;
    $myObj->error=$conn->connect_error;
    echo json_encode($myObj);
    die();
} 
$rawGrades = file_get_contents('php://input'); 
$data = json_decode($rawGrades, true); 
$scoreInfo = array('userID' => $data['userID'], 'examID' => (int)$data['examID'], 'score' => (int)$data['score']); 
$update_gradesTable = mysqli_query($conn, "UPDATE BETA_grades SET examScore='".$scoreInfo['score']."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."'");
?>