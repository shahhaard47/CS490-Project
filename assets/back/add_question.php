<?php



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//receiving json data from Haard (middle) for adding new exam question created by instructor to the question bank
$rawCreateQuestion = file_get_contents('php://input'); //get JSON data for new exam question
$data = json_decode($rawCreateQuestion, true); //decode JSON data for new exam question
$newExamQuestion = array('functName' => $data['functionName'], 'parameters' => $data['params'], 'functDescription' => $data['does'], 'output' => $data['prints'], 'difficulty' => $data['difficulty'], 'points' => $data['points'], 'correctResponse' => $data['correct_response'], 'testCases' => $data['test_cases']); //store JSON data for new exam question



//combine data contained in parameters into a string with ',' as the delimeter
$params=implode(',',$newExamQuestion['parameters']);
//echo(var_dump($newExamQuestion['parameters'])); //TEST

//testing to make sure that implode function is being implemented correctly and is working as expected
//$sampleArray=array("hello","hi","hey");
//$sample=implode(',',$sampleArray);
//echo(var_dump($sample));



//insert new exam question data into BETA_questionBank table in database
$newQuestion = "INSERT INTO BETA_questionBank (functionName,parameters,functionDescription,output,difficulty,points,correctResponse,testCases) VALUES ('%{$newExamQuestion['functName']}%','%{$params}%','%{$newExamQuestion['functDescription']}%','%{$newExamQuestion['output']}%','%{$newExamQuestion['difficulty']}%','%{$newExamQuestion['points']}%','%{$newExamQuestion['correctResponse']}%','%{$newExamQuestion['testCases']}%')";

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