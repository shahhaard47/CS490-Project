<?php
//add new question created by instructor to the question bank table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json data from Haard (middle) for adding new exam question created by instructor to the question bank
$rawCreateQuestion = file_get_contents('php://input'); //get JSON data for new exam question
$data = json_decode($rawCreateQuestion, true); //decode JSON data for new exam question
$newExamQuestion = array('functName' => $data['functionName'], 'parameters' => $data['params'], 'functDescription' => $data['does'], 'output' => $data['prints'], 'difficulty' => $data['difficulty'], 'points' => $data['points'], 'correctResponse' => $data['solution'], 'testCases' => $data['testCases']); //store JSON data for new exam question
//$newExamQuestion = array('functName' => 'roundNum', 'parameters' => array('num'), 'functDescription' => 'rounds a float number to an integer', 'output' => 'the rounded value of num', 'difficulty' => 'h', 'points' => 10, 'correctResponse' => 'def roundNum(num):\n\tprint(int(num))', 'testCases' => "float 3.14159:float 2.7183:float 1.5"); //TEST



//combine data contained in parameters into a string with ',' as the delimeter
$params=implode(',',$newExamQuestion['parameters']);
//echo(var_dump($newExamQuestion['parameters'])); //TEST



//insert new exam question data into BETA_questionBank table in database
$newQuestion = "INSERT INTO BETA_questionBank (functionName,parameters,functionDescription,output,difficulty,points,correctResponse,testCases) VALUES ('".$newExamQuestion['functName']."','".$params."','".$newExamQuestion['functDescription']."','".$newExamQuestion['output']."','".$newExamQuestion['difficulty']."','".$newExamQuestion['points']."','".$newExamQuestion['correctResponse']."','".$newExamQuestion['testCases']."')";

//TEST INSERT DATA INTO TABLE
//$newQuestion = "INSERT INTO BETA_questionBank (functionName,parameters,functionDescription,output,difficulty,points,correctResponse,testCases) VALUES ('findProduct','x,y','multiplies two integers together','the product','e',20,'(solution for function def findProduct(x,y))','int 6,int 3')";

if($conn->query($newQuestion)===TRUE)
{
  echo "exam question entered into questionBank";
}
else
{
  echo "error: question not added into questionBank";
}



?>