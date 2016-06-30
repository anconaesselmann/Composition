/*** include std/OnLoadQueue.js ***/
/*** include std/EventUtil.js ***/
/*** include std/_.js ***/
/*** include dataStructures/LinkedList.js ***/
/*** include ui/WordProcessor/Cursor.js ***/

// @author Axel Ancona Esselmann

var WordProcessorTextStreamElement = function(keyCode) {
    if (keyCode == 13) {
        this.node = _('<span', "\n");
    } else {
        var key = String.fromCharCode(keyCode);
        this.node = _('<span .tsElem', key);
    }
}

var WordProcessorTextStream = function(textBoxElement) {
    this.textBoxElement = textBoxElement;
    this.objects = new LinkedList();
    this.cursor  = new Cursor(this);
    this.objects.insertAfter(this.cursor);
    this.textBoxElement._(this.cursor.node);
    this.selectionStart = false;
    this.selectionEnd   = false;
    this.tempSelection  = false;
}
WordProcessorTextStream.prototype.hasSelection = function() {
    return this.selectionStart;
}
WordProcessorTextStream.prototype.add = function(keyCode) {
    var wptsElement = new WordProcessorTextStreamElement(keyCode);
    this.addNode(wptsElement);
};
WordProcessorTextStream.prototype.addNode = function(node) {
    var cursorNode = this.objects.peek().node.n;
    this.objects.insertAfter(node);

    var nextNode = cursorNode.nextSibling;
    if (nextNode == null) nextNode = cursorNode;
    this.textBoxElement.n.insertBefore(node.node.n, nextNode);
    this.cursor.setCursor(node.node.n);
}
WordProcessorTextStream.prototype.remove = function() {
    if (this.selectionStart) {
        this.removeMultipe();
        this.cursor.setCursor(this.objects.peek().node.n);
    } else {
        var cursorNode = this.objects.peek();
        if (cursorNode != null) {
            cursorNode.node.n.id = "";
            if (this.objects.hasPrev()) {
                var item = this.objects.remove();
                this.textBoxElement.n.removeChild(item.node.n);
                this.cursor.setCursor(this.objects.peek().node.n);
            };
        }
    }
}
WordProcessorTextStream.prototype.goToElement = function(source) {
    var currentPosition = this.objects.getPositionPointer();
    this.objects.reset();
    var current;
    current = this.objects.next();
    while(current != null){
        echo(current.node.n);
        if (current.node.n == source) {
            this.objects.prev();
            this.cursor.setCursor(source);
            return this.objects.peek();
        }
        current = this.objects.next();
    }
    this.objects.setPositionPointer(currentPosition);
}
WordProcessorTextStream.prototype.setBeginSelect = function(source) {
    var current = this.goToElement(source);
    this.beginSelect    = current;//this.objects.goToWithWrapper(source, "node", "n");
    this.selectionStart = this.beginSelect;
}
WordProcessorTextStream.prototype.updateSelect = function(source) {
    if (this.beginSelect) {

        if (this.tempSelection) {
            var prevSelection = this.objects.getSubArray(this.selectionStart, this.tempSelection);
            for (var i = 0; i < prevSelection.length; i++) {
                prevSelection[i].node.class.unset('highlight');
            };
        };
        this.tempSelection = this.goToElement(source);
        var selection = this.objects.getSubArray(this.selectionStart, this.tempSelection);
        for (var i = 0; i < selection.length; i++) {
            selection[i].node.class.set('highlight');
        };
    };
}
WordProcessorTextStream.prototype.clearSelect = function() {
    if (this.hasSelection()) {
        var selection = this.objects.getSubArray(this.selectionStart, this.selectionEnd);

        for (var i = 0; i < selection.length; i++) {
            var element = selection[i];
            element.node.class.unset('highlight');
        };
        this.selectionStart = false;
        this.selectionEnd   = false;
    };
}
WordProcessorTextStream.prototype.endSelect = function(source) {
    this.beginSelect = false;
    this.selectionEnd = this.goToElement(source);
    if (this.selectionEnd == this.selectionStart) {
        this.selectionEnd   = false;
        this.selectionStart = false;
    };
}
WordProcessorTextStream.prototype.removeMultipe = function() {
    if (this.selectionStart) {
        var linkText = "";
        var selection = this.objects.removeRangeInclusive(this.selectionStart, this.selectionEnd)
        for (var i = 0; i < selection.length; i++) {
            this.textBoxElement.n.removeChild(selection[i].node.n);
            linkText += selection[i].node.n.innerHTML;
        };
        this.selectionStart = false;
        this.selectionEnd   = false;
        if (linkText.length > 0) {
            this.objects.prev();
            return linkText;
        };
    }
    return false;
}
WordProcessorTextStream.prototype.createLink = function() {
    var linkText = this.removeMultipe();
    if (linkText) {
        var link = _('<a', 'http://www.google.com', linkText);
        link.class.set('link');
        var node = new WordProcessorTextStreamElement(null);
        node.node = link;
        this.addNode(node);
    };
}


var WordProcessor = function(id) {
    this.wpNode = _("#" + id);
    this.tbNode = this.wpNode.getChild('.wpTextField');
    this.textStream = new WordProcessorTextStream(this.tbNode);



    var that = this;
    this.createLink = this.wpNode.getChild('.createLink');
    this.createLink.click(function(obj,i) {
        echo("wokrs")
        echo(obj)
        that.textStream.createLink();
    });
    EventUtil.addHandler(document.body, 'keypress', function() {
        var ev=EventUtil.getEvent();
        var keyCode = EventUtil.getCharCode(ev);
        that.keyPress(keyCode);
    });
    EventUtil.addHandler(document.body, 'keydown', function() {
        var ev=EventUtil.getEvent();
        var keyCode = ev.keyCode;
        switch (keyCode) {
            case 37:
                EventUtil.preventDefault(ev);
                that.textStream.cursor.cursorLeft();
                break;
            case 39:
                EventUtil.preventDefault(ev);
                that.textStream.cursor.cursorRight();
                break;
            case 8:
                EventUtil.preventDefault(ev);
                that.textStream.remove();
                break;
            default:
                break;
        }
    });
    EventUtil.addHandler(this.tbNode.n, 'mouseup', function() {
        var ev=EventUtil.getEvent();
        var source = event.target || event.srcElement;
        echo(source);
        that.textStream.endSelect(source);
    });
    EventUtil.addHandler(this.tbNode.n, 'mousedown', function() {
        var ev=EventUtil.getEvent();
        var source = event.target || event.srcElement;
        echo("mouseDown")
        if (that.textStream.hasSelection()) {
            echo("clearing selection")
            that.textStream.clearSelect();
        };
        that.textStream.setBeginSelect(source);
    });
    EventUtil.addHandler(this.tbNode.n, 'mousemove', function() {
        var ev=EventUtil.getEvent();
        var source = event.target || event.srcElement;
        that.textStream.updateSelect(source);
    });
}
// window.onbeforeunload = function() { return "You work will be lost."; };

WordProcessor.prototype.createSpan = function() {

}

WordProcessor.prototype.keyPress = function(keyCode){
    echo(keyCode);
    this.textStream.add(keyCode);

};

var loadWordProcessorFromHtml = function(id) {
    var wp = new WordProcessor(id);
}

// WordProcessor.prototype.loadFromHtml = function(id){
//     return new WordProcessor(id);
// };


globalOnLoadQueue.push(function(){loadWordProcessorFromHtml('claimEditWordProcessor')});
// window.onLoad = loadWordProcessorFromHtml('claimEditWordProcessor');