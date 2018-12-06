/**
 *
 * This file is used for an instructor to publish, un-publish, or remove an exam that they have created.
 *
 **/

/* Request types */
const GET_ALL_CREATED_EXAMS = 'getAllCreatedExams',
    PUBLISH_EXAM_RT = 'publish_exam',
    UNPUBLISH_EXAM_RT = 'unpublish_exam';

/* AJAX requests */

//TODO: Simplify ajax reqs by passing function references to 'sendAJAXRequest'
function getAllCreatedExams() {
    let obj = {};
    obj.requestType = GET_ALL_CREATED_EXAMS;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // log(parseJSON(xhr.responseText));
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

function reloadView() {
    getAllCreatedExams();

}

function submitPublishOrUnpublishExamRequest(examID, requestType) {
    let obj = {};
    obj.examID = examID;
    obj.requestType = requestType;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // log(xhr.responseText);
                reloadView();
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

function submitRemoveExamRequest(examID) {
    let obj = {};
    obj.examID = examID;
    obj.requestType = 'delete_exam';

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // log(xhr.responseText);
                reloadView();
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
    let container = getelm('exams-container');
    container.innerHTML = '';

    for (let i in obj) {
        let divParent = appendNodeToNode('div', `row${i}`, 'examDivParent', container);

        let divHeader = appendNodeToNode('div', '', 'header', divParent);
        let headerTitle = appendNodeToNode('h2', '', 'examTitle', divHeader);
        headerTitle.innerHTML = obj[i].examName;

        let divBody = appendNodeToNode('div', '', 'divBody', divParent);

        let btnPubOrUnpub = appendNodeToNode('button', `btn${i}`, 'btnPublish', divBody);
        btnPubOrUnpub.innerHTML = 'Publish or Unpublish';

        if (checkExamPublished(i, obj)) {
            addClass(btnPubOrUnpub.id, 'published');
        }

        appendNodeToNode('br', '', '', divBody);

        let btnRemove = appendNodeToNode('button', `btnRemove${i}`, 'btnRemove', divBody);
        btnRemove.innerHTML = 'Remove';


        btnPubOrUnpub.onclick = function () {
            /* Check if the exam is already published */
            if (checkExamPublished(i, obj)) {
                submitPublishOrUnpublishExamRequest(publishedExamID, UNPUBLISH_EXAM_RT);
            } else {
                /* Send XHR request to unpublish current published exam. And publish new one */
                if (publishedExamID !== null)
                    submitPublishOrUnpublishExamRequest(publishedExamID, UNPUBLISH_EXAM_RT);
                submitPublishOrUnpublishExamRequest(obj[i].examID, PUBLISH_EXAM_RT);
            }

        };

        btnRemove.onclick = function () {
            submitRemoveExamRequest(obj[i].examID);
        }
    }
}

function checkExamPublished(rowNumber, obj) {
    return obj[rowNumber].published === "1";
}

function initializeViewCreatedExams() {
    setNavbarActive(TITLE_VIEW_CREATED_EXAMS);
    getAllCreatedExams();
    publishedExamID = '';
}

initializeViewCreatedExams();
//FIXME: this page not initializing when back is hit and 'View Created Exams' is clicked again
