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