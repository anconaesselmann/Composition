/*** include ajax/CORSRequest.js ***/

// @author Axel Ancona Esselmann

function UploadManager(simultaneousUploads, statusChangeCallback) {
    this.simultaneousUploads  = simultaneousUploads;
    this.currentlyUploading   = 0;
    this.nbrFilesUploading    = 0;
    this.nbrFilesUploaded     = 0;
    this.queue                = [];
    this.statusChangeCallback = statusChangeCallback;
}
UploadManager.prototype.ajaxUpload = function(url, file, fileName, callback) {
    var formData = new FormData();
    formData.append(fileName, file, file.name);
    url      = url + window.location.search;
    var xhr  = CORSRequest.create('POST', url);
    if (!xhr) return;
    var that = this;
    xhr.onload = function() {
        var imageId = xhr.responseText;
        callback(imageId);
        that.currentlyUploading--;
        that.nbrFilesUploaded++;
        that.statusChangeCallback(that.nbrFilesUploaded, that.nbrFilesUploading);
        if (that.queue.length > 0) {
            var item = that.queue.pop();
            that.ajaxUpload(item._url, item._file, item._fileName, item._callback);
        };
    };
    xhr.onerror = function() {
        console.log("There was an error with CORS request");
    };
    xhr.send(formData);
}
UploadManager.prototype.add = function(url, file, fileName, callback) {
    this.nbrFilesUploading++;
    if (this.currentlyUploading < this.simultaneousUploads) {
        this.currentlyUploading++;
        this.ajaxUpload(url, file, fileName, callback);
    } else {
        // TODOL: inefficient, since I am using an array
        this.queue.unshift({_url:url, _file:file, _fileName:fileName, _callback:callback});
    };
}