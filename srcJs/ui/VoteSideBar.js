/*** include std/OnLoadQueue.js ***/
/*** include ui/DivSwitch.js ***/

// @author Axel Ancona Esselmann

var registerVoteSideBars = function() {
    var iconDivClassNames = ['agree', 'object', 'disagree', 'noVote'];
    var iconSelectDivClassNames = ['is1', 'is2', 'is3'];

    var voteSideBars = _('.voteSideBar');
    for (var i = 0; i < voteSideBars.length; i++) {
        var voteSideBar = voteSideBars.child(i);
        var iconBox         = voteSideBar.getChild('.iconBox');
        var iconSelect      = iconBox.getChild('.iconSelect');

        voteSideBar.mouseleave(function(obj, i) {
            obj.getChild('.iconSelect').unset('.visible');
        });
        iconBox.click(function(obj, i){
            obj.getChild('.iconSelect').toggle('.visible');
        });
        iconBox.mouseenter(function(obj, i){
            obj.getChild('.iconSelect').set('.visible');
        });
        iconSelect.mouseleave(function(obj){
            obj.unset('.visible');
        });
        iconSelect.getChild('.is1').click(function(obj, i){
            var icon = new DivSwitch(obj.parent('.iconBox'), iconDivClassNames, 'current', true);
            icon.set('agree');
            var iconSelect = new DivSwitch(obj.parent('.iconSelect'), iconSelectDivClassNames, 'current');
            iconSelect.set('is1');
        });
        iconSelect.getChild('.is2').click(function(obj, i){
            var icon = new DivSwitch(obj.parent('.iconBox'), iconDivClassNames, 'current', true);
            icon.set('object');
            var iconSelect = new DivSwitch(obj.parent('.iconSelect'), iconSelectDivClassNames, 'current');
            iconSelect.set('is2');
        });
        iconSelect.getChild('.is3').click(function(obj, i){
            var icon = new DivSwitch(obj.parent('.iconBox'), iconDivClassNames, 'current', true);
            icon.set('disagree');
            var iconSelect = new DivSwitch(obj.parent('.iconSelect'), iconSelectDivClassNames, 'current');
            iconSelect.set('is3');
        });
    };
}
globalOnLoadQueue.push(registerVoteSideBars);