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
$rawComments = file_get_contents('php://input'); 
$data = json_decode($rawComments, true);
$commentInfo = array('userID' => $data['userID'], 'examID' => (int)$data['examID'], 'comments' => $data['comments']); 
foreach($commentInfo['comments'] as $arr)
{
  $update_rawExamData = mysqli_query($conn, "UPDATE BETA_rawExamData SET instructorComments='".$arr[1]."' WHERE userID='".$commentInfo['userID']."' AND examID='".$commentInfo['examID']."' AND questionID='".$arr[0]."'"); //$arr = [questionID, instructorComments], $arr[1] = instructorComments
}
?>