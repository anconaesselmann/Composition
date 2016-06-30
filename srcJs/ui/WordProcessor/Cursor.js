
var Cursor = function(textBox) {
    this.textBoxElement = textBox.textBoxElement;
    this.objects = textBox.objects;
    this.textBox = textBox;

    this.node = _('<div .cursor');
    this.textBoxElement.n.appendChild(this.node.n);
}

Cursor.prototype.cursorLeft = function() {
    echo('cursor left');
    var cursorNode = this.objects.peek();
    if (cursorNode != null) {
        if (this.objects.hasPrev()) {
            this.objects.prev()
            var previousNode = this.objects.peek();
            if (previousNode != null) {
                this.setCursor(previousNode.node.n);
            } else {
                this.objects.next();
            }
        }
    }
}
Cursor.prototype.cursorRight = function() {
    echo('cursor right');
    var cursorNode = this.objects.peek();
    if (cursorNode != null) {
        if (this.objects.hasNext()) {
            this.objects.next()
            var nextNode = this.objects.peek();
            if (nextNode != null) {
                this.setCursor(nextNode.node.n);
            }
        }
    }
}
Cursor.prototype.setCursor = function(source) {
    if (!this.textBox.hasSelection()) {
        this.showCursor();
        var boundsTextBox = this.textBoxElement.n.getBoundingClientRect();
        var boundsSource  = source.getBoundingClientRect();
        var leftS         = boundsSource.left;
        var widhtS        = boundsSource.width;
        var topS          = boundsSource.top;
        var leftT         = boundsTextBox.left;
        var topT          = boundsTextBox.top;

        this.node.n.style.left = (leftS - leftT + widhtS) + "px";
        this.node.n.style.top  = (topS  - topT)           + "px";
    } else {
        this.hideCursor();
    };
}
Cursor.prototype.hideCursor = function(source) {
    this.node.class.set('hidden');
}
Cursor.prototype.showCursor = function(source) {
    this.node.class.unset('hidden');
}