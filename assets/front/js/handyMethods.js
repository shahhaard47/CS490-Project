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


/*
{
  "qBank": "true",
  "userid": "null",
  "examid": "null",
  "questionID": "null",
  "difficulty": "'a'"
}
*/