const URL = 'https://web.njit.edu/~sk2283/assets/front/php/contact_middle.php';
const GET_AVAILABLE_EXAM_RT = 'getAvailableExam';

/* Difficulties for questions */
const DIF_EASY = 'e', DIF_MED = 'm', DIF_HARD = 'h';

/* CSS classes for difficulties. */
const EASY_QUESTION_CLASS = 'question-easy',
    MEDIUM_QUESTION_CLASS = 'question-medium',
    HARD_QUESTION_CLASS = 'question-hard';

/* Instructor HTML file names */
const TITLE_LOGIN = 'index.html',
    TITLE_EXAM_CREATOR = 'exam-creator.html',
    TITLE_INSTRUCTOR_HOME = 'instructor-home.html',
    TITLE_VIEW_CREATED_EXAMS = 'view-created-exams.html',
    TITLE_VIEW_COMPLETED_EXAMS = 'view-completed-exams.html',
    TITLE_GRADE_AN_EXAM = 'grade-an-exam.html';

/* Student HTML file names */
const TITLE_STUDENT_HOME = 'student-home.html',
    TITLE_STUDENT_VIEW_EXAMS = 'view-all-graded-exams.html',
    TITLE_STUDENT_VIEW_AN_EXAM = 'student-view-grades.html',
    TITLE_STUDENT_TAKE_EXAM = 'take-exam.html';

/* Instructor JS file names */
const JS_LOGIN = 'login.js',
    JS_EXAM_CREATOR = 'examCreator',
    JS_CREATED_EXAMS = 'instructorControlCreatedExams',
    JS_GRADE_AN_EXAM = 'instructorGradeAnExam',
    JS_VIEW_COMPLETED_EXAMS = 'instructorViewCompletedExams',
    JS_INSTRUCTOR_HOME = 'instructorHome';

/* Student JS file names */
const JS_STUDENT_HOME = 'studentHome',
    JS_STUDENT_VIEW_ALL_GRADED_EXAMS = 'studentViewAllGradedExams',
    JS_STUDENT_VIEW_AN_EXAM = 'studentViewGrades',
    JS_STUDENT_TAKE_EXAM = 'takeExam';

/* Used for debug. If true, console logs are executed. */
let debug = true;

let scriptsLoaded = [];

function includeJS(jsFileName) {
    /* Only include JS file if it's not already included. */
    if (!scriptsLoaded.includes(jsFileName)) {
        if (debug)
            log(`INCLUDE JS FILE: file *${jsFileName}* added`);
        let scriptElement = appendNodeToNode('script', '', '', document.getElementsByTagName('html')[0]);
        scriptElement.src = `/~sk2283/assets/front/js/${jsFileName}.js`;
        scriptsLoaded.push(jsFileName);

    } else {
        if (debug)
            log(`INCLUDE JS FILE: file *${jsFileName}* NOT added`);
        /* If the functions *initialize* exists, call it. This function initializes the content of the page being loaded. */

    }

}

/* Add stylesheet in head element. Returns the link element. */
function addStylesheet(cssFileName) {
    let head = document.getElementsByTagName('head')[0];
    let linkElm = appendNodeToNode('link', '', '', head);
    linkElm.rel = 'stylesheet';
    linkElm.href = '/~sk2283/assets/front/stylesheets/' + cssFileName;

    return linkElm;
}

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
    if (typeof element === "object")
        element.style = 'display:none';
    else
        getelm(element).style = 'display:none';
}

/** Takes the element to be shown as argument */
function showElement(element) {
    element.style = 'display:inline-block';
}

/** Show a modal based on its DOM ID */
function showModalBH(id) {
    getelm(id).style.display = "block";
}

/** Close a modal based on its DOM ID */
function closeModalBH(id) {
    getelm(id).style.display = "none";
}

function showButtonLoading(btnID) {
    let iTag = getelm(btnID).getElementsByTagName('i');
    if (iTag.length > 0)
        showElement(iTag[0]);
}

function hideButtonLoading(btnID) {
    let iTag = getelm(btnID).getElementsByTagName('i');
    if (iTag.length > 0)
        hideElement(iTag[0]);
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

function getPage(contentToSend, callback) {
    let parsed = parseJSON(contentToSend);

    url = 'https://web.njit.edu/~sk2283/assets/front/php/getPage.php';
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        /* Check if the xhr request was successful */
        if (this.readyState === 4 && this.status === 200) {
            log('GET PAGE GOOD. TITLE: ' + parsed.page);
            callback(contentToSend, xhr.responseText, parsed.page, parsed.js, parsed.url);
        }

    };
    /* Open a POST request */
    xhr.open("POST", url, true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(contentToSend);
}

function changePageHTML(xhrResponse, title, newURL) {
    let obj = {};
    obj.html = xhrResponse;
    obj.title = title;

    historyPushState(obj, title, newURL);

    // log(xhrResponse);
    /* This is the callback for a XHR Request. It will load the page requested. The response text is HTML. */
    // document.write(xhrResponse);
    document.getElementsByTagName('html')[0].innerHTML = xhrResponse;
    // getelm('body').innerHTML = xhrResponse;

}

function changeToNewPage(sentContent, xhrResponse, title, jsFileToInclude, newURL) {
    /* sentContent is what was sent in the AJAX request which called this callback. */
    /* This is the callback for a XHR Request. It will load the page requested. The response text is HTML. */
    let obj = {};
    obj.html = xhrResponse;
    obj.title = title;
    historyPushState(obj, title, null);
    document.getElementsByTagName('html')[0].innerHTML = xhrResponse;

    initPage(title, sentContent);
}

function initPage(htmlFileName, sentContent, isPopstate) {
    /* Add the font stylesheet to each page. */
    addStylesheet('font.css');

    /* sentContent is what was sent in an AJAX request. */
    switch (htmlFileName) {
        /* Instructor Pages */
        case TITLE_LOGIN:
            if (typeof initializeLogin === "function")
                initializeLogin();
            break;
        case TITLE_INSTRUCTOR_HOME:
            includeJS(JS_INSTRUCTOR_HOME);
            if (typeof initializeInstructorHome === "function")
                initializeInstructorHome();
            break;
        case TITLE_EXAM_CREATOR:
            includeJS(JS_EXAM_CREATOR);
            if (typeof initializeExamCreator === "function")
                initializeExamCreator();
            break;
        case TITLE_VIEW_CREATED_EXAMS:
            includeJS(JS_CREATED_EXAMS);
            if (typeof initializeViewCreatedExams === "function")
                initializeViewCreatedExams();
            break;
        case TITLE_GRADE_AN_EXAM:
            includeJS(JS_GRADE_AN_EXAM);
            if (sentContent) {
                let parsed = parseJSON(sentContent);
                examID = parsed.examID;
            }
            if (typeof initializeGradeAnExam === "function") {
                initializeGradeAnExam();
            }
            break;
        case TITLE_VIEW_COMPLETED_EXAMS:
            includeJS(JS_VIEW_COMPLETED_EXAMS);
            if (typeof initializeViewCompletedExams === "function")
                initializeViewCompletedExams();
            break;

        /* Student Pages */
        case TITLE_STUDENT_HOME:
            includeJS(JS_STUDENT_HOME);
            if (typeof initializeStudentHome === "function")
                initializeStudentHome();
            break;
        case TITLE_STUDENT_VIEW_EXAMS:
            includeJS(JS_STUDENT_VIEW_ALL_GRADED_EXAMS);
            if (typeof initializeStudentViewExams === "function")
                initializeStudentViewExams();
            break;
        case TITLE_STUDENT_TAKE_EXAM:
            includeJS(JS_STUDENT_TAKE_EXAM);
            if (typeof initializeTakeExam === "function")
                initializeTakeExam();
            break;
        default:
            break;

    }
}


function getCurrentPageHTML() {
    let htmlElement = document.getElementsByTagName('html');
    if (htmlElement.length > 0)
        return htmlElement[0].innerHTML;
    else
        return null;
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
    let obj = {};
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

/** Checks if a javascript file is included on the current page. Will not work for dynamically loaded scripts. */
function jsFileOnPage(file) {
    let scriptElems = document.getElementsByTagName('script');

    for (let i = 0; i < scriptElems.length; i++) {
        log(scriptElems[i].src);
        if (scriptElems[i].src.includes(`/~sk2283/assets/front/js/${file}.js`)) {
            if (debug) {
                log(`jsFileOnPage(): *${file}* exists.`)
            }
            return true;
        }
    }

    return false;
}

function appendBreakTag(appendTo) {
    return appendNodeToNode('br', '', '', appendTo);
}

function log(str) {
    console.log(str);
}

addStylesheet('font.css');