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

//receiving json data from Haard (middle) for creating an exam
$rawCreateExam = file_get_contents('php://input'); //get JSON data for creating an exam
$data = json_decode($rawCreateExam, true); //decode JSON data for creating an exam
$createExam = array('examName' => $data['examName'], 'questionIDs' => $data['questions'], 'points' => $data['points']); //store JSON data for creating an exam

//convert $qIDs from an array into a string
$questionIDs=implode(',',$createExam['questionIDs']);
$points=implode(',',$createExam['points']);

//insert exam data into exams table
$exam = "INSERT INTO BETA_exams (examName,questionIDs,points) VALUES ('".$createExam['examName']."','$questionIDs','$points')";

if($conn->query($exam)===TRUE){
  $myObj->examCreated=true;
}
else{
  $myObj->examCreated=false;
}
echo json_encode($myObj);



?>