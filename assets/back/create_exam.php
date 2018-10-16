<?php
//add exam record in exams table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json data from Haard (middle) for creating an exam
$rawCreateExam = file_get_contents('php://input'); //get JSON data for creating an exam
$data = json_decode($rawCreateExam, true); //decode JSON data for creating an exam
$qIDs = array('questionIDs' => $data['questions']); //store JSON data for creating an exam

//$qIDs = array('questionIDs' => array(1,3));
//convert $qIDs from an array into a string
$questionIDs=implode(',',$qIDs['questionIDs']);

//insert exam data into exams table
$exam = "INSERT INTO BETA_exams (questionIDs) VALUES ('$questionIDs')";

//$testExam = "INSERT INTO BETA_exams (questionIDs) VALUES ('$test')"; //TEST

if($conn->query($exam)===TRUE)
{
  echo "exam created";
}
else
{
  echo "error: exam not created";
}



?>