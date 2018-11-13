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
$info = array('examID' => $data['examID']);
//$info = array('examID' => 57); //TEST

if(mysqli_query($conn, "SELECT published FROM BETA_exams WHERE examID='".$info['examID']."'")->fetch_assoc()['published']==true)
{
  $myObj->published=true;
}
else
{
  $myObj->published=false;
}
echo json_encode($myObj);


?>