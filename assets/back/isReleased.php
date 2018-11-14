<?php
//add exam record in exams table



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

$rawRequest = file_get_contents('php://input');
$data = json_decode($rawRequest, true); 
$info = array('userID' => $data['userID'],'examID' => $data['examID']);

if(mysqli_query($conn, "SELECT released FROM BETA_grades WHERE examID='".$info['examID']."' AND userID='".$info['userID']."'")->fetch_assoc()['released']==true)
{
  $myObj->released=true;
}
else
{
  $myObj->released=false;
}
echo json_encode($myObj);


?>