const SUBMIT_EXAM_RT = 'submit_exam',
    GRADE_EXAM_RT = 'gradeExam';
let questionIDsInExam = [], solutions = {}, previousIDSelected = '', currentSelectedID = '';

function submitGetAvailableExamsRequest() {
    let obj = {};
    obj.requestType = GET_AVAILABLE_EXAM_RT;
    obj.userID = userID;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(parseJSON(xhr.responseText));
                loadQuestions(parseJSON(xhr.responseText));
                initView();
            } else {
            }
        }
    };

    /* Open a POST request */
    xhr.open("POST", URL, true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(JSON.stringify(obj));
}

/* Grade exam request */
function submitGradeExamRequest() {
    x = {};
    x.examID = parseInt(examID);
    x.userID = userID;
    x.requestType = GRADE_EXAM_RT;
    log(x);

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(xhr.responseText);
            } else {
            }
        }
    };

    /* Open a POST request */
    xhr.open("POST", URL, true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(JSON.stringify(x));

}

/* AJAX request to submit exam */
function submitExamRequest(obj) {
    getelm('submitBtn').disabled = true;
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                /* Check if exam was successfully submitted */
                let response = parseJSON(xhr.responseText);
                log(`Response from back after submitting exam: ${response.query}`);
                log(response);
                if (response.query == true) {
                    let d = showDialog('Success!', 'Exam was submitted successfully!');
                    d.show();
                    let btn = d.getElementsByTagName('button');
                    btn[0].onclick = function () {
                        goToStudentHomePage();
                    };

                    /* When the submit request is good, send an auto grade exam request */
                    submitGradeExamRequest();

                } else {
                    // TODO: Save exam somehow in case database is down
                    getelm('submitBtn').disabled = false;
                    let d = showDialog('Whoops...', 'Exam was not submitted. Please try again.');
                    d.show();
                }

            } else {
            }
        }
    };

    /* Open a POST request */
    xhr.open("POST", URL, true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(JSON.stringify(obj));

}

/* src: https://jsfiddle.net/2wAzx/13/ */
function enableTab(id) {
    var el = document.getElementById(id);
    el.onkeydown = function (e) {
        if (e.keyCode === 9) { // tab was pressed

            // get caret position/selection
            var val = this.value,
                start = this.selectionStart,
                end = this.selectionEnd;

            // set textarea value to: text before caret + tab + text after caret
            this.value = val.substring(0, start) + '\t' + val.substring(end);

            // put caret at right position again
            this.selectionStart = this.selectionEnd = start + 1;

            // prevent the focus lose
            return false;

        }
    };
}

function openQuestion(questionsObj) {
    questionTextArea.innerHTML = questionsObj.constructed;
}

function loadQuestions(obj) {
    log(obj)
    let num = 1, questionsList = obj.questions;
    for (let i = 0; i < questionsList.length; i++) {
        questionIDsInExam.push(questionsList[i].questionID);
        let btn = appendNodeToNode('button', `btn${questionsList[i].questionID}`, 'btn', getelm('questions-in-exam'));
        btn.innerHTML = `Question ${num++}`;
        btn.setAttribute('style', 'width:100%');
        btn.onclick = function () {
            currentSelectedID = this.id;
            getelm('questionTitle').innerHTML = `${btn.innerHTML}<br>Points: ${questionsList[i].points}`;
            saveSolution(previousIDSelected, solutionTextArea.value);

            removeClass(`${previousIDSelected}`, 'btn-selected');
            addClass(this.id, 'btn-selected');

            previousIDSelected = this.id;
            // log('questionsList[i]:' + questionsList[i].constructed)
            openQuestion(questionsList[i]);
            fillSolutionTextArea(this.id);
            log(solutions);
        };
        appendNodeToNode('br', '', '', getelm('questions-in-exam'));
    }
}

function saveSolution(questionID, textareaValue) {
    if (textareaValue !== null || textareaValue !== '')
        solutions[questionID] = textareaValue;
    else
        solutions[questionID] = '';
}

function fillSolutionTextArea(questionID) {
    // if (solutionTextArea.value !== null || solutionTextArea.value !== '')
    if (solutions.hasOwnProperty(questionID))
        solutionTextArea.value = solutions[questionID];
    else
        solutionTextArea.value = '';
}

function getAnswers() {
    let list = [], keys = Object.keys(solutions);
    for (let i = 0; i < keys.length; i++) {
        if (solutions.hasOwnProperty(keys[i])) {
            let obj = {};
            obj.questionID = parseInt(getLastNumbersFromString(keys[i]));
            obj.studentResponse = solutions[keys[i]];

            list.push(obj);
        }
    }
    return list;
}

function submitExamBH() {
    /* Make sure the solution to the last question is saved */
    saveSolution(currentSelectedID, solutionTextArea.value);

    let obj = {};
    obj.examID = parseInt(examID);
    obj.userID = userID;
    obj.answers = getAnswers();
    obj.totalQuestions = obj.answers.length;
    obj.requestType = SUBMIT_EXAM_RT;

    log(obj);

    submitExamRequest(obj);

    /* Auto Grade the exam after the student has submitted it */

    // window.location = 'student-home.html?userid=' + userID;
}

function initView() {
    if (questionIDsInExam[0] !== null || questionIDsInExam !== '')
        previousIDSelected = 'btn' + questionIDsInExam[0];
    let btn;
    if ((btn = getelm('left').getElementsByTagName('button')[0]) !== null)
        btn.click();
}

/*
document.addEventListener('DOMContentLoaded', function () {
    log('=takeExamList=');

    questionTextArea = getelm('question-content');
    solutionTextArea = getelm('solution-code');
    enableTab(solutionTextArea.id);
    let params = getURLParams(window.location.href);
    examID = params.id;
    userID = params.userid;
    examName = params.examName;
    changeInnerHTML('header-examName', `<strong>Exam Name</strong><br>${params.examName}`);
    submitGetAvailableExamsRequest();
});
*/

function initializeTakeExam() {
    questionTextArea = getelm('question-content');
    solutionTextArea = getelm('solution-code');
    enableTab(solutionTextArea.id);
    examID = parseInt(examIDToOpen);
    examName = examNameToOpen;
    changeInnerHTML('header-examName', `<strong>Exam Name</strong><br>${examNameToOpen}`);
    submitGetAvailableExamsRequest();
}

initializeTakeExam();
