<?php
//receiving from middle
//echo "hello";

//if(isset($_POST['user'])){
//  echo "hello";
//}
//else{
//  echo "bye";
//}

//receiving json data from Haard (middle)
$raw_data = file_get_contents('php://input'); //get JSON data
$data = json_decode($raw_data, true); //decode JSON data
$user_pass = array('ucid' => $data['user'], 'pass' => $data['pass']); //store JSON data
//echo $user_pass['ucid']; //TEST
//echo $user_pass['pass']; //TEST
$TESTpass = 'hello';

$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";

$conn = new mysqli($servername, $username, $password, $databaseName);

$table = "CREATE TABLE cs490PROJECT (
id INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
userID VARCHAR(6) NOT NULL,
password CHAR(130) NOT NULL)";

//$insert = "INSERT INTO cs490PROJECT (userID,password) VALUES ($user_pass['ucid'], $user_pass['pass'])";
//$insert = "INSERT INTO cs490PROJECT (userID,password) VALUES ('jonSnow', 'snow')";

$hashPass = hash('sha1', $user_pass['pass']);
//$hashPass = hash('sha512', $TESTpass);

$query = "SELECT * FROM cs490PROJECT WHERE password LIKE '%{$hashPass}%' AND userID LIKE '%{$user_pass['ucid']}%'";
//$query = "SELECT * FROM cs490PROJECT WHERE password='b94e9f3d7e001981b2dd49f2a70822a8ac8f3e68' AND userID='jon'";
$result = mysqli_query($conn, $query);
//$myObj->send=TRUE;
//echo $result;
if($result->num_rows!=0){
  $myObj->send=TRUE;
  echo 'good';
}
else{
  $myObj->send=FALSE;
  echo 'bad';
}

//sending reply to Haard (middle)
$myJSON=json_encode($myObj);
echo $myJSON;

?>