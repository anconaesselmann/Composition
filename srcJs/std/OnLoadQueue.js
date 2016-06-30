
// @author Axel Ancona Esselmann

function OnLoadQueue () {
    this.onLoadQueue = [];
    var q = this;
    window.onload = function() {
        for (var i = 0; i < q.onLoadQueue.length; i++) q.onLoadQueue[i]();
    };
}
OnLoadQueue.prototype.registerQueue = function (queue) {
    var callback = function() {
        for (var i = 0; i < queue.length; i++) queue[i]();
    };
    this.push(callback);
}
OnLoadQueue.prototype.push = function (callback) {
    var body = document.getElementsByTagName('body')[0];
    if (body == null) this.onLoadQueue.push(callback);
    else callback();
}
var globalOnLoadQueue = new OnLoadQueue();