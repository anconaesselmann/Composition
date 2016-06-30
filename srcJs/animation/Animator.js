
// @author Axel Ancona Esselmann

function Animator(counterInterval) {
    this.counterInterval = (counterInterval != undefined) ? counterInterval : 10;
    this.movers          = [];
    this.animating       = false;
}
Animator.prototype.animate = function(isRecursiveCall) {
    // console.log("animating");
    if (isRecursiveCall != true) this.animating = true;
    var allDone = true;
    for (var i = 0; i < this.movers.length; i++) if (this.movers[i].move()) allDone = false;
    if (!allDone && this.animating) {
        var that = this;
        setTimeout(function() {that.animate(true);}, this.counterInterval);
    };
}
Animator.prototype.addMover = function(mover) {
    this.movers.push(mover);
}