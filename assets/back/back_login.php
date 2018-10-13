<?php



//echo "Hello"; //TEST



$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";
//connecting to database
$conn = new mysqli($servername, $username, $password, $databaseName);



//creating users table for students and instructors
$table1 = "CREATE TABLE BETA_users ( 
userID VARCHAR(6) NOT NULL,
password CHAR(130) NOT NULL),
userType CHAR(1) NOT NULL, /* will store S=student or I=instructor */
examID INT(1) NULL,
examScore INT(3) NULL,
PRIMARY KEY(userID))"; 

//creating table to store raw exam question responses and corresponding comments
$table2 = "CREATE TABLE BETA_rawExamData (
userID VARCHAR(6) NOT NULL,
examID INT(2) NOT NULL,
questionID INT(2) NOT NULL,
response VARCHAR(1000) NULL,
comments VARCHAR(1000) NULL,
PRIMARY KEY(userID))";

$table3 = "CREATE TABLE BETA_exams (
examID INT(2) NOT NULL,
examTitle CHAR(255) NOT NULL,
questionID INT(2) NOT NULL,
"

//when exam is requested for student, will be given examID and will send back question data for the requested examID

//creating questionBank table to store all questions created by instructor
$table4 = "CREATE TABLE BETA_questionBank (
questionID INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
functionName CHAR(30) NOT NULL, /* Write a function named {ex.Add} */
parameters VARCHAR(1000) NOT NULL, /* that takes parameters {parameter_1, parameter_2, ..., parameter_n} */
functionDescription VARCHAR(1000) NOT NULL, /* and does {} */
outputs VARCHAR(1000) NOT NULL, /* and prints out {} */
difficulty CHAR(1) NOT NULL, /* EASY, MEDIUM, HARD */
testCases CHAR(255) NOT NULL, /* ex.(2,3:4,5:7,4) */
correctResponse VARCHAR(1000) NOT NULL)";



//receiving json data from Haard (middle) for login
$rawLoginData = file_get_contents('php://input'); //get JSON data for login
$data = json_decode($rawLoginData, true); //decode JSON data for login
$user_pass = array('ucid' => $data['user'], 'pass' => $data['pass']); //store JSON data for login

//echo $user_pass['ucid']; //TEST
//echo $user_pass['pass']; //TEST

//encrypt password entered at login
$hashPass = hash('sha1', $user_pass['pass']);
//echo $hashPass; //TEST; concastenate something at the end of the hashPass to see if there are any special characters being created or added
//echo "\n"; //TEST

//find out if UCID/password that were entered correspond to a student or an instructor
$result = mysqli_query($conn, "SELECT userType FROM BETA_users WHERE password LIKE '%{$hashPass}%' AND userID LIKE '%{$user_pass['ucid']}%'");
//$result = mysqli_query($conn, "SELECT userType FROM BETA_users WHERE password = '975eee8f77078ca6ab8f59d26824e3be84ba8a46' AND userID = 'jsnow'");

if($result->num_rows!=0){ //if result contains data from query
  $row = $result->fetch_row(); //fetch the information contained in result
  //echo $row[0]; //TEST
  if ($row[0]=='s'){
  	$myObj->user='student';
  	//echo 'ucid/password correspond to userType student'; //TEST
  }
  else { //if not student, then instructor
  	$myObj->user='instructor';
  	//echo 'ucid/password correspond to userType instructor'; //TEST
  }
  //echo 'true';
}
else{ //ucid/password entered at login were not found in users table
  $myObj->user=NULL; //if ucid/password not found, return null
  //echo 'false';
}



//sending reply to Haard (middle) for student/instructor 
$myJSON=json_encode($myObj);
echo $myJSON;



?>