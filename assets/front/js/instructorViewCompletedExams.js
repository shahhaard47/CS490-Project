function showExams(obj) {
    obj = parseJSON(obj);
    for (let i = 0; i < obj.length; i++) {
        let divParent = appendNodeToNode('div', `row${i}`, 'col examDivParent', container);

        let divHeader = appendNodeToNode('div', '', 'header', divParent);
        let headerTitle = appendNodeToNode('h2', '', 'examTitle', divHeader);
        headerTitle.innerHTML = obj[i].examName;

        let divBody = appendNodeToNode('div', '', 'divBody', divParent);

        appendNodeToNode('p', '', '', divBody).innerHTML = '<strong>UCID</strong>';
        appendNodeToNode('p', '', '', divBody).innerHTML = obj[i].userID;

        appendNodeToNode('p', '', '', divBody).innerHTML = '<strong>Overall Score</strong>';
        let inputScore = appendNodeToNode('input', '', '', divBody);
        inputScore.setAttribute('value', obj[i].overallScore);
        inputScore.disabled = true;
        inputScore.style = 'text-align: center;background-color:white;';

        appendBreakTag(divBody);

        appendNodeToNode('p', '', '', divBody).innerHTML = '<strong>Released</strong>';
        let checkbox = appendNodeToNode('input', '', '', divBody);
        checkbox.setAttribute('type', 'checkbox');
        checkbox.disabled = true;
        checkbox.checked = obj[i].released == "1";

        let viewExamBtn = appendNodeToNode('button', `btn${i}`, 'btnViewExam', divBody);
        viewExamBtn.innerHTML = 'View Exam';

        appendBreakTag(divBody);

        /*let btnRemove = appendNodeToNode('button', `btnRemove${i}`, 'btnRemove', divBody);
        btnRemove.innerHTML = 'Remove';*/

        divParent.onclick = function () {
            let x = {};
            x.page = TITLE_GRADE_AN_EXAM;
            x.js = JS_GRADE_AN_EXAM;
            x.url = './grade';
            // TODO: Get the correct ID's here
            x.user = obj[i].userID;
            x.examID = obj[i].examID;

            getPage(JSON.stringify(x), changeToNewPage);
        };

        viewExamBtn.onclick = function () {
            let x = {};
            x.page = TITLE_GRADE_AN_EXAM;
            x.js = JS_GRADE_AN_EXAM;
            x.url = './grade';
            // TODO: Get the correct ID's here
            x.user = obj[i].userID;
            x.examID = obj[i].examID;

            getPage(JSON.stringify(x), changeToNewPage);
        };

        /*btnRemove.onclick = function () {

        }*/
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