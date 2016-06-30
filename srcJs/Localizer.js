/*** include ajax/CORSRequest.js ***/

// @author Axel Ancona Esselmann

function Localizer(resources) {
    this.resources         = {};
    this.hasLoaded         = false;
    this.localizationCueue = [];
    var resourcesLoaded    = 0;
    var that = this;
    for (var i = 0; i < resources.length; i++) {
        var xhr  = CORSRequest.create('GET', resources[i]);
        if (!xhr) return;
        xhr.setRequestHeader("Content-Type","application/json");
        xhr.onload = function() {
            var response = JSON.parse(xhr.responseText)["response"];
            for (var attrname in response) {that.resources[attrname] = response[attrname];}
            resourcesLoaded++;
            if (resourcesLoaded == resources.length) {
                that.hasLoaded = true;
                that.clearLocalizationCueue();
            };
        };
        xhr.onerror = function() {
            console.log("There was an error with CORS request");
        };
        xhr.send();
    };
}
Localizer.prototype.localize = function(id, varName) {
    var localizationObject = {_id:id,_varName:varName};
    if (this.hasLoaded) {
        this.resolveLocalization(localizationObject);
    } else {
        this.localizationCueue.push(localizationObject);
    };
}
Localizer.prototype.clearLocalizationCueue = function() {
    for (var i = 0; i < this.localizationCueue.length; i++) {
        this.resolveLocalization(this.localizationCueue[i]);
    };
    this.localizationCueue = null;
}
Localizer.prototype.resolveLocalization = function(localizationObject) {
    var element = localizationObject._id;
    if (!element.nodeType) {
        element = document.getElementById(localizationObject._id);
    }
    if (element == undefined) {
        console.log("Undefined id encountered during localizition: " + localizationObject._id);
        return;
    }
    var localizedString = this.getResolvedString(localizationObject._varName);
    element.innerHTML = localizedString;
}
Localizer.prototype.getResolvedString = function(varName) {
    var localizedString = this.resources[varName];
    return localizedString;
}