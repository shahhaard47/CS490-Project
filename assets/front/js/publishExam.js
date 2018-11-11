/* Request types */
const GET_ALL_CREATED_EXAMS = 'getAllCreatedExams';

/* AJAX requests */
function getAllCreatedExams() {
    let obj = {};
    obj.requestType = GET_ALL_CREATED_EXAMS;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(parseJSON(xhr.responseText));
                populateTable(parseJSON(xhr.responseText));

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

function submitPublishExamRequest(examID) {
    let obj = {};
    obj.examID = examID;
    obj.requestType = 'publish_exam';

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(parseJSON(xhr.responseText));
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

/* Show all exams in a table */
function populateTable(obj) {
    let table = getelm('table');
    let tr = appendNodeToNode('tr', '', '', table);
    appendNodeToNode('th', '', '', tr).innerHTML = 'Exam Name';

    for (let i in obj) {
        tr = appendNodeToNode('tr', '', '', table);
        let td = appendNodeToNode('td', `data${i}`, '', tr);
        td.innerHTML = obj[i].examName;

        td = appendNodeToNode('td', `data${i}`, '', tr);
        td.innerHTML = obj[i].examName;
        // let btn = appendNodeToNode('button', `btn${obj[i].examID}`, '', td);
        // btn.innerHTML = obj[i].examName;
    }
}

window.onload = function () {
    getAllCreatedExams();
};