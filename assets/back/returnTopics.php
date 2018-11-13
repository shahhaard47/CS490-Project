<?php
//update grade information for a specific student for a specific exam in the rawExamData table and in the grades table



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "OVzSWetym";
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


$topics=array();
$topicsQuery = mysqli_query($conn, "SELECT topic FROM BETA_questionBank");
if($topicsQuery->num_rows!=0)
{
  while($row = $topicsQuery->fetch_assoc())
  {
    if(!in_array($row['topic'],$topics))
    {
      array_push($topics,$row['topic']);
    }
  }
}
echo json_encode($topics);


?>