<?php
//update grade information for a specific student for a specific exam in the rawExamData table and in the grades table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json request from Haard (middle) to update questionScore
$rawGrades = file_get_contents('php://input'); //get JSON request data to update questionScore
$data = json_decode($rawGrades, true); //decode JSON request data to update questionScore
$scoreInfo = array('userID' => $data['userID'], 'examID' => $data['examID'], 'scores' => $data['scores']); //store JSON request data to update questionScore
//$scoreInfo = array('userID' => 'mscott', 'examID' => 6, 'scores' => [[1,17],[2,15]]); //TEST

$totalExamScore = 0;
$totalPossiblePoints = 0;

//extract questionID and questionScore from $scoreInfo['scores'], and update questionScores in BETA_rawExamData
foreach($scoreInfo['scores'] as $arr)
{
  $update_rawExamData = mysqli_query($conn, "UPDATE BETA_rawExamData SET questionScore='".$arr[1]."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."' AND questionID='".$arr[0]."'"); //$arr = [questionID, questionScore], $arr[1] = questionScore
  $totalExamScore+=$arr[1];
  $totalPossiblePoints+=mysqli_query($conn, "SELECT points FROM BETA_questionBank WHERE questionID='".$arr[0]."'")->fetch_row()[0];
}
//echo($totalExamScore); //TEST
echo('grades updated');



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