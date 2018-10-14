<?php



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json request from Haard (middle) for grading info
$rawGradingRequest = file_get_contents('php://input'); //get JSON request data for grading info
$data = json_decode($rawGradingRequest, true); //decode JSON request data for grading info
$requestInfo = array('examID' => $data['examID'], 'userID' => $data['studentID']); //store JSON request data for grading info



//$requestInfo = array('examID' => 5, 'userID' => 'jsnow'); //TEST
//$requestInfo = array('examID' => 6, 'userID' => 'mscott'); //TEST

$result = mysqli_query($conn, "SELECT BETA_rawExamData.questionID,BETA_questionBank.points,BETA_questionBank.functionName,BETA_rawExamData.studentResponse,BETA_questionBank.correctResponse,BETA_questionBank.testCases FROM BETA_rawExamData, BETA_questionBank WHERE BETA_rawExamData.questionID=BETA_questionBank.questionID AND BETA_rawExamData.examID='".$requestInfo['examID']."' AND BETA_rawExamData.userID='".$requestInfo['userID']."'");

//$result = mysqli_query($conn, "SELECT BETA_rawExamData.questionID,BETA_questionBank.points,BETA_questionBank.functionName,BETA_rawExamData.studentResponse,BETA_questionBank.correctResponse,BETA_questionBank.testCases FROM BETA_rawExamData, BETA_questionBank WHERE BETA_rawExamData.questionID=BETA_questionBank.questionID AND BETA_rawExamData.examID=5 AND BETA_rawExamData.userID='jsnow'"); //TEST



$returnToMiddle = array();
//$returnToMiddle = array("gradeInfo" => array());
if($result->num_rows!=0)
{
  while($row = $result->fetch_assoc())
  {
    $tempArray = array();
    //echo 'hello, in loop in if';
    $tempArray["questionID"]=$row['questionID'];
    $tempArray["points"]=$row['points'];
    $tempArray["fnction_name"]=$row['functionName'];
    $tempArray["student_response"]=$row['studentResponse'];
    $tempArray["correct_response"]=$row['correctResponse'];
    $tempArray["test_cases"]=explode(':',$row['testCases']);
    //echo(var_dump($tempArray));
    array_push($returnToMiddle,$tempArray);
  }
  $myJSON=json_encode($returnToMiddle);
  echo $myJSON;
}
else //the query did not return any data
{
  $myJSON=json_encode($returnToMiddle);
  echo $myJSON;
}



?>