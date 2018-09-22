function authenticateLogin() {
    /* Check if username and password are empty */
    let user = document.forms["login-form"]["user"].value;
    let pass = document.forms["login-form"]["pass"].value;
    if (user === '' || pass === '') {
        changeInnerHTML('error', "Username and password are required.");
        return;
    }

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
            /* Check if json was successfully parsed */
            if (json === false)
                return;

            /* Update status tags */
            if (json.njit) {
                chngClass('is-njit', 'fail', 'success');
                changeInnerHTML('is-njit', '<strong>NJIT:</strong> Success');
            } else {
                chngClass('is-njit', 'success', 'fail');
                changeInnerHTML('is-njit', '<strong>NJIT:</strong> Fail');
            }

            if (json.back) {
                chngClass('is-database', 'fail', 'success');
                changeInnerHTML('is-database', '<strong>Database:</strong> Success');
            } else {
                chngClass('is-database', 'success', 'fail');
                changeInnerHTML('is-database', '<strong>Database:</strong> Fail');
            }

        }

    };
    /* Open a POST request */
    xhr.open("POST", "/~sk2283/assets/front/php/auth-login.php", true);
    /* Encode the data properly. Otherwise, php will not be able to get the values */
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /* Send the POST request with the data */
    xhr.send("user=" + user + "&pass=" + pass);

}

/* Enable the input fields when the page loads */
window.onload = function () {
    /* Enable the form inputs */
    getelm('field-set').disabled = false;
};

/* Wait for the DOM to be created */
// document.addEventListener('DOMContentLoaded', function () {
// });

