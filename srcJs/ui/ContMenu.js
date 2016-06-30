/*** include math/Point.js ***/
/*** include std/EventUtil.js ***/
/*** include std/OnLoadQueue.js ***/
/** include ui/ContMenu.css **/

// @author Axel Ancona Esselmann

/**
 * [ContMenu description]
 * @param {[type]} name          [description]
 * @param {[type]} x             [description]
 * @param {[type]} y             [description]
 * @param {[type]} domParent     [description]
 * @param bool onContextMenu If true, the cont menu replaces the right click menu.
 *                           If false, it replaces the click event.default is true.
 */
function ContMenu (name, x, y, domParent, onContextMenu) {
	this.name         = name;
	this.pos          = new Point(x,y);
	this.elements     = [];
	this.onLoadQueue  = [];
	this.domParent    = (domParent != undefined) ? domParent : null;
	this.clickedEvent = null;
	this.activeSubMenu = null;
	this.delegate      = null;
	this.onContextMenu = (onContextMenu != undefined) ? onContextMenu : true;
	var cm            = this;
	var buildCallback = function () {
	    cm.build();
	}
	window.globalOnLoadQueue.push(buildCallback);
};

ContMenu.prototype.build = function () {
	if (this.getNode() == null) this.createView();
	else this.connectView();
	for (var i = 0; i < this.onLoadQueue.length; i++) this.onLoadQueue[i]();
	this.set();
}

ContMenu.prototype.type = 'ContMenu';

ContMenu.prototype.getNode = function () {
	return document.getElementById(this.name);
};

ContMenu.prototype.toString = function() {
	return '('+this.x+', '+this.y+', '+this.z+')';
};

ContMenu.prototype.loaded = function() {
    var body = document.getElementsByTagName('body')[0];
    if (body == null) return false;
    else return true;
}

ContMenu.prototype.set = function (x, y) {
	x = (x == undefined) ? this.pos.x : x;
	y = (y == undefined) ? this.pos.y : y;
	this.pos.set(x, y);
	var that           = this.getNode();
	that.style.left    = x+"px";
	that.style.top     = y+"px";
	// this.hide(false);
};

ContMenu.prototype.createView = function () {
	if (this.getNode() != null) return;
	var menuView = document.createElement("ul");
	menuView.setAttribute("class", "ContMenu");
	menuView.setAttribute("id", this.name);
	this.connectView(menuView);
};

ContMenu.prototype.connectView = function (menuView) {
	var cm = this;
	this.onLoadQueue.unshift(
		function() {
			menuView = menuView || document.getElementById(cm.name);
			var body = document.getElementsByTagName('body')[0];
			body.appendChild(menuView);
			if (cm.domParent == null) {
				var parent = document.getElementsByTagName('body')[0];
				// show when clicked anywhere
				EventUtil.addHandler(parent, "click", function(event) {
				    event = EventUtil.getEvent(event);
					EventUtil.stopPropagation(event);
					cm.set(event.pageX, event.pageY);
					cm.hide(false);
				});
			} else if (cm.domParent == false) {
				var body = document.getElementsByTagName('body')[0];
				EventUtil.addHandler(body, "click", function(event) {
				    event = EventUtil.getEvent(event);
					EventUtil.stopPropagation(event);
					cm.hide();
					if (cm.delegate != null) cm.delegate.hide();
				});
			} else {
				var parents = document.getElementsByClassName(cm.domParent);
				for (var i = 0; i < parents.length; i++) {
					 cm.linkElement(parents[i]);
				}
			}
		}
	);
};
/* adds the context menu to elem */
ContMenu.prototype.linkElement = function (elem) {
	var body = document.getElementsByTagName('body')[0];
	var cm = this;
	 (function(obj) {
	 	// show when clicked on a parent element
	 	var clickCallback = function(event) {
	 	    event = EventUtil.getEvent(event);
	 		EventUtil.stopPropagation(event);
	 		cm.set(event.pageX, event.pageY);
	 		console.log(event.toElement);
	 		cm.clickedEvent = event;
			cm.hide(false);
			EventUtil.preventDefault(event);
	 	};
	 	if (cm.onContextMenu) {
	 		EventUtil.addHandler(obj, "contextmenu", clickCallback);
	 	} else {
	 		EventUtil.addHandler(obj, "click", clickCallback);
	 	};
	 	// hide when clicked anywhere else
	 	EventUtil.addHandler(body, "click", function(event) {
	 	    event = EventUtil.getEvent(event);
	 		EventUtil.stopPropagation(event);
	 		cm.hide();
	 		if (cm.delegate != null) cm.delegate.hide();
	 	});
	 	// EventUtil.addHandler(obj, "contextmenu", alert("works"));
    })(elem);
}

/** funct is the name of a function. The original click event is passed. */
ContMenu.prototype.addElement = function (name, funct, caption) {
	var menuItemNode = document.getElementById(name);
	if (menuItemNode == null) {
		menuItemNode = document.createElement('li');
		menuItemNode.innerHTML = caption;

		menuItemNode.setAttribute('class', 'MenuItem');
		menuItemNode.setAttribute('id', name);

		var cm = this;
		this.onLoadQueue.push(
		    function() {
		        var that = cm.getNode();
		        that.appendChild(menuItemNode);
		    }
		);
	};

    this.connectElement(name, funct);
};

ContMenu.prototype.connectElement = function (name, funct) {
    var menuItem = new MenuItem(name, funct, "");
    this.elements.push(menuItem);

    var cm = this;
    this.onLoadQueue.push(
        function() {
        	var menuItemNode = document.getElementById(name);
        	EventUtil.addHandler(menuItemNode, "click", function(event) {
        	    event = EventUtil.getEvent(event);
        	    EventUtil.stopPropagation(event);
        	    cm.hide();
        	    if (cm.delegate != null) cm.delegate.hide();
        	    window[funct](cm.clickedEvent, menuItem);
        	});
        }
    );
};

ContMenu.prototype.addSubHeaderElement = function (name, menuCallback, caption) {
    var menuItemNode       = document.createElement('li');
    menuItemNode.innerHTML = caption;

    menuItemNode.setAttribute('class', 'MenuItem');
    menuItemNode.setAttribute('class', 'SubMenuHeaderItem');
    menuItemNode.setAttribute('id', name);



    var cm = this;
    this.onLoadQueue.push(
        function() {
            var that = cm.getNode();
            that.appendChild(menuItemNode);
        }
    );
    this.connectSubHeaderElement(name, menuCallback);
};

ContMenu.prototype.connectSubHeaderElement = function (name, menuCallback) {
    var menuItem = new MenuItem(name, null, "");
    this.elements.push(menuItem);

    var cm = this;
    var connectSubHeaderElementCallback = function() {
    	var menuItemNode = document.getElementById(name);
    	EventUtil.addHandler(menuItemNode, "mouseenter", function(event) {
    	    event = EventUtil.getEvent(event);
    	    EventUtil.stopPropagation(event);
    	    console.log("mouseenter on " + name);
    	    var menu = menuCallback(cm.clickedEvent);
    	    if (menu.clickedEvent == null) menu.clickedEvent = cm.clickedEvent;
    	    menu.delegate    = cm;
    	    cm.activeSubMenu = menu;
    	    var width = parseInt(getComputedStyle(cm.getNode()).getPropertyValue("width"));
    	    menu.pos.set(cm.pos.x + width, cm.pos.y);
    	    cm.hide(false);
    	});
    	EventUtil.addHandler(menuItemNode, "mouseout", function(event) {
    	    event = EventUtil.getEvent(event);
    	    EventUtil.stopPropagation(event);
    	    console.log("mouseout on " + name);
    	});
    }
    if (this.loaded()) connectSubHeaderElementCallback();
    else this.onLoadQueue.push(connectSubHeaderElementCallback);
};

ContMenu.prototype.deleteElements = function() {
	this.elements = [];
	this.getNode().innerHTML = '';
}

ContMenu.prototype.hide = function(bool) {
	var that = this.getNode();
	if (that == undefined) return;
	bool = (bool == undefined) ? true : bool;
	if (bool) {
		that.style.display = "none";
		if (this.delegate != null) {
			this.deleteElements();
		};
		if (this.activeSubMenu != null) {
			this.activeSubMenu.hide();
		}
	} else {
		that.style.display = "inherit";
		if (this.activeSubMenu != null) {
			this.activeSubMenu.hide();
		};
	}
}

function MenuItem (name, funct, capt) {
	this.name = name;
	this.set(funct, capt);
};

MenuItem.prototype.type = 'MenuItem';

MenuItem.prototype.getNode = function () {
	return document.getElementById(this.name);
};

MenuItem.prototype.set = function(funct, capt) {
	this.caption = capt;
	this.funct = funct;
};


function SubMenuHeaderItem (name, capt) {
	this.name = name;
	this.set(capt);
}
MenuItem.prototype.set = function(capt) {
	this.caption = capt;
};

SubMenuHeaderItem.prototype.getNode = function () {
	return document.getElementById(this.name);
};
