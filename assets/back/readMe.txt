Deborah Santo
Release Candidate
CS 490-001

Back



----------------------------------------------------------------------------------------------
add_question.php 
----------------------------------------------------------------------------------------------
Add a new question created by the instructor to the questionBank table in the database.

Receives JSON from the middle in the form of:
	
	functionName: str, 'nameOfFunction'
	params: array(), [str a, str b, int c]
	does: str, 'what the function is supposed to do'
	returns: str, 'what the function is supposed to return'
	topic: str, 'what topic in CS 100 is this question related to, ex. lists, array, ...'
	difficulty: str, e | m | h
	constraints: str, 'what must the answer include, ex. for loop, while loop, recursion'
	testCases: str, 'str hello,str world;hello world:str hey,str there;hey there:...'

Data from JSON is decoded and put into an array. The parameters are converted from an array 
into a string. Then, all of the data is inserted into the questionBank table in the database.
If the data is inserted successfully, then "success" is echoed, else, "error: failure" is
echoed. 


**UPDATED ON Thursday, 11/8/2018
----------------------------------------------------------------------------------------------
allExamsToBeGraded.php
----------------------------------------------------------------------------------------------
Returns all exams that need to have the grading reviewed by the professor and are waiting to 
be released for the student to view their scores.

Receives JSON from the middle in the form of: 

	N/A

Runs a query for the following data from the database:

	BETA_grades.examScore
	BETA_questionBank.functionName
	BETA_questionBank.parameters
	BETA_questionBank.functionDescription
	BETA_questionBank.output
	BETA_rawExamData.userID
	BETA_rawExamData.examID
	BETA_rawExamData.questionID
	BETA_rawExamData.studentResponse
	BETA_rawExamData.questionScore
	BETA_questionBank.testCases
	BETA_rawExamData.testCasesPassFail	

All of the above data is stored in an array to then be returned to the middle, as shown:

	$tempArray['userID']=$row['userID'];
    	$tempArray['examID']=(int)$row['examID'];
	$tempArray['questionID']=(int)$row['questionID'];
    	$tempArray['studentResponse']=$row['studentResponse'];
    	$tempArray['functionName']=$row['functionName'];
    	$tempArray['parameters']=explode(',',$row['parameters']);
    	$tempArray['does']=$row['functionDescription'];
    	$tempArray['prints']=$row['output'];
    	$tempArray['points']=(int)$row['questionScore'];
	$tempArray['testCases']=explode(':',$row['testCases']);
	$tempArray['testCasesPassFail']=explode(',',$row['testCasesPassFail']);
    	$tempArray['examScore']=(int)$row['examScore'];

The array created with the data from the query in the above format is then stored as an
object named $myObj with the key 'raw'.
If the query returns nothing from the database, then null is stored in the object.

Runs another query for the following data from the database:

	* FROM BETA_exams
	--> examID,examName,questionIDs,points,published

The above data for the second query is stored in an array to then be returned to the middle,
as shown:
	
	$tempArray['examID']=$row['examID'];
	$tempArray['examName']=$row['examName'];
	$tempArray['qIDs']=explode(',',$row['questionIDs']);
	$tempArray['points']=explode(',',$row['points']);

The array created with the data from the query in the above format is then stored as an
object named $myObj with the key 'exam'.
If the query returns nothing from the database, then null is stored in the object.

$myObj is then encoded as JSON and echoed. 


--> All data is sent to middle to then be reorganized into an array and then sent to front.


NOTE: Should I add an if-statement when iterating through the query results to check which 
exams have already had the grade released and should therefore not be sent for grading?


**UPDATED ON Thursday, 11/8/2018
----------------------------------------------------------------------------------------------
allStudentExamInfo.php
----------------------------------------------------------------------------------------------
Returns exam/question data for a specific student for a specific exam.

Receives JSON from the middle in the form of:

	userID: 'mscott'
	examID: 6

Runs a query for the following data from the database:
	
	RC_questionBank.questionID
	RC_questionBank.functionName
	RC_questionBank.parameters
	RC_questionBank.functionDescription
	RC_questionBank.output
	RC_rawExamData.studentResponse
	RC_rawExamData.questionScore
	RC_rawExamData.instructorComments

All above data is stored into an array as follows:

	$tempArray['questionID']=(int)$row['questionID'];
      	$tempArray['functionName']=$row['functionName'];
      	$tempArray['parameters']=explode(',',$row['parameters']);
      	$tempArray['functionDescription']=$row['functionDescription'];
      	$tempArray['output']=$row['output'];
      	$tempArray['studentResponse']=$row['studentResponse'];
      	$tempArray['questionScore']=(int)$row['questionScore'];
      	$tempArray['instructorComments']=$row['instructorComments'];

Each array created in the above format is stored in another array.
That main array is then encoded as a JSON and echoed.

If the query does not return any data from the database, then an empty array is encoded as a 
JSON and that is echoed.
	


----------------------------------------------------------------------------------------------
back_login.php
----------------------------------------------------------------------------------------------
Returns whether the ucid and password entered are for a student, an instructor, or if the 
ucid and password pair do not exist in the database.

Receives JSON from the middle in the form of:
	
	ucid: 'userID'
	password: 'password'

Runs a query to search for the ucid and password in the database. 
If the query returns that the ucid/password are for a student, then 'student' is encoded in a 
JSON object and echoed. Else, if the query returns that the ucid/password are for an 
instructor, then 'instructor' is encoded in a JSON object and echoed. Else, meaning the ucid/
password pair does not exist in the database (the ucid and password both don't exist in the 
database, or the user entered the wrong password or ucid) then a NULL value is encoded in a 
JSON object and that is echoed.



----------------------------------------------------------------------------------------------
back_questionBank.php
----------------------------------------------------------------------------------------------
Returns all questions currently in the question bank table in the database.

Receives JSON from middle in the form of:
	
	N/A

Runs a query for the following data from the database:

	* FROM BETA_questionBank

All above data is stored into an array as follows:

	$tempArray["questionID"]=$row['questionID'];
    	$tempArray["functionName"]=$row['functionName'];
    	$tempArray["params"]=explode(',',$row['parameters']);
    	$tempArray["does"]=$row['functionDescription'];
    	$tempArray["prints"]=$row['output'];
	$tempArray["topic"]=$row['topic'];
    	$tempArray["difficulty"]=$row['difficulty'];
    	$tempArray["constraints"]=$row['constraints'];

Each array created in the above format is stored in another array.
That main array is then encoded as a JSON and echoed.

If the query does not return any data from the database, then an empty array is encoded as a 
JSON and that is echoed.


**UPDATED ON Thursday, 11/8/2018
----------------------------------------------------------------------------------------------
create_exam.php
----------------------------------------------------------------------------------------------
Add exam record in exams table.

Receives JSON from the middle in the form of:

	examName: 'exam name'
	questions: array()

A query is run to insert all of the received data into the RC_exams table in the database. 

If the data is inserted into the table successfully, then the string "exam created" is echoed.
Else, the string "error: exam not created" is echoed.



----------------------------------------------------------------------------------------------
get_grading_info.php
----------------------------------------------------------------------------------------------
Return data needed in order to grade exams (for middle).

Receives JSON from middle in the form of:

	userID: 'mscott'
	examID: 6

Runs a query for the following data from the database:
	
	BETA_rawExamData.questionID
	BETA_questionBank.functionName
	BETA_rawExamData.studentResponse
	BETA_questionBank.testCases

Runs another query for the following data from the database:

	BETA_exams.questionIDs
	BETA_exams.points

All above data is stored into an array as follows:

	$tempArray["questionID"]=$row['questionID'];
    	$tempArray["points"]=$row['points'];
    	$tempArray["function_name"]=$row['functionName'];
    	$tempArray["student_response"]=$row['studentResponse'];
    	$tempArray["test_cases"]=explode(':',$row['testCases']);

Each array created in the above format is stored in another array.
That main array is then encoded as a JSON and echoed.

If the query does not return any data from the database, then an empty array is encoded as a 
JSON and that is echoed.


**UPDATED ON Thursday, 11/8/2018
----------------------------------------------------------------------------------------------
getAvailableExams.php
----------------------------------------------------------------------------------------------
Return all exams currently available/in the exams table.

Receives JSON from the middle in the form of:

	N/A

Runs a query for the following data from the database:

	* FROM RC_exams

For each exam, a query is run for the information of the the questions contained in the
question bank table:

	functionName
	parameters
	functionDescription
	output
	points

All above data is stored into an array as follows:

	$tempArray['examID']=(int)$row_i['examID'];
     	$tempArray['questionID']=(int)$question;
      	$tempArray['functionName']=$row_j['functionName'];
      	$tempArray['parameters']=explode(',',$row_j['parameters']);
      	$tempArray['functionDescription']=$row_j['functionDescription'];
      	$tempArray['output']=$row_j['output'];
      	$tempArray['points']=(int)$row_j['points'];

Each array created in the above format is stored in another array.
That main array is then encoded as a JSON and echoed.

If the query does not return any data from the database, then an empty array is encoded as a 
JSON and that is echoed.


----------------------------------------------------------------------------------------------
publish_exam.php
----------------------------------------------------------------------------------------------
Updates the release column in the BETA_grades table in the database, so that the exam grade 
can be made visible to the student.

Receives JSON from the middle in the form of:

	examID: 46

Runs a query to update the published column in the exams table to TRUE.


**UPDATED ON Thursday, 11/8/2018
----------------------------------------------------------------------------------------------
release_grades.php
----------------------------------------------------------------------------------------------
Update released column in grades table so that the exam scores can be released to the student.

Receives JSON from the middle in the form of:
	
	userID: 'mscott'
	examID: 6

Runs a query to update the released column in the grades table to TRUE.

Nothing is echoed/returned.



----------------------------------------------------------------------------------------------
submit_exam.php
----------------------------------------------------------------------------------------------
Create record in rawExamData table and add student information and question responses/data.

Receives JSON from the middle in the form of:
	
	userID: 'mscott'
	examID: 6
	answers: array() --> [[questionID, studentResponse],[questionID, studentResponse],...]

All data is inserted into the rawExamData table.

Nothing is echoed/returned.



----------------------------------------------------------------------------------------------
update_comments.php
----------------------------------------------------------------------------------------------
Update the comments section with the comments the professor added for each exam question on 
an exam.

Receives JSON from the middle in the form of:

	userID: 'mscott'
	examID: 6
	comments: array() --> [[questionID, instructorComments],[questionID, instructorComments],...]

Runs a query to update the instructorComments columns in the rawExamData table. 

Nothing is echoed/returned.



----------------------------------------------------------------------------------------------
update_grade.php
----------------------------------------------------------------------------------------------
Update grade information for a specific student for a specific exam in the rawExamData table 
and in the grades table.

Receives JSON from the middle in the form of:

	userID: 'mscott'
	examID: 6
	scores: array() --> [[questionID, questionScore],[questionID, questionScore],...]

Runs a query to update the individula questionScore in the rawExamData table and updates/
creates a record in the grades table for the total exam score/percentage.



----------------------------------------------------------------------------------------------
update_overallScore.php
----------------------------------------------------------------------------------------------
Update grade information for a specific student for a specific exam in the rawExamData table
and in the grades table.

Receives JSON from the middle in the form of:
	
	userID: 'mscott'
	examID: 6
	score: 89

Runs a query to update the overall score for a student's exam in the grades table.

Nothing is echoed/returned.