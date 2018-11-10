<?php
//update released column in grades table so that the exam scores can be released to the student



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "zrwEzyTq";
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


//receiving json request from Haard (middle) to update released
$publish = file_get_contents('php://input'); //get JSON request data to update released
$data = json_decode($publish, true); //decode JSON request data to update released
$publishInfo = array('examID' => $data['examID']); //store JSON request data to update released
//$publishInfo = array('examID' => 49); //TEST



$update_published = mysqli_query($conn, "UPDATE BETA_exams SET published=TRUE WHERE examID='".$publishInfo['examID']."'");



?>