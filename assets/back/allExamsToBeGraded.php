<?php
//return all exams that need to be graded



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json request from Haard (middle) for data to display for student/instructor
//$rawStuExamData = file_get_contents('php://input'); //get JSON request data for data to display for student/instructor
//$data = json_decode($rawStuExamData, true); //decode JSON request data for data to display for student/instructor
//$requestInfo = array('userID' => $data['userID'], 'examID' => $data['examID']); //store JSON request data for data to display for student/instructor
//$requestInfo = array('userID' => 'mscott', 'examID' => 6); //TEST



$returnArray=array();

$allFinishedExams = mysqli_query($conn, "SELECT BETA_rawExamData.userID,BETA_rawExamData.examID,BETA_rawExamData.questionID,BETA_rawExamData.studentResponse,BETA_questionBank.functionName,BETA_questionBank.parameters,BETA_questionBank.functionDescription,BETA_questionBank.output,BETA_questionBank.points,BETA_grades.examScore FROM BETA_rawExamData,BETA_questionBank,BETA_grades WHERE BETA_rawExamData.questionID=BETA_questionBank.questionID AND BETA_rawExamData.examID=BETA_grades.examID");

//$allGrades = mysqli_query($conn, "SELECT examID,examScore FROM BETA_exams");



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
    $tempArray['points']=(int)$row['points'];
    $tempArray['examScore']=(int)$row['examScore'];
    
    array_push($returnArray,$tempArray);
  }
  $myJSON=json_encode($returnArray);
  echo $myJSON;
}
else
{
  $myJSON=json_encode($returnArray);
  echo $myJSON;
}



?>