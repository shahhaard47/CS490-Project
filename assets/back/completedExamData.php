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

$rawStuExamData = file_get_contents('php://input'); 
$data = json_decode($rawStuExamData, true); 
//$requestInfo = array('userID' => $data['userID'], 'examID' => $data['examID']);

if($requestInfo['userID']==null && $requestInfo['examID']==null)
{
  echo "userID=null and examID=null";
  $info = mysqli_query($conn, "SELECT BETA_grades.examScore,BETA_grades.released,BETA_questionBank.functionName,BETA_questionBank.parameters,BETA_questionBank.functionDescription,BETA_questionBank.output,BETA_questionBank.topic,BETA_questionBank.constraints,BETA_questionBank.testCases,BETA_rawExamData.questionID,BETA_rawExamData.studentResponse,BETA_rawExamData.questionScore,BETA_rawExamData.testCasesPassFail,BETA_rawExamData.gradedComments,BETA_rawExamData.instructorComments,BETA_rawExamData.userID,BETA_rawExamData.examID FROM BETA_grades,BETA_questionBank,BETA_rawExamData WHERE BETA_rawExamData.userID=BETA_grades.userID AND BETA_rawExamData.examID=BETA_grades.examID AND BETA_rawExamData.questionID=BETA_questionBank.questionID ORDER BY BETA_rawExamData.userID");
  
  $examsTableData = mysqli_query($conn, "SELECT examID,examName,questionIDs,points FROM BETA_exams");
}
else //meaning, userID and examID are not NULL
{
  echo "userID!=null and examID!=null";
  if(mysqli_query($conn, "SELECT released FROM BETA_grades WHERE userID='".$requestInfo['userID']."' AND examID='".$requestInfo['examID']."'")->fetch_assoc()['released']==true)
  {
    echo "released=true";
    $info = mysqli_query($conn, "SELECT BETA_grades.examScore,BETA_grades.released,BETA_questionBank.functionName,BETA_questionBank.parameters,BETA_questionBank.functionDescription,BETA_questionBank.output,BETA_questionBank.topic,BETA_questionBank.constraints,BETA_questionBank.testCases,BETA_rawExamData.questionID,BETA_rawExamData.studentResponse,BETA_rawExamData.questionScore,BETA_rawExamData.testCasesPassFail,BETA_rawExamData.gradedComments,BETA_rawExamData.instructorComments,BETA_rawExamData.userID,BETA_rawExamData.examID FROM BETA_grades,BETA_questionBank,BETA_rawExamData WHERE BETA_rawExamData.userID=BETA_grades.userID AND BETA_grades.userID='".$requestInfo['userID']."' AND BETA_rawExamData.examID=BETA_grades.examID AND BETA_grades.examID='".$requestInfo['examID']."' AND BETA_rawExamData.questionID=BETA_questionBank.questionID ORDER BY BETA_rawExamData.userID");
  
    $examsTableData = mysqli_query($conn, "SELECT examID,examName,questionIDs,points FROM BETA_exams WHERE examID='".$requestInfo['examID']."'");
  }
  else
  {
    echo "released=false";
    $myObj->raw=null;
    $myObj->exam=null;
    $myJSON=json_encode($myObj);
    echo $myJSON;
    exit();
  }
}


$returnArrayRAW=array();
$returnArrayEXAMS=array();
if($info->num_rows!=0)
{
  $tempArray = array();
  while($row = $info->fetch_assoc())
  {
    $tempArray['userID']=$row['userID'];
    $tempArray['examID']=(int)$row['examID'];
    $tempArray['questionID']=(int)$row['questionID'];
    $tempArray['studentResponse']=$row['studentResponse'];
    $tempArray['functionName']=$row['functionName'];
    $tempArray['parameters']=explode(':',$row['parameters']);
    $tempArray['does']=$row['functionDescription'];
    $tempArray['prints']=$row['output'];
    $tempArray['points']=(int)$row['questionScore'];
    $tempArray['gradedComments']=$row['gradedComments'];
    $tempArray['instructorComments']=$row['instructorComments']
    $tempArray['topic']=$row['topic'];
    $tempArray['constraints']=$row['constraints'];
    $tempArray['testCases']=explode(':',$row['testCases']);
    $tempArray['testCasesPassFail']=explode(',',$row['testCasesPassFail']);
    $tempArray['examScore']=(int)$row['examScore'];
    $tempArray['released']=(bool)$row['released'];
    array_push($returnArrayRAW,$tempArray);
  }
  $myObj->raw=$returnArrayRAW;
  var_dump($returnArrayRAW);
  
  while($row = $examsTableData->fetch_assoc())
  {
    $tempArray['examID']=$row['examID'];
    $tempArray['examName']=$row['examName'];
    $tempArray['qIDs']=explode(',',$row['questionIDs']);
    $tempArray['points']=explode(',',$row['points']);
    array_push($returnArrayEXAMS,$tempArray);
  }
  $myObj->exam=$returnArrayEXAMS;
  var_dump($returnArrayEXAMS);
}
else
{
  $myObj->raw=null;
  $myObj->exam=null;
}

$myJSON=json_encode($myObj);
echo $myJSON;

?>