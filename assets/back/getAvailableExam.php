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

$rawRequest = file_get_contents('php://input'); 
$data = json_decode($rawRequest, true); 
$request = array('userID' => $data['user']); 

if(!$request['userID'])
{
  $myObj->error='back: no userID was passed';
  echo json_encode($myObj);
  exit();
}

$exam = array();
$questionData = array();
$examRecord = mysqli_query($conn, "SELECT examID,examName,questionIDs,points FROM BETA_exams WHERE published=TRUE");

if($examRecord->num_rows!=0)
{
  $tempArray=array();
  $examInfo = $examRecord->fetch_assoc();
  if(mysqli_query($conn, "SELECT * FROM BETA_rawExamData WHERE userID='".$request['userID']."' AND examID='".$examInfo['examID']."'")->num_rows!=0)
  {
    $myObj->error='student already took exam';
    echo json_encode($myObj);
    exit();
  }
  $exam['examID']=$examInfo['examID'];
  $exam['examName']=$examInfo['examName'];
  
  $questionIDs=explode(',',$examInfo['questionIDs']);
  $points=explode(',',$examInfo['points']);
  for($i=0;$i<sizeof($questionIDs);$i++)
  {
    $tempArray['questionID']=$questionIDs[$i];

    $tempArray['points']=$points[$i];
    $questionRecord = mysqli_query($conn, "SELECT functionName,parameters,functionDescription,output,topic,constraints FROM BETA_questionBank WHERE questionID=$questionIDs[$i]");
    if($questionRecord!=FALSE)
    {
      $questionInfo = $questionRecord->fetch_assoc(); 
      $tempArray['functionName']=$questionInfo['functionName'];
      $tempArray['params']=explode(':',$questionInfo['parameters']);
      $tempArray['functionDescription']=$questionInfo['functionDescription'];
      $tempArray['output']=$questionInfo['output'];
      $tempArray['topic']=$questionInfo['topic'];
      $tempArray['constraints']=$questionInfo['constraints'];
      array_push($questionData,$tempArray);      
    }
    else
    {
      $exam['warning']='unexpected boolean value, false';
    }       
  }
  $exam['count']=sizeof($questionData);
  $exam['questions']=$questionData;
  echo json_encode($exam);
}
else //meaning, no exams were found in the table that have been published
{
  echo json_encode($exam);
}



?>