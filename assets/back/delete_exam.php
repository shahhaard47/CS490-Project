<?php

$servername = "sql2.njit.edu";
$username = "ds547";
$password = "OVzSWetym";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);

if ($conn->connect_error) 
{
    $myObj->conn=false;
    $myObj->error=$conn->connect_error;
    echo json_encode($myObj);
    die();
} 

$rawDeleteRequest = file_get_contents('php://input');
$data = json_decode($rawDeleteRequest, true);
$deleteRequest = array('examID' => $data['examID']);


$delete = mysqli_query($conn, "DELETE FROM BETA_exams WHERE examID='".$deleteRequest['examID']."'");


?>