<?php
//create record in rawExamData table and add student information and question responses



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


//receiving json request from Haard (middle) to add raw exam data for student
$rawComments = file_get_contents('php://input'); //get JSON request data to add raw exam data for student
$data = json_decode($rawComments, true); //decode JSON request data to add raw exam data for student
$responseInfo = array('userID' => $data['userID'], 'examID' => $data['examID'], 'studentResponses' => $data['answers']); 


//extract questionID and instructorComments from $responseInfo['studentResponses']
$i=array();
$ids=array();
foreach($responseInfo['studentResponses'] as $arr)
{
  $insert=mysqli_real_escape_string($conn, $arr['studentResponse']);
  //$insert_rawExamData = mysqli_query($conn, "INSERT INTO BETA_rawExamData (userID,examID,questionID,studentResponse) VALUES ('".$responseInfo['userID']."','".$responseInfo['examID']."','".$arr['questionID']."','".$insert."')"); //$arr[0]=questionID and $arr[1]=studentResponse(python code)
  array_push($ids,$arr['questionID']);
  if($conn->query("INSERT INTO BETA_rawExamData (userID,examID,questionID,studentResponse) VALUES ('".$responseInfo['userID']."','".$responseInfo['examID']."','".$arr['questionID']."','".$insert."')")===TRUE)
  {
    $myObj->query=true;
  }
  else
  {
    $myObj->query=false;
  }
}
echo json_encode($myObj);



?>