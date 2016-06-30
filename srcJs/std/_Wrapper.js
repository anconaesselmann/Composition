/*** include std/EventUtil.js ***/

// @author Axel Ancona Esselmann

var _Wrapper = function(n, kind) {
    this.kind = kind;
    this.n = n;
    this.class = new _Class(this);
    if (kind == 1) {
        this.length = n.length;
    } else {
        this.length = null;
    };
}
_Wrapper.prototype.node = function() {
    return this.n;
}
_Wrapper.prototype.child = function(c) {
    return new _Wrapper(this.n[c]);
}
/*
 * String with # returns id, with . returns class, without returns tag
 */
_Wrapper.prototype.parent = function(s) {
    switch(s[0]){
        case '.':
            var className = s.substr(1);
            var parent    = new _Wrapper(this.n.parentNode);
            var tagName   = parent.n.tagName;

            while(tagName != undefined){
                if (aae.std.inArray(className, parent.class.get())) return parent;
                parent  = new _Wrapper(parent.n.parentNode);
                tagName = parent.n.tagName;
            }
            return false;
        // case '#':
        //     var idName = s.substr(1);
            return false;
        default:
            // var tagName = s;
            return false;
    }
}
_Wrapper.prototype.sibling = function(s) {
    switch(s[0]){
        case '.':
            var className = s.substr(1);
            var sibling   = new _Wrapper(this.n.nextSibling);

            while(sibling.n != undefined){
                if (aae.std.inArray(className, sibling.class.get())) return sibling;
                sibling  = new _Wrapper(sibling.n.nextSibling);
            }
            sibling   = new _Wrapper(this.n.previousSibling);
            while(sibling.n != undefined){
                if (aae.std.inArray(className, sibling.class.get())) return sibling;
                sibling  = new _Wrapper(sibling.n.previousSibling);
            }
            return false;
        // case '#':
        //     var idName = s.substr(1);
            return false;
        default:
            // var tagName = s;
            return false;
    }
}
_Wrapper.prototype.getChild = function(s) {
    var element = this;
    s = s.substr(1);
    function recurse(element, s, found) {
        if (element.n == null) return null;
        for (var i = 0; i < element.n.childNodes.length && !found; i++) {
            var el = new _Wrapper(element.n.childNodes[i]);
            var classes = el.class.get();
            for (var j = 0, jl = classes.length; j < jl; j++) {
                if (classes[j] == s) return new _Wrapper(element.n.childNodes[i]);
            }
            foundElement = recurse(new _Wrapper(element.n.childNodes[i]), s, found);
            if (foundElement != null) return foundElement;
        }
    }
    return recurse(element, s, false);
}
_Wrapper.prototype.addEventHandler = function(f, eventType) {
    if (this.length != null) {
        for (var i = 0; i < this.length; i++) {
            var obj = this.child(i);
            (function(obj, f, i){
                EventUtil.addHandler(obj.n, eventType, function() {
                    f(obj, i);
                });
            })(obj, f, i);
        };
    } else {
        var obj = this;
        EventUtil.addHandler(this.n, eventType, function() {
            f(obj, i);
        });
    };
}
_Wrapper.prototype.mouseenter = function(f) {
    this.addEventHandler(f, "mouseenter");
}
_Wrapper.prototype.mouseleave = function(f) {
    this.addEventHandler(f, "mouseleave");
}
_Wrapper.prototype.click = function(f) {
    this.addEventHandler(f, "click");
}
_Wrapper.prototype.set = function(s) {
    switch(s[0]){
        case '.':
            return this.class.set(s.substr(1));
        default:
            return false;
    }
}
_Wrapper.prototype.unset = function(s) {
    var parts = s.split(' ');
    for (var i = 0; i < parts.length; i++) {
        switch(parts[i][0]){
            case '.':
                this.class.unset(parts[i].substr(1));
                break;
            default:
                break;
        }
    };
}

_Wrapper.prototype.isSet = function(s) {
    switch(s[0]){
        case '.':
            return this.class.isSet(s.substr(1));
        default:
            return false;
    }
}
_Wrapper.prototype.toggle = function(s) {
    switch(s[0]){
        case '.':
            return this.class.toggle(s.substr(1));
        default:
            return false;
    }
}
/**
 * Cause a toggle in a parent object on mouseenter and mouseleave
 * @param  {string} name   class name (with leading .), id (with leading #) or tag name of parent element
 * @param  {string} toggle Name of css class that is toggled on the parent element
 * @return {this}
 */
_Wrapper.prototype.parentHover = function(name, toggle, callbackEnter, callbackLeave){
    this.mouseenter(function(obj, i){
        obj.parent(name).class.set(toggle);
        if (callbackEnter != undefined) callbackEnter(obj, i);
    });
    this.mouseleave(function(obj, i){
        obj.parent(name).class.unset(toggle);
        if (callbackLeave != undefined) callbackLeave(obj, i);
    });
    return this;
}
// _Wrapper.prototype.appdendTo = function(s, i) {
//     switch(s[0]){
//         case '.':

//             return false;
//         case '#':

//             return false;
//         default:
//             if (i == undefined) {i = 0};
//             var parent = document.getElementsByTagName(s)[i];
//             parent.appendChild(this.n);
//             return this;
//     }
// }
_Wrapper.prototype._ = function(s, arg1, arg2) {
    if (!aae.str.isString(s)) {
        this.n.appendChild(s.n);
        return s;
    };
    var tagParts = s.split(' ');
    tagName = tagParts[0].substr(1);
    if (tagName.length == 0) {
        tagName = 'div';
    };
    var element = document.createElement(tagName);
    wrapper = new _Wrapper(element, 0);
    tagParts.shift();
    for (var i = 0; i < tagParts.length; i++) {
        var char1 = tagParts[i][0];
        var name = tagParts[i].substr(1).trim();
        if (char1 == '.') {
            wrapper.class.set(name);
        } else {
            if (char1 != '#') {
                name = tagParts[i].trim();
            }
            element.id = name;
        };
    };
    if (tagName == "img") {
        element.setAttribute("src", arg1);
    } else if (tagName == "a") {
        element.setAttribute("href", arg1);
        var textNode = document.createTextNode(arg2);
        element.appendChild(textNode);
    } else if (arg1 != undefined) {
        var textNode = document.createTextNode(arg1);
        element.appendChild(textNode);
    };
    if (this.n == null) {
        this.n = element;
        return wrapper;
    };
    this.n.appendChild(element);
    return wrapper;
}
_Wrapper.prototype.bounds = function() {
    return this.n.getBoundingClientRect();
}