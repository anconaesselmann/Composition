/*** include ui/TabbedMenu.js ***/
/*** include ajax/CORSRequest.js ***/
/*** include ui/TabbedPropertyEditor.css ***/

// @author Axel Ancona Esselmann

function TabbedPropertyEditor(targetNodeName, localizer) {
    TabbedMenu.call(this, targetNodeName, localizer);
    this.className    = "tabbedPorpertyEditor";
    this.currentId    = null;
    this.submitUrls   = [];
    this.getUrls      = [];
    this.textAreaNode = null;
} inheritPrototype(TabbedPropertyEditor, TabbedMenu);

TabbedPropertyEditor.prototype.setCurrentId = function(id) {
    this.currentId = id;
}
TabbedPropertyEditor.prototype.addTab = function(idName, getUrl, setUrl) {
    this.submitUrls.push(setUrl);
    var that = this;
    this.getUrls.push(getUrl);
    var activeTabCallback = function(idName) {
        that.getData();
    }
    TabbedMenu.prototype.addTab.call(this, idName, activeTabCallback);
}
TabbedPropertyEditor.prototype.build = function() {
    TabbedMenu.prototype.build.call(this);
    var that = this;
    var buildAfterPageLoad = function() {
        var tpeNode       = that.node;
        var formContainer = document.createElement("div");
        var formNode      = document.createElement("form");
        var textAreaNode  = document.createElement("textarea");
        var submitButtonNode = document.createElement("div");
        that.textAreaNode = textAreaNode;
        formNode.setAttribute("name", "tabbedPorpertyEditorForm");
        formContainer.setAttribute("class", that.className + "FormContainer")
        textAreaNode.setAttribute("class", that.className + "Textarea");
        submitButtonNode.setAttribute("class", "tabbedPropertyEditorSubmitButton");
        submitButtonNode.setAttribute("id", "tabbedPropertyEditorSubmitButton");
        var onSubmitclick = function() {
            that.submitData();
        };
        EventUtil.addHandler(submitButtonNode, "click", onSubmitclick);
        formNode.appendChild(textAreaNode);
        formContainer.appendChild(formNode);
        tpeNode.appendChild(formContainer);
        tpeNode.appendChild(submitButtonNode);

        that.localizer.localize(submitButtonNode, "tabbedPropertyEditorSubmitButtonLabel");
    }
    window.globalOnLoadQueue.push(buildAfterPageLoad);
}
TabbedPropertyEditor.prototype.submitData = function() {
    var content = this.textAreaNode.value;
    var url     = this.submitUrls[this.activeTab] + "/" + this.currentId + "?data=" + encodeURIComponent(content);
    var xhr     = CORSRequest.create('GET', url);
    xhr.setRequestHeader("Content-Type","application/json");
    if (!xhr) return;
    xhr.onload = function() {
        var response = xhr.responseText;
        console.log(response);
    };
    xhr.onerror = function() {
        console.log("There was an error with CORS request");
    };
    xhr.send();
}
TabbedPropertyEditor.prototype.getData = function() {
    var getUrl = this.getUrls[this.activeTab];
    var url    = getUrl + "/" + this.currentId;
    var xhr    = CORSRequest.create('GET', url);
    xhr.setRequestHeader("Content-Type","application/json");
    if (!xhr) return;
    var that = this;
    xhr.onload = function() {

        console.log(xhr.responseText);
        var response = JSON.parse(xhr.responseText)["response"];
        that.textAreaNode.value = response;
    };
    xhr.onerror = function() {
        console.log("There was an error with CORS request");
    };
    xhr.send();
}