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
$result = mysqli_query($conn, "SELECT * FROM BETA_questionBank");
$returnToMiddle=array("raw"=>array());
if($result->num_rows!=0)
{
  while($row = $result->fetch_assoc())
  {
    if($row['archived']==True)
    {
      continue;
    }
    $tempArray = array();
    $tempArray["questionID"]=$row['questionID'];
    $tempArray["functionName"]=$row['functionName'];
    $tempArray["params"]=explode(':',$row['parameters']);
    $tempArray["does"]=$row['functionDescription'];
    $tempArray["prints"]=$row['output'];
    $tempArray["topic"]=$row['topic'];
    $tempArray["difficulty"]=$row['difficulty'];
    $tempArray["constraints"]=$row['constraints'];
    array_push($returnToMiddle["raw"],$tempArray);
  }
  $myJSON=json_encode($returnToMiddle);
  echo $myJSON;
}
else 
{
  $myJSON=json_encode($returnToMiddle);
  echo $myJSON;
}
?>