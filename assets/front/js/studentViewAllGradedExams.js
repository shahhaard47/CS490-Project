examObjs = [];
releasedExam = {};

/* Check exam released */
function submitCheckExamReleasedRequest(examID) {
    let obj = {};
    obj.userID = getURLParams(window.location.href).ucid;
    obj.examID = examID;
    obj.requestType = 'isReleased';
    // log(obj);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(xhr.responseText);
                x = parseJSON(xhr.responseText);
                if (x.released) {
                    log(examID + ' is released')
                }
            } else {
                log(examID + ' is NOT released')
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

/* Get Released exams */

/* Check exam released */
function submitGetReleasedExams() {
    let obj = {};
    obj.requestType = 'getReleasedExams';
    // log(obj);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(xhr.responseText);
                x = parseJSON(xhr.responseText);
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

/* AJAX request when don't care about response */
function submitGetExamGradesRequest() {
    let obj = {};
    obj.userID = getURLParams(window.location.href).ucid;
    obj.requestType = 'allStudentExamInfo';
    log(obj);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // log(xhr.responseText);
                x = parseJSON(xhr.responseText);
                for (let i = 0; i < x.length; i++) {
                    // log(x[i]);
                    //TODO: receive one exam
                    if (x[i].userID === obj.userID) {
                        examObjs.push(x[i]);
                    }
                }
                showExams(x);
                // log(examObjs)
                // loadView();
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

function getReleasedExams() {
    for (let i = 0; i < examObjs.length; i++) {
        // log(x[i]);
        if (getReleasedExams(examObjs[i].examID)) {
            // examObjs.push(x[i]);
        }
    }

}

function showExams(obj) {
    // log('-----');
    log(obj);
    if (obj.length === 0) {
        let dialog = showDialog(document.body, 'There are no grades to view. Try again later.');
        dialog.show();
        let btn = dialog.getElementsByTagName('button');

        btn[0].onclick = function () {
            window.history.back();
            // window.location = 'student-home.html?ucid=' + userID;
        };
        return;
    }
    // log(obj);
    let table = appendNodeToNode('table', 'table', '', container);
    table.setAttribute('width', '100%');
    table.style = 'text-align:center;';
    let tr = appendNodeToNode('tr', '', '', table);
    let th1 = appendNodeToNode('th', '', '', tr);
    th1.innerHTML = 'Exam Name';
    let th3 = appendNodeToNode('th', '', '', tr);
    th3.innerHTML = 'Overall Score';

    for (let i = 0; i < obj.length; i++) {
        let tr = appendNodeToNode('tr', `row${i}`, '', table);

        let td1 = appendNodeToNode('td', '', '', tr);
        td1.innerHTML = obj[i].examName;

        let td2 = appendNodeToNode('td', '', '', tr);
        let inputScore = appendNodeToNode('input', '', '', td2);
        inputScore.setAttribute('value', obj[i].overallScore);
        inputScore.disabled = true;
        inputScore.style = 'text-align: center;background-color:white;';

        // let td5 = appendNodeToNode('td', '', '', tr);
        // let changeGradeBtn = appendNodeToNode('button', 'b' + (i + 1), '', td5);
        // changeGradeBtn.innerHTML = 'Change Grade';

        let td6 = appendNodeToNode('td', '', '', tr);
        let viewExamBtn = appendNodeToNode('button', 'b' + (i + 1), '', td6);
        viewExamBtn.innerHTML = 'View Exam';

        viewExamBtn.onclick = function () {
            let id = this.id.split('b')[1];
            let tbl = getelm('table');
            /*let examID = obj[id - 1].examID,
                userID = tbl.rows[id].cells[1].innerHTML,
                score = tbl.rows[id].cells[2].childNodes[0].value,
                comments = tbl.rows[id].cells[3].childNodes[0].value;
*/
            window.location = `student-view-grades.html?user=${obj[i].userID}&examID=${obj[i].examID}`;
        };

    }
}

window.onload = function () {
    submitGetExamGradesRequest();
    // submitGetReleasedExams();
};