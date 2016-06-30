/*** include std/OnLoadQueue.js ***/
/*** include ui/Slideshow.css ***/

// @author Axel Ancona Esselmann

function Slideshow(targetNodeName, transitionTime, assets, callback) {
    this.targetNodeName = targetNodeName;
    this.transitionTime = transitionTime;
    this.assets         = assets;
    this.callback       = callback;
    this.node           = null;
}

Slideshow.prototype.build = function() {
    var that = this;
    var buildAfterPageLoad = function() {
        var parentNode = that.targetNodeName;
        if (!parentNode.nodeType) {
            parentNode = document.getElementById(that.targetNodeName);
        }
        ssNode = document.createElement("div");
        parentNode.appendChild(ssNode);
        ssNode.setAttribute("class", "Slideshow");
        that.node = ssNode;
    }
    window.globalOnLoadQueue.push(buildAfterPageLoad);
}