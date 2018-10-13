const RT_GETQBANK = 'getqbank';
// const URL = '/~sk2283/assets/front/php/contact_middle.php';
const URL = '/~sk2283/assets/front/php/getQues.php';

function appendNodeToNode(type, id, clss, addTo) {
    let newNode = addTo.appendChild(document.createElement(type));
    if (id !== '')
        newNode.setAttribute('id', id);
    if (clss !== '')
        newNode.setAttribute('class', clss);
    return newNode;
}

function loadQuestionBank() {
    let questions = JSON.parse(getQuestionBank());
    for (let i = 0; i < questions.length; i++) {
        let row = appendNodeToNode('div', '', 'row', getelm('question-id'));

        let questionContent = appendNodeToNode('div', '', 'question-content', row);
        let textarea = appendNodeToNode('textarea', '', '', questionContent);
        textarea.setAttribute('style', 'width:100%');
        textarea.setAttribute('rows', '6');
        textarea.setAttribute('wrap', 'soft');

        let options = appendNodeToNode('div', '', 'options', 'row');
        let lblDifficulty = appendNodeToNode('label', options);
        let inputDifficulty = appendNodeToNode('div', '', 'row', lblDifficulty);
        inputDifficulty.setAttribute('size', '4');
        // inputDifficulty.setAttribute('value', '4');
        let lblPoints = appendNodeToNode('label', options);
        let inputPoints = appendNodeToNode('div', '', 'row', lblPoints);
        inputPoints.setAttribute('size', '1');
        // inputPoints.setAttribute('value', '4');

    }
}

function addQuestionToExam(questionNode) {
    let questionText = questionNode.getElementsByTagName('textarea')[0].value;
    let inputs = questionNode.getElementsByTagName('input');
    let questionsDifficulty = inputs[0].value;
    let questionPts = inputs[1].value;
    // console.log(qDifficulty);

    let row = appendNodeToNode('div', '', 'row', getelm('exam-questions'));

    let questionLeft = appendNodeToNode('div', '', 'col question-left', row);
    let del = appendNodeToNode('div', '', '', questionLeft);
    let btn = appendNodeToNode('button', 'deleteBtn', 'deleteBtn', del);
    btn.setAttribute('style', 'width:100%');
    btn.innerHTML = 'x';

    let questionRight = appendNodeToNode('div', '', 'col question-right', row);
    let textarea = appendNodeToNode('textarea', '', 'question-text', questionRight);
    textarea.value = questionText;
    textarea.setAttribute('style', 'width:100%');
    textarea.setAttribute('rows', '6');
    textarea.setAttribute('wrap', 'soft');

    let options = appendNodeToNode('div', '', 'options', row);
    let lblDifficulty = appendNodeToNode('label', '', '', options);
    lblDifficulty.innerHTML = 'Difficulty';
    let inputDifficulty = appendNodeToNode('input', '', '', lblDifficulty);
    inputDifficulty.setAttribute('size', '4');
    inputDifficulty.setAttribute('value', questionsDifficulty);
    let lblPoints = appendNodeToNode('label', '', '', options);
    lblPoints.innerHTML = 'Points';
    let inputPoints = appendNodeToNode('input', '', '', lblPoints);
    inputPoints.setAttribute('size', '1');
    inputPoints.setAttribute('value', questionPts);

}

function getQuestionBank() {
    /* Construct obj for JSON */
    let x = {
        'qBank': true,
        'userid': null,
        'examid': null,
        'questionID': null,
        'difficulty': 'a',
        'requestType': RT_GETQBANK
    };
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (xhr.status === 200) {
                return xhr.responseText;
            } else{
            }
        }
    };

    /* Open a POST request */
    xhr.open("POST", URL, true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(JSON.stringify(x));

    return 'oops';
}

window.onload = function () {
    addQuestionToExam(getelm('q1'));
    console.log('====gBank=====');
    console.log(getQuestionBank());
};
/*
{
  "question": "foo",
  "qid": "null",
  "": "null",
  "questionID": "null",
  "difficulty": "'a'"
}
*/
