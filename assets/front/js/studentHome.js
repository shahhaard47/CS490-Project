let examIDsArr = [];

function getPublishedExam() {
    let obj = {};
    obj.requestType = GET_AVAILABLE_EXAM_RT;
    obj.userID = getURLParams(window.location.href).ucid;
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(parseJSON(xhr.responseText));
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
    let table = appendNodeToNode('table', 'table', 'table', container);
    let tr = appendNodeToNode('tr', '', '', table);
    appendNodeToNode('th', '', '', tr).innerHTML = 'Available to Take';

    // for (let i = 0; i < jsonObj.length; i++) {}
    let tRow = appendNodeToNode('tr', 'examID' + jsonObj.examID, '', table);
    let td = appendNodeToNode('td', '', '', tRow);
    let btn = appendNodeToNode('button', '', 'examBtn', td);
    btn.innerHTML = jsonObj.examName;
    btn.onclick = function () {
        openExam(jsonObj.examID);
    }
}

function populateExamsArray(jsonObj) {
    log(`In populateExamsArray. jsonObj= ${jsonObj}`);
    for (let i = 0; i < jsonObj.length; i++) {
        if (!inArray(examIDsArr, jsonObj[i].examID)) {
            examIDsArr.push(jsonObj[i].examID);
        } else {
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
    getPublishedExam();
};
