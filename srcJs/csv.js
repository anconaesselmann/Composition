
// @author Axel Ancona Esselmann

var _idCounter = 0;
function addTextField(id, parentId, labelText) {
	var fullId = id + _idCounter++;
	var input = document.createElement('input');
	input.setAttribute('id', fullId);
	input.setAttribute('type', 'text');
	input.setAttribute('name', fullId);

	var label = document.createElement('label');
	label.setAttribute('for', fullId);
	label.innerHTML=labelText;

	var parent = document.getElementById(parentId);
	parent.appendChild(label);
	parent.appendChild(input);
}
function addRowToAttribute() {
	var fullId = 'rtaElement' + _idCounter++;
	var div = document.createElement('div');
	div.setAttribute('class', 'rtaElement jsAddedOptionElement');
	div.setAttribute('id', fullId);
	var parent = document.getElementById('rta');
	parent.appendChild(div);

	addTextField('attrNodeName', fullId, 'Target Node');
	addTextField('attrName', fullId, 'Attribute Name');
	addTextField('colName', fullId, 'Column Name');

	addRemoveButton(fullId);
}
function removeButton(id) {
	var element = document.getElementById(id);
	var parent = element.parentNode;
	parent.removeChild(element);
}
function addRemoveButton(id) {
	var div = document.createElement('div');
	div.setAttribute('class', 'removeButton jsButton');
	div.setAttribute('id', id+'removeButton');
	div.setAttribute('onclick', 'removeButton("'+id+'")');
	div.innerHTML = "-";
	var parent = document.getElementById(id);

	parent.appendChild(div);

	//parent.parentNode.insertBefore(div, parent.nextSibling);
}
function addRowToGroup() {
	var fullId = 'tGroupsElement' + _idCounter++;
	var div = document.createElement('div');
	div.setAttribute('class', 'tGroupsElement jsAddedOptionElement');
	div.setAttribute('id', fullId);
	var parent = document.getElementById('tGroups');
	parent.appendChild(div);

	addTextField('parent', fullId, 'Parent Node');
	addTextField('child', fullId, 'Child Node');

	addRemoveButton(fullId);
}
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime()+(exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i].trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	}
	return "";
}
function showCsvOptions() {
	var element = document.getElementById('advancedCsvOptions');
	var text = document.getElementById('advancedToggle');
    var style = window.getComputedStyle(element);
	var display = element.style.display;
	element.style.display = "block";
	text.innerHTML ="Hide Advanced";
	setCookie("advancedCsvOptions", 1, 31);
}
function hideCsvOptions() {
	var element = document.getElementById('advancedCsvOptions');
	var text = document.getElementById('advancedToggle');
    var style = window.getComputedStyle(element);
	var display = element.style.display;
	element.style.display = "none";
	text.innerHTML = "Show Advanced";
	setCookie("advancedCsvOptions", 0, 31);
}
function toggleCsvOptions() {
	var element = document.getElementById('advancedCsvOptions');
	var text = document.getElementById('advancedToggle');
    var style = window.getComputedStyle(element);
	var display = element.style.display;
	if	(display == "none") {
		showCsvOptions();
	} else {
		hideCsvOptions();
	}
}

function _getInputTextInsideDivsNameValueUriPairs(nodes) {
	var result = "";
	for (var i=0; i < nodes.length; i++) {
		var div = nodes[i];
		if	(div.tagName == "DIV") {
			var divChildren = div.childNodes;
			for (var k=0; k < divChildren.length; k++) {
				var child = divChildren[k];
				if (child.tagName == "INPUT") {
					result += "&" + child.name + "=" + child.value;
				}
			}
		}
	}
	return result;
}

function updateDirectLink() {
	var linkString = "http://" + window.location.host + window.location.pathname;

	var rootNameElement = document.getElementById('rootName');
	linkString += "?" + rootNameElement.name + "=" + rootNameElement.value;

	var rowElement = document.getElementById('elementName');
	linkString += "&" + rowElement.name + "=" + rowElement.value;

	var rtaChildNodes = document.getElementById('rta').childNodes;
	linkString += _getInputTextInsideDivsNameValueUriPairs(rtaChildNodes);
	var tGroupsChildNodes = document.getElementById('tGroups').childNodes;
	linkString += _getInputTextInsideDivsNameValueUriPairs(tGroupsChildNodes);

	var linkElement = document.getElementById('directLink');
	linkElement.innerHTML = linkString;
	linkElement.setAttribute('href', linkString);
}

function csvOnLoad() {
	if	(getCookie("advancedCsvOptions") == 1) {
		showCsvOptions();
		updateDirectLink();
	}
}

window.addEventListener('load', csvOnLoad, false);
