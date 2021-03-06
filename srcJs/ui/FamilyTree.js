/*** include animation/LinearAcceleratedMover.js ***/
/*** include std/EventUtil.js ***/
/*** include std/OnLoadQueue.js ***/

// @author Axel Ancona Esselmann

/**
 * FamilyTree displays hierarchical nodes with two parent nodes as a family tree.
 *
 * Nodes have to have an id, and parent ids.
 * Example:
 * {
 *     id: 23,
 *     father_id: 12,
 *     mother_id:2
 * }
 *
 * cellDrawCallbacks receive the following arguments:
 * element:    The element supplied either with "values" in the constructor, or through setGrid.
 * svgElement: append elements created with document.createElementNS(this.ns, "") to this variable.
 * width:      The width allotted for the cell
 * height:     The height allotted for the cell
 *
 * @param string   targetNodeName   The node to which the tree attaches itself during building
 * @param callback cellDrawCallback callback function called during cell construction
 * @param string   idName           Id name of the node
 * @param string   parent1IdName    Parent id of the node
 * @param string   parent2IdName    Parent id of the node
 * @param array    values           During construction, this is an optional parameter. Call setGrid to supply the grid after construction
 */
function FamilyTree (targetNodeName, cellDrawCallback, idName, parent1IdName, parent2IdName, values) {
    if (values != undefined) this.setGrid(values);
    this.targetNodeName   = targetNodeName;
    this.parent1IdName    = parent1IdName;
    this.parent2IdName    = parent2IdName;
    this.gridResolutionX  = 80;
    this.gridResolutionY  = 80;
    this.cellPadLeft      = 5;
    this.cellPadRight     = 5;
    this.cellPadTop       = 30;
    this.cellPadBottom    = 30;
    this.sibblingPad      = 10;
    this.parentPad        = 15;
    this.cellPositions    = {};
    this.idName           = idName;
    this.svgNode          = null;
    this.elementInFocus   = null;
    this.ns               = "http://www.w3.org/2000/svg";
    this.cellDrawCallback = cellDrawCallback;
}
/**
 * Grid layout specifies:
 *     gridResolutionX : The total length allocated for a cell
 *     gridResolutionY : The total height allocated for a cell
 *     cellPadLeft     : Padding to the left
 *     cellPadRight    : Padding to the right
 *     cellPadTop      : Padding to the top
 *     cellPadBottom   : Padding to the bottom
 *     sibblingPad     : Vertical line length from sibling to parent
 *     parentPad       : Vertical line length from parent to sibling

 * @param {[type]} gridLayout [description]
 */
FamilyTree.prototype.setGridLayout = function(gridLayout) {
    if (gridLayout["gridResolutionX"] != undefined) this.gridResolutionX = gridLayout["gridResolutionX"];
    if (gridLayout["gridResolutionY"] != undefined) this.gridResolutionY = gridLayout["gridResolutionY"];
    if (gridLayout["cellPadLeft"]     != undefined) this.cellPadLeft     = gridLayout["cellPadLeft"];
    if (gridLayout["cellPadRight"]    != undefined) this.cellPadRight    = gridLayout["cellPadRight"];
    if (gridLayout["cellPadTop"]      != undefined) this.cellPadTop      = gridLayout["cellPadTop"];
    if (gridLayout["cellPadBottom"]   != undefined) this.cellPadBottom   = gridLayout["cellPadBottom"];
    if (gridLayout["sibblingPad"]     != undefined) this.sibblingPad     = gridLayout["sibblingPad"];
    if (gridLayout["parentPad"]       != undefined) this.parentPad       = gridLayout["parentPad"];
}
/**
 * See constructor for details
 */
FamilyTree.prototype.setGrid = function(values) {
    var siblings    = {};
    var allNodes    = {};
    var generations = {};
    for (var i = 0; i < values.length; i++) {
        var value  = values[i];
        allNodes[value[this.idName]] = value;
        var siblId = this._getSiblingId(value);
        if (Array.isArray(siblings[siblId])) {
            siblings[siblId].push(value);
        } else {
            siblings[siblId] = [value];
        };
        var parentsGeneration = this._getParentsGeneration(generations, allNodes, value);
        var thisGeneration = parentsGeneration + 1;
        if (generations[siblId] == undefined) {
            generations[siblId] = thisGeneration;
        } else {
            var siblingsGeneration = generations[siblId];
            if (siblingsGeneration < thisGeneration) {
                this._adjustGenerations(siblings, thisGeneration);
            } else {
                // console.log("generations don't have to be adjusted");
            };
        };
    };
    this.siblings = [];
    for (sibs in siblings) {
        var parents   = sibs.split("-");
        // console.log(parents);
        if (parents[0] != "null") {
            var idSiblings = [];
            for (var i = 0; i < siblings[sibs].length; i++) {
                idSiblings.push(siblings[sibs][i][this.idName]);
            }
            this.siblings.push([parents, idSiblings]);
        };
    }
    var genLengths = {}
    var max = 0;
    var nbrGens = 0;
    for (sibId in generations) {
        var genNbr = generations[sibId];
        if (genNbr > nbrGens) {
            nbrGens = genNbr;
        };
        if (genLengths[genNbr] == null) {
            genLengths[genNbr] = 0;
        };
        genLengths[genNbr] += siblings[sibId].length;
        if (genLengths[genNbr] > max) {
            max = genLengths[genNbr];
        };
    }
    this.grid = [];
    for (var rows = 0; rows <= nbrGens; rows++) {
        var row = [];
        this.grid.push(row);
    };
    for (sibId in siblings) {
        var sibs = siblings[sibId];
        var gen = generations[sibId];
        for (var i = 0; i < sibs.length; i++) {
            this.grid[gen].push(sibs[i]);
        };
    }
    for (var rows = 0; rows <= nbrGens; rows++) {
        for (var cols = this.grid[rows].length; cols < max; cols++) {
            this.grid[rows].push(null);
        }
    };
}
/**
 * Build the family tree
 */
FamilyTree.prototype.build = function () {
    if (this.svgNode != null) {
        this.svgNode.parentNode.removeChild(this.svgNode);
        this.svgNode = null;
    };
    this.svgNode = document.createElementNS(this.ns, "svg");
    var ft = this;
    var treeBuildCallback = function() {
        ft.svgNode.setAttribute('class', 'familyTree');
        ft._createCells();
        ft._createConnections();
        var targetNode = document.getElementById(ft.targetNodeName);
        ft.svgNode.setAttribute('width', targetNode.clientWidth);
        ft.svgNode.setAttribute('height', targetNode.clientHeight);
        ft.svgNode.setAttribute('viewBox', 0 + " " + 0 + " " + targetNode.clientWidth + " " +  targetNode.clientHeight);
        targetNode.appendChild(ft.svgNode);
    }
    var body = document.getElementsByTagName('body')[0];
    if (body == null) globalOnLoadQueue.push(treeBuildCallback);
    else treeBuildCallback();
    if (this.elementInFocus) {this.focusOnId(this.elementInFocus)};
}
/**
 * Bring the cell of element with the given id in focus.
 *
 * @param  int  id      Id to be focused on
 * @param  bool animate Optional parameter. Pass true to animate the transition. Requires LinearAcceleratedMover and all its dependencies.
 */
FamilyTree.prototype.focusOnId = function(id, animate) {
    this.elementInFocus = id;
    var ftId            = "ftId_" + id;
    var elementNode     = document.getElementById(ftId);
    var x               = elementNode.x.animVal.value;
    var y               = elementNode.y.animVal.value;
    var widht           = elementNode.width.animVal.value;
    var height          = elementNode.height.animVal.value
    var contNode        = this.svgNode.parentNode
    var newViewBoxX     = (x - (contNode.clientWidth - widht) / 2);
    var newViewBoxY     = (y - (contNode.clientHeight - height) / 2 );
    if (animate) {
        if (window.animator == undefined) {
            window.animator = new Animator();
        };
        lam = new LinearAcceleratedMover();
        var ft = this;
        var moveCallback = function(x, y) {
            var viewBox = x + " " + y + " " + contNode.clientWidth + " " +  contNode.clientHeight;
            ft.svgNode.setAttribute("viewBox", viewBox);
        }
        var oldViewObxString = this.svgNode.getAttribute("viewBox");
        var oldViewBoxValues = oldViewObxString.split(" ");
        var xInit = parseInt(oldViewBoxValues[0]);
        var yInit = parseInt(oldViewBoxValues[1]);
        var acceleration = 5;
        var maxVelocity = 500;
        var initVelocity = 10;
        lam.addCallback(moveCallback, xInit, yInit, newViewBoxX, newViewBoxY, acceleration, maxVelocity, initVelocity);
        window.animator.addMover(lam);
        window.animator.animate();
    } else {
        var viewBox = newViewBoxX + " " + newViewBoxY + " " + contNode.clientWidth + " " +  contNode.clientHeight;
        this.svgNode.setAttribute("viewBox", viewBox);
    };
}
/**
 * A cloned copy of the svg node
 */
FamilyTree.prototype.getSvg = function(x,y,width,height,centerX,centerY) {
    widht     = (width   != undefined) ? width   : this.grid[0].length * this.gridResolutionX;
    height    = (height  != undefined) ? height  : this.grid.length * this.gridResolutionY;
    centerX   = (centerX != undefined) ? centerX : null;
    centerY   = (centerY != undefined) ? centerY : null;
    var clone = this.svgNode.cloneNode(true);
    clone.setAttribute('width', widht);
    clone.setAttribute('height', height);
    if (centerX == null || centerY == null) {
        clone.removeAttribute("viewBox");
    };
    return clone;
}
/**
 * Manually set the grid. Processing must have occurred on the server side.
 * @param array   grid     Nodes. See constructor for format.
 * @param array   siblings Connections. Array of two arrays. First array are the two parent ids. The second array specifies the siblings.
 */
FamilyTree.prototype.setGridAndSiblings = function(grid, siblings) {
    this.grid            = grid;
    this.siblings        = siblings;
}

/******************************************************************************/
/* private functions */
FamilyTree.prototype._getSiblingId = function(value) {
    var par1Id = (value != null) ? value[this.parent1IdName] : "0";
    var par2Id = (value != null) ? value[this.parent2IdName] : "0";
    if (par2Id < par1Id) {
        var temp = par1Id;
        par1Id   = par2Id;
        par2Id   = temp;
    };
    var siblId = par1Id + "-" + par2Id;
    return siblId;
}
FamilyTree.prototype._getParentsGeneration = function(generations, allNodes, value) {
    var parent1          = allNodes[value[this.parent1IdName]];
    var parent2          = allNodes[value[this.parent2IdName]];
    var siblingIdParent1 = this._getSiblingId(parent1);
    var siblingIdParent2 = this._getSiblingId(parent2);
    var parentGenerationId = (generations[siblingIdParent1] > generations[siblingIdParent2]) ? generations[siblingIdParent1] : generations[siblingIdParent2];
    var result = (parentGenerationId != null) ? parentGenerationId : -1;
    return result;
}
FamilyTree.prototype._adjustGenerations = function(siblings, thisGeneration) {
    throw ("not implemented");
}
FamilyTree.prototype._createCell = function (element, rowOffset, columnOffset) {
    var elemX      = columnOffset + this.cellPadRight;
    var elemY      = rowOffset + this.cellPadTop;
    var elemWidth  = this.gridResolutionX - this.cellPadRight - this.cellPadLeft;
    var elemHeight = this.gridResolutionY - this.cellPadTop - this.cellPadBottom;
    var cell = document.createElementNS(this.ns,"svg");
    this.cellDrawCallback(element, cell, elemWidth, elemHeight);
    cell.setAttribute('x',      elemX);
    cell.setAttribute('y',      elemY);
    cell.setAttribute('width',  elemWidth);
    cell.setAttribute('height', elemHeight);
    cell.setAttribute("id", "ftId_" + element[this.idName]);
    this.svgNode.appendChild(cell);
}

FamilyTree.prototype._createCells = function () {
    var svg = "";
    var rowOffset = 0;
    for (var i = 0; i < this.grid.length; i++) {
        var row = this.grid[i];
        var columnOffset = 0;
        for (var j = 0; j < row.length; j++) {
            var element = row[j];
            if (element != null) {
                this._createCell(element, rowOffset, columnOffset);
                this.cellPositions[element[this.idName]] = {"y":i, "x":j};
            };
            columnOffset += this.gridResolutionX;
        };
        rowOffset += this.gridResolutionY;
    };
    return svg;
}
FamilyTree.prototype._getSmallest = function (oldVal, newVal) {
    return (oldVal == null || newVal < oldVal) ? newVal : oldVal;
}
FamilyTree.prototype._getLargest = function (oldVal, newVal) {
    return (oldVal == null || newVal > oldVal) ? newVal : oldVal;
}
FamilyTree.prototype._createConnections = function () {
    var sibStemLength = this.cellPadTop - this.sibblingPad;
    var svg = '';
    for (var i = 0; i < this.siblings.length; i++) {
        var group = document.createElementNS(this.ns,"g");
        group.setAttribute('class', 'ftLines');
        var gridLocations = [];
        var arrayOfSiblings = this.siblings[i];
        // console.log("arrayOfSiblings");
        // console.log(arrayOfSiblings);
        var parents    = arrayOfSiblings[0];
        var siblings   = arrayOfSiblings[1];
        var prev       = null;
        var leftMostSiblX  = null;
        var rightMostSiblX = null;
        var smallestSiblY  = null;
        var largestSiblY   = null;
        for (var j = 0; j < siblings.length; j++) {
            try{
            var sibling    = siblings[j];
            var cellPos    = this.cellPositions[sibling];
            leftMostSiblX  = this._getSmallest(leftMostSiblX, cellPos.x);
            rightMostSiblX = this._getLargest(rightMostSiblX, cellPos.x);
            smallestSiblY  = this._getSmallest(smallestSiblY, cellPos.y);
            largestSiblY   = this._getLargest(  largestSiblY, cellPos.y);

            if (prev != null) {
                var prevX = prev.x * this.gridResolutionX + (this.gridResolutionX/2);
                var prevY = prev.y * this.gridResolutionY + this.sibblingPad;
            };
            if (j > 0 && j < siblings.length - 1) {
                var x1 = cellPos.x * this.gridResolutionX + (this.gridResolutionX/2.0);
                var y1 = cellPos.y * this.gridResolutionY + this.sibblingPad;

                var path = document.createElementNS(this.ns, 'path');
                var d =  'M ' + x1 + ' ' + (y1 + sibStemLength)  +
                        ' l ' + 0  + ' ' + (-sibStemLength);
                path.setAttribute('d', d);
                group.appendChild(path);
            };
            prev = cellPos;
            } catch(e){};
        };

        var sibLineX      = leftMostSiblX * this.gridResolutionX + this.gridResolutionX/2.0;
        var sibLineY      = largestSiblY  * this.gridResolutionY + this.sibblingPad;
        var sibLineLength = (rightMostSiblX - leftMostSiblX) * this.gridResolutionX;
        var path          = document.createElementNS(this.ns, 'path');
        var d = 'M ' + sibLineX      + ' ' + (sibLineY + sibStemLength)  +
               ' l ' + 0             + ' ' + (-sibStemLength) +
               ' l ' + sibLineLength + ' ' + 0 +
               ' l ' + 0 +             ' ' + (sibStemLength);
        path.setAttribute('d', d);
        group.appendChild(path);

        var leftMostParX  = null;
        var rightMostParX = null;
        var smallestParY  = null;
        var largestParY   = null;
        for (var j = 0; j < parents.length; j++) {
            var parent    = parents[j];
            var cellPos   = this.cellPositions[parent];
            leftMostParX  = this._getSmallest(leftMostParX, cellPos.x);
            rightMostParX = this._getLargest(rightMostParX, cellPos.x);
            smallestParY  = this._getSmallest(smallestParY, cellPos.y);
            largestParY   = this._getLargest(  largestParY, cellPos.y);
        };
        var leftParentNbr        = (this.cellPositions[parents[0]].x < this.cellPositions[parents[1]].x) ? 0 : 1;
        var rightParentNbr       = (leftParentNbr == 0) ? 1 : 0;
        var parentYDifference    = Math.abs(this.cellPositions[parents[0]].y - this.cellPositions[parents[1]].y);
        var parentStemDifference = parentYDifference * this.gridResolutionY;
        var parentBaseStemLength = this.cellPadBottom - this.parentPad;
        var parLeftStemLength    = parentBaseStemLength;
        var parRightStemLength   = parentBaseStemLength;
        var parentLineLength     = (this.cellPositions[parents[rightParentNbr]].x - this.cellPositions[parents[leftParentNbr]].x) * this.gridResolutionX;
        if (this.cellPositions[parents[leftParentNbr]].y < this.cellPositions[parents[rightParentNbr]].y) {
            parLeftStemLength += parentStemDifference;
        } else {
            parRightStemLength += parentStemDifference;
        };

        var parX = (this.cellPositions[parents[leftParentNbr]].x * this.gridResolutionX) + (this.gridResolutionX/2.0);
        var parY = (this.cellPositions[parents[leftParentNbr]].y + 1) * this.gridResolutionY -  this.cellPadBottom;
        var path = document.createElementNS(this.ns, 'path');
        var d = 'M ' + parX             + ' ' + parY  +
               ' l ' + 0                + ' ' + parLeftStemLength +
               ' l ' + parentLineLength + ' ' + 0 +
               ' l ' + 0                + ' ' + (-parRightStemLength);
        path.setAttribute('d', d);
        group.appendChild(path);

        var siblCenterX    = (leftMostSiblX + ((rightMostSiblX - leftMostSiblX) / 2.0)) * this.gridResolutionX + (this.gridResolutionX/2.0);
        var siblCenterY1   = largestSiblY * this.gridResolutionX + this.sibblingPad;
        var parCenterX     = (leftMostParX + ((rightMostParX - leftMostParX) / 2.0)) * this.gridResolutionX + (this.gridResolutionX/2.0);
        var parCenterY1    = (largestParY + 1) * this.gridResolutionX - this.parentPad;
        var parSiblCenterY = parCenterY1 + ((siblCenterY1 - parCenterY1) / 2.0);

        var path = document.createElementNS(this.ns, 'path');
        var d = 'M ' + siblCenterX                + ' ' + siblCenterY1  +
               ' l ' + 0                          + ' ' + (parSiblCenterY - siblCenterY1) +
               ' l ' + (parCenterX - siblCenterX) + ' ' + 0 +
               ' l ' + 0                          + ' ' + (parCenterY1 - parSiblCenterY);
        path.setAttribute('d', d);
        group.appendChild(path);
        this.svgNode.appendChild(group);
    };
    return svg;
}