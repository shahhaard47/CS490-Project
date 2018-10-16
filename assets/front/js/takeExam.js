function addQuestionToView(node) {
    let questionText = node.getElementsByTagName('textarea')[0].value;
    let inputs = node.getElementsByTagName('input');
    let questionsDifficulty = inputs[0].value;
    let questionPts = inputs[1].value;
    let qid = node.id;
    let row = appendNodeToNode('div', qid, 'row', getelm('question-bank'));

    let questionContent = appendNodeToNode('div', '', 'question-content', row);
    let textarea = appendNodeToNode('textarea', '', '', questionContent);
    textarea.setAttribute('style', 'width:100%');
    textarea.setAttribute('rows', '6');
    textarea.setAttribute('wrap', 'soft');
    textarea.innerHTML = questionText;

    let options = appendNodeToNode('div', '', 'options centered', row);

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

    let btnAdd = appendNodeToNode('button', 'btnAdd' + qid, 'btn-add', options);
    btnAdd.innerHTML = 'Add';
    btnAdd.onclick = function () {
        addQuestionToExam(getelm(this.id));
    }
}


function selectQuestion(elm) {

}

/*function createQuestionNode(questionData) {
    let {examID, functionDescription, functionName, output, parameters, points, questionID} = questionData;

    let row = appendNodeToNode('div', questionID, 'row', getelm('questions-in-exam'));

    let questionContent = appendNodeToNode('div', '', 'question-content', row);
    let textarea = appendNodeToNode('textarea', '', '', questionContent);
    textarea.setAttribute('style', 'width:100%');
    textarea.setAttribute('rows', '6');
    textarea.setAttribute('wrap', 'soft');
    textarea.setAttribute('readonly', '');
    textarea.innerHTML = '';

    let options = appendNodeToNode('div', '', 'options centered', row);

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

    let btnAdd = appendNodeToNode('button', 'btnAdd' + questionID, 'btn-add', options);
    btnAdd.innerHTML = 'Select';
    btnAdd.onclick = function () {
        selectQuestion(getelm(this.id));
    }
}*/


function loadQuestions(jsonStr) {
    let jsonObj = parseJSON(jsonStr);
    log(jsonObj);
    let data = jsonObj.data;
    for (let i = 0; i < data.length; i++) {
        
    }
}

window.onload = function () {
    let params = getURLParams(window.location.href);
    changeInnerHTML('header-examID', `Exam ID: ${params.id}`);
    loadQuestions(params.data);
};
