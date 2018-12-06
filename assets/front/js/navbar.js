function goToInstructorHome() {
    let obj = {};
    obj.page = TITLE_INSTRUCTOR_HOME;
    obj.js = JS_INSTRUCTOR_HOME;
    obj.url = './instructor';

    getPage(JSON.stringify(obj), changeToNewPage);
}

function goToExamCreator() {
    let obj = {};
    obj.page = TITLE_EXAM_CREATOR;
    obj.js = JS_EXAM_CREATOR;
    obj.url = './examCreator';

    getPage(JSON.stringify(obj), changeToNewPage);
}

function goToViewCreatedExamsPage() {
    let obj = {};
    obj.page = TITLE_VIEW_CREATED_EXAMS;
    obj.js = JS_CREATED_EXAMS;
    obj.url = './viewCreatedExams';

    getPage(JSON.stringify(obj), changeToNewPage);
}

function goToGradePage() {
    let obj = {};
    obj.page = TITLE_VIEW_COMPLETED_EXAMS;
    obj.js = JS_VIEW_COMPLETED_EXAMS;
    obj.url = './grade';

    getPage(JSON.stringify(obj), changeToNewPage);
}

function logout() {
    let obj = {};
    obj.page = TITLE_LOGIN;
    obj.js = JS_LOGIN;
    obj.url = '';

    getPage(JSON.stringify(obj), changeToNewPage);
}

function setNavbarActive(pageTitle) {
    /* Remove active class from all 'a' attributes in the navbar */
    let aElms = getelm('nav').getElementsByTagName('a');
    for (let i = 0; i < aElms.length; i++) {
        removeClass(aElms[i].id, 'active');
    }
    switch (pageTitle) {
        case TITLE_LOGIN:
            break;
        case TITLE_INSTRUCTOR_HOME:
            addClass('homeBtn', 'active');
            break;
        case TITLE_EXAM_CREATOR:
            // addClass('examCreatorBtn', 'active');
            break;
        case TITLE_VIEW_CREATED_EXAMS:
            addClass('createdExamsBtn', 'active');
            break;
        case TITLE_VIEW_COMPLETED_EXAMS:
            addClass('gradeBtn', 'active');
            break;
        default:
            break;

    }
}