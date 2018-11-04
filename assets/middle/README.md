Haard Shah
Release Candidate
CS 490-001

# Middle (Controller)
This director contains files that perform tasks of the `Controller` aspect of this MVC project architecture

## auth_login.php
1. Receive login info from FRONT
2. Check that the JSON has `user` and `pass` attributes
3. Pass along the original JSON to BACK.
4. Echo the result of BACK to FRONT.

## comms.php
for common communication between FRONT and BACK that doesn't require involvement of middle
1. Receives JSON from FRONT
2. looks at `requestType` attribute to get the file in the BACK to send it to
3. Echo's the result of BACK to FRONT.

Backfile's accessed through comms.php (`assets/back/`)
- `create_exam.php`
- `add_question.php`
- `allStudentExamInfo.php` (used in the old version -->newer version `getExamsToBeGraded.php`)
- `getExamsToBeGraded.php` (need to go through middle to inserted constructed question)
- `release_grades.php`
- `submit_exam.php`
- `update_comments.php`
- `update_grade.php`

## getallexams.php
Wrapper for BACK's getAvailableExams.php
- Inserts the `"constructed"` Question as one of the attributes 
- used for student on FRONT
- FIX ME: query just for the "showcased" exam 

## grade.php
Grades whatever exam FRONT requests to be graded
1. requests BACK for the raw exam data from the student
2. performs grading
3. calls `update_grade.php` in the BACK and send the scores
4. sends the scores to FRONT if the update complete successfully else echo's the error from the BACK

## request_question.php (outdated)
Previously used to get list of all questions
- where I inserted the `"constructed"` attributed with the constructed question








