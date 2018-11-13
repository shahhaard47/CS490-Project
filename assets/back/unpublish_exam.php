<?php
//update released column in grades table so that the exam scores can be released to the student



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


//receiving json request from Haard (middle) to update released
//$unpublish = file_get_contents('php://input'); //get JSON request data to update released
//$data = json_decode($unpublish, true); //decode JSON request data to update released
//$unpublishInfo = array('examID' => $data['examID']); //store JSON request data to update released
//$publishInfo = array('examID' => 49); //TEST



//$update_published = mysqli_query($conn, "UPDATE BETA_exams SET published=FALSE WHERE examID='".$unpublishInfo['examID']."'");


//if examID is not provided
$query = mysqli_query($conn, "SELECT * FROM BETA_exams");
if($query->num_rows!=0)
{
  while($row=$query->fetch_assoc())
  {
    if($row['published']==TRUE)
    {
      $update_published = mysqli_query($conn, "UPDATE BETA_exams SET published=FALSE WHERE examID='".$row['examID']."'");
      break;
    }
  }
}



?>