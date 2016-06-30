
// @author Axel Ancona Esselmann

var VectorMath = {
    getLength: function(v) {
        return Math.abs(Math.sqrt(v.x * v.x + v.y * v.y));
    },
    scalMult: function(v, s) {
        return new Vector(v.x * s, v.y * s);
    },
    scalDiv: function(v, s) {
        return new Vector(v.x / s, v.y / s);
    },
    getUnitVector: function(v) {
        var lenth = VectorMath.getLength(v);
        return VectorMath.scalDiv(v, lenth);
    },
    addV: function(v1, v2) {
        return new Vector(v1.x + v2.x, v1.y + v2.y);
    },
    subtV: function(v1, v2){
        return new Vector(v1.x - v2.x, v1.y - v2.y);
    },
    getDirectionUnitVector: function(v1, v2) {
        var direction = VectorMath.subtV(v2, v1);
        var length    = VectorMath.getLength(direction);
        return VectorMath.scalDiv(direction, length);
    }
}