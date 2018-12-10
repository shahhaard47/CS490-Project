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
$rawLoginData = file_get_contents('php://input');
$data = json_decode($rawLoginData, true); 
$user_pass = array('ucid' => $data['user'], 'pass' => $data['pass']); 
$hashPass = hash('sha1', $user_pass['pass']);
$result = mysqli_query($conn, "SELECT userType FROM BETA_users WHERE password='".$hashPass."' AND userID='".$user_pass['ucid']."'");
if($result->num_rows!=0){ 
  $row = $result->fetch_row(); 
  if ($row[0]=='s'){
  	$myObj->user='student';
  }
  else { 
  	$myObj->user='instructor';
  }
}
else{ 
  $myObj->user=NULL; 
}
$myJSON=json_encode($myObj);
echo $myJSON;
?>