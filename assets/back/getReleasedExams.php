<?php
$servername = "sql2.njit.edu";
$username = "ds547";
$password = "OVzSWetym";
$databaseName = "ds547";
$conn = new mysqli($servername, $username, $password, $databaseName)
if ($conn->connect_error) 
{
    $myObj->conn=false;
    $myObj->error=$conn->connect_error;
    echo json_encode($myObj);
    die();
} 
$studentExams=mysqli_query($conn,"SELECT userID,examID FROM BETA_rawExamData");
if($studentExams->num_rows!=0)
{
  while($row=$studentExams->fetch_assoc())
  {
    $released=mysqli_query($conn,"SELECT released FROM BETA_grades WHERE userID='".$row['userID']."' AND examID='".$row['examID']."'")->fetch_assoc()['released'];
    if($released==true)
    {
      $examData=mysqli_query($conn,"SELECT BETA_grades.examScore,BETA_questionBank.functionName,BETA_questionBank.parameters,BETA_questionBank.functionDescription,BETA_questionBank.output,
	BETA_questionBank.topic,BETA_questionBank.constraints,BETA_rawExamData.examID,BETA_rawExamData.questionID,BETA_rawExamData.studentResponse,BETA_rawExamData.questionScore,
	BETA_questionBank.testCases,BETA_rawExamData.testCasesPassFail,BETA_rawExamData.gradedComments,BETA_rawExamData.modResponse FROM BETA_grades,BETA_questionBank,BETA_rawExamData WHERE 
	BETA_rawExamData.userID=BETA_grades.userID AND BETA_rawExamData.userID='".$row['userID']."' AND BETA_rawExamData.examID=BETA_grades.examID AND 
	BETA_rawExamData.examID='".$row['examID']."' AND BETA_rawExamData.questionID=BETA_questionBank.questionID");
      $examsTableData = mysqli_query($conn, "SELECT * FROM BETA_exams WHERE examID='".$row['examID']."'");
      $returnArrayRAW=array();
      $returnArrayEXAMS=array();
      if($examData->num_rows!=0)
      {
        while($row_j=$examData->fetch_assoc())
        {
          $tempArray=array();
          $tempArray['released']=$released;
          $tempArray['examScore']=$row_j['examScore'];
          $tempArray['functionName']=$row_j['functionName'];
          $tempArray['parameters']=explode(':',$row_j['parameters']);
          $tempArray['does']=$row_j['functionDescription'];          
          $tempArray['prints']=$row_j['output'];        
          $tempArray['topic']=$row_j['topic'];         
          $tempArray['constraints']=$row_j['constraints'];         
          $tempArray['examID']=$row_j['examID'];   
          $tempArray['userID']=$row['userID'];     
          $tempArray['questionID']=$row_j['questionID'];         
          $tempArray['studentResponse']=$row_j['studentResponse'];
          $tempArray['modResponse']=$row_j['modResponse'];
          $tempArray['points']=$row_j['questionScore'];
          $tempArray['testCases']=explode(':',$row_j['testCases']);
          $tempArray['testCasesPassFail']=explode(',',$row_j['testCasesPassFail']);
          $tempArray['gradedComments']=$row_j['gradedComments'];
          array_push($returnArrayRAW,$tempArray);
        }
      }
      if($examsTableData->num_rows!=0)
      { 
        while($row=$examsTableData->fetch_assoc())
        {
          $tempArray=array();
          $tempArray['examID']=$row['examID'];
          $tempArray['examName']=$row['examName'];
          $tempArray['questionIDs']=$row['questionIDs'];
          $tempArray['points']=$row['points'];
          array_push($returnArrayEXAMS,$tempArray);
        }      
      }
    }
  }
}
$myObj->raw=$returnArrayRAW;
var_dump($returnArrayRAW);
$myObj->exams=$returnArrayEXAMS;
var_dump($returnArrayEXAMS);
echo json_encode($myObj);
?>
