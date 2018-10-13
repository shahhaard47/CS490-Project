let STUDENT = 'student';
let INSTRUCTOR = 'instructor';
let INVALID = null;


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
        "requestType" : "login"
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

            // console.log('result: ' + xhr.responseText);

            /* Try to parse JSON */
            let json = parseJSON(xhr.responseText);

            /* Check if json was successfully parsed */
            if (json === false) {
                return;
            }

            /* Update status tags */
            if (json.user === INVALID) {
                changeInnerHTML('error', "Username or password is incorrect.");
            } else if (json.user === INSTRUCTOR) {
                changeInnerHTML('identity', "You are an instructor");
            } else if (json.user === STUDENT) {
                changeInnerHTML('identity', "You are a student");
            }
            console.log(xhr.responseText);
        }

    };
    /* Open a POST request */
    xhr.open("POST", "/~sk2283/assets/front/php/contact_middle.php", true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send(JSON.stringify(credentialsObj));

}

/* Enable the input fields when the page loads */
window.onload = function () {
    /* Enable the form inputs */
    getelm('field-set').disabled = false;
};

/* Wait for the DOM to be created */
// document.addEventListener('DOMContentLoaded', function () {
// });