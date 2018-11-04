<?php
//add new question created by instructor to the question bank table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json data from Haard (middle) for adding new exam question created by instructor to the question bank

$rawCreateQuestion = file_get_contents('php://input');
$data = json_decode($rawCreateQuestion, true);

$newExamQuestion = array('functionName' => $data['functionName'], 'parameters' => $data['params'], 'functionDescription' => $data['does'], 'output' => $data['returns'], 'difficulty' => $data['difficulty'], 'testCases' => $data['testCases']);

//$newExamQuestion = array('functionName' => 'roundNum', 'parameters' => array('num'), 'functionDescription' => 'rounds a float number to an integer', 'output' => 'the rounded value of num', 'difficulty' => 'h', 'testCases' => "float 3.14159:int 3;float 2.7183:int 3;float 1.5:int 2"); //TEST



//combine data contained in parameters into a string with ':' as the delimeter
$params=implode(':',$newExamQuestion['parameters']);
//echo(var_dump($newExamQuestion['parameters'])); //TEST



//insert new exam question data into BETA_questionBank table in database
$newQuestion = "INSERT INTO BETA_questionBank (functionName,parameters,functionDescription,output,difficulty,testCases) VALUES ('".$newExamQuestion['functionName']."','".$params."','".$newExamQuestion['functionDescription']."','".$newExamQuestion['output']."','".$newExamQuestion['difficulty']."','".$newExamQuestion['testCases']."')";



if($conn->query($newQuestion)===TRUE)
{
  echo "exam question entered into questionBank";
}
else
{
  echo "error: question not added into questionBank";
}



?>