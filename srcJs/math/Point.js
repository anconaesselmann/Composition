
// @author Axel Ancona Esselmann

// declare the parent constructor:
function Point (x, y, z) {
	this.set(x, y, z);
	this._drawn = false;
	this.name = null;

}; //inheritPrototype(Sprite, Element);

Point.prototype.type = 'Point';

Point.prototype.toString = function() {
	return '('+this.x+', '+this.y+', '+this.z+')';
}

Point.prototype._instanceCount = 0;

Point.prototype.getNode = function () {
	if (this.name !== null) {
		return document.getElementById(this.name);
	} else return null;
};

Point.prototype.set = function (x, y, z) {
	x = typeof x !== 'undefined' ? x : 0;
	y = typeof y !== 'undefined' ? y : 0;
	z = typeof z !== 'undefined' ? z : 0;
	this.x = x;
	this.y = y;
	this.z = z;
};

Point.prototype.subtr = function(p) {
	return new Point(
		this.x-p.x,
		this.y-p.y,
		this.z-p.z
	);
};

Point.prototype.round = function(decPl) {
	decPl = typeof decPl !== 'undefined' ? decPl : 0;
	var fact = Math.pow(10,Math.abs(Math.round(decPl)));
	this.x = Math.round(this.x * fact) / fact;
	this.y = Math.round(this.y * fact) / fact;
	this.z = Math.round(this.z * fact) / fact;
}

Point.prototype.shift = function (xShift, yShift, zShift) {
	xShift = typeof xShift !== 'undefined' ? xShift : 0;
	yShift = typeof yShift !== 'undefined' ? yShift : 0;
	zShift = typeof zShift !== 'undefined' ? zShift : 0;
	this.x += xShift;
	this.y += yShift;
	this.x += zShift;
};

Point.prototype.draw = function (domNode, color) {
	color = typeof color !== 'undefined' ? color : 'black';
	if (this.name === null) {
		this.name = 'Point_'+Point.prototype._instanceCount;
		Point.prototype._instanceCount++;

		var fragment = document.createDocumentFragment();

		var svgns = "http://www.w3.org/2000/svg";

		var svg = document.createElementNS(svgns,'svg');

		svg.setAttribute('version', '1.1');
		svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
		svg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');



		svg.setAttribute('style', 'position:absolute;left:'+(this.x-1)+'px;top:'+(this.y-1)+'px;width:100px;height:100px');

		svg.setAttribute('viewBox', '0 0 200 200');
		svg.setAttribute('enable-background', 'new 0 0 200 200');
		svg.setAttribute('xml:space', 'preserve');
		svg.setAttribute('id', this.name);


		var circle = document.createElementNS(svgns, "circle");
		//circle.setAttribute('class', 'circle');
		//circle.setAttributeNS(null, 'id', this.name+' circle');
		circle.setAttributeNS(null, 'fill', color);
		circle.setAttributeNS(null, 'stroke', color);
		circle.setAttributeNS(null, 'stroke-miterlimit', '10');
		circle.setAttributeNS(null, 'cx', 5.5);
		circle.setAttributeNS(null, 'cy', 5.5);
		circle.setAttributeNS(null, 'r', 5);

		svg.appendChild(circle);
		fragment.appendChild(svg);

		domNode.appendChild(fragment);
	} else {
		alert('inside old node, this has not been tested');
		var svg = this.getNode();
		svg.setAttribute('style', 'position:absolute;left:'+(this.x-1)+'px;top:'+(this.y-1)+'px;width:100px;height:100px;');
	}


}

Point.prototype.norm = function() {
	return Math.sqrt(this.x*this.x + this.y*this.y);
}
Point.prototype.remove = function () {
	alert('not tested');
	alert(this.name);
	this.getNode().parentNode.removeChild(this);
}
