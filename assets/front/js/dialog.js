function showDialog(header, bodyText) {
    let dialog = appendNodeToNode('dialog', 'dialog', '', document.body);

    let divHeader = appendNodeToNode('div', '', 'dialogHeader', dialog);
    appendNodeToNode('h3', '', '', divHeader).innerHTML = header;

    let divBody = appendNodeToNode('div', '', 'dialogBody', dialog);
    appendNodeToNode('p', '', '', divBody).innerHTML = bodyText;

    let footer = appendNodeToNode('footer', '', 'dialogFooter', dialog);

    let btnCloseDialog = appendNodeToNode('button', 'button', 'button', footer);
    btnCloseDialog.innerHTML = 'Close';

    btnCloseDialog.onclick = function () {
        dialog.close();
        dialog.remove();
    };

    return dialog;
}

function getDialogCloseButton(dialogElement) {
    /* The first button on the dialog is the close button. */
    return dialogElement.getElementsByTagName('button')[0];
}