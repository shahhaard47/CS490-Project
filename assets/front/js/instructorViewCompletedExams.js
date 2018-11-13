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
    th3.innerHTML = 'Overall Score';

    for (let i = 0; i < obj.length; i++) {
        let tr = appendNodeToNode('tr', `row${i}`, '', table);

        let td1 = appendNodeToNode('td', '', '', tr);
        td1.innerHTML = obj[i].examName;

        let td2 = appendNodeToNode('td', '', '', tr);
        td2.innerHTML = obj[i].userID;

        let td3 = appendNodeToNode('td', '', '', tr);
        let inputScore = appendNodeToNode('input', '', '', td3);
        inputScore.setAttribute('value', obj[i].overallScore);
        inputScore.disabled = true;
        inputScore.style = 'text-align: center;background-color:white;'

        // let td5 = appendNodeToNode('td', '', '', tr);
        // let changeGradeBtn = appendNodeToNode('button', 'b' + (i + 1), '', td5);
        // changeGradeBtn.innerHTML = 'Change Grade';

        let td6 = appendNodeToNode('td', '', '', tr);
        let viewExamBtn = appendNodeToNode('button', 'b' + (i), '', td6);
        viewExamBtn.innerHTML = 'View Exam';

        viewExamBtn.onclick = function () {
            let id = this.id.split('b')[1];
            log(id)
            let tbl = getelm('table');
            let examID = obj[id].examID,
                userID = tbl.rows[id].cells[1].innerHTML;
                // score = tbl.rows[id].cells[2].childNodes[0].value,
                // comments = tbl.rows[id].cells[3].childNodes[0].value;

            window.location = `grade-an-exam.html?user=${userID}&examID=${examID}`;
        };

        //TODO: CHANGE GRADE
        /*
        changeGradeBtn.onclick = function () {
            sendObj = {};
            sendObj.examID = examID;
            sendObj.userID = userID;
            sendObj.score = score;
            sendObj.requestType = 'sendGradeRequest';
            sendObj.comments = comments;

        }*/
    }
}

window.onload = function () {
    container = getelm('container');
    getExamsCompletedByStudents();
};