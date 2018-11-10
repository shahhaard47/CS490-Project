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
$myObj->conn=true;
$myObj->error=null;
echo json_encode($myObj);

$rawDeleteRequest = file_get_contents('php://input');
$data = json_decode($rawDeleteRequest, true);
$deleteRequest = array('questionID' => $data['questionID']);

$delete = mysqli_query($conn, "DELETE FROM BETA_questionBank WHERE questionID='".$$deleteRequest['questionID']."'");

?>