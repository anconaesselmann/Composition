// @author Axel Ancona Esselmann

Node = function(data, next, prev) {
    this.data = data;
    this.next = next;
    this.prev = prev;
}

/**
 * A doubly linked list
 */
LinkedList = function() {
    this.head    = null;
    this.tail    = null;
    this.current = null;
    this.from    = null;
    this.length  = 0;
};
/**
 * inserts a new item at the end of the list. peak() will return this item.
 */
LinkedList.prototype.append = function(item) {
    var newNode = new Node(item,null,null);
    if (this.head == null) {
        this.head = newNode;
        this.tail = newNode;
    } else {
        this.tail.next = newNode;
        newNode.prev   = this.tail;
        this.tail      = newNode;
    }
    this.length += 1;
    this.current = newNode;
};
/**
 * inserts a new item at the beginning of the list. peak() will return this item.
 */
LinkedList.prototype.prepend = function(item) {
    var newNode = new Node(item,null,null);
    if (this.head == null) {
        this.head = newNode;
        this.tail = newNode;
    } else {
        this.head.prev = newNode;
        newNode.next   = this.head;
        this.head      = newNode;
    }
    this.length += 1;
    this.current = newNode;
};
/**
 * removes an item from the end of the list and returns the item.
 * peak() will return the new last item.
 */
LinkedList.prototype.pop = function(item){
    if (this.tail == null) return null;
    var returnNode = this.tail;
    var newTail    = returnNode.prev;
    if (this.tail == this.head) {
        this.head = null;
        this.tail = null;
    } else {
        this.tail    = newTail;
        newTail.next = null;
    }
    this.current = this.tail;
    this.length -= 1;
    return returnNode.data;
};
/**
 * peek() will return the first item in the list
 */
LinkedList.prototype.reset = function() {
    this.current = this.head;
    this.from = null;
}
/**
 * peek() will return the last item in the list
 */
LinkedList.prototype.resetEnd = function() {
    this.current = this.tail;
    this.from = null;
}
/**
 * returns the current item. (the last inserted, or the one advanced to with next or prev)
 */
LinkedList.prototype.peek = function() {
    return (this.current != null) ? this.current.data : null;
}
/**
 * returns the next item without advancing the position in the list
 */
LinkedList.prototype.peekNext = function() {
    if (this.current == null) return null;
    if (this.current.next == null) return null
    return this.current.next.data;
}
/**
 * returns true if there is a next item. Returns true when the iterator fell off past the beginning of the list.
 */
LinkedList.prototype.hasNext = function() {
    if (this.current == null && this.from != null && this.from == this.head) return true;
    return (this.current == null || this.current.next == null) ? false : true;
}
/**
 * returns CURRENT item and advances the iterator forward.
 * peek() will return the next item.
 * @return null when no more items
 */
LinkedList.prototype.next = function() {
    if (this.current == null && this.from != null && this.from == this.head) {
        this.current = this.from;
        return null;
    }
    var current     = this.current;
    var currentData = this.peek();
    this.current    = (current != null && current.next != null) ? current.next : null;
    if (this.current == null) this.from = current;
    return currentData;
}
/**
 * returns the previous item without decrementing the iterator
 */
LinkedList.prototype.peekPrev = function() {
    if (this.current == null) return null;
    if (this.current.prev == null) return null
    return this.current.prev.data;
}
/**
 * returns true if there is a previous item. Returns true when the iterator fell off past the end of the list.
 */
LinkedList.prototype.hasPrev = function() {
    if (this.current == null && this.from != null && this.from == this.tail) return true;
    return (this.current == null || this.current.prev == null) ? false : true;
}
/**
 * returns CURRENT item and moves the iterator backwards.
 * peek() will return the previous item.
 * @return null when no more items
 */
LinkedList.prototype.prev = function() {
    if (this.current == null && this.from != null && this.from == this.tail) {
        this.current = this.from;
        return null;
    }
    var current     = this.current;
    var currentData = this.peek();
    this.current    = (current != null && current.prev != null) ? current.prev : null;
    if (this.current == null) this.from = current;
    return currentData;
}
/**
 * returns item at the head without changing the position of the iterator
 */
LinkedList.prototype.peekHead = function() {
    return (this.head != null) ? this.head.data : null;
}
/**
 * returns item at the tail without changing the position of the iterator
 */
LinkedList.prototype.peekTail = function() {
    return (this.tail != null) ? this.tail.data : null;
}
/**
 * returns true when the end of the list is reached. This means peak() returns null
 * and there was a previous item
 */
LinkedList.prototype.eof = function() {
    return (this.current == null && this.from == this.tail);
}
/**
 * returns true when the beginning of the list is reached. This means peak() returns null
 * and there was a next item
 */
LinkedList.prototype.bof = function() {
    return (this.current == null && this.from == this.head);
}
/**
 * removes the current item. The iterator will point to the previous item.
 */
LinkedList.prototype.remove = function() {
    if (this.current == null) return null;
    var currentNode = this.current;
    var prevNode    = currentNode.prev;
    var nextNode    = currentNode.next;
    if (nextNode !== null && prevNode != null) {
        prevNode.next = nextNode;
        nextNode.prev = prevNode;
        this.current  = prevNode;
    } else if (nextNode == null && prevNode != null) {
        prevNode.next = null;
        this.tail     = prevNode;
        this.current  = prevNode;
    } else if (nextNode != null && prevNode == null){
        nextNode.prev = null;
        this.head     = nextNode;
        this.current  = nextNode;
    } else {
        this.head    = null;
        this.tail    = null;
        this.current = null;
        this.from    = null;
    }
    this.length -= 1;
    return currentNode.data;
}
/**
 * inserts after the current position. peek() will return this new item
 */
LinkedList.prototype.insertAfter = function(item) {
    if (!this.hasNext()) {
        this.append(item);
    } else {
        var nextNode    = this.current.next;
        var currentNode = this.current;
        var newNode     = new Node(
            item,
            nextNode,
            currentNode);
        currentNode.next = newNode;
        nextNode.prev    = newNode;
        this.current     = newNode;
        this.length += 1;
    }
}
/**
 * insert before the current position. peek() will return this item.
 */
LinkedList.prototype.insertBefore = function(item) {
    if (!this.hasPrev()) {
        this.prepend(item);
    } else {
        var prevNode    = this.current.prev;
        var currentNode = this.current;
        var newNode     = new Node(
            item,
            currentNode,
            prevNode);
        currentNode.prev = newNode;
        prevNode.next    = newNode;
        this.current     = newNode;
        this.length += 1;
    }
}
/**
 * test if the item is in the list. For objects, it checks for identical instances
 * complexity: n
 */
LinkedList.prototype.hasItem = function(item) {
    var current = this.head;
    while (current != null) {
        if (current.data == item) return true;
        current = current.next;
    }
    return false;
}
LinkedList.prototype.getPositionPointer = function() {
    return this.current;
}
LinkedList.prototype.setPositionPointer = function(pp) {
    this.current = pp;
}
LinkedList.prototype.getSubArray = function(item1, item2) {
    var current = this.head;
    var returnArray = [];
    var start = false;
    var stop  = false;
    while (current != null) {
        if (current.data == item1 || current.data == item2) {
            if (start) stop = true;
            else {
                start = true;
                if (item1 == item2) stop = true;
            }
        }
        if (start) {
            returnArray.push(current.data);
            if (stop) break;
        };
        current = current.next;
    }
    if (!stop) return [];
    return returnArray;
}
LinkedList.prototype.goToItem = function(item) {
    var current = this.head;
    while (current != null) {
        if (current.data == item) {
            this.current = current;
            return;
        };
        current = current.next;
    }
}
LinkedList.prototype.removeRangeInclusive = function(item1, item2) {
    var subArray = this.getSubArray(item1, item2);
    if (subArray.length > 0) {
        this.goToItem(subArray[0]);
        for (var i = 0; i < subArray.length; i++) {
            this.remove();
            this.next();
        }
    };
    return subArray;
}

