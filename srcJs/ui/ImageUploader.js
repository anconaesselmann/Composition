/*** include std/OnLoadQueue.js ***/
/*** include std/EventUtil.js ***/
/*** include ajax/CORSRequest.js ***/
/*** include ajax/UploadManager.js ***/
/*** include ui/ProgressBar.js ***/
/*** include ui/ImageUploader.css ***/

// @author Axel Ancona Esselmann

function ImageUploader (targetNodeName, url, formName, fileName, imagePath, onclickCallback) {
    this.targetNodeName  = targetNodeName;
    this.url             = url;
    this.formName        = formName;
    this.fileName        = fileName;
    this.imagePath       = imagePath;
    this.onclickCallback = onclickCallback;
    this.images          = [];
    this.node            = null;
    this.uploadManager = new UploadManager(3);
}
ImageUploader.prototype.ajaxFormSubmitUploadImage = function() {
    var form     = document[this.formName];
    var files = form[this.fileName].files;
    var progressBar = new ProgressBar("imageUploader", files.length);
    var progressBarCallback = function(uploaded, uploading) {
        progressBar.update(uploaded, uploading);
    }
    this.uploadManager.statusChangeCallback = progressBarCallback;
    progressBar.build();
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var that = this;
        var callback = function(imageId) {
            that.imageUploaded(imageId);
        }
        this.uploadManager.add(this.url + "/upload", file, this.fileName, callback);
    };
}
ImageUploader.prototype.ajaxDeleteImage = function(imageId) {
    console.log(imageId);
    var url  = this.url + "/delete/" + imageId + window.location.search;
    var xhr  = CORSRequest.create('GET', url);
    var that = this;
    if (!xhr) return;
    xhr.onload = function() {
        var result = parseInt(xhr.responseText);
        if (result == 1) {
            console.log("success");
            var imageContainerNode = document.getElementById("iuii_" + imageId);
            var parent = imageContainerNode.parentNode;
            parent.removeChild(imageContainerNode);
        } else {
            console.log("could not delete image");
        };
    };
    xhr.onerror = function() {
        console.log("There was an error with CORS request");
    };
    xhr.send();
}
ImageUploader.prototype.imageUploaded = function(imageId) {
    this.images.push(imageId);
    this.createImageNode(imageId);
    console.log(imageId);
}
ImageUploader.prototype.createImageNode = function(imageId) {
    var imageNode          = document.createElement("img");
    var imageContainerNode = document.createElement("div");
    var deleteNode         = document.createElement("div");
    imageNode.setAttribute("src", this.imagePath + "/" + imageId + ".jpg");
    imageNode.setAttribute("class", "iuImage");
    imageContainerNode.setAttribute("class", "iuImageContainer");
    imageContainerNode.setAttribute("id", "iuii_" + imageId);
    imageContainerNode.appendChild(imageNode);
    deleteNode.innerHTML = "X";
    deleteNode.setAttribute("class", "iuDeleteBtn hidden");

    var that = this;
    var deleteCallback = function() {
        event = EventUtil.getEvent(event);
        EventUtil.stopPropagation(event);
        that.ajaxDeleteImage(imageId);
    }
    var clickCallback = function() {
        that.onclickCallback(imageId);
    }
    var mouseoverCallback = function() {
        deleteNode.classList.remove("hidden");
    }
    var mouseleaveCallback = function() {
        deleteNode.className += " hidden";
    }
    EventUtil.addHandler(deleteNode, "click", deleteCallback);
    EventUtil.addHandler(imageContainerNode, "click", clickCallback);
    EventUtil.addHandler(imageContainerNode, "mouseover", mouseoverCallback);
    EventUtil.addHandler(imageContainerNode, "mouseleave", mouseleaveCallback);
    imageContainerNode.appendChild(deleteNode);

    this.node.appendChild(imageContainerNode);
}
ImageUploader.prototype.build = function() {
    var that = this;
    var buildAfterPageLoad = function() {
        var parentNode = document.getElementById(that.targetNodeName);

        var ilNode = document.createElement("div");
        ilNode.setAttribute("id", "imageUploader");

        parentNode.appendChild(ilNode);
        that.node = ilNode;

        var formNode = document[that.formName];
        var fileNode = formNode[that.fileName];

        EventUtil.addHandler(parentNode, "change", function(event) {
            event = EventUtil.getEvent(event);
            EventUtil.stopPropagation(event);
            that.ajaxFormSubmitUploadImage();
        });
    }
    window.globalOnLoadQueue.push(buildAfterPageLoad);
}