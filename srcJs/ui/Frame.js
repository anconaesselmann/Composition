// @author Axel Ancona Esselmann

Frame = function(id, parentId) {
    this.id = id;
    this.state = 0;
    var that = this;
    if (parentId != undefined) window.globalOnLoadQueue.push(function() {that.build(document.getElementById(parentId));});
};

Frame.prototype.build = function(parentNode) {
    this.node = document.createElement('div');
    parentNode.appendChild(this.node);
    this.node.id = this.id;
    this.node.className = "ui-frame state" + this.state;
    var that = this;
    EventUtil.addHandler(this.node, "mouseup", function(event) {
        if (that.state == 0) {
            that.switchState(1);
        }
    });
    EventUtil.addHandler(this.node, "mouseenter", function(event) {
        that.focus = true;
    });
    EventUtil.addHandler(this.node, "mouseleave", function(event) {
        that.focus = false;
    });
    this.mainNode = document.createElement('div');
    this.node.appendChild(this.mainNode);
    this.mainNode.className = "frame-main";
}
Frame.prototype.switchState = function(stateNbr) {
    this.state = stateNbr;
    this.node.className = "ui-frame state" + stateNbr;
}
Frame.prototype.drawContent = function(callback) {
    var that = this;
    window.globalOnLoadQueue.push(function() {callback(that.mainNode);});
}

