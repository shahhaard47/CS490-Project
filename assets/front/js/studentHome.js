let examIDsArr = [];

function getAvailableExams() {
    let obj = {};
    obj.requestType = GET_AVAILABLE_EXAMS_RT;
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // log(xhr.responseText);
                populateExamsArray(parseJSON(xhr.responseText));
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
    log(examIDsArr);


    for (let i = 0; i < examIDsArr.length; i++) {
        let examID = examIDsArr[i];
        let tRow = appendNodeToNode('tr', examID, '', table);
        let td = appendNodeToNode('td', '', '', tRow);
        let btn = appendNodeToNode('button', '', 'examBtn', td);
        btn.innerHTML = examID;
        btn.onclick = function () {
            openExam(examID);
        }
        // tRow.cells[0].onclick = function () {
        //     log(`Clicked: ${this.innerHTML}`);
        // }
    }
}

function populateExamsArray(jsonObj) {
    log(jsonObj);
    for (let i = 0; i < jsonObj.length; i++) {
        // log('-----' + jsonObj[i].examID);
        if (!inArray(examIDsArr, jsonObj[i].examID)) {
            log('-not in: ' + jsonObj[i].examID);
            examIDsArr.push(jsonObj[i].examID);
        } else {
            log('in: ' + jsonObj[i].examID);
        }
    }

    log(examIDsArr);
}

function openExam(examID) {
    let ucid = getURLParams(window.location.href).ucid;
    window.location = `take-exam.html?userid=${ucid}&id=${examID}`;
}

function takeToViewExamBH(url) {
    let ucid = getURLParams(window.location.href).ucid;
    // prevUrl = url;
    window.location = 'view-exams.html?ucid=' + ucid;
}

window.onload = function () {
    getAvailableExams();
};
