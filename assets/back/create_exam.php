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
$rawCreateExam = file_get_contents('php://input');
$data = json_decode($rawCreateExam, true); 
$createExam = array('examName' => $data['examName'], 'questionIDs' => $data['questions'], 'points' => $data['points']); 
$questionIDs=implode(',',$createExam['questionIDs']);
$points=implode(',',$createExam['points']);
$exam = "INSERT INTO BETA_exams (examName,questionIDs,points) VALUES ('".$createExam['examName']."','$questionIDs','$points')"
if($conn->query($exam)===TRUE){
  $myObj->examCreated=true;
}
else{
  $myObj->examCreated=false;
}
echo json_encode($myObj);
?>