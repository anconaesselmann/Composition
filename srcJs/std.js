/*** include std/OnLoadQueue.js ***/

// @author Axel Ancona Esselmann

function object(o){
    function F(){}
    F.prototype = o;
    return new F();
}

function inheritPrototype(subType, superType) {
    var prototype = object(superType.prototype);
    prototype.constructor = subType;
    subType.prototype = prototype;
}

/**
 * selectOnClick allows setting of active elements via css class.
 * A callback for when the object is active can be provided.
 * Pass all variables that change for each instance in args, and create a callback that has one argument.
 *
 */
function selectOnClick(obj, selectClassName, callback, args) {
    (function(obj, selectClassName) {
        var onclick = function() {
            var selected = document.getElementsByClassName(selectClassName);
            for (var j = 0; j < selected.length; j++) {
                var old_element = selected[j]
                old_element.classList.remove(selectClassName);
            };
            obj.classList.add(selectClassName);
            if (callback != undefined) callback(args);
        };
        EventUtil.addHandler(obj, "click", onclick);
    })(obj, selectClassName, callback, args);
}