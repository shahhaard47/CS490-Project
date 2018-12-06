<?php
//returns all exams in the exams table in the database



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


$returnArray = array();
$allExamRecords = mysqli_query($conn, "SELECT * FROM BETA_exams");
if($allExamRecords->num_rows!=0)
{
  $tempArray = array();
  while($row = $allExamRecords->fetch_assoc())
  {
    $tempArray['examID']=$row['examID'];
    $tempArray['examName']=$row['examName'];
    $tempArray['published']=$row['published'];
    $tempArray['archieved']=$row['archieved'];
    array_push($returnArray,$tempArray);
  }
  $myJSON=json_encode($returnArray);
}
else
{
  $myJSON=json_encode($returnArray);
}
echo $myJSON;


?>