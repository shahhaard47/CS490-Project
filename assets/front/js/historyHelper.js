function historyPushState(state, title, url) {
    log(`Pushing ${state.title} to history`);
    // TODO: Go though history object to see if state.title already in it. If so, dont add again, but replace the state.
    history.pushState(state, title, url);
}

function historyReplaceState(state, title, url) {
    log(`Replacing ${state.title} to history`);

    history.replaceState(state, title, url);
}


function updateState(event) {
    /* State is an object. The 'html' tag will contain all the html of the page updating to. */
    document.getElementsByTagName('html')[0].innerHTML = event.state.html;
    log("UPDATE STATE TITLE: " + event.state.title);
    /*if (typeof initialize === "function") {
        log(`INITIALIZE IS A FUNCTION`);
        initialize();
    }*/

    initPage(event.state.title);
}

window.addEventListener('popstate', function (event) {
    // log(`Popping ${event.title} to history`);
    log(event);
    // log('Getting PAGE: ' + event.state.html);
    updateState(event);
    // getPage(JSON.stringify(event.state), updateState);
});