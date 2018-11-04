Deborah Santo
Release Candidate
CS 490-001

Back



----------------------------------------------------------------------------------------------
add_question.php
----------------------------------------------------------------------------------------------
Add a new question created by the instructor to the questionBank table in the database.

Receives JSON from the middle in the form of:
	
	functionName: 'name of function'
	params: array()
	does: 'description of what the function is supposed to do'
	returns: 'description of what the function is supposed to return'
	difficulty: e | m | h
	testCases: array() 

Once the data from the JSON is read, all data is inserted into the RC_guestionBank table.
If the data isinserted into the table/the query runs properly, then the string "exam question
entered into questionBank" is echoed, otherwise, if it is not inserted into the questionBank
table, then the string "error: question not added in questionBank" is echoed.



----------------------------------------------------------------------------------------------
allExamsToBeGraded.php
----------------------------------------------------------------------------------------------
Returns all exams that still need to be graded/released by the instructor.

Receives JSON from the middle in the form of: 

	N/A

Runs a query for the following data from the database:

	RC_rawExamData.userID
	RC_rawExamData.examID
	RC_rawExamData.questionID
	RC_rawExamData.studentResponse
	RC_questionBank.functionName
	RC_questionBank.parameters
	RC_questionBank.functionDescription
	RC_questionBank.output
	RC_questionBank.points
	RC_grades.examScore	

All above data is stored into an array as follows:

	$tempArray['userID']=$row['userID'];
    	$tempArray['examID']=(int)$row['examID'];
	$tempArray['questionID']=(int)$row['questionID'];
    	$tempArray['studentResponse']=$row['studentResponse'];
    	$tempArray['functionName']=$row['functionName'];
    	$tempArray['parameters']=explode(',',$row['parameters']);
    	$tempArray['does']=$row['functionDescription'];
    	$tempArray['prints']=$row['output'];
    	$tempArray['points']=(int)$row['points'];
    	$tempArray['examScore']=(int)$row['examScore'];

Each array created in the above format is stored in another array.
That main array is then encoded as a JSON and echoed.

If the query does not return any data from the database, then an empty array is encoded as a 
JSON and that is echoed.



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

	* FROM RC_questionBank

All above data is stored into an array as follows:

	$tempArray["questionID"]=$row['questionID'];
    	$tempArray["functionName"]=$row['functionName'];
    	$tempArray["params"]=explode(',',$row['parameters']);
    	$tempArray["does"]=$row['functionDescription'];
    	$tempArray["prints"]=$row['output'];
    	$tempArray["difficulty"]=$row['difficulty'];
    	$tempArray["points"]=$row['points'];

Each array created in the above format is stored in another array.
That main array is then encoded as a JSON and echoed.

If the query does not return any data from the database, then an empty array is encoded as a 
JSON and that is echoed.



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
	
	RC_rawExamData.questionID
	RC_questionBank.points
	RC_questionBank.functionName
	RC_rawExamData.studentResponse
	RC_questionBank.correctResponse
	RC_questionBank.testCase

All above data is stored into an array as follows:

	$tempArray["questionID"]=$row['questionID'];
    	$tempArray["points"]=$row['points'];
    	$tempArray["function_name"]=$row['functionName'];
    	$tempArray["student_response"]=$row['studentResponse'];
    	$tempArray["correct_response"]=$row['correctResponse'];
    	$tempArray["test_cases"]=explode(':',$row['testCases']);

Each array created in the above format is stored in another array.
That main array is then encoded as a JSON and echoed.

If the query does not return any data from the database, then an empty array is encoded as a 
JSON and that is echoed.


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