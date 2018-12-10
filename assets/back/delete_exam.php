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
$rawDeleteRequest = file_get_contents('php://input');
$data = json_decode($rawDeleteRequest, true);
$deleteRequest = array('examID' => (int)$data['examID']);
if(mysqli_query($conn,"SELECT * FROM BETA_rawExamData WHERE examID='".$deleteRequest['examID']."'")->num_rows!=0)
{
  $archieve = mysqli_query($conn,"UPDATE BETA_exams SET archived=True WHERE examID='".$deleteRequest['examID']."'");
  if(mysqli_query($conn,"SELECT published FROM BETA_exams WHERE examID='".$deleteRequest['examID']."'")->fetch_assoc()['published']==True)
  {
    $unPublish = mysqli_query($conn, "UPDATE BETA_exams SET published=FALSE WHERE examID='".$deleteRequest['examID']."'");
  }
}
else
{
  $delete = mysqli_query($conn, "DELETE FROM BETA_exams WHERE examID='".$deleteRequest['examID']."'");
}
?>