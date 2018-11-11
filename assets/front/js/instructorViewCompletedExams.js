function getExamsCompletedByStudents() {
    let obj = {};
    obj.requestType = 'allExamsToBeGraded';
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                showExams(parseJSON(xhr.responseText));
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

function showExams(obj) {
    log(obj);
    let table = appendNodeToNode('table', 'table', '', container);
    table.setAttribute('width', '100%');
    table.style = 'text-align:center;';
    let tr = appendNodeToNode('tr', '', '', table);
    let th1 = appendNodeToNode('th', '', '', tr);
    th1.innerHTML = 'Exam Name';
    let th2 = appendNodeToNode('th', '', '', tr);
    th2.innerHTML = 'UCID';
    let th3 = appendNodeToNode('th', '', '', tr);
    th3.innerHTML = 'Autograde/Change Grade';
    let th4 = appendNodeToNode('th', '', '', tr);
    th4.innerHTML = 'Comments';

    for (let i = 0; i < obj.length; i++) {
        let tr = appendNodeToNode('tr', `row${i}`, '', table);

        let td1 = appendNodeToNode('td', '', '', tr);
        td1.innerHTML = obj[i].examName;

        let td2 = appendNodeToNode('td', '', '', tr);
        td2.innerHTML = obj[i].userID;

        let td3 = appendNodeToNode('td', '', '', tr);
        let inputScore = appendNodeToNode('input', '', '', td3);
        inputScore.setAttribute('value', obj[i].overallScore);

        let td4 = appendNodeToNode('td', '', '', tr);
        appendNodeToNode('input', '', '', td4);

        let td5 = appendNodeToNode('td', '', '', tr);
        let changeGradeBtn = appendNodeToNode('button', 'b' + (i + 1), '', td5);
        changeGradeBtn.innerHTML = 'Change Grade';

        let td6 = appendNodeToNode('td', '', '', tr);
        let viewExamBtn = appendNodeToNode('button', 'b' + (i + 1), '', td6);
        viewExamBtn.innerHTML = 'View Exam';

        viewExamBtn.onclick = function () {
            let id = this.id.split('b')[1];
            let tbl = getelm('table');
            let examID = obj[id - 1].examID,
                userID = tbl.rows[id].cells[1].innerHTML,
                score = tbl.rows[id].cells[2].childNodes[0].value,
                comments = tbl.rows[id].cells[3].childNodes[0].value;

            window.location = `grade-an-exam.html?user=${userID}&examID=${examID}`;
        };

        changeGradeBtn.onclick = function () {
            let tbl = getelm('table');
            let examID = tbl.rows[this.id].cells[0].innerHTML,
                userID = tbl.rows[this.id].cells[1].innerHTML,
                score = tbl.rows[this.id].cells[2].childNodes[0].value,
                comments = tbl.rows[this.id].cells[3].childNodes[0].value;
            sendObj = {};
            sendObj.examID = examID;
            sendObj.userID = userID;
            sendObj.score = score;
            sendObj.requestType = 'sendGradeRequest';
            sendObj.comments = comments;

        }
    }
}

window.onload = function () {
    container = getelm('container');
    getExamsCompletedByStudents();
};