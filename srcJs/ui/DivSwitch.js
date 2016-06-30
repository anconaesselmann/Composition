/*** include std/_.js ***/
// @author Axel Ancona Esselmann

/**
 * A DivSwitch allows one div element that is part of a grouping to gain an active status.
 * The last class name provided with divClassName is a toggle element, which means it gains
 * active status when the active object is marked as active.
 *
 */
DivSwitch = function(htmlElement, divClassNames, activeClassName, activateLastDivOnToggle) {
    this.container    = htmlElement;
    this.elementNames = [];
    for (var i = 0; i < divClassNames.length; i++) {
        this[divClassNames[i]] = this.container.getChild('.'+divClassNames[i]);
        this.elementNames.push(divClassNames[i]);
    };
    this.defaultElement  = this[divClassNames[divClassNames.length - 1]];
    this.activeClassName = activeClassName;
    this.activateLastDivOnToggle = (activateLastDivOnToggle == undefined) ? false : activateLastDivOnToggle;
    return this;
}
DivSwitch.prototype.set = function(status){
    var active = this[status];
    var toggled = active.toggle('.' + this.activeClassName);
    for (var i = 0; i < this.elementNames.length; i++) {
        if (status != this.elementNames[i]) this[this.elementNames[i]].unset('.' + this.activeClassName);
    };
    if (this.activateLastDivOnToggle) {
        if (!toggled) {
            this.defaultElement.toggle('.' + this.activeClassName);
        } else this.defaultElement.unset('.' + this.activeClassName);
    };
    return toggled;
};