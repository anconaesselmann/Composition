/*** include std/OnLoadQueue.js ***/
/*** include std/EventUtil.js ***/

// @author Axel Ancona Esselmann

function ViewBox(svgNode) {
	this.svgNode = svgNode;
	this.init();
}
ViewBox.prototype.set = function(x, y, width, height) {
	this.x      = x;
	this.y      = y;
	this.width  = width  || this.width;
	this.height = height || this.height;
	this.svgNode.setAttribute('viewBox', this.x + " " + this.y + " " + this.width + " " + this.height);
}
ViewBox.prototype.setDeltas = function(xDelta, yDelta) {
	this.x = this.x + xDelta;
	this.y = this.y + yDelta;
	this.svgNode.setAttribute('viewBox', this.x + " " + this.y + " " + this.width + " " + this.height);
}
ViewBox.prototype.init = function() {
	var viewBoxString     = this.svgNode.getAttribute('viewBox');
	var viewBoxData       = viewBoxString.split(" ");
	this.x                = parseFloat(viewBoxData[0]);
	this.y                = parseFloat(viewBoxData[1]);
	var viewBoxWidth      = parseFloat(viewBoxData[2]);
	var viewBoxHeight     = parseFloat(viewBoxData[3]);
	this.svgWidth         = parseFloat(this.svgNode.getAttribute("width"));
	this.svgHeight        = parseFloat(this.svgNode.getAttribute("height"));
	var svgAspectRatio     = this.svgWidth / this.svgHeight;
	var viewBoxAspectRatio = viewBoxWidth  / viewBoxHeight;

	if (viewBoxAspectRatio < svgAspectRatio) {
		this.width  = viewBoxHeight * svgAspectRatio;
		this.height = viewBoxHeight;
	} else {
		this.width  = viewBoxWidth;
		this.height = viewBoxWidth * svgAspectRatio;
	}
	this.set(this.x, this.y);
}

function SvgImage(id) {
	this.id         = id;
	this.dragging   = false;
	this.viewBox    = undefined;
	this.svgNode    = undefined;
	this.invert     = false;
	this.moveFactor = 20;

	var that = this;
	window.globalOnLoadQueue.push(function() {that.build();});
}

SvgImage.prototype.build = function() {
	this.svgNode = document.getElementById(this.id);
	var that = this;
	EventUtil.addHandler(this.svgNode, "mousewheel", function(event) {that.svgScroll(event);});
	EventUtil.addHandler(this.svgNode, "mousedown",  function(event) {that.startDrag(event);});
	EventUtil.addHandler(this.svgNode, "mouseup",    function(event) {that.stopDrag (event);});
	EventUtil.addHandler(this.svgNode, "mousemove",  function(event) {that.drag     (event);});
	EventUtil.addHandler(this.svgNode, "mouseleave", function(event) {that.stopDrag (event);});
	EventUtil.addHandler(document,     "keydown",    function(event) {that.keyCheck (event);});
	this.viewBox = new ViewBox(that.svgNode);
}
SvgImage.prototype.resize = function(width, height) {
	this.svgNode.setAttribute('width',  width);
	this.svgNode.setAttribute('height', height);
}
SvgImage.prototype.fullScreen = function() {
	var that = this;
	window.globalOnLoadQueue.push(function() {
		var parent = that.svgNode.parentNode;
		parent.style.position = 'absolute';
		parent.style.left     = '0';
		parent.style.top      = '0';
		parent.style.width    = '100%';
		parent.style.height   = '100%';
		// parent.style.backgroundColor = 'white';

		var style = window.getComputedStyle(parent);
		// console.log(style.width)
		that.svgNode.setAttribute('width',  style.width);
		that.svgNode.setAttribute('height', style.height);

		that.viewBox.init();
	});
}

SvgImage.prototype.zoomSvgProportional = function(cursorX, cursorY, delta) {
	var viewBox     = this.viewBox;
	var scrollDelta = delta/100 * viewBox.width;
	var newWidth    = viewBox.width + scrollDelta;
	var newHeight   = newWidth * (viewBox.height/viewBox.width);
	if (newWidth < 100 || newHeight < 100) {
		newWidth  = viewBox.width;
		newHeight = viewBox.height;
	}
	if (cursorX === false) {
		var xFract = .5;
	} else {
		var xFract = (cursorX / viewBox.svgWidth);
	}
	if (cursorY === false) {
		var yFract = .5;
	} else {
		var yFract  = (cursorY / viewBox.svgHeight);
	}
	var xShift  = (viewBox.width - newWidth) * xFract;
	var yShift  = (viewBox.height - newHeight) * yFract;
	var newTopX = viewBox.x + xShift;
	var newTopY = viewBox.y + yShift;
	this.viewBox.set(newTopX, newTopY, newWidth, newHeight);
}
SvgImage.prototype.smoothZoomSvgProportional = function(cursorX, cursorY, delta, i) {
	var that = this;
	this.zoomSvgProportional(cursorX, cursorY, delta);
	if (i > 0) {
		setTimeout(function() {
			that.smoothZoomSvgProportional(cursorX, cursorY, delta, i - 1)
		},30);
	}
}
SvgImage.prototype.moveSvgProportional = function(deltaX, deltaY) {
	var viewBox   = this.viewBox;
	var boxDeltaX = deltaX * viewBox.width  / viewBox.svgWidth;
	var boxDeltaY = deltaY * viewBox.height / viewBox.svgHeight;
	this.viewBox.set(this.viewBox.x + boxDeltaX, this.viewBox.y + boxDeltaY);
}
SvgImage.prototype.smoothMoveSvgProportional = function(deltaX, deltaY, i) {
	var that = this;
	this.moveSvgProportional(deltaX, deltaY);
	if (i > 0) {
		setTimeout(function() {
			that.smoothMoveSvgProportional(deltaX, deltaY, i - 1)
		},30);
	}
}

SvgImage.prototype.svgScroll = function(e) {
	e.preventDefault();
	var cursorX = e.offsetX;
	var cursorY = e.offsetY;
	var delta   = e.deltaY;
	this.zoomSvgProportional(cursorX, cursorY, delta);
}
SvgImage.prototype.startDrag = function(e) {this.dragging = {x:e.x,y:e.y,vbx:this.viewBox.x,vby:this.viewBox.y};}
SvgImage.prototype.stopDrag  = function(e) {this.dragging = false;}
SvgImage.prototype.drag      = function(e) {
	if (this.dragging) {
		var deltaX       = this.dragging.x - e.x;
		var deltaY       = this.dragging.y - e.y;
		var scaledDeltaX = deltaX * this.viewBox.width  / this.viewBox.svgWidth;
		var scaledDeltaY = deltaY * this.viewBox.height / this.viewBox.svgHeight;
		var x            = this.dragging.vbx + scaledDeltaX;
		var y            = this.dragging.vby + scaledDeltaY;
		this.viewBox.set(x, y);
	}
}
SvgImage.prototype.keyCheck = function(e) {
	var inv = this.invert ? 1 : -1;

	if (e.keyCode >= 37 && e.keyCode <= 40) {
		e.preventDefault();
		var deltaX = 0;
		var deltaY = 0;
		if	(e.keyCode == 38) {
			deltaY = inv * this.moveFactor;
		} else if (e.keyCode == 40) {
			deltaY = inv * -this.moveFactor;
		} else if	(e.keyCode == 37) {
			deltaX = inv * this.moveFactor;
		} else if (e.keyCode == 39) {
			deltaX = inv * -this.moveFactor;
		}
		this.smoothMoveSvgProportional(deltaX, deltaY, 5);
	} else if (e.keyCode == 187 || e.keyCode == 189) {
		e.preventDefault();
		var zoom = 0;
		if (e.keyCode == 187) {
			zoom = -5;
		} else if (e.keyCode == 189) {
			zoom = 5;
		}
		this.smoothZoomSvgProportional(false, false, zoom, 10);
	}
}
SvgImage.prototype.triggerDownload = function(imgURI) {
	var evt = new MouseEvent('click', {
	  view: window,
	  bubbles: false,
	  cancelable: true
	});

	var a = document.createElement('a');
	a.setAttribute('download', 'MY_COOL_IMAGE.png');
	a.setAttribute('href', imgURI);
	a.setAttribute('target', '_blank');

	a.dispatchEvent(evt);
}
SvgImage.prototype.dowload = function() {
	var that = this;
	var downloadScale = 2;
	var canvas = document.createElement('canvas');

	canvas.setAttribute('width',  (this.viewBox.svgWidth  * downloadScale) + 'px');
	canvas.setAttribute('height', (this.viewBox.svgHeight * downloadScale) + 'px');
	canvas.style.display = 'none';

	document.body.appendChild(canvas);

	var ctx = canvas.getContext('2d');
	    ctx.scale(downloadScale,downloadScale);
	var data = (new XMLSerializer()).serializeToString(this.svgNode);
	var DOMURL = window.URL || window.webkitURL || window;

	var img     = new Image();
	var svgBlob = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
	var url     = DOMURL.createObjectURL(svgBlob);

	img.onload = function () {
	  ctx.drawImage(img, 0, 0);
	  DOMURL.revokeObjectURL(url);

	  var imgURI = canvas
	      .toDataURL('image/png')
	      .replace('image/png', 'image/octet-stream');

	  that.triggerDownload(imgURI);
	  document.body.removeChild(canvas);
	};

	img.src = url;
}


