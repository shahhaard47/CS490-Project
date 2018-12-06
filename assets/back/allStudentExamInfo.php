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
$requestInfo = array('userID' => $data['userID']);
//$requestInfo = array('userID' => 'jsnow');

$studentExams=mysqli_query($conn,"SELECT examID FROM BETA_grades WHERE userID='".$requestInfo['userID']."'");

$returnArrayRAW=array();

if($studentExams->num_rows!=0)
{
  while($row=$studentExams->fetch_assoc())
  {
    //echo $row['examID'];
    $released=mysqli_query($conn,"SELECT released FROM BETA_grades WHERE userID='".$requestInfo['userID']."' AND examID='".$row['examID']."'")->fetch_assoc()['released'];
    //echo $released;
    if($released==true)
    {
      //echo "released=true";
      //echo $row['examID'];
      $examData=mysqli_query($conn,"SELECT BETA_grades.examScore,BETA_questionBank.functionName,BETA_questionBank.parameters,BETA_questionBank.functionDescription,BETA_questionBank.output,BETA_questionBank.topic,BETA_questionBank.constraints,BETA_rawExamData.questionID,BETA_rawExamData.studentResponse,BETA_rawExamData.questionScore,BETA_questionBank.testCases,BETA_rawExamData.testCasesPassFail,BETA_rawExamData.gradedComments,BETA_rawExamData.instructorComments,BETA_rawExamData.modResponse FROM BETA_grades,BETA_questionBank,BETA_rawExamData WHERE BETA_rawExamData.userID=BETA_grades.userID AND BETA_rawExamData.userID='".$requestInfo['userID']."' AND BETA_rawExamData.examID=BETA_grades.examID AND BETA_rawExamData.examID='".$row['examID']."' AND BETA_rawExamData.questionID=BETA_questionBank.questionID");
      $examTable=mysqli_query($conn,"SELECT examName,questionIDs,points,archieved FROM BETA_exams WHERE examID='".$row['examID']."'");
      $test=$examTable->fetch_assoc();
      $questions=explode(',',$test['questionIDs']);
      $points=explode(',',$test['points']);
      //while($test=$examTable->fetch_assoc())
      //{
      //  echo $test['examName'];
      //  echo $test['questionIDs'];
      //  echo $test['points'];
      //}
      if($examData->num_rows!=0)
      {
        //echo "records exist";
        while($ROW=$examData->fetch_assoc())
        {
          $tempArray=array();
          $tempArray['examID']=$row['examID'];
          $tempArray['examName']=$test['examName'];
          $tempArray['questionID']=$ROW['questionID'];
          $tempArray['released']=$released;
          $tempArray['examScore']=$ROW['examScore'];
          
          for($i=0;$i<$questions;$i++)
          {
            if((int)$questions[$i]==(int)$ROW['questionID'])
            {
              $tempArray['totalPoints']=$points[$i];
              break;
            }
          }
          
          $tempArray['points']=$ROW['questionScore'];
          $tempArray['studentResponse']=$ROW['studentResponse'];
          $tempArray['modResponse']=$ROW['modResponse'];
          $tempArray['functionName']=$ROW['functionName'];
          $tempArray['parameters']=explode(':',$ROW['parameters']);
          $tempArray['does']=$ROW['functionDescription'];
          $tempArray['prints']=$ROW['output'];
          $tempArray['topic']=$ROW['topic'];
          $tempArray['constraints']=$ROW['constraints'];
          $tempArray['testCases']=$ROW['testCases'];
          $tempArray['testCasesPassFail']=$ROW['testCasesPassFail'];
          $tempArray['gradedComments']=explode('|',$ROW['gradedComments']);
          $tempArray['instructorComments']=$ROW['instructorComments'];
          $tempArray['archieved']=$test['archieved'];
          //var_dump($tempArray);
          array_push($returnArrayRAW,$tempArray);
        }
      }
    }
  }
  $myObj->raw=$returnArrayRAW;
}
else
{
  $myObj->raw=NULL;
}
echo json_encode($myObj);

?>