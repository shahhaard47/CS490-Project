const RT_GETQBANK = 'getqbank';
const CREATEEXAM_RT = 'create_exam';
const ADDQUESTION_RT = 'add_question';

/* Used to check if this JS file is included. */
const EXAM_CREATOR_INCLUDED = true;

function loadTopicsSelectElement(obj) {
    let topicsSelectElement = getelm('topicSelect');

    for (i in obj) {
        if (obj.hasOwnProperty(i)) {
            let optionElement = appendNodeToNode('option', '', '', topicsSelectElement);
            optionElement.innerHTML = obj[i];
        }
    }
}

/** Loads questions to the question bank view on the left side of screen */
function loadQuestionBank(xhrResponseText) {
    let json = parseJSON(xhrResponseText);
    let questionArray = json.raw;
    for (let i = 0; i < questionArray.length; i++) {
        if (!questionIdInExam(questionArray[i].questionID)) {
            let qid = questionArray[i].questionID;
            questionIDsInBank.push(qid);

            let row = appendNodeToNode('div', qid, 'row', getelm('question-bank'));

            let questionContent = appendNodeToNode('div', '', 'question-content', row);
            let textarea = appendNodeToNode('textarea', '', '', questionContent);
            textarea.setAttribute('style', 'width:100%');
            textarea.setAttribute('rows', '6');
            textarea.setAttribute('wrap', 'soft');
            textarea.disabled = true;
            textarea.innerHTML = questionArray[i].constructed;

            let options = appendNodeToNode('div', '', 'options centered', row);

            let lblDifficulty = appendNodeToNode('label', '', '', options);
            lblDifficulty.innerHTML = 'Difficulty';
            let inputDifficulty = appendNodeToNode('input', '', '', lblDifficulty);
            inputDifficulty.setAttribute('size', '4');
            inputDifficulty.disabled = true;

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

            let label = appendNodeToNode('label', '', '', options);
            label.innerHTML = 'Constraints';
            let constraintsInput = appendNodeToNode('input', '', '', label);
            constraintsInput.setAttribute('size', '8');
            constraintsInput.disabled = true;
            constraintsInput.value = questionArray[i].constraints;

            let btnRemove = appendNodeToNode('button', 'btnRemove' + qid, 'btn-remove', options);
            btnRemove.innerHTML = 'Remove';
            btnRemove.onclick = function () {
                deleteQuestionFromQBankDatabase(getelm(this.id).id.split('btnRemove')[1]);
            };

            let btnAdd = appendNodeToNode('button', 'btnAdd' + qid, 'btn-add', options);
            btnAdd.innerHTML = 'Add';
            btnAdd.onclick = function () {
                addQuestionToExam(getelm(this.id));
            };

            /* Add node to the array which is used to sort nodes later */
            nodesInQuestionBank.push(row);

            /* Add node to topics object */
            let topic = questionArray[i].topic;
            if (topicQuestions.hasOwnProperty(topic))
                topicQuestions[topic].push(qid);
            else {
                topicQuestions[topic] = [qid];
            }
        }
    }

}

/** Reloads question bank. */
function reloadQuestionBank() {
    getelm('question-bank').innerHTML = '';
    submitGetQuestionBankRequest();
}

/** Remove a question that was added to the exam (right side of screen) and place it back into the bank (left side) */
function deleteQuestionFromExam(btnNode) {
    let node = findParent(btnNode, 'row');
    // addQuestionToBank(node);
    updateExamQuestionsArray(getLastNumbersFromString(node.id), true);

    node.parentNode.removeChild(node);

    showElement(getelm(getLastNumbersFromString(node.id)));
}

/** Add a question from bank (left side of screen) to the exam (right side of screen) */
function addQuestionToExam(btnNode) {
    let parentRow = findParent(btnNode, 'row');
    updateExamQuestionsArray(parentRow.getAttribute('id'));

    let questionText = parentRow.getElementsByTagName('textarea')[0].value;
    let inputs = parentRow.getElementsByTagName('input');
    let questionDifficulty = inputs[0].value;

    let row = appendNodeToNode('div', `question${parentRow.id}`, 'row', getelm('exam-questions'));

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
    textarea.disabled = true;


    let options = appendNodeToNode('div', '', 'options centered', row);

    let lblDifficulty = appendNodeToNode('label', '', '', options);
    lblDifficulty.innerHTML = 'Difficulty';
    let inputDifficulty = appendNodeToNode('input', '', '', lblDifficulty);
    inputDifficulty.setAttribute('size', '4');
    inputDifficulty.setAttribute('value', questionDifficulty);
    inputDifficulty.disabled = true;

    inputDifficulty.setAttribute('class', `question-${questionDifficulty.toLowerCase()}`);

    let label = appendNodeToNode('label', '', '', options);
    label.innerHTML = 'Contraints';
    let constraintsInput = appendNodeToNode('input', '', '', label);
    constraintsInput.setAttribute('size', '4');
    constraintsInput.disabled = true;
    constraintsInput.value = parentRow.getElementsByTagName('input')[1].value;

    let lblPoints = appendNodeToNode('label', '', '', options);
    lblPoints.innerHTML = 'Points';
    let inputPoints = appendNodeToNode('input', '', '', lblPoints);
    inputPoints.setAttribute('size', '1');
    inputPoints.setAttribute('placeholder', 'Value');

    /* Add change listener to points to change total points in exam input element */
    let pointsElement = getelm('pointsInExam');
    inputPoints.onkeyup = function () {
        let pts = getPoints(), sum = 0;
        for (let i in pts) {
            if (!isNaN(pts[i]))
                sum += pts[i];
        }

        pointsElement.value = sum;
    };

    hideElement(parentRow);
}


/** Add a question to the question bank */
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
    textarea.disabled = true;
    textarea.innerHTML = questionText;

    let options = appendNodeToNode('div', '', 'options centered', row);

    let lblDifficulty = appendNodeToNode('label', '', '', options);
    lblDifficulty.innerHTML = 'Difficulty';
    let inputDifficulty = appendNodeToNode('input', '', '', lblDifficulty);
    inputDifficulty.setAttribute('size', '4');
    inputDifficulty.setAttribute('value', questionsDifficulty);
    inputDifficulty.disabled = true;

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

    let label = appendNodeToNode('label', '', '', options);
    label.innerHTML = 'Contraints';
    let constraintsInput = appendNodeToNode('input', '', '', label);
    constraintsInput.setAttribute('size', '4');
    constraintsInput.disabled = true;
    constraintsInput.value = node.getElementsByTagName('input')[1].value;

    let btnRemove = appendNodeToNode('button', 'btnRemove' + qid, 'btn-remove', options);
    btnRemove.innerHTML = 'Remove';
    btnRemove.onclick = function () {
        deleteQuestionFromQBankDatabase(getelm(this.id).id.split('btnRemove')[1]);
    };

    let btnAdd = appendNodeToNode('button', 'btnAdd' + qid, 'btn-add', options);
    btnAdd.innerHTML = 'Add';
    btnAdd.onclick = function () {
        addQuestionToExam(getelm(this.id));
    }
}

/** Get all the questions from the database */
function submitGetQuestionBankRequest() {
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
                if (debug)
                    log(parseJSON(xhr.responseText));
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

function submitGetTopicsRequest() {
    /* Construct obj for JSON */
    let x = {
        'requestType': 'returnTopics'
    };

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                loadTopicsSelectElement(parseJSON(xhr.responseText));
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

/** Helper function to find the parent of an element */
function findParent(elm, cls) {
    while ((elm = elm.parentElement) && !elm.classList.contains(cls)) ;
    return elm;
}

/** Add or remove a question ID from the array that keeps track of the questions added to the exam */
function updateExamQuestionsArray(questionId, removeId) {
    if (removeId) {
        // log(`-Removing ID ${questionId}`);
        // log(`-Question IDs in exam: ${questionIDsInExam}`);

        let idx = questionIDsInExam.indexOf(questionId.toString());
        if (idx > -1) {
            questionIDsInExam.splice(idx, 1);
            // log(`-Removed index: ${idx}`);
            // return true;
        }
        // log('Did not remove')
    } else {
        questionIDsInExam.push(questionId);
    }

    getelm('questionsInExam').value = questionIDsInExam.length;
    // log(`+QID added to array. questionIDsInExam= ${questionIDsInExam}`)
}

/** Remove a node from DOM */
function removeNode(node) {
    node.parentNode.removeChild(node);
}

/** Button handler for adding a new parameter when adding a new question to the question bank */
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
    let optBool = appendNodeToNode('option', '', '', sel);
    optBool.innerHTML = 'bool';

    let input = appendNodeToNode('input', 'param' + paramsNum++, 'modal-param', modalParams);
    input.onkeyup = function () {
        populateTestCaseName(this);
    };
    addParamToTestCase();
    populateTestCaseType(sel);
}

/** Remove the last parameter from the adding a new questions modal */
function removeLastParam() {
    if (paramsNum > 1) {
        getelm('modal-param-sel' + (paramsNum - 1)).remove();
        getelm('param' + (paramsNum - 1)).remove();
        paramsNum--;
        removeLastParamFromTestCase();
    } else {
    }
}

/** When a new parameter is added, a new parameter to each test case is added */
function addParamToTestCase() {
    for (let i = 0; i < testCasesNum; i++) {
        let label = appendNodeToNode('label', '', '', getelm('modal-testcase-container' + i));
        appendNodeToNode('i', '', '', label).innerHTML = ' , ';
        appendNodeToNode('i', '', '', label);
        appendNodeToNode('i', '', '', label);
        appendNodeToNode('input', '', '', label);
    }
}

/** Retrieves points for each question in exam and returns an array of numbers */
function getPoints() {
    let points = [];

    for (let i in questionIDsInExam) {
        // log('---:' + `question${questionIDsInExam[i]}`);
        let pointsElement = getelm(`question${questionIDsInExam[i]}`).getElementsByTagName('input')[2];
        // log(pointsElement)
        points.push(parseInt(pointsElement.value));
    }

    return points;
}

/**  When the last parameter is removed, a last parameter from each test case is removed */
function removeLastParamFromTestCase() {
//modal-testcase-container
    for (let i = 0; i < testCasesNum; i++) {
        let labels = getelm('modal-testcase-container' + i).getElementsByTagName('label');
        labels[paramsNum].remove();

    }
}

/** Changes inner html of parameter 'type' when the parameter type of parameter is changed */
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

/** Changes inner html of parameter 'name' when the parameter name of parameter is changed */
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

/** Adds a new test case to modal */
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
    let optBool = appendNodeToNode('option', '', '', sel);
    optBool.innerHTML = 'bool';

    // allTestCasesNode.innerHTML += '  Output ';
    allTestCasesNode.appendChild(document.createTextNode(" Output "));

    let outputInputNode = appendNodeToNode('input', '', '', allTestCasesNode);
    outputInputNode.id = 'modal-testcase-output' + testCasesNum++;

    appendNodeToNode('br', '', '', allTestCasesNode);

}

/** Removes the last test case from modal*/
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

/** Returns the difficulty as a lowercase letter (Esay -> e, Medium -> m, Hard -> h) */
function getDifficultyAsChar(str) {
    return str.toLowerCase()[0];
}

/** Delete a question from question bank database */
// TODO: Add functionality to edit a question too
function deleteQuestionFromQBankDatabase(qid) {
    let obj = {
        'questionID': parseInt(qid),
        'requestType': 'delete_question'
    };

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                reloadQuestionBank();
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

function clearModalFieldsBH() {
    let inputElements = getelm('addQuestionModal').getElementsByTagName('input');

    for (let i in inputElements) {
        inputElements[i].value = '';
    }
}

/** Sends an add question to the bank request to middle-end */
function submitAddNewQuestionToBankRequestBH() {
    //TODO: Check for empty fields
    let obj = {
        'functionName': getelm('modal-fname').value,
        'params': getParams(),
        'does': getelm('modal-does').value,
        'returns': getelm('modal-returns').value,
        'difficulty': getDifficultyAsChar(getelm('modal-difficulty').value),
        'topic': getelm('modal-topic').value,
        'testCases': getTestCases(),
        'examTitle': getelm('exam-title').value,
        'constraints': getelm('modal-constraints').value,
        'requestType': ADDQUESTION_RT
    };

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                /* Reload question bank once a response is received */
                reloadQuestionBank();
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


    closeModalBH('addQuestionModal');
}

/** Get the parameters that were entered in add question modal */
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

/** Builds the test cases string. Returns one string */
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

/** Send create exam rquest to middle */
function createExamBH() {
    if (questionIDsInExam.length === 0) {
        let dialog = showDialog('Whoops...', 'You haven\'t added any questions in the exam.');
        dialog.show();

        return;
    }
    let obj = {
        'examName': getelm('exam-title').value,
        'questions': [],
        'points': [],
        'requestType': CREATEEXAM_RT
    };
    for (let i in questionIDsInExam) {
        obj.questions.push(parseInt(questionIDsInExam[i]));
    }
    obj.points = getPoints();

    log(obj);

    submitCreateExamRequest(JSON.stringify(obj));
}

/* AJAX request when don't care about response */
function submitCreateExamRequest(content) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                let response = parseJSON(xhr.responseText);
                log('Response for CREATE EXAM:');
                log(response);
                if (response.examCreated) {
                    let d = showDialog('Success!', 'Exam was successfully created.');
                    d.show();

                    getDialogCloseButton(d).onclick = function () {
                        d.close();
                        d.remove();
                        goToViewCreatedExamsPage();
                    }
                } else {
                    showDialog('Whoops...', 'Something went wrong. Please try again.');
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
    xhr.send(content);
}

function checkQuestionNodesSame(a, b) {
    return a.id === b.id;
}

/** Check if a question from the question bank exists in the exam being created */
function checkQuestionNodeInExam(node) {
    for (let i in questionIDsInExam) {
        if (questionIDsInExam[i] === node.id) {
            return true;
        }
    }
    return false;
}

/** Check if a question ID from the question bank exists in the exam being created */
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

/** Sort question bank based on easy difficulty */
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

/** Sort question bank based on medium difficulty */
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

/** Sort question bank based on hard difficulty */
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

/** Utility function to sort the question bank */
function sortQuestionBank(selectElm) {
    let sortOption = selectElm.value;
    switch (sortOption) {
        case 'Easy':
            showEasyQuestionDifficulties();
            break;
        case 'Medium':
            showMedQuestionDifficulties();
            break;
        case 'Hard':
            showHardQuestionDifficulties();
            break;
        default:

    }
    /* Filter question bank after sorting with whatever the current selection is. */
    filterQuestionBankTopic(getelm('topicSelect'));
}

function filterQuestionBankTopic(selectElm) {
    let option = selectElm.value;

    if (option === 'None') {
        for (let i in questionIDsInBank) {
            if (!inArray(questionIDsInExam, questionIDsInBank[i])) {

                let elm = getelm(questionIDsInBank[i]);
                elm.style = 'display:block';
            }
        }
        return;
    }

    let keys = Object.keys(topicQuestions);
    if (debug) {
        log(`keys:`);
        log(keys);
    }

    for (let i in keys) {
        if (debug) {
            log(`keys[i] = ${keys[i]}`);
        }
        if (keys[i] !== option) {
            if (debug) {
                log(`${keys[i]} != ${option}`);
            }
            for (let j in topicQuestions[keys[i]]) {
                let elm = getelm(topicQuestions[keys[i]][j]);
                elm.style = 'display:none';
            }
        } else {
            if (debug)
                log(`${keys[i]} == ${option}`);
            for (let j in topicQuestions[keys[i]]) {
                if (!inArray(questionIDsInExam, topicQuestions[keys[i]][j])) {
                    let elm = getelm(topicQuestions[keys[i]][j]);
                    elm.style = 'display:block';
                }
            }
        }
    }
}

/* Initialize some things when the DOM tree loads */

// window.onload = function () {
function initializeExamCreator() {
    /** Counts how many parameters are added when adding a question to the bank */
    paramsNum = 1;

    /** Counts how many test cases are added when adding a question to the bank */
    testCasesNum = 2;

    /** Lists to track the questions added to exam, easy difficulty questions, medium difficulty questions, and
     hard difficulty questions */
    questionIDsInExam = [], questionIDsInBank = [], easyQuestionNodes = [], medQuestionNodes = [],
        hardQuestionNodes = [];

    topicQuestions = {};

    /** Used to keep track of node elements in question bank view (left side of screen). Used for sorting */
    nodesInQuestionBank = [];

    // setNavbarActive(TITLE_EXAM_CREATOR);
    submitGetQuestionBankRequest();
    showEasyQuestionDifficulties();
    populateTestCaseType(getelm('modal-param-sel0'));
    populateTestCaseName(getelm('param0'));
    submitGetTopicsRequest();
}

initializeExamCreator();

window.onclick = function (event) {
    let errModal = getelm("modalError");
    if (event.target == errModal) {
        errModal.style.display = "none";
    }
};
