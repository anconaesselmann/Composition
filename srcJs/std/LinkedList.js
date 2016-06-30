
// @author Axel Ancona Esselmann

function Node(data) {
	this.data = data;
	this.next = null;
	this.prev = null;
};
// flipps next and prev links
Node.prototype.invert = function(node) {
	var  temp = this.next;
	this.next = this.prev;
	this.prev = temp;
}

/**
	* TODO: Fix design flaw: if a data item is set to null outside of the linked list,
	* any function within linked list that iterates through the list will think that
	* the list ends with that object. Implement an EOF object that is passed when the
	* list iterates past the head or the tail, which can be tested for by === listInstance.EOF(),
	* or set a variable to true that is return with listInstance.EOF().
	*
	* For now: Make sure that any object that is adde to the linked list can not be
	* set to null from the outside! */
function LinkedList() {
	this.head = null;
	this.tail = null;
	this.itr = null;
	this.length = 0;
	this._hasChanged = false;
};

LinkedList.prototype.toString = function() {
	var itr = this.itr;
	this.reset();
	var out = '';
	var data = this.next();
	while (data !== null) {
		out += data+'<br />';
		data = this.next();
	}
	if (out == '') out = 'empty';
	this.itr = itr;
	return out;
}
//
LinkedList.prototype.hasChanged = function() {
	return this._hasChanged;
}
// adds @data to the end of the list, points iterator to the item inserted
LinkedList.prototype.push = function(data) {
	var node = new Node(data);
	if (this.head === null) {
		this.head = node;
		this.tail = node;
		this.itr  = node;
	} else {
		node.prev = this.tail;
		this.tail.next = node;
		this.tail = node;
	}
	this.length++;
	this._hasChanged = true;
};
// adds @data to the front of the list, points iterator to the item inserted
LinkedList.prototype.pushHead = function(data) {
	var node = new Node(data);
	if (this.head === null) {
		this.tail = node;
		this.head = node;
		this.itr  = node;
	} else {
		node.next = this.head;
		this.head.prev = node;
		this.head = node;
	}
	this.length++;
	this._hasChanged = true;
}
/**
	* removes from the end of the list and returns the data item.
	* Returns null if the list is empty. */
LinkedList.prototype.pop = function() {
	// the list is empty
	if (this.tail === null) return null;
	// if the iterator points at the tail, it has to be moved back
	if (this.itr === this.tail) {
		this.itr = this.itr.prev;
	}
	var popNode = this.tail;
	// if the tail points at null, the list is empty
	if (this.tail.prev === null) {
		this.head = null;
		this.tail = null;
		this.itr  = null;
	} else {
		this.tail.prev.next = null;
		this.tail = this.tail.prev;
	}

	var data = popNode.data;
	popNode.data = null;
	this.length--;
	this._hasChanged = true;
	return data;
};
/**
	* removes from the front of the list and returns the data item.
	* Returns null if the list is empty. */
LinkedList.prototype.popHead = function() {
	// the list is empty
	if (this.head === null) return null;
	// if the iterator points at the head, it has to be moved forward
	if (this.itr === this.head) {
		this.itr = this.itr.next;
	}
	var popNode = this.head;
	// if the head points at null, the list is empty
	if (this.head.next === null) {
		this.tail = null;
		this.head = null;
		this.itr  = null;
	} else {
		this.head.next.prev = null;
		this.head = this.head.next;
	}

	var data = popNode.data;
	popNode.data = null;
	this.length--;
	this._hasChanged = true;
	return data;
};
/**
	* returns the data item the iterator is currently pointing
	* to and advances the iterator. Returns null when the iterator
	* advances past the end. */
LinkedList.prototype.next = function() {
	if (this.itr === null) return null;
	var data = this.itr.data;
	this.itr = this.itr.next;
	return data;
};
LinkedList.prototype.hasNext = function() {
	if (this.itr === null) return false;
	return (this.itr.next != null) ? true : false;
};
/**
	* returns the data item the iterator is currently pointing
	* to and reverses the iterator. Returns null when the iterator
	* reverses past the head. */
LinkedList.prototype.prev = function() {
	if (this.itr === null) return null;
	var data = this.itr.data;
	this.itr = this.itr.prev;
	return data;
};
LinkedList.prototype.hasPrev = function() {
	if (this.itr === null) return false;
	return (this.itr.prev != null) ? true : false;
};
/**
	* returns the data item the iterator is currently pointing to
	* without advancing the iterator. Returns null when the iterator
	* whent either past the head or the end, or when the list is empty. */
LinkedList.prototype.peak = function() {
	if (this.itr === null) return null;
	return this.itr.data;
};
/**
	* inserts a data item after the node the iterator is pointing to
	* and points the iterator to that node. */
LinkedList.prototype.insert = function(data) {
	if (this.head === null) {
		this.push(data);
		return true;
	};
	if (this.itr === null) return false;
	var node = new Node(data);
	node.next = this.itr.next;
	node.prev = this.itr;
	// if iterator is pointing to the last node:
	if (this.itr.next === null) {
		this.itr.next = node;
		this.tail = node;
	} else {
		this.itr.next.prev = node;
		this.itr.next = node;
	}
	this.itr = node;
	this.length++;
	this._hasChanged = true;
	return true;
};
/**
	* inserts a data item before the node the iterator is pointing to
	* and points the iterator to that node. */
LinkedList.prototype.insertBefore = function(data) {
	if (this.itr === null) return false;
	var node = new Node(data);
	node.next = this.itr;
	node.prev = this.itr.prev;
	// if iterator is pointing to the last node:
	if (this.itr.prev === null) {
		this.itr.prev = node;
		this.head = node;
	} else {
		this.itr.prev.next = node;
		this.itr.prev = node;
	}
	this.itr = node;
	this.length++;
	this._hasChanged = true;
	return true;
};
/**
	* removes the node the iterator is currently pointing to and returns
	* the data item. Iterator will point to the next data item, or null
	* when the item that was removed was the last in the list. */
LinkedList.prototype.remove = function() {
	if (this.itr === null) return false;
	var popNode = this.itr;
	var data = popNode.data;
	if (this.itr.next === null) {
		if (this.itr.prev === null) {
			// iterator points at the last tiem in the list
			this.tail = null;
			this.head = null;
			this.itr = null;
		} else {
			// iterator points to the last item
			this.itr.prev.next = null;
			this.tail = this.itr.prev;
			this.itr = this.itr.prev
		}
	} else if (this.itr.prev === null) {
		// iterator points to the first item
		this.itr.next.prev = null;
		this.head = this.itr.next;
		this.itr = this.itr.next;
	} else {
		// iterator points to some item in the middle
		this.itr.prev.next = this.itr.next;
		this.itr.next.prev = this.itr.prev;
		this.itr = this.itr.next;
	}
	popNode.next = null;
	popNode.prev = null;
	popNode.data = null;
	this.length--;
	this._hasChanged = true;
	return data;
};
// points the iterator to the first node
LinkedList.prototype.reset = function() {
	this.itr = this.head;
};
// points the iterator to the last node
LinkedList.prototype.reverseReset = function() {
	this.itr = this.tail;
};
// reverses the order of the list
LinkedList.prototype.reverse = function() {
	if (this.head === null) return;
	var temp = this.head;
	this.head = this.tail;
	this.tail = temp;
	do {
		temp.invert();
		temp = temp.prev;
	} while (temp !== null);
	this._hasChanged = true;
};
/**
	* Sorts the linked list. Uses sortFunction to determine the order.
	* When the list contains only primitives, the list is sorted ascendingly
	*
	* example sort function:
	* 		function(a,b){return a.item1 > b.item1 ? 1 : -1;} */
LinkedList.prototype.sort = function(sortFunction) {
	alert('LinkedList.sort() has not been implemented yet!');
	this._hasChanged = true;
};
// removes all data from the list
LinkedList.prototype.clear = function() {
	// should work for modern browsers using mark ans sweep garbage collection
	this.head = null;
	this.tail = null;
	this.itr = null;
	this.length = 0;
	this._hasChanged = true;
};
// removes all data from the list and replaces it with the data in list (shallow copy)
LinkedList.prototype.cloneFrom = function(list) {
	this.clear();
	this.append(list);
};
/**
	*Removes all data from the list and replaces it with the data in list.
	* Uses @cloneFunction to make a deep copy.
	*
	* example for a clone function:
	* 		function(p){return new Point(p.x, p.y, p.z);} */
LinkedList.prototype.deepCloneFrom = function(list, cloneFunction) {
	this.clear();
	this.deepAppend(list, cloneFunction);
};
// adds all data in list to the end of the list (shallow copy)
LinkedList.prototype.append = function(list) {
	var tempItr = list.itr;
	list.reset();
	var data = list.next();
	while (data !== null) {
		this.push(data);
		data = list.next();
	}
	list.itr = tempItr;
};
/**
	* adds all data in list to the end of the list and uses
	* @copyFunction to make a deep copy of each data item.
	*
	* example for a clone function:
	* 		function(p){return new Point(p.x, p.y, p.z);}
*/
LinkedList.prototype.deepAppend = function(list, cloneFunction) {
	var tempItr = list.itr;
	list.reset();
	var data = list.next();
	while (data !== null) {
		this.push(cloneFunction(data));
		data = list.next();
	}
	list.itr = tempItr;
};
/**
	* Inserts a linked list @list after the current position of the iterator,
	* points the iterator at the last item inserted and returns true if successful
	*.
	* Returns false if the iterator went past the head or tail of the list,
	* or if either of the lists is empty.
	* @list will be empty after it was inserted. */
LinkedList.prototype.insertList = function(list) {
	if (this.itr === null) return false; // itr went past ends or list is empty
	if (list.head === null) return false; // list is empty
	if (this.itr.next !== null) {
		list.tail.next = this.itr.next;
		this.itr.next.prev = list.tail;
	} else {
		this.tail = list.tail;
	}
	list.head.prev = this.itr;
	this.itr.next = list.head;
	this.itr = list.tail;

	list.head = null;
	list.tail = null;
	list.itr  = null;
	this._hasChanged = true;
	return true;
}
/**
	* Inserts a linked list @list before the current position of the iterator,
	* points the iterator at the last item inserted and returns true if successful
	*.
	* Returns false if the iterator went past the head or tail of the list,
	* or if either of the lists is empty.
	* @list will be empty after it was inserted. */
LinkedList.prototype.insertListBefore = function(list) {
	if (this.itr === null) return false; // itr went past ends or list is empty
	if (list.tail === null) return false; // list is empty
	if (this.itr.prev !== null) {
		list.head.prev = this.itr.prev;
		this.itr.prev.next = list.head;
	} else {
		this.head = list.head;
	}
	list.tail.next = this.itr;
	this.itr.prev = list.tail;
	this.itr = list.head;

	list.head = null;
	list.tail = null;
	list.itr  = null;
	this._hasChanged = true;
	return true;
}
// returns the number of nodes in the list
/*LinkedList.prototype.length = function() {
	return this.length;
};*/
// returns the position of the iterator in the list
LinkedList.prototype.pos = function() {
	if (this.itr === null) return false;
	var pos = 0;
	var tempItr = this.itr;
	this.reset();
	while (this.itr !== tempItr) {
		this.next();
		pos++;
		if (pos > this.length) return false; // something went wrong...
	}
	this.itr = tempItr;
	return pos;
};
/**
	* If @data is in the list, the iterator is set to point at that
	* position and the function returns the position of data in the list.
	* If @data is not in the list, the function returns false. */
LinkedList.prototype.goTo = function(data) {
	if (this.head === null) return false;
	var pos = 0;
	this.reset();
	do {
		if (this.itr.data === data) return pos;
		else this.itr = this.itr.next;
		pos++;
	} while (this.itr !== null);
	return false;
};
LinkedList.prototype.goToWithWrapper = function(data, sub, sub2) {
	if (this.head === null) return false;
	var pos = 0;
	this.reset();
	do {
		// echo(this.itr.data[sub][sub2])
		if (this.itr.data[sub][sub2] === data) return pos;
		else this.itr = this.itr.next;
		pos++;
	} while (this.itr !== null);
	return false;
};
/**
	* If both @data1 and @data2 are in the list, all data items between the
	* two are removed and returned as a new linked list.
	* If either @data1 or @data2 are not in the list, or the two are the same object,
	* the function returns false.
	* If either of the data items occurs twice in the list, the behaviour is unpredictable. */
LinkedList.prototype.removeBetween = function(data1, data2) {
	if (data1 === data2) return false;
	var data1Pos = this.goTo(data1);
	if (data1Pos === false) return false;
	var data1Itr = this.itr;
	var data2Pos = this.goTo(data2);
	if (data2Pos === false) return false;
	var data2Itr = this.itr;
	var	first, last;
	var newList = new LinkedList();
	if (data1Pos < data2Pos) {
		first = data1Itr;
		last = data2Itr;
	} else {
		first = data2Itr;
		last = data1Itr;
	}
	newList.head = first.next;
	newList.tail = last.prev;
	newList.itr = newList.head;
	newList.head.prev = null;
	newList.tail.next = null;
	first.next = last;
	last.prev = first;
	this._hasChanged = true;
	return newList;
};
/**
	* If @data is in the list, it is removed, and the iterator points at the next
	* item, or null, if @data was at the end of the list. */
LinkedList.prototype.removeData = function(data) {
	if (this.goTo(data) !== false) {
		return this.remove(data);
	} else return false;
};
/**
	* If @data is in the list, @newData is inserted before @data and the iterator
	* points at the newly inserted data item.
	* Returns false, if @data is not in the list. */
LinkedList.prototype.insertBeforeData = function(data, newData) {
	if (this.goTo(data) !== false) {
		return this.insertBefore(newData);
	} else return false;
};
/**
	* If @data is in the list, @newData is inserted after @data and the iterator
	* points at the newly inserted data item.
	* Returns false, if @data is not in the list. */
LinkedList.prototype.insertAfterData = function(data, newData) {
	if (this.goTo(data) !== false) {
		return this.insert(newData);
	} else return false;
};
/**
	* If @data is in the list, the linked list @list is inserted before the position of @data in the list,
	* points the iterator at the last item inserted and returns true if successful
	*.
	* Returns false if @data is not in the list
	* or if either of the lists is empty.
	* @list will be empty after it was inserted. */
LinkedList.prototype.insertListBeforeData = function(data, list) {
	if (this.goTo(data) !== false) {
		return this.insertListBefore(list);
	} else return false;
}
/**
	* If @data is in the list, the linked list @list is inserted after the
	* position of @data in the list.
	* Returns false if @data is not in the list. Males a shallow copy. */
LinkedList.prototype.insertListAfterData = function(data, list) {
	if (this.goTo(data) !== false) {
		return this.insertList(list);
	} else return false;
};
/**
	* Removes all data items in the list and replaces them with the data items
	* in the array. Only copies object references. The array stays in tact. */
LinkedList.prototype.cloneFromArray = function(array) {
	this.clear();
	this.appendArray(array);
};
/**
	* Adds all data in @array to the end of the list. Only copies object references.
	* The array stays in tact. */
LinkedList.prototype.appendArray = function(array) {
	for (var i = 0; i < array.length; i++) {
		this.push(array[i]);
	}
	this._hasChanged = true;
};
/**
	* A representation of the list as array allows random access,
	* until a change has been made to the list structure.
	*
	* Returns an array with all the object-references in the list.
	* The order of the data items is preserved.
	* Changes to objects in the array, but not changes made to their
	* order, will be reflected in the list, and vice versa. Data items
	* added to the array will not be added to the list and vice versa.
	*
	* After a list has been turned into an array, the function .hasChanged()
	* on the list will return false, until a change has been made to the
	* list that does not get reflected in the array
	* (Node added, removed, change in node order.)*/
LinkedList.prototype.getArray = function() {
	var itr = this.itr;
	this.reset();
	var a = [];
	var data = this.next();
	while (data !== null) {
		a.push(data);
		data = this.next();
	}
	this.itr = itr;
	this._hasChanged = false;
	return a;
}