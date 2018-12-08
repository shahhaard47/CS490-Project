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
$rawGradingRequest = file_get_contents('php://input');
$data = json_decode($rawGradingRequest, true); 
$requestInfo = array('examID' => (int)$data['examID'], 'userID' => $data['userID']); 
$result = mysqli_query($conn, "SELECT BETA_rawExamData.questionID,BETA_questionBank.functionName,BETA_rawExamData.studentResponse,BETA_questionBank.testCases,BETA_questionBank.topic,BETA_questionBank.constraints,BETA_questionBank.parameters FROM BETA_rawExamData, BETA_questionBank WHERE BETA_rawExamData.questionID=BETA_questionBank.questionID AND BETA_rawExamData.examID='".$requestInfo['examID']."' AND BETA_rawExamData.userID='".$requestInfo['userID']."'");
$examPoints = mysqli_query($conn, "SELECT questionIDs,points FROM BETA_exams WHERE examID='".$requestInfo['examID']."'");
$returnToMiddle = array();
if($result->num_rows!=0)
{
  $items=$examPoints->fetch_assoc();
  $questionIDs=explode(',',$items['questionIDs']);
  $points=explode(',',$items['points']);
  while($row = $result->fetch_assoc())
  {
    $tempArray = array();
    $tempArray["questionID"]=$row['questionID'];
    $tempArray["points"]=0;
    for($i=0;sizeof($questionIDs);$i++)
    {
      if($questionIDs[$i]==(int)$row['questionID'])
      {
        $tempArray["points"]=(int)$points[$i];
        break;
      }
    }
    $tempArray["function_name"]=$row['functionName'];
    $tempArray["student_response"]=$row['studentResponse'];
    $tempArray["test_cases"]=explode(':',$row['testCases']);
    $tempArray["topic"]=$row['topic'];
    $tempArray["constraints"]=$row['constraints'];
    $tempArray["params"]=explode(':',$row['parameters']);
    array_push($returnToMiddle,$tempArray);
  }
  $myJSON=json_encode($returnToMiddle);
  echo $myJSON;
}
else
{
  $myJSON=json_encode($returnToMiddle);
  echo $myJSON;
}
?>