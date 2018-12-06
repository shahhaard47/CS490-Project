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

$rawDeleteRequest = file_get_contents('php://input');
$data = json_decode($rawDeleteRequest, true);
$deleteRequest = array('questionID' => $data['questionID']);
$deleteRequest = array('questionID' => 57);

$existsInExam=False;
$examRecords=mysqli_query($conn,"SELECT * FROM BETA_exams");
if($examRecords->num_rows!=0)
{
  while($row=$examRecords->fetch_assoc())
  {
    foreach(explode(',',$row['questionIDs']) as $q)
    {
      if((int)$deleteRequest['questionID']==(int)$q)
      {
        $existsInExam=True;
      }
    }
  }
}

if(mysqli_query($conn,"SELECT * FROM BETA_rawExamData WHERE questionID='".$deleteRequest['questionID']."'")->num_rows!=0 || $existsInExam==True)
{
  $archieve=mysqli_query($conn,"UPDATE BETA_questionBank SET archived=True WHERE questionID='".$deleteRequest['questionID']."'");
}
else
{
  $delete = mysqli_query($conn, "DELETE FROM BETA_questionBank WHERE questionID='".$deleteRequest['questionID']."'");
}



?>