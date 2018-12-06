function showExams(obj) {
    obj = parseJSON(obj);
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
    let th4 = appendNodeToNode('th', '', '', tr);
    th4.innerHTML = 'Released';

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
        inputScore.style = 'text-align: center;background-color:white;';

        let td4 = appendNodeToNode('td', '', '', tr);
        let checkbox = appendNodeToNode('input', '', '', td4);
        checkbox.setAttribute('type', 'checkbox');
        checkbox.disabled = true;
        checkbox.checked = obj[i].released == "1";


        // let td5 = appendNodeToNode('td', '', '', tr);
        // let changeGradeBtn = appendNodeToNode('button', 'b' + (i + 1), '', td5);
        // changeGradeBtn.innerHTML = 'Change Grade';

        let td6 = appendNodeToNode('td', '', '', tr);
        let viewExamBtn = appendNodeToNode('button', 'b' + (i), '', td6);
        viewExamBtn.innerHTML = 'View Exam';

        viewExamBtn.onclick = function () {
            let id = this.id.split('b')[1];
            let tbl = getelm('table');
            let examID = obj[id].examID,
                userID = tbl.rows[id].cells[1].innerHTML;
            //TODO: use AJAX to change page here and pass usrID and examID to next page.
            let x = {};
            x.page = TITLE_GRADE_AN_EXAM;
            x.js = JS_GRADE_AN_EXAM;
            x.url = './grade';
            x.user = userID;
            x.examID = examID;

            getPage(JSON.stringify(x), changeToNewPage);
            // window.location = `grade-an-exam.html?user=${userID}&examID=${examID}`;
        };


    }
}

function btnGoBack() {
    window.history.back();
}

function initializeViewCompletedExams() {
    container = getelm('container');

    /* Request to retrieve all exams that students have taken from database */
    let obj = {};
    obj.requestType = 'allExamsToBeGraded';
    sendAJAXReq(showExams, JSON.stringify(obj));
    setNavbarActive(TITLE_VIEW_COMPLETED_EXAMS);

}

initializeViewCompletedExams();