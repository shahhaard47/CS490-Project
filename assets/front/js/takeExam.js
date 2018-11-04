let questionIDsInExam = [], solutions = {}, examID = '', previousIDSelected = '';

function getAvailableExams() {
    let obj = {};
    obj.requestType = GET_AVAILABLE_EXAMS_RT;
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // log(xhr.responseText);
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


function openQuestion(questionID, obj) {
    for (let i = 0; i < obj.length; i++) {
        let x = obj[i];
        if (x.examID == examID && x.questionID == questionID) {
            questionTextArea.innerHTML = x.constructed;
        }
    }
}

function loadQuestions(obj) {
    log(obj);
    let num = 1;
    for (let i = 0; i < obj.length; i++) {
        if (obj[i].examID == examID) {
            questionIDsInExam.push(obj[i].questionID);
            let btn = appendNodeToNode('button', obj[i].questionID, '', getelm('questions-in-exam'));
            btn.innerHTML = num++;
            btn.setAttribute('id', obj[i].questionID);
            btn.setAttribute('style', 'width:100%');
            btn.onclick = function () {
                getelm('questionTitle').innerHTML = 'Question ' + btn.innerHTML;
                saveSolution(previousIDSelected, solutionTextArea.value);
                previousIDSelected = this.id;
                openQuestion(this.id, obj);
                fillSolutionTextArea(this.id);
                log(solutions);
            };
            appendNodeToNode('br', '', '', getelm('questions-in-exam'));
        }
    }
    log(questionIDsInExam);
}

function saveSolution(questionID, textareaValue) {
    log('saving sol for qid: ' + questionID);
    log('value: ' + textareaValue);
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
            l2.push(parseInt(keys[i]));
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
        previousIDSelected = questionIDsInExam[0];
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