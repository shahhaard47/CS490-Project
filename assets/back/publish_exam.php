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
$publish = file_get_contents('php://input'); 
$data = json_decode($publish, true); 
$publishInfo = array('examID' => (int)$data['examID']); 
$update_published = mysqli_query($conn, "UPDATE BETA_exams SET published=TRUE WHERE examID='".$publishInfo['examID']."'");
?>