<?php
$servername = "sql2.njit.edu";
$username = "ds547";
$password = "OVzSWetym";
$databaseName = "ds547";
$conn = new mysqli($servername, $username, $password, $databaseName);
if ($conn->connect_error) 
{
    $myObj->conn=false;
    $myObj->error=$conn->connect_error;
    echo json_encode($myObj);
    die();
} 
$rawGrades = file_get_contents('php://input'); 
$data = json_decode($rawGrades, true);
$scoreInfo = array('userID' => $data['userID'], 'examID' => (int)$data['examID'], 'scores' => $data['scores']); 
$examPoints = mysqli_query($conn, "SELECT questionIDs,points FROM BETA_exams WHERE examID='".$scoreInfo['examID']."'");
$items=$examPoints->fetch_assoc();
$questionIDs=explode(',',$items['questionIDs']);
$points=explode(',',$items['points']);
$totalExamScore = 0;
$totalPossiblePoints = 0;
foreach($scoreInfo['scores'] as $arr)
{
  if(sizeOf($arr)>3)
  {
    $insert=mysqli_real_escape_string($conn, $arr['modResponse']);
    $update_testCases = mysqli_query($conn, "UPDATE BETA_rawExamData SET testCasesPassFail='".implode(',',$arr["testCasesPassFail"])."',gradedComments='".implode('|',$arr["comments"])."',modResponse='".$insert."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."' AND questionID='".$arr["questionID"]."'");
  }
  $update_scores = mysqli_query($conn, "UPDATE BETA_rawExamData SET questionScore='".(int)$arr["qScore"]."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."' AND questionID='".(int)$arr["questionID"]."'"); 
  $totalExamScore+=$arr["qScore"];
  if($conn->query($update_scores)===TRUE){
  $myObj->query=true;
}
else{
  $myObj->query=false;
}
echo json_encode($myObj);
  for($i=0;sizeof($questionIDs);$i++)
  {
    if($questionIDs[$i]==(int)$arr["questionID"])
    {
      $totalPossiblePoints+=(int)$points[$i];
      break;
    }
  }
}
$gradePercentage = ($totalExamScore/$totalPossiblePoints)*100;
$checkGrades = mysqli_query($conn, "SELECT * FROM BETA_grades WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."'");
if($checkGrades->num_rows!=0)
{
  $update_gradesTable = mysqli_query($conn, "UPDATE BETA_grades SET examScore='".$gradePercentage."' WHERE userID='".$scoreInfo['userID']."' AND examID='".$scoreInfo['examID']."'");
}
else 
{
  $insert_gradesTable = mysqli_query($conn, "INSERT INTO BETA_grades (userID,examID,examScore,released) VALUES ('".$scoreInfo['userID']."','".$scoreInfo['examID']."','".$gradePercentage."',FALSE)");
}
?>