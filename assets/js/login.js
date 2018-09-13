function authenticateLogin() {
    /* Display the loading icon */
    document.getElementById('loading').style.visibility = 'visible';
    document.getElementById('submit-btn').setAttribute("value", "Signing In ...");
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            /* Hide the loading icon */
            document.getElementById('loading').style.visibility = 'hidden';
            document.getElementById('response').innerHTML = xhr.responseText;
            document.getElementById('submit-btn').setAttribute("value", "Sign In");
        }
    };
    xhr.open('POST', '/~hks32/CS490-Project/assets/middle/index.php', true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    let user = document.getElementById("user").value;
    let pass = document.getElementById("pass").value;
    xhr.send("user=" + user + "&pass=" + pass);
}

// document.addEventListener('DOMContentLoaded', function () {}