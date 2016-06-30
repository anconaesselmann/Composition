/*** include std/OnLoadQueue.js ***/

// @author Axel Ancona Esselmann

var registerTextAbstracts = function() {
    var containers = _('.textAbstractBodyText');
    for (var i = 0; i < containers.length; i++) {
        var textAbstractId = containers.child(i).parent('.textAbstract').n.getAttribute('data-textAbstractId');
        // console.log(textAbstractId);
        var container = containers.child(i).n.childNodes[0];
        aae.ui.lineClampAndLink(container, container.childNodes[0].data, 3, _('<a .more', "/claim/view/"+textAbstractId, 'read more'));
    };
    containers = _('.textAbstractBodyTitle');
    for (var i = 0; i < containers.length; i++) {
        var container = containers.child(i).n.childNodes[0].childNodes[0];
        aae.ui.lineClampAndLink(container, container.childNodes[0].data, 2);
    };
}
globalOnLoadQueue.push(registerTextAbstracts);