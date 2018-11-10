<?php
//return exam that has been marked as published for the student to take



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
$myObj->conn=true;
$myObj->error=null;
echo json_encode($myObj);

$exams = array();

$allExamRecord = mysqli_query($conn, "SELECT * FROM BETA_exams WHERE published=TRUE");

if($allExamRecord->num_rows!=0)
{
  $tempArray = array();
  while($row = $allExamRecord->fetch_assoc())
  {      
    $questionIDs = explode(',',$row['questionIDs']);
    $points = explode(',',$row['questionIDs']);
    array_push($exams,$row['examName']);
    array_push($exams,$row['examID']);  
    for($i=0;sizeof($questionIDs);$i++)
    {
      $tempArray['questionID']=$questionIDs[$i];
      $questionInfo = mysqli_query($conn,"SELECT functionName,parameters,functionDescription,output FROM BETA_questionBank WHERE questionID='".$questionIDs[$i]"'");
      $tempArray['points']=$points[$i];
      $tempArray['functionName']=$questionInfo['functionName'];
      $tempArray['params']=explode(',',$questionInfo['parameters']);
      $tempArray['functionDescription']=$questionInfo['functionDescription'];
      $tempArray['return']=$questionInfo['output']; 
      array_push($exams,$tempArray);     
      }
    }
  }
  //var_dump($exams);
  $myJSON=json_encode($exams);
  echo($myJSON);
}
else //meaning, no exams were found in the table that have been published
{
  $myJSON=json_encode($exams);
  echo($myJSON);
}



?>