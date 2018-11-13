/**
 * This file is used for an instructor to grade a specific exam they clicked on in 'view-completed-exams.html'
 *
 **/


const RELEASE_GRADE_RT = 'release_grades',
    GRADE_EXAM_RT = 'gradeExam';
/* The current exam as an object */
let examObj = {}, currentExamID = '';

/* Get the exam from middle-end who gets it from back-end */
function getAvailableExams() {
    let obj = {};
    obj.requestType = 'allExamsToBeGraded';
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(parseJSON(xhr.responseText));
                loadView(parseJSON(xhr.responseText));
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

function sendGradeRequest(obj) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(parseJSON(xhr.responseText));
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

function submitSaveExamRequest() {
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
                log(parseJSON(xhr.responseText));
                let overallGrade = getelm('overallGrade').value;
                submitUpdateOverallGradeRequest(overallGrade, examObj.userID, examObj.examID);
                window.location.reload();

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
    // log(examQs.length)
    for (let i = 0; i < examQs.length; i++) {
        // log(examQs[i]);
        let obj = {};
        obj.questionID = parseInt(examQs[i].questionID);
        obj.points = parseInt(getelm('points' + i).value);
        obj.comments = getelm('comments' + i).value;

        list.push(obj);
    }

    // log(list);

    return list;
}

function sendReleaseGradeRequest(obj) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(parseJSON(xhr.responseText));
                /* Update the overall score in the database for an exam */
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


function loadView(jsonObj) {
    for (let i = 0; i < jsonObj.length; i++) {
        if (jsonObj[i].examID == currentExamID) {
            examObj = jsonObj[i];
            break;
        }
    }

    populateHeaderBar();
    loadQuestionsInExam();
}


function loadQuestionsInExam() {
    let examQuestions = examObj.examQuestions;

    for (let i = 0; i < examQuestions.length; i++) {
        let questionContainer = appendNodeToNode('div', 'question' + i, 'questionContainer', getelm('allQuestionsContainer'));

        let questionTop = appendNodeToNode('div', '', 'questionTop', questionContainer);
        appendNodeToNode('p', 'questionNumber' + i, '', questionTop).innerHTML = 'Question ' + (i + 1);
        let questionConstructedContainer = appendNodeToNode('div', 'questionConstructedContainer', '', questionTop);
        let label = appendNodeToNode('label', '', '', questionConstructedContainer);
        let textarea = appendNodeToNode('textarea', 'questionStringInput', '', label);
        textarea.rows = 5;
        textarea.innerHTML = examQuestions[i].constructed;
        textarea.disabled = true;

        let studentResponseContainer = appendNodeToNode('div', '', 'studentResponseContainer', questionContainer);
        label = appendNodeToNode('label', '', '', studentResponseContainer);
        textarea = appendNodeToNode('textarea', '', '', studentResponseContainer);
        textarea.rows = 20;
        textarea.innerHTML = examQuestions[i].studentResponse;
        textarea.disabled = true;

        let questionBottom = appendNodeToNode('div', '', 'questionBottom', questionContainer);
        label = appendNodeToNode('label', '', '', questionBottom);
        label.innerHTML = 'Points<br>';
        let inputPoints = appendNodeToNode('input', 'points' + i, '', label);
        inputPoints.value = examQuestions[i].points;
        appendNodeToNode('br', '', '', label);

        /*
        label = appendNodeToNode('label', '', '', questionBottom);
        label.innerHTML = 'Points<br>';
        let inputOutOfPoints = appendNodeToNode('input', 'points' + i, '', label);
        inputOutOfPoints.value = examQuestions[i].points;
        appendNodeToNode('br', '', '', label);
        */ //TODO: Showw out of how many points
        label = appendNodeToNode('label', '', '', questionBottom);
        label.innerHTML = 'Comments<br>';
        textarea = appendNodeToNode('textarea', 'comments' + i, '', label);
        textarea.rows = 10;
        // textarea.innerHTML = examQuestions[i].comments;

        let x = examQuestions[i].gradedComments;
        /* Display comments line by line */
        for (let j = 0; j < x.length; j++) {
            textarea.innerHTML += examQuestions[i].gradedComments[j] + '\n';
        }

        let overallScore = getelm('overallGrade');
        inputPoints.onkeyup = function () {
            let pts = getPoints(), sum = 0;
            for (let i in pts) {
                if (!isNaN(pts[i]))
                    sum += pts[i];
            }

            overallScore.value = sum;
            log(sum);
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

}


/* Button Handlers */

function btnGoBack() {
    window.history.back();

}

function btnGradeExam() {
    let obj = {};
    obj.userID = examObj.userID;
    obj.examID = parseInt(currentExamID);
    obj.scores = getScores();
    obj.requestType = GRADE_EXAM_RT;
    log(obj);

    sendGradeRequest(obj);
}

function btnReleaseGrade() {
    let obj = {};
    obj.examID = currentExamID;
    obj.userID = getelm('userID').value;
    obj.requestType = RELEASE_GRADE_RT;

    sendReleaseGradeRequest(obj);

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

window.onload = function () {
    currentExamID = getURLParams(window.location.href).examID;
    getAvailableExams();
};
