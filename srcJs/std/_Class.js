
// @author Axel Ancona Esselmann

var _Class = function(wrapper) {
    this._classes = null;
    this._wrapper = wrapper
}
_Class.prototype.get = function() {
    if (this._classes == null) {
        var className = this._wrapper.n.className;
        if (className != undefined) {
            this._classes = className.split(" ");
        } else {
            this._classes = [];
        };
    };
    return this._classes;
}
_Class.prototype.set = function(cn) {
    if (!aae.std.inArray(cn, this.get())) {
        this._classes.push(cn);
        this._wrapper.n.className = this.get().join(" ").trim();
    };
    return this._wrapper;
}
_Class.prototype.unset = function(cn) {
    var parts = cn.split(" ");
    for (var i = 0; i < parts.length; i++) {
        aae.std.arrayUnset(parts[i], this.get())
    };
    this._wrapper.n.className = this.get().join(" ");
    return this._wrapper;
}
_Class.prototype.isSet = function(cn) {
    var classes = this.get();
    return aae.std.inArray(cn, classes);
}
_Class.prototype.toggle = function(cn) {
    if (aae.std.inArray(cn, this.get())) {
        this.unset(cn);
        return false;
    } else {
        this.set(cn);
        return true;
    };
}