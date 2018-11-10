<?php
//update the comments section with the comments the professor added for each exam question on an exam



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


//receiving json request from Haard (middle) to update instructorComments
$rawComments = file_get_contents('php://input'); //get JSON request data to update instructorComments
$data = json_decode($rawComments, true); //decode JSON request data to update instructorComments
$commentInfo = array('userID' => $data['userID'], 'examID' => $data['examID'], 'comments' => $data['comments']); //store JSON request data to update instructorComments
//$commentInfo = array('userID' => 'jsnow', 'examID' => 34, 'comments' => [[30,'bad'],[31,'try again'],[32,'not good']]); //TEST



//extract questionID and instructorComments from $commentInfo['comments']
foreach($commentInfo['comments'] as $arr)
{
  $update_rawExamData = mysqli_query($conn, "UPDATE BETA_rawExamData SET instructorComments='".$arr[1]."' WHERE userID='".$commentInfo['userID']."' AND examID='".$commentInfo['examID']."' AND questionID='".$arr[0]."'"); //$arr = [questionID, instructorComments], $arr[1] = instructorComments
}



?>