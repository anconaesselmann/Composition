/*** include std.js ***/
/*** include std/OnLoadQueue.js ***/
/*** include ui/TabbedMenu.css ***/

// @author Axel Ancona Esselmann

function TabbedMenu(targetNodeName, localizer) {
    this.targetNodeName = targetNodeName;
    this.localizer      = localizer;
    this.tabObjects     = [];
    this.className      = "tabbedMenu";
    this.node           = null;
    this.activeTab      = 0;
    this.customElement = function(element, idName) {
        return element;
    }
}
/**
 * A customElementCallback has to take element and idName as arguments and return element
 */
TabbedMenu.prototype.setCustomElementCallback = function(callback) {
    this.customElement = callback;
}
TabbedMenu.prototype.addTab = function(idName, callback) {
    var tabObj = {_idName:idName, _callback:callback};
    this.tabObjects.push(tabObj);
}
TabbedMenu.prototype.build = function() {
    var that = this;
    var buildAfterPageLoad = function() {
        var parentNode = document.getElementById(that.targetNodeName);
        var tmNode     = document.createElement("div");
        tmNode.setAttribute("class", that.className);
        parentNode.appendChild(tmNode);
        that.node = tmNode;

        for (var i = 0; i < that.tabObjects.length; i++) {
            var selectedClassName = that.className + "Selected";
            var tabNode = document.createElement("div");
            var tabTextNode = document.createElement("div");
            var idName  = that.tabObjects[i]["_idName"];
            tabNode.setAttribute("id", idName);
            tabNode.setAttribute("class", that.className + "Element");
            tabTextNode.setAttribute("id", idName + "Label");
            that.customElement(tabNode, idName);
            tabNode.appendChild(tabTextNode);
            if (i == that.activeTab) {
                tabNode.classList.add(selectedClassName);
                that.callTabCallback();
            };
            tmNode.appendChild(tabNode);
            that.localizer.localize(idName + "Label", that.tabObjects[i]["_idName"]);

            var callback = function(i) {
                that.activeTab = i;
                that.callTabCallback();
            }
            selectOnClick(tabNode, selectedClassName, callback, i);
        };
    }
    window.globalOnLoadQueue.push(buildAfterPageLoad);
}
TabbedMenu.prototype.callTabCallback = function() {
    this.tabObjects[this.activeTab]["_callback"](this);
}