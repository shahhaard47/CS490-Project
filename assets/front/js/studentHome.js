let examIDsArr = [];

/* userID is globally defined in login.js */
function getPublishedExam() {
    let obj = {};
    obj.requestType = GET_AVAILABLE_EXAM_RT;
    obj.userID = userID;
    log(obj);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                let response = parseJSON(xhr.responseText);
                log(response);
                if ((response.length === 0 || response.error)) {
                    let d = showDialog('Whoops...', 'There are no exams available to take at this moment.');
                    d.show();

                    getDialogCloseButton(d).remove();
                    return;
                }
                jsonExamObj = parseJSON(xhr.responseText);
                populateExamsContainer(jsonExamObj);
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

function submitCheckIfExamPublishedRequest(examID, examName) {
    let obj = {};
    obj.examID = examID;
    obj.requestType = 'isPublished';
    obj.userID = userID;
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                let response = parseJSON(xhr.responseText);
                log(response);
                /* Check to see if response was true or false */
                if (response.published) {
                    openExam(examID, examName);
                } else {
                    showDialog('Whoops...', 'This exam is no longer available to take. Please refresh and try again.').show();
                }
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
        submitCheckIfExamPublishedRequest(jsonObj.examID, jsonObj.examName);
    }
}

function populateExamsContainer(jsonObj) {
    //TODO: What if there's more then one exam published?
    let container = getelm('availableExamsContainer');
    let divParent = appendNodeToNode('div', 'examID' + jsonObj.examID, 'divParent', container);
    let divHeader = appendNodeToNode('div', '', 'divHeader', divParent);
    let title = appendNodeToNode('h2', '', '', divHeader);
    title.innerHTML = jsonObj.examName;

    let divBody = appendNodeToNode('div', '', 'divBody', divParent);
    let headerTag = appendNodeToNode('p', '', '', divBody);
    headerTag.innerHTML = 'Questions';

    // let pTag = appendNodeToNode('p', '', '', divBody);
    // pTag.innerHTML = jsonObj.count;

    let inputTextTag = appendNodeToNode('input', '', '', divBody);
    inputTextTag.disabled = true;
    inputTextTag.value = jsonObj.count;
    inputTextTag.setAttribute('size', 3);

    let btn = appendNodeToNode('button', '', 'startExamBtn', divBody);
    btn.innerHTML = 'Start';
    btn.onclick = function () {
        submitCheckIfExamPublishedRequest(jsonObj.examID, jsonObj.examName);
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

function openExam(examID, examName) {
    //TODO: Use AJAX here
    log('Open Exam here');
    let obj = {};
    obj.page = TITLE_STUDENT_TAKE_EXAM;
    obj.js = JS_STUDENT_TAKE_EXAM;
    obj.url = './take-exam';
    obj.examID = examID;
    obj.examName = examName;

    examIDToOpen = examID;
    examNameToOpen = examName;
    getPage(JSON.stringify(obj), changeToNewPage);
}

function initializeStudentHome() {
    setNavbarActive(TITLE_STUDENT_HOME);
    getPublishedExam();
}

function btnGoBack() {
    window.history.back();

}

initializeStudentHome();
