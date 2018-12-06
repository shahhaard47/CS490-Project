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
$requestInfo = array('userID' => 'jsnow');

$returnArrayRAW=array();
$returnArrayEXAMS=array();
$tempArray=array();

$info=mysqli_query($conn,"SELECT * FROM BETA_grades WHERE userID='".$requestInfo['userID']."'");
if($info->num_rows!=0)
{
  while($row=$info->fetch_assoc())
  {
    if($row['released']==True)
    {
      $questionsPoints=mysqli_query($conn,"SELECT examName,questionIDs,points,archived FROM BETA_exams WHERE examID='".$row['examID']."'");
    
      $data=$questionsPoints->fetch_assoc();
      $questions=explode(',',$data['questionIDs']);
      $points=explode(',',$data['points']);
    
      for($i=0;$i<sizeOf($questions);$i++)
      {
        $questionData=mysqli_query($conn,"SELECT BETA_questionBank.functionName,BETA_questionBank.parameters,BETA_questionBank.functionDescription,BETA_questionBank.output,BETA_questionBank.topic,BETA_questionBank.constraints,BETA_rawExamData.studentResponse,BETA_rawExamData.questionScore,BETA_questionBank.testCases,BETA_rawExamData.testCasesPassFail,BETA_rawExamData.gradedComments,BETA_rawExamData.instructorComments,BETA_rawExamData.modResponse FROM BETA_grades,BETA_questionBank,BETA_rawExamData WHERE BETA_rawExamData.userID=BETA_grades.userID AND BETA_rawExamData.userID='".$requestInfo['userID']."' AND BETA_rawExamData.examID=BETA_grades.examID AND BETA_rawExamData.examID='".$row['examID']."' AND BETA_rawExamData.questionID=BETA_questionBank.questionID AND BETA_questionBank.questionID='".$questions[$i]."'");
      
        $questionInfo=$questionData->fetch_assoc();
      
        $tempArray['userID']=$row['userID'];
        $tempArray['examID']=(int)$row['examID'];
        $tempArray['examName']=$data['examName'];
        $tempArray['questionID']=(int)$questions[$i];
        $tempArray['studentResponse']=$questionInfo['studentResponse'];
        $tempArray['modResponse']=$questionInfo['modResponse'];
        $tempArray['functionName']=$questionInfo['functionName'];
        $tempArray['parameters']=explode(':',$questionInfo['parameters']);
        $tempArray['does']=$questionInfo['functionDescription'];
        $tempArray['prints']=$questionInfo['output'];
        $tempArray['points']=(int)$questionInfo['questionScore'];
        $tempArray['totalPoints']=$points[$i];
        $tempArray['gradedComments']=explode('|',$questionInfo['gradedComments']);
        $tempArray['instructorComments']=explode(':',$questionInfo['instructorComments']);
        $tempArray['topic']=$questionInfo['topic'];
        $tempArray['constraints']=$questionInfo['constraints'];
        $tempArray['testCases']=explode(':',$questionInfo['testCases']);
        $tempArray['testCasesPassFail']=explode(',',$questionInfo['testCasesPassFail']);
        $tempArray['examScore']=(int)$row['examScore'];
        $tempArray['released']=$row['released'];
        $tempArray['archived']=$data['archived'];
        array_push($returnArrayRAW,$tempArray);
      }
    }
    $myObj->raw=$returnArrayRAW;
  }
}
else
{
  $myObj->raw=NULL;
}
echo json_encode($myObj);


?>