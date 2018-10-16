<?php
//return all questions currently in the question bank table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json data from Haard (middle) for question bank request
$rawQuestionBankDataRequest = file_get_contents('php://input'); //get JSON data for question bank request
$data = json_decode($rawQuestionBankDataRequest, true); //decode JSON data for question bank request
$QB_Data = array('questionBank' => $data['qBank'], 'difficulty' => $data['difficulty']); //store JSON data for question bank request
//$QB_Data = array('questionBank' => TRUE, 'difficulty' => 'a'); //TEST



//check if there is a request for the questions from the questionBank
if($QB_Data['questionBank']==TRUE)
{
  //check to see if the request is for all questions, or only questions with a certain difficulty
  if($QB_Data['difficulty']=='a')
  {
    $result = mysqli_query($conn, "SELECT * FROM BETA_questionBank");
  }
  elseif($QB_Data['difficulty']=='e')
  {
    $result = mysqli_query($conn, "SELECT * FROM BETA_questionBank WHERE difficulty='e'");
  }
  elseif($QB_Data['difficulty']=='m')
  {
    $result = mysqli_query($conn, "SELECT * FROM BETA_questionBank WHERE difficulty='m'");
  }
  else //meaning, if($QB_Data['difficulty']=='h')
  {
    $result = mysqli_query($conn, "SELECT * FROM BETA_questionBank WHERE difficulty='h'");
  }
}
else //meaning, if($QB_Data['questionBank']==FALSE)
{
}



//pack up data and return if $result contains anything
$returnToMiddle=array("raw"=>array());
if($result->num_rows!=0)
{
  while($row = $result->fetch_assoc())
  {
    $tempArray = array();
    
    $tempArray["questionID"]=$row['questionID'];
    $tempArray["functionName"]=$row['functionName'];
    $tempArray["params"]=explode(',',$row['parameters']);
    $tempArray["does"]=$row['functionDescription'];
    $tempArray["prints"]=$row['output'];
    $tempArray["difficulty"]=$row['difficulty'];
    $tempArray["points"]=$row['points'];
    //echo(var_dump($tempArray));
    array_push($returnToMiddle["raw"],$tempArray);
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