log(`-INSTRUCTOR HOME-`);
let INSTRUCTOR_HOME_INCLUDED = true;

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
    window.history.back();
}

/*
<!--<script src="assets/front/js/instructorHome.js"></script>-->
<!--<script src="assets/front/js/examCreator.js"></script>-->
<!--<script src="assets/front/js/handyMethods.js"></script>-->
<!--<script src="assets/front/js/dialog.js"></script>-->
* */