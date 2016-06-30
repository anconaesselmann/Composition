/*** include aae.js ***/
/*** include std/_Class.js ***/
/*** include std/_Wrapper.js ***/

// @author Axel Ancona Esselmann

var _ = function(s, arg1, arg2) {
    var kind = 0;
    var n = null;
    if (s[0] == '.') {
        var className = s.substr(1);
        n = document.getElementsByClassName(className);
        kind = 1;
    } else if (s[0] == '#') {
        var idName = s.substr(1);
        n = document.getElementById(idName);
    } else if (s[0] == '<') {
        var wrapper = new _Wrapper(null, kind);
        wrapper._(s, arg1, arg2);
        return wrapper;
    } else {
        var tagName = s;
        n = document.getElementsByTagName(tagName);
        if (n.length == 1) n = n[0];
        else kind = 1;
    };
    return new _Wrapper(n, kind);
}

var echo = function(logThis) {
    console.log(logThis);
}