<?php
//create record in rawExamData table and add student information and question responses



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json request from Haard (middle) to add raw exam data for student
$rawComments = file_get_contents('php://input'); //get JSON request data to add raw exam data for student
$data = json_decode($rawComments, true); //decode JSON request data to add raw exam data for student
$responseInfo = array('userID' => $data['ucid'], 'examID' => $data['examID'], 'studentResponses' => $data['answers']); //store JSON request data to add raw exam data for student
//$responseInfo = array('userID' => 'mscott', 'examID' => 5, 'studentResponses' => [[1,'(student response to question)']]); //TEST
//$responseInfo = array('userID' => 'jsnow', 'examID' => 6, 'studentResponses' => [[1,'(student response to question1)'],[2,'(student response to question2)']]); //TEST



//extract questionID and instructorComments from $responseInfo['studentResponses']
foreach($responseInfo['studentResponses'] as $arr)
{
  $insert_rawExamData = mysqli_query($conn, "INSERT INTO BETA_rawExamData (userID,examID,questionID,studentResponse) VALUES ('".$responseInfo['userID']."','".$responseInfo['examID']."','".$arr[0]."','".$arr[1]."')"); //$arr[0]=questionID and $arr[1]=studentResponse(python code)
}



?>