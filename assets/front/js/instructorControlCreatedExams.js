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

function getAllCreatedExams() {
    let obj = {};
    obj.requestType = GET_ALL_CREATED_EXAMS;
    sendAJAXReq(populateTable, (JSON.stringify(obj)));
}

function reloadViewControlExams() {
    getAllCreatedExams();

}

function submitPublishOrUnpublishExamRequest(examID, requestType) {
    let obj = {};
    obj.examID = examID;
    obj.requestType = requestType;
    sendAJAXReq(reloadViewControlExams, JSON.stringify(obj));
}

function submitRemoveExamRequest(examID) {
    let obj = {};
    obj.examID = examID;
    obj.requestType = 'delete_exam';

    sendAJAXReq(reloadViewControlExams, JSON.stringify(obj));
}

/* Show all exams in a table */

//FIXME: Weird error here. This function being executed through gradeAnExam.js somehow.
function populateTable(obj) {
    obj = parseJSON(obj);
    // TODO: Add a preview exam button
    log('--popTable()--');
    log(obj);

    let container = getelm('exams-container');
    if (!container)
        return;
    container.innerHTML = '';

    for (let i in obj) {
        let divParent = appendNodeToNode('div', `row${i}`, 'col examDivParent', container);

        let divHeader = appendNodeToNode('div', '', 'examHeader', divParent);
        divHeader.title = 'Preview';

        let headerTitle = appendNodeToNode('h2', '', 'examTitle', divHeader);
        headerTitle.innerHTML = obj[i].examName;

        let divBody = appendNodeToNode('div', '', 'divBody', divParent);

        appendNodeToNode('p', '', '', divBody).innerHTML = `Questions: ${obj[i].questions.length}`;
        let btnPubOrUnpub = appendNodeToNode('button', `btn${i}`, 'btnPublish', divBody);
        btnPubOrUnpub.innerHTML = 'Publish or Unpublish';

        if (checkExamPublished(i, obj)) {
            addClass(btnPubOrUnpub.id, 'published');
        }

        appendNodeToNode('br', '', '', divBody);

        let btnRemove = appendNodeToNode('button', `btnRemove${i}`, 'btnRemove', divBody);
        btnRemove.innerHTML = 'Remove';


        divHeader.onclick = function () {
            loadPreviewExamModal(obj[i]);
        };

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

function loadPreviewExamModal(examObj) {
    showModalBH('previewExamModal');

    /* Change the modal header. */
    let title = getelm('modalHeaderTitle');
    title.innerHTML = examObj.examName;

    /* Get the content div. */
    let modalQuestions = getelm('questions'),
        questionsArray = examObj.questions;
    /* Clear the content div for the new exam preview. */
    modalQuestions.innerHTML = '';

    for (let i = 0; i < questionsArray.length; i++) {
        let row = appendNodeToNode('div', '', 'row', modalQuestions);

        let questionContent = appendNodeToNode('div', '', 'question-content', row);
        let textarea = appendNodeToNode('textarea', '', '', questionContent);
        textarea.setAttribute('style', 'width:100%');
        textarea.setAttribute('rows', '6');
        textarea.setAttribute('wrap', 'soft');
        textarea.disabled = true;
        textarea.innerHTML = constructQuestion(questionsArray[i]);

        let options = appendNodeToNode('div', '', 'options centered', row);

        let lblDifficulty = appendNodeToNode('label', '', '', options);
        lblDifficulty.innerHTML = 'Difficulty ';
        let inputDifficulty = appendNodeToNode('input', '', '', lblDifficulty);
        inputDifficulty.setAttribute('size', '6');
        inputDifficulty.disabled = true;

        let dif = '';
        switch (questionsArray[i].difficulty) {
            case DIF_EASY:
                inputDifficulty.setAttribute('class', EASY_QUESTION_CLASS);
                dif = 'Easy';
                break;
            case DIF_MED:
                inputDifficulty.setAttribute('class', MEDIUM_QUESTION_CLASS);
                dif = 'Medium';
                break;
            case DIF_HARD:
                inputDifficulty.setAttribute('class', HARD_QUESTION_CLASS);
                dif = 'Hard';
                break;
            default:

        }
        inputDifficulty.setAttribute('value', dif);

        let label = appendNodeToNode('label', '', '', options);
        label.innerHTML = 'Constraints ';

        let constraintsInput = appendNodeToNode('input', '', '', label);
        constraintsInput.setAttribute('size', '8');
        constraintsInput.disabled = true;
        constraintsInput.value = questionsArray[i].constraints;
        if (!questionsArray[i].constraints) {
            constraintsInput.value = 'None';
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

    window.onclick = function (event) {
        let modal = getelm("previewExamModal");
        if (event.target == modal) {
            closeModalBH('previewExamModal');
        }
    };

}

initializeViewCreatedExams();
