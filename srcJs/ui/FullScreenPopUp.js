/*** include std/OnLoadQueue.js ***/
/*** include std/EventUtil.js ***/
/*** include ui/FullScreenPopUp.css ***/

// @author Axel Ancona Esselmann

function FullScreenPopUp (content, idName) {
    this.content = content;
    this.idName = idName;
}

FullScreenPopUp.prototype.build = function() {
    var that = this;
    var buildAfterPageLoad = function() {
        var fsPopUp = document.getElementById("fsPopUp");
        if (fsPopUp != null) {fsPopUp.parentNode.removeChild(fsPopUp);};
        fsPopUp      = document.createElement("div");
        var cont     = document.createElement("div");
        var closeBtn = document.createElement('div');
        fsBackground = document.createElement("div");
        fsBackground.setAttribute("class", "fsPopUpBackground");
        cont.setAttribute('class', 'fsPopCont');
        cont.appendChild(fsBackground);
        closeBtn.setAttribute('class', 'fsPopClose');
        closeBtn.innerHTML = 'X';
        var onclick = function() {
            var fsPopUp = document.getElementById("fsPopUp");
            fsPopUp.parentNode.removeChild(fsPopUp);
        };
        EventUtil.addHandler(closeBtn, "click", onclick);
        EventUtil.addHandler(fsBackground, "click", onclick);
        fsPopUp.appendChild(cont);
        fsPopUp.setAttribute('id', 'fsPopUp');
        if (that.content && that.content.nodeType) {
            cont.appendChild(that.content);
        } else {
            cont.innerHTML = that.content;
        }
        if (that.idName == undefined) {
            var parent = document.getElementsByTagName('body')[0];
        } else {
            var parent = document.getElementById(that.idName);
        };
        parent.appendChild(fsPopUp);
        cont.appendChild(closeBtn);
    }
    window.globalOnLoadQueue.push(buildAfterPageLoad);
}