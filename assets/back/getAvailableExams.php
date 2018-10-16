<?php
//return all exams currently available/in the exams table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



$exams = array("data"=>array());

$allExamRecords = mysqli_query($conn, "SELECT * FROM BETA_exams");
if($allExamRecords->num_rows!=0)
{
  while($row_i = $allExamRecords->fetch_assoc())
  {
    //$examRecordArray=array("$row_i['examID']"=>array());
    
    foreach((explode(',',$row_i['questionIDs'])) as $question)
    {
      //query for information for each question in BETA_questionBank
      $questionInfo = mysqli_query($conn, "SELECT functionName,parameters,functionDescription,output,points from BETA_questionBank WHERE questionID='".(int)$question."'");
      $row_j=$questionInfo->fetch_assoc();
      
      //extract data and store in array
      $tempArray=array();
      $tempArray['examID']=(int)$row_i['examID'];
      $tempArray['questionID']=(int)$question;
      //var_dump($tempArray['questionID']);
      $tempArray['functionName']=$row_j['functionName'];
      $tempArray['parameters']=explode(',',$row_j['parameters']);
      $tempArray['functionDescription']=$row_j['functionDescription'];
      $tempArray['output']=$row_j['output'];
      $tempArray['points']=(int)$row_j['points'];
      //var_dump($tempArray);
      array_push($exams["data"],$tempArray);
    }
  }
  //var_dump($exams);
  $myJSON=json_encode($exams);
  echo($myJSON);
}
else //meaning, no exams were found in the table
{
  $myJSON=json_encode($exams);
  echo($myJSON);
}



?>