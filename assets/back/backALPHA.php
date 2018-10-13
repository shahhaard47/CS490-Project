<?php

echo "Hello";

$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";

$conn = new mysqli($servername, $username, $password, $databaseName); //connecting to database



//creating users table for students and instructors
$table1 = "CREATE TABLE BETA_users ( 
id INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
userID VARCHAR(6) NOT NULL,
/* make userID PRIMARY KEY */
password CHAR(130) NOT NULL),
userType CHAR(1) NOT NULL, /* will store S=student or I=instructor */
exam1Score INT(3) NOT NULL, /* midtermExam1 */
e1question1Response CHAR(750) NOT NULL,
e1question2Response CHAR(750) NOT NULL,
e1question3Response CHAR(750) NOT NULL,
exam2Score INT(3) NOT NULL, /* midtermExam2 */
e2question1Response CHAR(750) NOT NULL,
e2question2Response CHAR(750) NOT NULL,
e2question3Response CHAR(750) NOT NULL,
exam3Score INT(3) NOT NULL, /* finalExam */
e3question1Response CHAR(750) NOT NULL,
e3question2Response CHAR(750) NOT NULL,
e3question3Response CHAR(750) NOT NULL)"; 

//creating questionBank table to store all questions created by instructor
$table2 = "CREATE TABLE BETA_questionBank (
id INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
question CHAR(500) NOT NULL), /* full question will be saved here with all fill-in data from instructor */
difficulty CHAR(1) NOT NULL, /* EASY, MEDIUM, HARD */)";



//receiving json data from Haard (middle) for login
$raw_data = file_get_contents('php://input'); //get JSON data
$data = json_decode($raw_data, true); //decode JSON data
$user_pass = array('ucid' => $data['user'], 'pass' => $data['pass']); //store JSON data


echo $user_pass['ucid'];
echo $user_pass['pass'];

//encrypt password
$hashPass = hash('sha1', $user_pass['pass']);
echo $hashPass;
echo "\n";
//find out if UCID/password that were entered correspond to a student or an instructor
//$queryStuOrInst = "SELECT * FROM BETA_users WHERE password = '$hashPass' AND userID = '$user_pass['ucid']'";
$result = mysqli_query($conn, "SELECT userType FROM BETA_users WHERE password LIKE '%{$hashPass}%' AND userID LIKE '%{$user_pass['ucid']}%'");
//$result = mysqli_query($conn, "SELECT userType FROM BETA_users WHERE password = '975eee8f77078ca6ab8f59d26824e3be84ba8a46' AND userID = 'jsnow'");
//$result=mysqli_query($conn,$queryStuOrInst); 
//echo $result;
if($result->num_rows!=0){
  $row = $result->fetch_row();
  echo $row[0];
  //$myObj->user="student";
  //echo "student";
  echo 'true';
}
else{
  //$myObj->user="instructor";
  //echo "instructor";
  echo 'false';
}



//sending reply to Haard (middle) for student/instructor 
//$myJSON=json_encode($myObj);
//echo $myJSON;






?>