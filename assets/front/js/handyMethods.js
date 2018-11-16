const URL = 'https://web.njit.edu/~sk2283/assets/front/php/contact_middle.php';
const GET_AVAILABLE_EXAM_RT = 'getAvailableExam';

function loader(id, state) {
    getelm(id).style.visibility = state;
}

function changeInnerHTML(id, str) {
    getelm(id).innerHTML = str;
}

function setAttribute(id, attr, value) {
    getelm(id).setAttribute(attr, value);
}

function removeAttr(id, attr) {
    getelm(id).removeAttribute(attr);
}

function getelm(id) {
    return document.getElementById(id);
}

function chngClass(id, oldClass, newClass) {
    getelm(id).classList.remove(oldClass);
    getelm(id).classList.add(newClass);
}

function addClass(id, clss) {
    getelm(id).classList.add(clss);
}

function removeClass(id, clss) {
    let classList = getelm(id).classList;
    if (classList.length > 0) {
        classList.remove(clss);
    }
}

function hideElement(element) {
    element.style = 'display:none';
}


function showElement(element) {
    element.style = 'display:block'

}

function showDialog(elementToAddTo, bodyText) {
    let dialog = appendNodeToNode('dialog', 'dialog', 'dialog', elementToAddTo);
    // dialog.innerHTML = 'This exam is no longer available to take. Please refresh and try again.';
    dialog.innerHTML = bodyText;
    dialog.style = 'text-align:center; background-color:whitesmoke;';
    appendNodeToNode('br', '', '', dialog);

    let btnCloseDialog = appendNodeToNode('button', 'button', 'button', dialog);
    btnCloseDialog.innerHTML = 'Close';

    btnCloseDialog.onclick = function () {
        dialog.close();
        dialog.remove();
    };

    return dialog;
}

// function showInfoModal() {
// }

function parseJSON(str) {
    let json = '';
    try {
        json = JSON.parse(str);
    } catch (e) {
        console.log('ERROR:' + e);
        return '';
    }
    return json;
}

function getPage(url) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4 && this.status === 200) {
            return xhr.responseText;
        }

    };
    /* Open a POST request */
    xhr.open("POST", url, true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send();

    return 'oops';
}

function appendNodeToNode(type, id, clss, addTo) {
    let newNode = addTo.appendChild(document.createElement(type));
    if (id !== '')
        newNode.setAttribute('id', id);
    if (clss !== '')
        newNode.setAttribute('class', clss);
    return newNode;
}

/* AJAX request when don't care about response */
function sendAJAXReq(callback, contentToSend) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                // TODO: Handle more than one callback
                if (callback !== '')
                    callback(xhr.responseText);
            } else {
                if (callback !== '')
                    callback(xhr.status);
            }
        }
    };

    /* Open a POST request */
    xhr.open("POST", URL, true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(contentToSend);
}

function submitUpdateOverallGradeRequest(grade, userID, examID) {
    obj = {};
    obj.userID = userID;
    obj.examID = examID;
    obj.score = parseInt(grade);
    obj.requestType = 'update_overallScore';
    log(obj);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4) {
            if (this.status === 200) {
                log(xhr.responseText);
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

function getURLParams(url) {
    let params = {};
    let x = url.split('&');
    let firstParam = x[0].split('?')[1].split('=');
    params[firstParam[0]] = decodeURIComponent(firstParam[1]);

    for (let i = 1; i < x.length; i++) {
        let y = x[i].split('=');
        params[y[0]] = decodeURIComponent(y[1]);
    }
    return params;
}

function inArray(arr, b) {
    for (let i = 0; i < arr.length; i++) {
        if (arr[i] === b) {
            return true;
        }
    }
    return false;
}

function constructQuestion(questionsObj) {
    let params = '', paramsList = questionsObj.params, i;

    /* Construct parameter as a string */
    for (i = 0; i < paramsList.length; i++) {
        params += `<${paramsList[i]}>`;

        if (i + 1 < paramsList.length)
            params += ', ';

    }

    let str = `Write a function named ${questionsObj.functionName} that takes parameters ${params}, ${questionsObj.functionDescription} and returns ${questionsObj.output}`;

    return str
}

/** A utility function to get the last number(s) from a string. Ex: 'button49' input will return 49 */
function getLastNumbersFromString(str) {
    let num = '';
    for (let i in str) {
        if (!isNaN(str[i])) {
            num += str[i];
        }
    }
    return parseInt(num);
}

function log(str) {
    console.log(str);
}
