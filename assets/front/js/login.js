const STUDENT = 'student';
const INSTRUCTOR = 'instructor';
const INVALID = null;
// historyReplaceState({page: 'index.html'}, null, null);
historyReplaceState({
    html: getCurrentPageHTML(),
    title: TITLE_LOGIN
}, 'login', null);


function authenticateLogin() {
    /* Check if username and password are empty */
    let user = document.forms["login-form"]["user"].value;
    let pass = document.forms["login-form"]["pass"].value;
    if (user === '' || pass === '') {
        changeInnerHTML('error', "Username and password are required.");
        return;
    }
    /* Later used to send as JSON */
    let credentialsObj = {
        "user": user,
        "pass": pass,
        "requestType": "login"
    };

    /* Show the loading tag */
    loader('loading', 'visible');
    setAttribute('submit-btn', "value", "Signing In ...");

    /* Disable the form inputs when sending the request */
    getelm('field-set').disabled = true;
    let xhr = new XMLHttpRequest();
    /* Listener to receive a response from server */
    xhr.onreadystatechange = function () {
        /* Enable the form inputs */
        getelm('field-set').disabled = false;

        /* Check if the xhr request was successful */
        if (this.readyState === 4 && this.status === 200) {
            /* Remove any text from the 'error' tag */
            changeInnerHTML('error', '');

            /* Hide Loading icon */
            loader('loading', 'hidden');
            setAttribute('submit-btn', 'value', 'Sign In');

            /* Try to parse JSON */
            let json = parseJSON(xhr.responseText);
            log(json);

            /* Check if json was successfully parsed */
            if (json === false) {
                return;
            }

            /* Check to see if back-end connected to the database */
            if (json.hasOwnProperty('conn') && !json.conn) {
                changeInnerHTML('error', "Unable to access database. Please try again later.");
                return;
            }

            /*  Used for JSON to request a page. */
            let obj = {};

            /* Update status tags */
            if (json.user === INVALID) {
                changeInnerHTML('error', "Username or password is incorrect.");
            } else if (json.user === INSTRUCTOR) {
                obj.page = TITLE_INSTRUCTOR_HOME;
                obj.url = './instructor';
                obj.user = user;
                getPage(JSON.stringify(obj), changeToNewPage);
                includeJS('instructorHome');

            } else if (json.user === STUDENT) {
                obj.page = 'student-home.html?ucid=' + user;
                history.pushState({'user': user}, 'Login', './student');
                getPage(JSON.stringify(obj), changeToNewPage);
            }
        }

    };
    /* Open a POST request */
    xhr.open("POST", "/~sk2283/assets/front/php/contact_middle.php", true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(JSON.stringify(credentialsObj));

}

function initializeLogin() {
    getelm('field-set').disabled = false;
}


window.onload = function () {
    initializeLogin();
};

/* Wait for the DOM to be created */
// document.addEventListener('DOMContentLoaded', function () {
// });