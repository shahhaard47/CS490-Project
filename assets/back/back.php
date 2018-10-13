<?php

echo "Hello";

$servername = "sql2.njit.edu";
$username = "ds547";
$password = "ZvwiSKhG";
$databaseName = "ds547";

$conn = new mysqli($servername, $username, $password, $databaseName); //connecting to database



//creating users table for students and instructors
$table1 = "CREATE TABLE BETA_users ( 
userID VARCHAR(6) NOT NULL,
password CHAR(130) NOT NULL),
userType CHAR(1) NOT NULL, /* will store S=student or I=instructor */
exam1Score INT(3) NULL, /* midtermExam1 */
exam2Score INT(3) NULL, /* midtermExam2 */
exam3Score INT(3) NULL, /* finalExam */
PRIMARY KEY(userID))"; 

//creating table to store raw exam question responses and corresponding comments
$table2 = "CREATE TABLE rawExamData (
userID VARCHAR(6) NOT NULL,
e1q1Response CHAR(750) NULL,
e1q1Comments CHAR(750) NULL,
e1q2Response CHAR(750) NULL,
e1q2Comments CHAR(750) NULL,
e1q3Response CHAR(750) NULL,
e1q3Comments CHAR(750) NULL,
e2q1Response CHAR(750) NULL,
e2q1Comments CHAR(750) NULL,
e2q2Response CHAR(750) NULL,
e2q2Comments CHAR(750) NULL,
e2q3Response CHAR(750) NULL,
e2q3Comments CHAR(750) NULL,
e3q1Response CHAR(750) NULL,
e3q1Comments CHAR(750) NULL,
e3q2Response CHAR(750) NULL,
e3q2Comments CHAR(750) NULL,
e3q3Response CHAR(750) NULL,
e3q3Comments CHAR(750) NULL,
PRIMARY KEY(userID))";

//creating questionBank table to store all questions created by instructor
$table3 = "CREATE TABLE BETA_questionBank (
questionID INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
questionText CHAR(255) NOT NULL), /* full question will be saved here with all fill-in data from instructor */
difficulty CHAR(1) NOT NULL /* EASY, MEDIUM, HARD */)";



//receiving json data from Haard (middle) for login
$rawLoginData = file_get_contents('php://input'); //get JSON data for login
$data = json_decode($rawLoginData, true); //decode JSON data for login
$user_pass = array('ucid' => $data['user'], 'pass' => $data['pass']); //store JSON data for login



//encrypt password
$hashPass = hash('sha1', $user_pass['pass']);

//find out if UCID/password that were entered correspond to a student or an instructor
$query = "SELECT * FROM BETA_users WHERE password LIKE '%{$hashPass}%' AND userID LIKE '%{$user_pass['ucid']}%'";
$result = mysqli_query($conn, $query);
echo $result;
if($result->num_rows!=0){
  $myObj->send=TRUE;
  echo 'good';
}
else{
  $myObj->send=FALSE;
  echo 'bad';
}
//if($result=="s"){ //if the ucid/password entered at login is associated with a student account
//  	$myObj->user="student";
//  	echo "student";
//} 
//elseif($result=="i") { //if the ucid/password entered at login is associated with an// instructor account
//  	$myObj->user="instructor";
//  	echo "instructor";
//} 
//else { //if ucid/password entered at login is not found in the BETA_users table
//  $myObj->notFound=TRUE;
//	echo "UCID/password not recognized";
//}

//sending reply to Haard (middle) for student/instructor 
$myJSON=json_encode($myObj);
echo $myJSON;



//receiving json data from Haard (middle) for login
//$rawLoginData = file_get_contents('php://input'); //get JSON data
//$data = json_decode($rawLoginData, true); //decode JSON data
//$user_pass = array('ucid' => $data['user'], 'pass' => $data['pass']); //store JSON data



?>