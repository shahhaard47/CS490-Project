<?php

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

$release = file_get_contents('php://input'); 
$data = json_decode($release, true); 
$info = array('userID' => $data['userID'], 'examID' => $data['examID'], 'data' => $data['data']); //data is a list of lists

foreach($info['data'] as $arr)
{
  $insert=mysqli_real_escape_string($conn, $arr['comments']);
  //echo $insert;
  $insert_data=mysqli_query($conn, "UPDATE BETA_rawExamData SET questionScore='".$arr['points']."',instructorComments='".$insert."' WHERE userID='".$info['userID']."' AND examID='".$info['examID']."' AND questionID='".$arr['questionID']."'");
  $insert_comments = mysqli_query($conn, "UPDATE BETA_rawExamData SET instructorComments='".$insert."' WHERE userID='".$info['userID']."' AND examID='".$info['examID']."' AND questionID='".$arr['questionID']."'");
  //echo $insert_data;
}
echo "done";
?>