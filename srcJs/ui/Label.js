// @author Axel Ancona Esselmann

Label = function(caption, id) {
    this.id  = id;
    this.caption = caption;
    var that = this;
};
Label.prototype.build = function(parentNode) {
    this.node = document.createElement('div');
    parentNode.appendChild(this.node);
    this.node.id = this.id;
    this.node.className = "label";
    this.node.innerHTML = this.caption;
}

