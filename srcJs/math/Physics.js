
// @author Axel Ancona Esselmann

var Physics = {
    getDistanceTraveled: function(v, a, t) {
        var d = v * t + (a * t * t) / 2;
        return d;
    },
    getVelocity: function(v0, a, t, maxV) {
        var v = v0 + a * t;
        if (maxV != undefined && v > maxV) v = maxV;
        return v;
    }
}