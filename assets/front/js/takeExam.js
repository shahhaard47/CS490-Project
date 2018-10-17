let questionIDsInExam = [], solutions = {}, examID = '', currIDSelected = '', prevUrl = '';

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

function saveSolution(questionID, solutionCode) {
    if (solutionCode !== '')
        solutions[questionID] = solutionCode;
}

function loadQuestions(obj) {
    log(obj);
    let num = 1;
    for (let i = 0; i < obj.length; i++) {
        // log(obj[i].examID);
        if (obj[i].examID == examID) {
            questionIDsInExam.push(obj[i].questionID);
            let btn = appendNodeToNode('button', obj[i].questionID, '', getelm('questions-in-exam'));
            btn.innerHTML = num++;
            btn.setAttribute('id', obj[i].questionID);
            btn.setAttribute('style', 'width:100%');
            btn.onclick = function () {
                getelm('questionTitle').innerHTML = 'Question ' + btn.innerHTML;
                saveSolution(currIDSelected, solutionTextArea.value);
                currIDSelected = this.id;
                openQuestion(this.id, obj);
                // if (solutionTextArea.value !== '')
                solutionTextArea.innerHTML = solutions[this.id];
                solutionTextArea.value = '';
                // log(solutions);
            };
            appendNodeToNode('br', '', '', getelm('questions-in-exam'));
        }
    }

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
    let obj = {};
    obj.examID = parseInt(examID);
    obj.userID = getURLParams(window.location.href).userid;
    obj.answers = getAnswers();
    obj.requestType = 'submit_exam';
    log(obj);
    sendAJAXReq(JSON.stringify(obj));
    window.location = 'student-home.html';
}

window.onload = function () {
    questionTextArea = getelm('question-content');
    solutionTextArea = getelm('solution-code');
    enableTab(solutionTextArea.id);
    let params = getURLParams(window.location.href);
    examID = params.id;
    changeInnerHTML('header-examID', `Exam ID: ${params.id}`);
    getAvailableExams();
    currIDSelected = questionIDsInExam[0];
};
/*
grade.php -> userID, examID

 */