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
$myObj->conn=true;
$myObj->error=null;
echo json_encode($myObj);

$allFinishedExams = mysqli_query($conn, "SELECT BETA_grades.examScore,BETA_questionBank.functionName,BETA_questionBank.parameters,BETA_questionBank.functionDescription,BETA_questionBank.output,BETA_questionBank.topic,BETA_questionBank.constraints,BETA_rawExamData.userID,BETA_rawExamData.examID,BETA_rawExamData.questionID,BETA_rawExamData.studentResponse,BETA_rawExamData.questionScore,BETA_questionBank.testCases,BETA_rawExamData.testCasesPassFail,BETA_rawExamData.gradedComments,BETA_grades.released FROM BETA_grades,BETA_questionBank,BETA_rawExamData WHERE BETA_rawExamData.userID=BETA_grades.userID AND BETA_rawExamData.examID=BETA_grades.examID AND BETA_rawExamData.questionID=BETA_questionBank.questionID ORDER BY BETA_rawExamData.userID");

$examsTableData = mysqli_query($conn, "SELECT * FROM BETA_exams");


$returnArrayRAW=array();
$returnArrayEXAMS=array();
if($allFinishedExams->num_rows!=0)
{
  $tempArray = array();
  while($row = $allFinishedExams->fetch_assoc())
  {
    $tempArray['userID']=$row['userID'];
    $tempArray['examID']=(int)$row['examID'];
    $tempArray['questionID']=(int)$row['questionID'];
    $tempArray['studentResponse']=$row['studentResponse'];
    $tempArray['functionName']=$row['functionName'];
    $tempArray['parameters']=explode(',',$row['parameters']);
    $tempArray['does']=$row['functionDescription'];
    $tempArray['prints']=$row['output'];
    $tempArray['points']=(int)$row['questionScore'];
    $tempArray['gradedComments']=$row['gradedComments'];
    $tempArray['topic']=$row['topic'];
    $tempArray['constraints']=$row['constraints'];
    $tempArray['testCases']=explode(':',$row['testCases']);
    $tempArray['testCasesPassFail']=explode(',',$row['testCasesPassFail']);
    $tempArray['examScore']=(int)$row['examScore'];
    $tempArray['released']=$row['released'];
    array_push($returnArrayRAW,$tempArray);
  }
  $myObj->raw=$returnArrayRAW;
}
else
{
  $myObj->raw=null;
}
if($examsTableData->num_rows!=0)
{
  $tempArray=array();
  while($row = $examsTableData->fetch_assoc())
  {
    $tempArray['examID']=$row['examID'];
    $tempArray['examName']=$row['examName'];
    $tempArray['qIDs']=explode(',',$row['questionIDs']);
    $tempArray['points']=explode(',',$row['points']);
    array_push($returnArrayEXAMS,$tempArray);
  }
  $myObj->exam=$returnArrayEXAMS;
}
else
{
  $myObj->exam=null;
}

$myJSON=json_encode($myObj);
echo $myJSON;

?>