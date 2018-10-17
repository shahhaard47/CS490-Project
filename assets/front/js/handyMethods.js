const URL = 'https://web.njit.edu/~sk2283/assets/front/php/contact_middle.php',
    GET_AVAILABLE_EXAMS_RT = 'getAvailableExams';

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


function sendAJAXReq(content) {
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
    xhr.send(content);
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

function log(str) {
    console.log(str);
}

/*
{
  "qBank": "true",
  "userid": "null",
  "examid": "null",
  "questionID": "null",
  "difficulty": "'a'"
}
*/