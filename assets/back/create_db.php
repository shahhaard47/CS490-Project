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
$BETA_exams = "CREATE TABLE BETA_exams (
examID INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
examName VARCHAR(500) NOT NULL,
questionIDs VARCHAR(500) NOT NULL,
points VARCHAR(500) NOT NULL,
published TINYINT(1) DEFAULT 0,
archived TINYINT(1) DEFAULT 0)";
$BETA_grades = "CREATE TABLE BETA_grades (
userID VARCHAR(6) NOT NULL,
examID INT(3) NOT NULL,
examScore INT(3) NOT NULL,
released TINYINT(1) DEFAULT 0)";
$BETA_questionBank = "CREATE TABLE BETA_questionBank (
questionID INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
functionName CHAR(30) NOT NULL,
parameters VARCHAR(1000) NOT NULL,
functionDescription VARCHAR(1000) NOT NULL,
output VARCHAR(1000) NOT NULL,
topic VARCHAR(500) NOT NULL,
difficulty CHAR(1) NOT NULL,
constraints VARCHAR(500) NULL,
testCases VARCHAR(1500) NOT NULL,
archived TINYINT(1) DEFAULT 0)";
$BETA_rawExamData = "CREATE TABLE BETA_rawExamData (
userID VARCHAR(6) NOT NULL,
examID INT(3) NOT NULL,
questionID INT(3) NOT NULL,
studentResponse VARCHAR(1000) NOT NULL,
modResponse VARCHAR(1000) NULL,
questionScore INT(3) NULL,
gradedComments VARCHAR(1000) NULL
instructorComments VARCHAR(1000) NULL,
testCasesPassFail VARCHAR(500) NULL)";
$BETA_users = "CREATE TABLE BETA_users (
userID VARCHAR(6) PRIMARY KEY,
password CHAR(130) NOT NULL,
userType CHAR(1) NOT NULL)";
?>