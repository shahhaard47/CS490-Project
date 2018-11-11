let questionIDsInExam = [], solutions = {}, examID = '', previousIDSelected = '';

function getAvailableExams() {
    let obj = {};
    obj.requestType = GET_AVAILABLE_EXAM_RT;
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
    let num = 1, questionsList = obj.questions;
    for (let i = 0; i < questionsList.length; i++) {
        questionIDsInExam.push(questionsList[i].questionID);
        let btn = appendNodeToNode('button', `btn${questionsList[i].questionID}`, '', getelm('questions-in-exam'));
        btn.innerHTML = `Question ${num++}`;
        btn.setAttribute('style', 'width:100%');
        btn.onclick = function () {
            getelm('questionTitle').innerHTML = 'Question ' + btn.innerHTML;
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
            let l2 = [];
            l2.push(parseInt(getLastNumbersFromString(keys[i])));
            l2.push(solutions[keys[i]]);
            list.push(l2);
        }
    }
    return list;
}

function submitExamBH() {
    /* Make sure the solution to the last question is saved */
    saveSolution(questionIDsInExam[questionIDsInExam.length - 1], solutionTextArea.value);
    let obj = {};
    obj.examID = parseInt(examID);
    obj.userID = getURLParams(window.location.href).userid;
    obj.answers = getAnswers();
    obj.requestType = 'submit_exam';
    log(obj);
    sendAJAXReq(JSON.stringify(obj));
    window.location = 'student-home.html';
}

function initView() {
    if (questionIDsInExam[0] !== null || questionIDsInExam !== '')
        previousIDSelected = 'btn' + questionIDsInExam[0];
    let btn;
    if ((btn = getelm('left').getElementsByTagName('button')[0]) !== null)
        btn.click();
}

window.onload = function () {
    questionTextArea = getelm('question-content');
    solutionTextArea = getelm('solution-code');
    enableTab(solutionTextArea.id);
    let params = getURLParams(window.location.href);
    examID = params.id;
    changeInnerHTML('header-examID', `Exam ID: ${params.id}`);
    getAvailableExams();
};

/*
grade.php -> userID, examID

 */