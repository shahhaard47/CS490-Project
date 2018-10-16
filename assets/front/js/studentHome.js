const GET_AVAILABLE_EXAMS_RT = 'getAvailableExams';

function getAvailableExams() {
    let obj = {};
    obj.requestType = GET_AVAILABLE_EXAMS_RT;
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // log(xhr.responseText);
                populateExamsTable(parseJSON(xhr.responseText));
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

function populateExamsTable(jsonObj) {
    let container = getelm('availableExamsContainer');
    let table = appendNodeToNode('table', 'examsTable', 'examsTable', container);
    let tr = appendNodeToNode('tr', '', '', table);
    appendNodeToNode('th', '', '', tr).innerHTML = 'Exam ID';


    for (let i = 0; i < jsonObj.data.length; i++) {
        let examID = jsonObj.data[i].examID;
        let tRow = appendNodeToNode('tr', examID, '', table);
        let td = appendNodeToNode('td', '', '', tRow);
        let btn = appendNodeToNode('button', '', 'examBtn', td);
        btn.innerHTML = examID;
        btn.onclick = function () {
            openExam(examID, jsonObj);
        }
        // tRow.cells[0].onclick = function () {
        //     log(`Clicked: ${this.innerHTML}`);
        // }
    }
}

function openExam(examID, jsonObj) {
    examIDForTakeExam = examID;
    jsonObjForTakeExam = jsonObj;
    window.location = 'take-exam.html?id=' + examID + '&data=' + JSON.stringify(jsonObj);

}

window.onload = function () {
    getAvailableExams();
};
