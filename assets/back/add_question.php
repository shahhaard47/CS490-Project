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
$rawCreateQuestion = file_get_contents('php://input');
$data = json_decode($rawCreateQuestion, true);
$newExamQuestion = array('functionName' => $data['functionName'], 'parameters' => $data['params'], 'functionDescription' => $data['does'], 'output' => $data['returns'], 'topic' => $data['topic'], 'difficulty' => $data['difficulty'], 'constraints' => $data['constraints'], 'testCases' => $data['testCases']);
$params=implode(':',$newExamQuestion['parameters']);
$newQuestion = "INSERT INTO BETA_questionBank (functionName,parameters,functionDescription,output,topic,difficulty,constraints,testCases) VALUES ('".$newExamQuestion['functionName']."','".$params."','".$newExamQuestion['functionDescription']."','".$newExamQuestion['output']."','".$newExamQuestion['topic']."','".$newExamQuestion['difficulty']."','".$newExamQuestion['constraints']."','".$newExamQuestion['testCases']."')";
if($conn->query($newQuestion)===TRUE){
  $myObj->query=true;
}
else{
  $myObj->query=false;
}
echo json_encode($myObj);
?>