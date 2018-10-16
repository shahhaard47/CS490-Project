<?php
//return exam/question data for a specific student for a specific exam



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json request from Haard (middle) for data to display for student/instructor
$rawStuExamData = file_get_contents('php://input'); //get JSON request data for data to display for student/instructor
$data = json_decode($rawStuExamData, true); //decode JSON request data for data to display for student/instructor
$requestInfo = array('userID' => $data['userID'], 'examID' => $data['examID']); //store JSON request data for data to display for student/instructor
//$requestInfo = array('userID' => 'mscott', 'examID' => 6); //TEST



$returnArray = array();
if(mysqli_query($conn, "SELECT released FROM BETA_grades WHERE userID='".$requestInfo['userID']."' AND examID='".$requestInfo['examID']."'")->fetch_assoc()['released'] == 1)
{
  //obtain all student exam data from database
  $allStuExamData = mysqli_query($conn, "SELECT BETA_questionBank.questionID,BETA_questionBank.functionName,BETA_questionBank.parameters,BETA_questionBank.functionDescription,BETA_questionBank.output,BETA_rawExamData.studentResponse,BETA_rawExamData.questionScore,BETA_rawExamData.instructorComments FROM BETA_questionBank,BETA_rawExamData WHERE BETA_questionBank.questionID=BETA_rawExamData.questionID and BETA_rawExamData.userID='".$requestInfo['userID']."' AND BETA_rawExamData.examID='".$requestInfo['examID']."'");
  //obtain exam grade for examID
  $stuExamScore = mysqli_query($conn, "SELECT examScore FROM BETA_grades WHERE userID='".$requestInfo['userID']."' AND examID='".$requestInfo['examID']."'");




  if($allStuExamData->num_rows!=0)
  {
    $tempArray = array();
  
    while($row = $allStuExamData->fetch_assoc())
    {
      $tempArray['questionID']=(int)$row['questionID'];
      $tempArray['functionName']=$row['functionName'];
      $tempArray['parameters']=explode(',',$row['parameters']);
      $tempArray['functionDescription']=$row['functionDescription'];
      $tempArray['output']=$row['output'];
      $tempArray['studentResponse']=$row['studentResponse'];
      $tempArray['questionScore']=(int)$row['questionScore'];
      $tempArray['instructorComments']=$row['instructorComments'];
      //var_dump($tempArray); //TEST
      array_push($returnArray,$tempArray);
    }
    $returnArray["examScore"]=(int)($stuExamScore->fetch_row()[0]);
  
    $myJSON=json_encode($returnArray);
    echo $myJSON;
  }
  else //the query did not return any data
  {
    $myJSON=json_encode($returnArray);
    echo $myJSON;
  }
}
else
{ 
  $myJSON=json_encode($returnArray);
  echo $myJSON;
}



?>