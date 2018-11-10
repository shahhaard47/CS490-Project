<?php
//update grade information for a specific student for a specific exam in the rawExamData table and in the grades table



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


//receiving json request from Haard (middle) to update questionScore
$rawGrades = file_get_contents('php://input'); //get JSON request data to update questionScore
$data = json_decode($rawGrades, true); //decode JSON request data to update questionScore
$scoreInfo = array('userID' => $data['userID'], 'examID' => $data['examID'], 'scores' => $data['scores']); //store JSON request data to update questionScore
//$scoreInfo = array('userID' => 'mscott', 'examID' => 49, 'scores' => [[45,25,[0],"graded comments"],[48,12,[0],"graded comments"]]); //TEST

$examPoints = mysqli_query($conn, "SELECT questionIDs,points FROM BETA_exams WHERE examID='".$scoreInfo['examID']."'");
$items=$examPoints->fetch_assoc();
$questionIDs=explode(',',$items['questionIDs']);
$points=explode(',',$items['points']);

$totalExamScore = 0;
$totalPossiblePoints = 0;

//extract questionID and questionScore from $scoreInfo['scores'], and update questionScores in BETA_rawExamData
foreach($scoreInfo['scores'] as $arr)
{
  if(sizeOf($arr)>2)
  {
    $testCases=implode(',',$arr["testcases"]);
    $update_testCases = mysqli_query($conn, "UPDATE BETA_rawExamData SET testCasesPassFail='".$testCases."',gradedComments='".$arr["gradedComments"]."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."' AND questionID='".$arr["questionID"]."'");
  }
  $update_scores = mysqli_query($conn, "UPDATE BETA_rawExamData SET questionScore='".$arr["qScore"]."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."' AND questionID='".$arr["questionID"]."'"); //$arr = [questionID, questionScore], $arr[1] = questionScore
  $totalExamScore+=$arr["qScore"];
  
  for($i=0;sizeof($questionIDs);$i++)
  {
    if($questionIDs[$i]==(int)$arr[0])
    {
      $totalPossiblePoints+=(int)$points[$i];
      break;
    }
  }
}



//add total grade into BETA_grades
$gradePercentage = ($totalExamScore/$totalPossiblePoints)*100;
$checkGrades = mysqli_query($conn, "SELECT * FROM BETA_grades WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."'");
if($checkGrades->num_rows!=0)
{
  $update_gradesTable = mysqli_query($conn, "UPDATE BETA_grades SET examScore='".$gradePercentage."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."'");
}
else //meaning, a record for userID with examID does not exist and needs to be created
{
  $insert_gradesTable = mysqli_query($conn, "INSERT INTO BETA_grades (userID,examID,examScore,released) VALUES ('".$scoreInfo['userID']."','".$scoreInfo['examID']."','".$gradePercentage."',FALSE)");
}



?>