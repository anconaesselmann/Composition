/*** include ui/ProgressBar.css ***/

// @author Axel Ancona Esselmann

function ProgressBar(targetNodeName, steps) {
    this.targetNodeName  = targetNodeName;
    this.steps           = steps;
    this.node            = null;
    this.psNode          = null;
    this.textNode        = null;
}

ProgressBar.prototype.build = function() {
    var that = this;
    var buildAfterPageLoad = function() {
        var parentNode = document.getElementById(that.targetNodeName);
        var pbNode     = document.createElement("div");
        var psNode     = document.createElement("div");
        var textNode   = document.createElement("div");

        pbNode.setAttribute("class", "progressBar");
        psNode.setAttribute("class", "progressStatusBar");
        textNode.setAttribute("class", "progressBarTextIndicator");
        textNode.innerHTML = "0%";

        pbNode.appendChild(psNode);
        pbNode.appendChild(textNode);
        parentNode.appendChild(pbNode);

        that.node     = pbNode;
        that.psNode   = psNode;
        that.textNode = textNode;
    }
    window.globalOnLoadQueue.push(buildAfterPageLoad);
}
ProgressBar.prototype.update = function(step, steps) {
    var percent = Math.round(step * 100 / steps);
    this.psNode.style.width = percent + "%";
    this.textNode.innerHTML = percent + "%";
    that = this;
    var removeBarCallback = function() {
        that.node.style.visibility = "hidden";
        // that.node.parentNode.removeChild(that.node);
    }
    if (percent == 100) {
        setTimeout(removeBarCallback, 2000);
    };
}