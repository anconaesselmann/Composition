
// @author Axel Ancona Esselmann

function PopUp (x, y, content, idName) {
    var popUp = document.getElementById("popUp");
    if (popUp != null) {popUp.parentNode.removeChild(popUp);};
    popUp        = document.createElement("div");
    var cont     = document.createElement("div");
    var header   = document.createElement('div');
    var closeBtn = document.createElement('div');
    cont.setAttribute('class', 'popCont');
    cont.setAttribute('id', 'popCont');
    header.setAttribute('class', 'popHead');
    closeBtn.setAttribute('class', 'popClose');
    closeBtn.innerHTML = 'x';
    closeBtn.onclick   = function() {
        var popUp = document.getElementById("popUp");
        popUp.parentNode.removeChild(popUp);
    };
    popUp.appendChild(header);
    header.appendChild(closeBtn);
    popUp.appendChild(cont);
    popUp.setAttribute('id', 'popUp');
    if (content && content.nodeType) {
        cont.appendChild(content);
    } else {
        cont.innerHTML = content;
    }

    if (x != undefined) {
        popUp.style.position = "absolute";
        popUp.style.left     = x + "px";
        popUp.style.top      = y + "px";
    };
    if (idName == undefined) {
        var parent = document.getElementsByTagName('body')[0];
    } else {
        var parent = document.getElementById(idName);
    };
    parent.appendChild(popUp);
    return popUp;
}