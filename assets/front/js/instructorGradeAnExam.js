/**
 * Controller for 'grade-an-exam.html'
 * This file is used for an instructor to grade a specific exam they clicked on a 'View Exam' button in
 * 'view-completed-exams.html'
 *
 **/

const RELEASE_GRADE_RT = 'release_grades',
    GRADE_EXAM_RT = 'gradeExam';

/* The current exam information as an object */
let examObj = {}, currentExamID = '';

/* Get the exam from middle-end who gets it from back-end */
function submitGetAvailableExams() {
    let obj = {};
    obj.requestType = 'allExamsToBeGraded';
    sendAJAXReq(loadView, JSON.stringify(obj));
}

function sendGradeRequest() {
    showButtonLoading('btnGradeExam');
    let obj = {};
    obj.userID = examObj.userID;
    obj.examID = parseInt(currentExamID);
    obj.scores = getScores();
    obj.requestType = GRADE_EXAM_RT;
    log(obj);

    sendAJAXReq(reloadViewGradeAnExam, JSON.stringify(obj))

}

function submitSaveExamRequest() {
    /* Show the loading icon in button. */
    showButtonLoading('btnSaveExam');

    let obj = {};
    obj.userID = examObj.userID;
    obj.examID = parseInt(examObj.examID);
    obj.data = getExamData();
    obj.requestType = 'saveExamInfo';
    log(obj);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                if (debug) {
                    log("--In Callback of 'submitSaveExamRequest'--");
                    log(parseJSON(xhr.responseText));
                }
                hideButtonLoading('btnSaveExam');
                let overallGrade = getelm('overallGrade').value;
                submitUpdateOverallGradeRequest(overallGrade, examObj.userID, examObj.examID);
                reloadViewGradeAnExam();
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

function getExamData() {
    let examQs = examObj.examQuestions, list = [];
    for (let i = 0; i < examQs.length; i++) {
        let obj = {};
        obj.questionID = parseInt(examQs[i].questionID);
        obj.points = parseInt(getelm('points' + i).value);
        obj.comments = getelm('comments' + i).value;

        list.push(obj);
    }

    return list;
}

function sendReleaseGradeRequest(obj) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(parseJSON(xhr.responseText));
                reloadViewGradeAnExam();
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

function loadView(AJAX_Response, onlyReloadHeader) {
    let jsonObj = parseJSON(AJAX_Response);
    if (debug) {
        log('---In loadView()---');
        log(jsonObj);
    }
    /* All exams to be graded are sent. Search for the examID that matches the one instructor clicked on. */
    for (let i = 0; i < jsonObj.length; i++) {
        if (jsonObj[i].examID == currentExamID) {
            examObj = jsonObj[i];
            break;
        }
    }

    populateHeaderBar();

    /* Only reload the view if 'onlyReloadHeader' is FALSE */
    if (!onlyReloadHeader)
        loadQuestionsInExam();
}


function loadQuestionsInExam() {
    getelm('allQuestionsContainer').innerHTML = '';

    let examQuestions = examObj.examQuestions;

    for (let i = 0; i < examQuestions.length; i++) {
        let questionContainer = appendNodeToNode('div', 'question' + i, 'questionContainer', getelm('allQuestionsContainer'));

        let questionTop = appendNodeToNode('div', '', 'questionTop', questionContainer);
        appendNodeToNode('p', 'questionNumber' + i, '', questionTop).innerHTML = '<strong>Question ' + (i + 1) + '</strong>';
        let questionConstructedContainer = appendNodeToNode('div', 'questionConstructedContainer', '', questionTop);
        let p = appendNodeToNode('p', '', '', questionConstructedContainer);
        let textarea = appendNodeToNode('textarea', 'questionStringInput', '', p);
        textarea.rows = 4;
        textarea.innerHTML = examQuestions[i].constructed;
        textarea.disabled = true;

        let studentResponseContainer = appendNodeToNode('div', '', 'studentResponseContainer', questionContainer);
        p = appendNodeToNode('p', '', '', studentResponseContainer);
        let studentResponseDiv = appendNodeToNode('div', 'studentResponseInput', 'textDiv', studentResponseContainer);
        studentResponseDiv.innerHTML = examQuestions[i].studentResponse.replace(/\n/g, "<br/>");


        let questionBottom = appendNodeToNode('div', '', 'questionBottom', questionContainer);
        p = appendNodeToNode('p', '', '', questionBottom);
        p.innerHTML = '<strong>Points </strong>';
        let inputPoints = appendNodeToNode('input', 'points' + i, '', p);
        inputPoints.setAttribute('size', 3);

        p.appendChild(document.createTextNode(`out of ${examQuestions[i].maxPoints}`));

        inputPoints.value = examQuestions[i].points;

        appendNodeToNode('br', '', '', p);

        p = appendNodeToNode('p', '', '', questionBottom);
        p.innerHTML = '<strong>Autograde Comments</strong><br>';

        let instructorCommentsDiv = appendNodeToNode('div', '', 'textDiv', questionBottom);

        let x = examQuestions[i].gradedComments;
        /* Display comments line by line */
        for (let j = 0; j < x.length; j++) {
            instructorCommentsDiv.innerHTML += examQuestions[i].gradedComments[j] + '<br><br>';
        }

        p = appendNodeToNode('p', '', '', questionBottom);
        p.innerHTML = '<strong>Instructor Comments</strong><br>';
        textarea = appendNodeToNode('textarea', 'comments' + i, 'instructorCommentsTextArea', p);
        textarea.rows = 5;
        textarea.innerHTML = examQuestions[i].instructorComments;

        /* Dynamically change overall score as points are changed for individual questions */
        let overallScore = getelm('overallGrade');
        inputPoints.onkeyup = function () {
            let pts = getPoints(), sum = 0;
            for (let i in pts) {
                if (!isNaN(pts[i]))
                    sum += pts[i];
            }

            overallScore.value = sum;
        };

    }
}

function getPoints() {
    let points = [];

    let questions = examObj.examQuestions;
    for (let i in questions) {
        let pointsElement = getelm(`points${i}`);
        // log(pointsElement)
        points.push(parseInt(pointsElement.value));
    }

    return points;
}


function populateHeaderBar() {
    getelm('userID').value = examObj.userID;
    getelm('examName').value = examObj.examName;
    getelm('overallGrade').value = examObj.overallScore;
    getelm('checkboxReleased').checked = examObj.released == "1";
}


/** Button Handlers */

function btnGoBack() {
    window.history.back();

}

function btnGradeExam() {
    sendGradeRequest();
}

function btnReleaseGrade() {
    showButtonLoading('btnReleaseGrade');
    let obj = {};
    obj.examID = currentExamID;
    obj.userID = getelm('userID').value;
    obj.requestType = RELEASE_GRADE_RT;

    sendAJAXReq(reloadViewGradeAnExam, JSON.stringify(obj));

}

function getScores() {
    let scores = [];
    for (let i = 0; i < examObj.examQuestions.length; i++) {
        /* Get points value from points input element */
        let points = parseInt(getelm('points' + i).value);
        /* Get the question id */
        let qID = parseInt(examObj.examQuestions[i].questionID);
        /* Get the comments value from the comments input element */
        let comments = getelm('comments' + i).innerHTML;
        scores.push([qID, points, comments])
    }

    return scores;
}

function reloadViewGradeAnExam() {
    hideButtonLoading('btnGradeExam');
    hideButtonLoading('btnReleaseGrade');
    submitGetAvailableExams();
}

function initializeGradeAnExam() {
    log('==initializeGradeAnExam Called==');

    if (debug) {
        log(`Exam ID: ${examID}.`);
    }
    currentExamID = examID;
    submitGetAvailableExams();
}

initializeGradeAnExam();
