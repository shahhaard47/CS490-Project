const RT_GETQBANK = 'getqbank';
const DIF_EASY = 'e', DIF_MED = 'm', DIF_HARD = 'h';
const CREATEEXAM_RT = 'create_exam';
const ADDQUESTION_RT = 'add_question';
const EASY_QUESTION_CLASS = 'question-easy', MEDIUM_QUESTION_CLASS = 'question-medium',
    HARD_QUESTION_CLASS = 'question-hard',
    ERR_MODAL_CONTENT = 'errorModalContent';

// const URL = '/~sk2283/assets/front/php/contact_middle.php';
// const URL = 'https://web.njit.edu/~ds547/CS490-Project/assets/back/back_questionBank.php';
// const URL = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/request_question.php';
let paramsNum = 1;
let testCasesNum = 2;
let questionIDsInExam = [], easyQuestionNodes = [], medQuestionNodes = [], hardQuestionNodes = [];

/* Used to keep track of node elements in question bank view (left side of screen). Used for sorting */
let nodesInQuestionBank = [];


function loadQuestionBank(xhrResponseText) {
    let json = parseJSON(xhrResponseText);
    let questionArray = json.raw;
    for (let i = 0; i < questionArray.length; i++) {
        if (!questionIdInExam(questionArray[i].questionID)) {
            let qid = questionArray[i].questionID;
            let row = appendNodeToNode('div', qid, 'row', getelm('question-bank'));

            let questionContent = appendNodeToNode('div', '', 'question-content', row);
            let textarea = appendNodeToNode('textarea', '', '', questionContent);
            textarea.setAttribute('style', 'width:100%');
            textarea.setAttribute('rows', '6');
            textarea.setAttribute('wrap', 'soft');
            textarea.innerHTML = questionArray[i].constructed;

            let options = appendNodeToNode('div', '', 'options centered', row);

            let lblDifficulty = appendNodeToNode('label', '', '', options);
            lblDifficulty.innerHTML = 'Difficulty';
            let inputDifficulty = appendNodeToNode('input', '', '', lblDifficulty);
            inputDifficulty.setAttribute('size', '4');

            let dif = '';
            switch (questionArray[i].difficulty) {
                case DIF_EASY:
                    inputDifficulty.setAttribute('class', EASY_QUESTION_CLASS);
                    dif = 'Easy';
                    easyQuestionNodes.push(row);
                    break;
                case DIF_MED:
                    inputDifficulty.setAttribute('class', MEDIUM_QUESTION_CLASS);
                    dif = 'Medium';
                    medQuestionNodes.push(row);
                    break;
                case DIF_HARD:
                    inputDifficulty.setAttribute('class', HARD_QUESTION_CLASS);
                    hardQuestionNodes.push(row);
                    dif = 'Hard';
                    break;
                default:

            }
            inputDifficulty.setAttribute('value', dif);

            let btnAdd = appendNodeToNode('button', 'btnAdd' + qid, 'btn-add', options);
            btnAdd.innerHTML = 'Add';
            btnAdd.onclick = function () {
                addQuestionToExam(getelm(this.id));
            };

            /* Add node to the array which is used to sort nodes later */
            nodesInQuestionBank.push(row);
        }

    }
}

function deleteQuestionFromExam(btnNode) {
    let node = findParent(btnNode, 'row');
    addQuestionToBank(node);
    updateExamQuestionsArray(node.getAttribute('id'), true);

    node.parentNode.removeChild(node);

}

function addQuestionToExam(btnNode) {
    let parentRow = findParent(btnNode, 'row');
    updateExamQuestionsArray(parentRow.getAttribute('id'));

    let questionText = parentRow.getElementsByTagName('textarea')[0].value;
    let inputs = parentRow.getElementsByTagName('input');
    let questionsDifficulty = inputs[0].value;

    let row = appendNodeToNode('div', parentRow.id, 'row', getelm('exam-questions'));

    let questionLeft = appendNodeToNode('div', '', 'col question-left', row);
    let del = appendNodeToNode('div', '', '', questionLeft);
    let btn = appendNodeToNode('button', 'del' + parentRow.id, 'deleteBtn', del);
    btn.onclick = function () {
        deleteQuestionFromExam(getelm(this.id));
    };
    btn.setAttribute('style', 'width:100%');
    btn.innerHTML = 'x';

    let questionRight = appendNodeToNode('div', '', 'col question-right', row);
    let textarea = appendNodeToNode('textarea', '', 'question-text', questionRight);
    textarea.value = questionText;
    textarea.setAttribute('style', 'width:100%');
    textarea.setAttribute('rows', '6');
    textarea.setAttribute('wrap', 'soft');

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
    inputPoints.setAttribute('placeholder', 'Value');

    removeNode(parentRow);
}

function addQuestionToBank(node) {
    let questionText = node.getElementsByTagName('textarea')[0].value;
    let inputs = node.getElementsByTagName('input');
    let questionsDifficulty = inputs[0].value;
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

    switch (questionsDifficulty) {
        case 'Easy':
            inputDifficulty.setAttribute('class', EASY_QUESTION_CLASS);
            break;
        case 'Medium':
            inputDifficulty.setAttribute('class', MEDIUM_QUESTION_CLASS);
            break;
        case 'Hard':
            inputDifficulty.setAttribute('class', HARD_QUESTION_CLASS);
            break;
        default:

    }

    let btnAdd = appendNodeToNode('button', 'btnAdd' + qid, 'btn-add', options);
    btnAdd.innerHTML = 'Add';
    btnAdd.onclick = function () {
        addQuestionToExam(getelm(this.id));
    }
}

function getQuestionBank() {
    nodesInQuestionBank = [];
    easyQuestionNodes = [];
    medQuestionNodes = [];
    hardQuestionNodes = [];
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
            if (this.status === 200) {
                loadQuestionBank(xhr.responseText);
            } else {
            }
        }
    };

    /* Open a POST request */
    xhr.open("POST", URL, true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(JSON.stringify(x));


}

function findParent(elm, cls) {
    while ((elm = elm.parentElement) && !elm.classList.contains(cls)) ;
    return elm;
}

function updateExamQuestionsArray(questionId, removeId) {
    if (removeId) {
        let idx = questionIDsInExam.indexOf(questionId);
        if (idx > -1) {
            questionIDsInExam.splice(idx, 1);
            return true;
        }
    }
    questionIDsInExam.push(questionId);
}

function removeNode(node) {
    // let parent = findParent(node, 'question-content');
    node.parentNode.removeChild(node);
}

function showModalBH(id) {
    getelm(id).style.display = "block";
}

function closeModalBH(id) {
    getelm(id).style.display = "none";
}

function addParamBH() {
    let modalParams = getelm('modal-params');
    appendNodeToNode('i', '', '', modalParams).value = ', ';
    let sel = appendNodeToNode('select', 'modal-param-sel' + paramsNum, '', modalParams);
    sel.onchange = function () {
        populateTestCaseType(this);
    };
    let optStr = appendNodeToNode('option', '', '', sel);
    optStr.innerHTML = 'str';
    let optInt = appendNodeToNode('option', '', '', sel);
    optInt.innerHTML = 'int';
    let optFloat = appendNodeToNode('option', '', '', sel);
    optFloat.innerHTML = 'float';
    let input = appendNodeToNode('input', 'param' + paramsNum++, 'modal-param', modalParams);
    input.onkeyup = function () {
        populateTestCaseName(this);
    };
    addParamToTestCase();
    populateTestCaseType(sel);
}

function removeLastParam() {
    if (paramsNum > 1) {
        getelm('modal-param-sel' + (paramsNum - 1)).remove();
        getelm('param' + (paramsNum - 1)).remove();
        paramsNum--;
        removeLastParamFromTestCase();
    } else {
    }
}

function addParamToTestCase() {
    for (let i = 0; i < testCasesNum; i++) {
        let label = appendNodeToNode('label', '', '', getelm('modal-testcase-container' + i));
        appendNodeToNode('i', '', '', label).innerHTML = ' , ';
        appendNodeToNode('i', '', '', label);
        appendNodeToNode('i', '', '', label);
        appendNodeToNode('input', '', '', label);
    }
}

function removeLastParamFromTestCase() {
//modal-testcase-container
    for (let i = 0; i < testCasesNum; i++) {
        let labels = getelm('modal-testcase-container' + i).getElementsByTagName('label');
        labels[paramsNum].remove();

    }
}

function populateTestCaseType(inputNode) {
    let paramNum = parseInt(inputNode.id.split('sel')[1]),
        val = getelm('modal-param-sel' + paramNum).value;
    for (let i = 0; i < testCasesNum; i++) {
        let iNodes = getelm('modal-testcase-container' + i).getElementsByTagName('label')[paramNum].getElementsByTagName('i'),
            x = 0;
        /* Account for first test case parameter which only has 2 i elements */
        if (iNodes.length > 2)
            x = 1;

        let paramType = iNodes[x];
        paramType.innerHTML = val + ' ';
    }
}

function populateTestCaseName(inputNode) {
    let paramNum = parseInt(inputNode.id.split('m')[1]),
        val = getelm('param' + paramNum).value;
    for (let i = 0; i < testCasesNum; i++) {
        let iNodes = getelm('modal-testcase-container' + i).getElementsByTagName('label')[paramNum].getElementsByTagName('i'),
            x = 1;
        /* Account for first test case parameter which only has 2 i elements */
        if (iNodes.length > 2)
            x = 2;

        let paramName = iNodes[x];
        paramName.innerHTML = val + ' ';
    }
}


function addTestCaseBH() {
    let allTestCasesNode = getelm('all-testcases'),
        paramsNodes = getelm('modal-params'),
        selectNodes = paramsNodes.getElementsByTagName('select'),
        inputNodes = paramsNodes.getElementsByTagName('input');

    let modalTCContainer = appendNodeToNode('label', 'modal-testcase-container' + testCasesNum, '', allTestCasesNode);

    for (let i = 0; i < selectNodes.length; i++) {
        let label = appendNodeToNode('label', '', '', modalTCContainer);

        /* Add a comma node if there is more than one parameter */
        if (i > 0 && selectNodes.length > 1)
            appendNodeToNode('i', '', '', label).innerHTML = ' , ';

        appendNodeToNode('i', '', '', label).innerHTML = ' ' + selectNodes[i].value + ' ';
        appendNodeToNode('i', '', '', label).innerHTML = ' ' + inputNodes[i].value + ' ';
        appendNodeToNode('input', '', '', label);


    }

    let sel = appendNodeToNode('select', 'output-type-sel' + testCasesNum, '', allTestCasesNode);
    let optStr = appendNodeToNode('option', '', '', sel);
    optStr.innerHTML = 'str';
    let optInt = appendNodeToNode('option', '', '', sel);
    optInt.innerHTML = 'int';
    let optFloat = appendNodeToNode('option', '', '', sel);
    optFloat.innerHTML = 'float';

    // allTestCasesNode.innerHTML += '  Output ';
    allTestCasesNode.appendChild(document.createTextNode(" Output "));

    let outputInputNode = appendNodeToNode('input', '', '', allTestCasesNode);
    outputInputNode.id = 'modal-testcase-output' + testCasesNum++;

    appendNodeToNode('br', '', '', allTestCasesNode);

}

function removeLastTestCaseBH() {
    if (testCasesNum > 2) {
        let allTestCasesNode = getelm('all-testcases'),
            labelToDelete = getelm('modal-testcase-container' + (testCasesNum - 1)),
            inputNodes = allTestCasesNode.getElementsByTagName('input');

        labelToDelete.remove();
        inputNodes[inputNodes.length - 1].remove();

        let children = allTestCasesNode.childNodes;
        children[children.length - 1].remove(); // remove input node
        children[children.length - 1].remove(); // remove ' Output ' text node
        children[children.length - 1].remove(); // remove select node

        testCasesNum--;
    }

}

function getDifficultyAsChar(str) {
    return str.toLowerCase()[0];

}

function addNewQuestionToBankBH() {
    //TODO: Check for empty fields
    let obj = {
        'functionName': getelm('modal-fname').value,
        'params': getParams(),
        'does': getelm('modal-does').value,
        'returns': getelm('modal-returns').value,
        'difficulty': getDifficultyAsChar(getelm('modal-difficulty').value),
        'testCases': getTestCases(),
        'examTitle': getelm('exam-title').value,
        'requestType': ADDQUESTION_RT
    };
    sendAJAXReq(JSON.stringify(obj));
    log(obj);
    getelm('question-bank').innerHTML = '';
    getQuestionBank();
    // location.reload();

    closeModalBH('addQuestionModal');
    // alert('Question was added.')
}

function getParams() {
    let modalParams = getelm('modal-params');
    let sel = modalParams.getElementsByTagName('select');
    let inputs = modalParams.getElementsByTagName('input');

    let list = [];
    for (let i = 0; i < sel.length; i++) {
        list.push(sel[i].value + ' ' + inputs[i].value);
    }

    return list;
}

function getTestCases() {
    let strTestcases = '';

    /* For all the test cases */
    for (let i = 0; i < testCasesNum; i++) {
        let modalTCContainer = getelm('modal-testcase-container' + i),
            testcaseLabelNodes = modalTCContainer.getElementsByTagName('label');

        /* For all the parameters */
        for (let j = 0; j < testcaseLabelNodes.length; j++) {
            let labelChildren = testcaseLabelNodes[j].childNodes;
            /* Check if there's a comma node */
            if (testcaseLabelNodes[j].childNodes[0].innerHTML === ' , ') {
                strTestcases += labelChildren[1].innerHTML; // parameter Type
                strTestcases += labelChildren[3].value; // input node value

            } else {
                strTestcases += labelChildren[0].innerHTML; // parameter Type
                strTestcases += labelChildren[2].value; // input node value
            }

            /* Add comma if not the last parameter */
            if (j + 1 !== testcaseLabelNodes.length)
                strTestcases += ',';
        }
        strTestcases += ';';
        strTestcases += getelm('output-type-sel' + i).value + ' ';
        strTestcases += getelm('modal-testcase-output' + i).value;

        /* Add colon if not the last test case */
        if (i + 1 !== testCasesNum)
            strTestcases += ':';
    }
    return strTestcases;

}

function createExamBH() {
    if (questionIDsInExam.length === 0) {
        getelm(ERR_MODAL_CONTENT).innerHTML = "You haven't added any questions in the exam.";
        showModalBH('modalError');
        return;
    }
    let obj = {
        'questions': [],
        'requestType': CREATEEXAM_RT
    };

    for (let i in questionIDsInExam) {
        obj.questions.push(parseInt(questionIDsInExam[i]));
    }

    sendAJAXReq(JSON.stringify(obj));
    alert('Exam was created!');
    window.location = 'instructor-home.html';
}

function checkQuestionNodesSame(a, b) {
    return a.id === b.id;
}

function checkQuestionNodeInExam(node) {
    for (let i in questionIDsInExam) {
        if (questionIDsInExam[i] === node.id) {
            return true;
        }
    }
    return false;
}

function questionIdInExam(id) {
    for (let i in questionIDsInExam) {
        if (questionIDsInExam[i] === id) {
            return true;
        }
    }
    return false;
}

function showAllQuestionDifficulties() {
    getelm('question-bank').innerHTML = '';
    for (let i in easyQuestionNodes) {
        if (!checkQuestionNodeInExam(easyQuestionNodes[i])) {
            addQuestionToBank(easyQuestionNodes[i]);
        }
    }

    for (let i in medQuestionNodes) {
        if (!checkQuestionNodeInExam(medQuestionNodes[i])) {
            addQuestionToBank(medQuestionNodes[i]);
        }
    }

    for (let i in hardQuestionNodes) {
        if (!checkQuestionNodeInExam(hardQuestionNodes[i])) {
            addQuestionToBank(hardQuestionNodes[i]);
        }
    }

}

function showEasyQuestionDifficulties() {
    getelm('question-bank').innerHTML = '';
    for (let i in easyQuestionNodes) {
        if (!checkQuestionNodeInExam(easyQuestionNodes[i])) {
            addQuestionToBank(easyQuestionNodes[i]);
        }
    }

    for (let i in medQuestionNodes) {
        if (!checkQuestionNodeInExam(medQuestionNodes[i])) {
            addQuestionToBank(medQuestionNodes[i]);
        }
    }

    for (let i in hardQuestionNodes) {
        if (!checkQuestionNodeInExam(hardQuestionNodes[i])) {
            addQuestionToBank(hardQuestionNodes[i]);
        }
    }

}

function showMedQuestionDifficulties() {
    getelm('question-bank').innerHTML = '';

    for (let i in medQuestionNodes) {
        if (!checkQuestionNodeInExam(medQuestionNodes[i])) {
            addQuestionToBank(medQuestionNodes[i]);
        }
    }

    for (let i in hardQuestionNodes) {
        if (!checkQuestionNodeInExam(hardQuestionNodes[i])) {
            addQuestionToBank(hardQuestionNodes[i]);
        }
    }

    for (let i in easyQuestionNodes) {
        if (!checkQuestionNodeInExam(easyQuestionNodes[i])) {
            addQuestionToBank(easyQuestionNodes[i]);
        }
    }
}

function showHardQuestionDifficulties() {
    getelm('question-bank').innerHTML = '';

    for (let i in hardQuestionNodes) {
        if (!checkQuestionNodeInExam(hardQuestionNodes[i])) {
            addQuestionToBank(hardQuestionNodes[i]);
        }
    }

    for (let i in medQuestionNodes) {
        if (!checkQuestionNodeInExam(medQuestionNodes[i])) {
            addQuestionToBank(medQuestionNodes[i]);
        }
    }

    for (let i in easyQuestionNodes) {
        if (!checkQuestionNodeInExam(easyQuestionNodes[i])) {
            addQuestionToBank(easyQuestionNodes[i]);
        }
    }
}

function sortQuestionBank(selectElm) {
    let sortOption = selectElm.value;
    switch (sortOption) {
        // case 'All':
        //     showAllQuestionDifficulties();
        //     break;
        case 'Easy':
            showEasyQuestionDifficulties();
            break;
        case 'Medium':
            showMedQuestionDifficulties();
            break;
        case 'Hard':
            showHardQuestionDifficulties();
            break;
        case 'Lowest':
            // showHardQuestionDifficulties();
            break;
        case 'Highest':
            // showHardQuestionDifficulties();
            break;
        default:

    }


}

window.onload = function () {
    getQuestionBank();
    showEasyQuestionDifficulties();
    populateTestCaseType(getelm('modal-param-sel0'));
    populateTestCaseName(getelm('param0'));
    window.onclick = function (event) {
        let errModal = getelm("modalError");
        if (event.target == errModal) {
            errModal.style.display = "none";
        }
    }
};