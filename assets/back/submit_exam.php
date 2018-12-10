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
$responseInfo = array('userID' => $data['userID'], 'examID' => (int)$data['examID'], 'studentResponses' => $data['answers']); 
$i=array();
$ids=array();
foreach($responseInfo['studentResponses'] as $arr)
{
  $insert=mysqli_real_escape_string($conn, $arr['studentResponse']);
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