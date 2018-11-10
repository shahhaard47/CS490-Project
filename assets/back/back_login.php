<?php
//return whether the ucid and password entered are for a student, an instructor, or if the pair does not exist in the database



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

//create tables in database

// $BETA_exams = "CREATE TABLE BETA_exams (
// examID INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
// questionIDs CHAR(30) NOT NULL)";

// $BETA_grades = "CREATE TABLE BETA_grades (
// userID VARCHAR(6) NOT NULL,
// examID INT(3) NOT NULL,
// examScore INT(3) NOT NULL)";

// $BETA_questionBank = "CREATE TABLE BETA_questionBank (
// questionID INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
// functionName CHAR(30) NOT NULL,
// parameters VARCHAR(1000) NOT NULL,
// functionDescription VARCHAR(1000) NOT NULL,
// output VARCHAR(1000) NOT NULL,
// difficulty CHAR(1) NOT NULL,
// points INT(3) NOT NULL,
// correctResponse VARCHAR(1000) NOT NULL,
// testCases VARCHAR(500) NOT NULL)";

// $BETA_rawExamData = "CREATE TABLE BETA_rawExamData (
// userID VARCHAR(6) NOT NULL,
// examID INT(3) NOT NULL,
// questionID INT(3) NOT NULL,
// studentResponse VARCHAR(1000) NOT NULL,
// questionScore INT(3) NULL,
// instructorComments VARCHAR(1000) NULL)";

// $BETA_users = "CREATE TABLE BETA_users (
// userID VARCHAR(6) PRIMARY KEY,
// password CHAR(130) NOT NULL,
// userType CHAR(1) NOT NULL)";



//receiving json data from Haard (middle) for login
$rawLoginData = file_get_contents('php://input'); //get JSON data for login
$data = json_decode($rawLoginData, true); //decode JSON data for login
$user_pass = array('ucid' => $data['user'], 'pass' => $data['pass']); //store JSON data for login



//encrypt password entered at login
$hashPass = hash('sha1', $user_pass['pass']);

//find out if UCID/password that were entered correspond to a student or an instructor
$result = mysqli_query($conn, "SELECT userType FROM BETA_users WHERE password='".$hashPass."' AND userID='".$user_pass['ucid']."'");
//$result = mysqli_query($conn, "SELECT userType FROM BETA_users WHERE password = '975eee8f77078ca6ab8f59d26824e3be84ba8a46' AND userID = 'jsnow'");

if($result->num_rows!=0){ //if result contains data from query
  $row = $result->fetch_row(); //fetch the information contained in result
  //echo $row[0]; //TEST
  if ($row[0]=='s'){
  	$myObj->user='student';
  }
  else { //if not student, then instructor
  	$myObj->user='instructor';
  }
}
else{ //ucid/password entered at login were not found in users table
  $myObj->user=NULL; //if ucid/password not found, return null
}



//sending reply to Haard (middle) for student/instructor 
$myJSON=json_encode($myObj);
echo $myJSON;



?>