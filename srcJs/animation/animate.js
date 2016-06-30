
// @author Axel Ancona Esselmann

jsonToCharacter(jsonCharacters);
jsonToImmovable(jsonImmovables);

// Game speciffics begin here


var test = new CharacterNeeds();
//var test = new PersonalityTrait(1,1,1,1,1);
//test.update();
//labels[0].caption(test);






var TIME_COUNTER = 100;

var framesInMinute = 10;
var frameInMinute = 1;

var changeFrame = function() {
	for (var i = 0; i < movables.length; i++) {
		if (typeof movables != 'undefined') {
			movables[i].move();
			if ((movables[i].getSpeedX() != 0) || (movables[i].getSpeedY() != 0)) {
				movables[i].sprite.advanceXFrame();
			}
			//collision.detect();
		}
	}
	if (frameInMinute > framesInMinute) {
		frameInMinute = 1;
		test.update();
		//labels[0].caption(test);


	} else frameInMinute++;
	setTimeout(changeFrame, TIME_COUNTER);
};






var collision = new Collision();
collision.addObjects(movables);
collision.addObjects(immovables);
changeFrame();









EventUtil.addHandler(document, "keydown", function(event){
	event = EventUtil.getEvent(event);
	switch (event.keyCode) {
		case 37:
		case 38:
		case 39:
		case 40:
			movables[1].moveToPos = null;
	}
	switch (event.keyCode) {
		case 37:
			movables[1].setSpeedX(-8);
			break;
		case 38:
			movables[1].setSpeedY(-8);
			break;
		case 39:
			movables[1].setSpeedX(8);
			break;
		case 40:
			movables[1].setSpeedY(8);
			break;
	}
});
EventUtil.addHandler(document, "keyup", function(event){
	event = EventUtil.getEvent(event);
	switch (event.keyCode) {
		case 37:
			movables[1].setSpeedX(0);
			break;
		case 38:
			movables[1].setSpeedY(0);
			break;
		case 39:
			movables[1].setSpeedX(0);
			break;
		case 40:
			movables[1].setSpeedY(0);
			break;
	}
});
EventUtil.addHandler(scrollables[0].getNode(), "contextmenu", function(event){
	event = EventUtil.getEvent(event);
	event.preventDefault();
	//labels[0].caption(event.pageX+', '+event.pageY);
});
for (var i = 0; i < movables.length; i++) {
	 (function(obj) {
        EventUtil.addHandler(obj.getNode(), "contextmenu", function(event) {
            event = EventUtil.getEvent(event);
            EventUtil.preventDefault(event);
			EventUtil.stopPropagation(event);
			var pos = scrollables[0].getContClickPos(event);
			contMenus[0].deleteElements()
			contMenus[0].set(pos.x, pos.y);
			contMenus[0].hide(false);
			contMenus[0].addElement(obj.name, obj.getCharacterName(), 'labels[0].caption("'+obj.getCharacterName()+'")');
        });
    })(movables[i]);
}
for (var i = 0; i < immovables.length; i++) {
	 (function(obj) {
        EventUtil.addHandler(obj.getNode(), "contextmenu", function(event) {
            event = EventUtil.getEvent(event);
            EventUtil.preventDefault(event);
			EventUtil.stopPropagation(event);
			var pos = scrollables[0].getContClickPos(event);
			contMenus[0].deleteElements()
			contMenus[0].set(pos.x, pos.y);
			contMenus[0].hide(false);
			contMenus[0].addElement(obj.name, obj.name, 'labels[0].caption("'+obj.name+'")');
        });
    })(immovables[i]);
}






EventUtil.addHandler(scrollables[0].getNode(), "click", function(event) {
	event = EventUtil.getEvent(event);

	// TODO: this is a temporary fix
	contClickPos = scrollables[0].getContClickPos(event);
	movables[0].moveTo(contClickPos.x, contClickPos.y);

	contMenus[0].hide(true);

	//labels[0].caption('pos in content: '+contClickPos);
});

var list = new LinkedList();
var p = new Point(2.123456789,2.123456789,2.123456789);
list.push(new Point(1.123456789,1.123456789,1.123456789));
list.push(p);
list.push(new Point(3.123456789,3.123456789,3.123456789));
list.push(new Point(4.123456789,4.123456789,4.123456789));

p.round(4);

//labels[0].captionLog('<br />'+list);


var list2 = new LinkedList();
var p2 = new Point(22,22,22);
list2.push(new Point(11,11,11));
list2.push(p2);
list2.push(new Point(33,33,33));
list2.push(new Point(44,44,44));
list2.push(p);


list2.reset();
list2.next();
list2.next();
list2.next();
list2.insertAfterData(p2, new Point(66,66,66));
list2.next().x = 99;
removedList = list2.removeBetween(p, p2);

//labels[0].captionLog('<br />'+list2);
//labels[0].captionLog('<br />'+removedList);
//labels[0].captionLog('<br />'+list2);
list2.reset();
list2.next();
list2.next();

//labels[0].captionLog('<br />'+list2.insertListAfterData(p, list));
//labels[0].captionLog('<br />'+list2);

//labels[0].captionLog('<br />Displaying array:');
var a = list2.getArray();

list2.reset();
list2.next();
list2.next();
list2.next().x = 77;
list2.reverse();
//a = list2.getArray();
/*for (var i = 0; i < a.length; i++) {
	labels[0].captionLog('<br />'+a[i]);
}*/
//list2.clear();
list.appendArray(a);
//labels[0].captionLog('<br />'+list);
//labels[0].captionLog('<br />has changed: '+list2.hasChanged());

//var g = new Grid(10,10);
//labels[0].captionLog(g);
