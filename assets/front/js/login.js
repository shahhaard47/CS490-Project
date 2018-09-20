function authenticateLogin() {
    /* Check if username and password are not empty */
    let user = document.forms["login-form"]["user"].value;
    let pass = document.forms["login-form"]["pass"].value;
    if (user === '' || pass === '') {
        alert("Username and password are required.");
        return;
    }

    document.getElementById('loading').style.visibility = 'visible';
    document.getElementById('submit-btn').setAttribute("value", "Signing In ...");
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            /* Hide the loading icon */
            document.getElementById('loading').style.visibility = 'hidden';
            document.getElementById('response').innerHTML = xhr.responseText;
            document.getElementById('submit-btn').setAttribute("value", "Sign In");
            console.log(xhr.responseText);
        }
    };
    xhr.open('POST', '/~sk2283/assets/front/php/auth-login.php', true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.send("user=" + user + "&pass=" + pass);
}

// document.addEventListener('DOMContentLoaded', function () {}