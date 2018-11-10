<?php
//return all questions currently in the question bank table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "zrwEzyTq";
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

$result = mysqli_query($conn, "SELECT * FROM BETA_questionBank");


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
    $tempArray["topic"]=$row['topic'];
    $tempArray["difficulty"]=$row['difficulty'];
    $tempArray["constraints"]=$row['constraints'];
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