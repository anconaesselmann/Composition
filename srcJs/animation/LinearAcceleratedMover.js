/*** include math/Vector.js ***/
/*** include math/VectorMath.js ***/
/*** include math/Physics.js ***/
/*** include animation/Animator.js ***/

// @author Axel Ancona Esselmann

function LinearAcceleratedMover() {
    this.callbacks = [];
}
LinearAcceleratedMover.prototype.addCallback = function(callback, xInit, yInit, xFin, yFin, acceleration, maxVelocity, initVelocity) {
    acceleration = (acceleration != undefined) ? acceleration : 1;
    maxVelocity  = (maxVelocity  != undefined) ? maxVelocity  : 1;
    initVelocity = (initVelocity != undefined) ? initVelocity : 0;

    var callBackObject = {
        callback     : callback,
        posInit      : new Vector(xInit, yInit),
        posFin       : new Vector(xFin, yFin),
        pos          : new Vector(xInit, yInit),
        v            : initVelocity,
        totalDist    : VectorMath.getLength(VectorMath.subtV(new Vector(xInit, yInit), new Vector(xFin, yFin))),
        distTraveled : 0,
        acceleration : acceleration,
        maxVelocity  : maxVelocity,
        initVelocity : initVelocity
    };
    this.callbacks.push(callBackObject);
}
LinearAcceleratedMover.prototype.move = function() {
    var hasNext = false;
    for (var i = 0; i < this.callbacks.length; i++) {
        if (this.calculateNext(this.callbacks[i])) {
            hasNext = true;
        };
        this.callbacks[i].callback(
            Math.round(this.callbacks[i].pos.x),
            Math.round(this.callbacks[i].pos.y)
        );
    };
    return hasNext;
}
LinearAcceleratedMover.prototype.calculateNext = function(callBackObject) {
    var time   = 1;
    var oldV   = callBackObject.v;
    var newV   = Physics.getVelocity(callBackObject.v, callBackObject.acceleration, time, callBackObject.maxVelocity);
    var dist   = Physics.getDistanceTraveled(callBackObject.v, callBackObject.acceleration, time);
    var oldPos = callBackObject.pos;
    var dirUV  = VectorMath.getDirectionUnitVector(oldPos, callBackObject.posFin);
    var displacement = VectorMath.scalMult(dirUV, dist);
    var newPos = VectorMath.addV(oldPos, displacement);
    callBackObject.distTraveled += dist;

    var hasNext = (callBackObject.totalDist - callBackObject.distTraveled) > 0;

    if (hasNext) {
        callBackObject.pos = newPos;
        callBackObject.v   = newV;
        return true;
    } else {
        callBackObject.v   = 0;
        callBackObject.distTraveled = callBackObject.totalDist;
        callBackObject.pos = callBackObject.posFin;
        return false;
    };
}



