/**
 * Controller for 'student-view-grades'.
 *
 * This file is used for a student to view their grades for all the questions in the exam. They can only get here after
 * the instructor has released grades.
 *
 **/
examObj = {};

/* AJAX request when don't care about response */
function submitGetExamGradesRequest() {
    let obj = {};
    obj.userID = getURLParams(window.location.href).user;
    obj.requestType = 'allStudentExamInfo';

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // log(xhr.responseText);
                x = parseJSON(xhr.responseText);
                // log('obj len');
                // log(x[0].examQuestions.length);
                examObj = x;
                loadView();
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


function loadView() {
    populateHeaderBar();
    loadQuestionsInExam();
}

function loadQuestionsInExam() {
    let examQuestions = examObj[0].examQuestions;
    log(examQuestions);
    for (let i = 0; i < examQuestions.length; i++) {
        let questionContainer = appendNodeToNode('div', 'question' + i, 'questionContainer', getelm('allQuestionsContainer'));

        let questionTop = appendNodeToNode('div', '', 'questionTop', questionContainer);
        appendNodeToNode('p', 'questionNumber' + i, '', questionTop).innerHTML = 'Question ' + (i + 1);
        let questionConstructedContainer = appendNodeToNode('div', 'questionConstructedContainer', '', questionTop);
        let label = appendNodeToNode('label', '', '', questionConstructedContainer);
        let textarea = appendNodeToNode('textarea', 'questionStringInput', '', label);
        textarea.disabled = true;
        textarea.rows = 5;
        textarea.innerHTML = examQuestions[i].constructed;
        textarea.disabled = true;

        let studentResponseContainer = appendNodeToNode('div', '', 'studentResponseContainer', questionContainer);
        label = appendNodeToNode('label', '', '', studentResponseContainer);
        textarea = appendNodeToNode('textarea', '', '', studentResponseContainer);
        textarea.disabled = true;
        textarea.rows = 20;
        textarea.innerHTML = examQuestions[i].studentResponse;

        let questionBottom = appendNodeToNode('div', '', 'questionBottom', questionContainer);
        label = appendNodeToNode('label', '', '', questionBottom);
        label.innerHTML = 'Points<br>';
        let input = appendNodeToNode('input', 'points' + i, '', label);
        input.value = examQuestions[i].points;
        input.size = 3;
        input.disabled = true;

        label.appendChild(document.createTextNode(`out of ${examQuestions[i].maxPoints}`));

        appendNodeToNode('br', '', '', label);

        label = appendNodeToNode('label', '', '', questionBottom);
        label.innerHTML = 'Comments<br>';
        textarea = appendNodeToNode('textarea', 'comments' + i, '', label);
        textarea.disabled = true;
        textarea.rows = 5;

        /* Display comments line by line */
        // log(examQuestions)
        // log(examQuestions.length)
        // for (let j = 0; j < examQuestions.length; j++) {
        //     if (examQuestions[i].gradedComments[j] !== undefined)
        textarea.innerHTML += examQuestions[i].gradedComments + '\n';
        // }

        label = appendNodeToNode('label', '', '', questionBottom);
        label.innerHTML = 'Instructor Comments<br>';
        textarea = appendNodeToNode('textarea', 'comments' + i, '', label);
        textarea.rows = 10;
        textarea.innerHTML = examQuestions[i].instructorComments;
        textarea.disabled = true;

    }
}

function populateHeaderBar() {
    log(examObj[0].userID);
    getelm('userID').value = examObj[0].userID;
    getelm('examName').value = examObj[0].examName;
    getelm('overallGrade').value = examObj[0].overallScore;

}


/* Button Handlers */

function btnGoBack() {
    window.history.back();

}

window.onload = function () {
    userID = getURLParams(window.location.href).user;
    examID = getURLParams(window.location.href).examID;
    submitGetExamGradesRequest();
};