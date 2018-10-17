const RT_GETQBANK = 'getqbank';
const DIF_EASY = 'e', DIF_MED = 'm', DIF_HARD = 'h';
const CREATEEXAM_RT = 'create_exam';
const ADDQUESTION_RT = 'add_question';

// const URL = '/~sk2283/assets/front/php/contact_middle.php';
// const URL = 'https://web.njit.edu/~ds547/CS490-Project/assets/back/back_questionBank.php';
// const URL = 'https://web.njit.edu/~hks32/CS490-Project/assets/middle/request_question.php';
let paramsNum = 1;
let testCasesNum = 1;
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
                    dif = 'Easy';
                    easyQuestionNodes.push(row);
                    break;
                case DIF_MED:
                    dif = 'Medium';
                    medQuestionNodes.push(row);
                    break;
                case DIF_HARD:
                    hardQuestionNodes.push(row);
                    dif = 'Hard';
                    break;
                default:

            }
            inputDifficulty.setAttribute('value', dif);

            let lblPoints = appendNodeToNode('label', '', '', options);
            lblPoints.innerHTML = 'Points';
            let inputPoints = appendNodeToNode('input', '', '', lblPoints);
            inputPoints.setAttribute('size', '1');
            inputPoints.setAttribute('value', questionArray[i].points);

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
    let questionPts = inputs[1].value;
    // console.log(qDifficulty);

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
    inputPoints.setAttribute('value', questionPts);

    removeNode(parentRow);
}

function addQuestionToBank(node) {
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

function showModalBH() {
    let modal = getelm('addQuestionModal');
    modal.style.display = "block";
}

function closeModalBH() {
    getelm('addQuestionModal').style.display = "none";
}

function addParamBH() {
    let modalParams = getelm('modal-params');
    let sel = appendNodeToNode('select', 'modal-param-sel' + testCasesNum, '', modalParams);
    let optStr = appendNodeToNode('option', '', '', sel);
    optStr.innerHTML = 'String';
    let optInt = appendNodeToNode('option', '', '', sel);
    optInt.innerHTML = 'Integer';
    let optFloat = appendNodeToNode('option', '', '', sel);
    optFloat.innerHTML = 'Float';

    appendNodeToNode('input', 'param' + paramsNum++, '', modalParams);
    appendNodeToNode('br', '', '', modalParams);
    appendNodeToNode('br', '', '', modalParams);

}

function addTestCaseBH() {
    let modalTC = getelm('modal-testcases');
    let sel = appendNodeToNode('select', 'modal-testcase-sel' + testCasesNum, '', modalTC);
    let optStr = appendNodeToNode('option', '', '', sel);
    optStr.innerHTML = 'str';
    let optInt = appendNodeToNode('option', '', '', sel);
    optInt.innerHTML = 'int';
    let optFloat = appendNodeToNode('option', '', '', sel);
    optFloat.innerHTML = 'float';

    appendNodeToNode('input', 'testcase' + testCasesNum++, '', modalTC);
    appendNodeToNode('br', '', '', modalTC);
    appendNodeToNode('br', '', '', modalTC);
}

function getDifficultyAsChar(str) {
    return str.toLowerCase()[0];

}

function addNewQuestionToBankBH() {
    let obj = {
        'functionName': getelm('modal-fname').value,
        'params': getModalParams(),
        'does': getelm('modal-does').value,
        'prints': getelm('modal-prints').value,
        'difficulty': getDifficultyAsChar(getelm('modal-difficulty').value),
        'points': parseInt(getelm('modal-points').value),
        'testCases': getModalTestCasesBeta(),
        'solution': getelm('modal-solution').value,
        'requestType': ADDQUESTION_RT
    };
    sendAJAXReq(JSON.stringify(obj));

    getelm('question-bank').innerHTML = '';
    getQuestionBank();
    closeModalBH();
    alert('Question was added.')
}

function getModalParams() {
    let modalParams = getelm('modal-params');
    let sel = modalParams.getElementsByTagName('select');
    let inputs = modalParams.getElementsByTagName('input');

    let list = [];
    for (let i = 0; i < sel.length; i++) {
        list.push(sel[i].value + ' ' + inputs[i].value);
    }

    return list;
}

// function getModalTestCases() {
//     let modalParams = getelm('modal-testcases'), strTestCases = '',
//         sel = modalParams.getElementsByTagName('select'),
//         inputs = modalParams.getElementsByTagName('input');
//
//     for (let i = 0; i < sel.length; i++) {
//         // log(sel[i].value + ' ' + inputs[i].value);
//         strTestCases += `${sel[i].value.toLowerCase()} ${inputs[i].value}`;
//         if (i + 1 !== sel.length) {
//             strTestCases += ',';
//         }
//     }
//     return strTestCases;
// }

function getModalTestCasesBeta() {
    let modalParams = getelm('modal-testcases'),
        input = modalParams.getElementsByTagName('input')[0];

    return input.value;
}

function createExamBH() {
    let obj = {
        'questions': [],
        'requestType': CREATEEXAM_RT
    };

    for (let i in questionIDsInExam) {
        obj.questions.push(parseInt(questionIDsInExam[i]));
    }

    // log(JSON.stringify(obj));

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
};

