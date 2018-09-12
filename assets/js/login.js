function authenticateLogin() {
    /* Display the loading icon */
    // document.getElementById('loading').style.visibility = 'visible';

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            /* Hide the loading icon */
            // document.getElementById('loading').style.visibility = 'hidden';
            document.getElementById('loading').innerHTML = xhr.responseText;
        }
    };
    xhr.open('POST', '/~hks32/index.php', true);
    xhr.setRequestHeader("Content-type", "application/x-www-for-urlencoded");
    let user = document.getElementById("user").value;
    let pass = document.getElementById("pass").value;
    xhr.send("user=" + user + "&pass=" + pass);
}

document.addEventListener('DOMContentLoaded', function () {

});