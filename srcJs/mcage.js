/*** include ui/ContMenu.js ***/
/*** include ui/PopUp.js ***/
/*** include ui/FamilyTree.js ***/
/*** include ui/ColorSelector.js ***/

// @author Axel Ancona Esselmann

function createCORSRequest(method, url) {
	var xhr = new XMLHttpRequest();
	if ("withCredentials" in xhr) {
		xhr.open(method, url, true); // Chrome/Firefox/Opera/Safari
	} else if (typeof XDomainRequest != "undefined") {
		xhr = new XDomainRequest(); // IE
		xhr.open(method, url);
	} else {
		console.log("CORS not supported");
		return null;
	}
	return xhr;
}

function getParentWithClassName(element, className) {
	do {
		try {
			if (element.classList.contains(className)) return element;
			element = element.parentNode;
		} catch(err) {
			return null;
		}
	} while (element);
}

function findClass(element, className) {
    var foundElement = null, found;
    function recurse(element, className, found) {
        for (var i = 0; i < element.childNodes.length && !found; i++) {
            var el = element.childNodes[i];
            var classes = el.className != undefined? el.className.split(" ") : [];
            for (var j = 0, jl = classes.length; j < jl; j++) {
                if (classes[j] == className) {
                    found = true;
                    foundElement = element.childNodes[i];
                    break;
                }
            }
            if(found)
                break;
            recurse(element.childNodes[i], className, found);
        }
    }
    recurse(element, className, false);
    return foundElement;
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function getQueryString() {
	var result = window.location.search;
	if (result.length < 1) result = "?";
	return result;
}

function addCage() {
	console.log(window.location.search);
	var uri = "/mouse/addCage" + window.location.search;
	var xhr    = createCORSRequest('GET', uri);
	xhr.setRequestHeader("Content-Type","text/html-fragment");
	if (!xhr) return;
	xhr.onload = function() {
		var tempNode       = document.createElement('div');
		tempNode.innerHTML = xhr.responseText;
		var cage           = findClass(tempNode, "cage");
		var cages          = document.getElementById("cages");
		cages.appendChild(cage);
		window.cageContMenu.linkElement(cage);
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}

function getCage(cageNbr) {
	var uri = "/mouse/getCage/" + cageNbr + window.location.search;
	var xhr    = createCORSRequest('GET', uri);
	xhr.setRequestHeader("Content-Type","application/json");
	if (!xhr) return;
	xhr.onload = function() {
		var response = JSON.parse(xhr.responseText);
		addHtmlCage(response.response);
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}

function addHtmlCage(cage) {
	console.log(cage.cage_id);
	var cages   = document.getElementById("cages");
	var cageDiv = document.createElement("div");
	cageDiv.setAttribute('class', 'cage');
	cageDiv.setAttribute('id', 'cage_' + cage.cage_id);
	var cageNameDiv = document.createElement("div");
	cageNameDiv.setAttribute('class', 'cageName');
	cageNameDiv.innerHTML = cage.cage_name;
	var cageContDiv = document.createElement("div");
	cageContDiv.setAttribute('class', 'cageCont');
	cageDiv.appendChild(cageNameDiv);
	cageDiv.appendChild(cageContDiv);
	cages.appendChild(cageDiv);
}


function ajaxFormSubmitCreateNewMouse(formName) {
	var cageNbr  = document[formName].cage.value;
	var sex      = document[formName].sex.value;
	var genotype = document[formName].genotype.value;
	var litter   = -1;
	console.log(sex);
	console.log(genotype);

	var url   = "/mouse/submit_new_mouse/" + cageNbr + "/" + sex + "/" + genotype + "/" + litter + window.location.search;
	var xhr      = createCORSRequest('GET', url);
	xhr.setRequestHeader("Content-Type","text/html-fragment");
	if (!xhr) return;
	xhr.onload = function() {
		var tempNode       = document.createElement("div");
		tempNode.innerHTML = xhr.responseText;
		console.log(xhr.responseText);
		var mouseNode      = findClass(tempNode, "mouse");
		var cageNode       = document.getElementById("cage_" + cageNbr);
		var cageContNode   = findClass(cageNode, 'cageCont');
		cageContNode.appendChild(mouseNode);
		window.mcm.linkElement(mouseNode);
		addMouseInspectorCallback(mouseNode);
		var popUp = document.getElementById("popUp");
		popUp.parentNode.removeChild(popUp);

		updateTree();
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
	return false;
}
function ajaxFormSubmitCreateNewLitter(formName) {
	var cageNbr   = document[formName].cage.value;
	var mother    = document[formName].mother.value;
	var father    = document[formName].father.value;
	var nbrPups   = document[formName].nbrPups.value;
	var birthDate = document[formName].birthDate.value;

	console.log(birthDate);

	var url   = "/mouse/submit_new_litter/" + cageNbr + "/" + mother + "/" + father + "/" + nbrPups + "/" + birthDate + "/" + window.location.search;
	var xhr      = createCORSRequest('GET', url);
	xhr.setRequestHeader("Content-Type","text/html-fragment");
	if (!xhr) return;
	xhr.onload = function() {
		var tempNode       = document.createElement("div");
		tempNode.innerHTML = xhr.responseText;
		console.log(tempNode);
		var cageNode     = document.getElementById("cage_" + cageNbr);
		var cageContNode = findClass(cageNode, 'cageCont');
		var mouseNodes = tempNode.childNodes;
		for (var i = 0; i < mouseNodes.length; i++) {
			var mouseNode = mouseNodes[i];
			try {
				if (mouseNode.classList.contains("mouse")) {
					cageContNode.appendChild(mouseNode);
					window.mcm.linkElement(mouseNode);
					addMouseInspectorCallback(mouseNode);
				};
			} catch (err) {};
		};
		var popUp = document.getElementById("popUp");
		popUp.parentNode.removeChild(popUp);

		updateTree();
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
	return false;
}


function ajaxFormSubmitEditNewMouse(formName) {
	var sex      = document[formName].sex.value;
	var genotype = document[formName].genotype.value;
	var mouseId  = document[formName].mouse_id.value;
	var url      = "/mouse/submit_edit_mouse/" + mouseId + "/"+ sex + "/" + genotype + window.location.search;
	var xhr      = createCORSRequest('GET', url);
	xhr.setRequestHeader("Content-Type","text/html-fragment");
	if (!xhr) return;
	xhr.onload = function() {
		var tempNode       = document.createElement("div");
		tempNode.innerHTML = xhr.responseText;
		var newMouseNode      = findClass(tempNode, "mouse");
		var oldMouseNode   = document.getElementById(newMouseNode.id);
		var cageContNode   = oldMouseNode.parentNode;
		cageContNode.replaceChild(newMouseNode, oldMouseNode);
		window.mcm.linkElement(newMouseNode);
		addMouseInspectorCallback(newMouseNode);
		console.log("mouse " + mouseId + " updated");
		updateTree();
		highlightMouse(newMouseNode);
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
	return false;
}
function addMouseCreateNew(event) {
	var cage     = getParentWithClassName(event.toElement, "cage");
	if (cage == null) {console.log("Error retrieving cage.");return;};
	var cageId   = cage.id;
	var cageNbr  = getNbrFromId(cageId);
	console.log("adding new mouse to cage " + cageNbr);

	var url = "/mouse/new_mouse/" + cageNbr + "/" + window.location.search;
	var xhr = createCORSRequest('GET', url);
	if (!xhr) return;
	xhr.onload = function() {
		var popUp = new PopUp(event.x, event.y, xhr.responseText, "main");
		var form = document.getElementsByName("newMouseForm")[0];
		form.setAttribute('onSubmit', 'return ajaxFormSubmitCreateNewMouse("newMouseForm")');
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}

function getNbrFromId(idString) {
	var position = 0;
	position     = idString.indexOf("_");
	return parseInt(idString.slice(position + 1));
}

function deleteCage(event) {
	var cage     = getParentWithClassName(event.toElement, "cage");
	if (cage == null) {console.log("Error retrieving cage.");return;};
	var cageId   = cage.id;
	var cageNbr  = getNbrFromId(cageId);
	var uri   = "/mouse/removeCage/" + cageNbr + window.location.search;
	var xhr      = createCORSRequest('GET', uri);
	xhr.setRequestHeader("Content-Type","application/json");
	if (!xhr) return;
	xhr.onload = function() {
		var response = JSON.parse(xhr.responseText);
		console.log(response);
		if (response.response == true) {
			console.log("deleting cage " + cageNbr);
			cage.parentNode.removeChild(cage);
		} else {
			console.log("error removing cage " + cageNbr);
		}
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}
function moveMouse(event, from) {
	console.log(from);
	var mouse      = getParentWithClassName(event.toElement, "mouse");
	if (mouse == null) {console.log("Error retrieving mouse.");return;};
	var mouseId    = mouse.id;
	var mouseNbr   = getNbrFromId(mouseId);
	var cageOrigin = getParentWithClassName(event.toElement, "cage");
	if (cageOrigin == null) {console.log("Error retrieving cageOrigin.");return;};
	var cageOriginId  = cageOrigin.id;
	var cageOriginNbr = getNbrFromId(cageOriginId);
	var cageDestNbr   = getNbrFromId(from.name);
	var uri        = "/mouse/move_mouse_to_cage/" + mouseNbr + "/" + cageDestNbr + window.location.search;
	var xhr           = createCORSRequest('GET', uri);
	xhr.setRequestHeader("Content-Type","application/json");
	if (!xhr) return;
	xhr.onload = function() {
		var response = JSON.parse(xhr.responseText);
		var destCage = findClass(document.getElementById("cage_" + cageDestNbr), "cageCont");
		var mouse    = document.getElementById(mouseId);
		destCage.appendChild(mouse);
		console.log("\n\nRESPONSE:\n\n");
		console.log(uri);
		console.log(from);
		console.log(response);
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}
function editMouse(event) {
	var mouse    = getParentWithClassName(event.toElement, "mouse");
	if (mouse == null) {console.log("Error retrieving mouse.");return;};
	var mouseId  = mouse.id;
	var mouseNbr = getNbrFromId(mouseId);
	console.log("editing " + mouseNbr);

	var url = "/mouse/edit_mouse/" + mouseNbr + "/" + window.location.search;
	var xhr = createCORSRequest('GET', url);
	if (!xhr) return;
	xhr.onload = function() {
		var popUp      = new PopUp(event.x, event.y, xhr.responseText, "main");
		var form       = document.getElementsByName("editMouseForm")[0];
		var currentSex = form.current_sex.value;
		if (currentSex == 1) {
			document.getElementById("rbMale").checked = true;
		} else if (currentSex == 2) {
			document.getElementById("rbFemale").checked = true;
		}
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}
function createGenotype(event) {
	var formName = "editMouseForm";
	console.log(event);
	var input = document[formName].newGenotypeName;
	var newGenotypeName = input.value;
	var genR = document[formName].genR.value;
	var genG = document[formName].genG.value;
	var genB = document[formName].genB.value;
	if (input.style.display == "none") {
		input.style.display = "block";

		var genColorCont = document.getElementById("genTypeColorSelector");
		genColorCont.style.display = "block";
		var onChangeCallback = function(r, g, b) {
			console.log(r, g, b);
			document[formName].genR.value = r;
			document[formName].genG.value = g;
			document[formName].genB.value = b;
		}
		var colorSelector = new RgbColorSelector(50,100,250, onChangeCallback);
		colorSelector.draw(genColorCont);
	} else {
		var color = genR + "," + genG + "," + genB;
		var uri = "/mouse/create_genotype/" + newGenotypeName + "/" + color + window.location.search;
		var xhr    = createCORSRequest('GET', uri);
		xhr.setRequestHeader("Content-Type","application/json");
		if (!xhr) return;
		xhr.onload = function() {
			var response = JSON.parse(xhr.responseText);
			console.log(response);
			if (response.response > 0) {
				var genotypeId = response.response;
				var selectElem = document[formName].genotype;
				var newOptElem = new Option(newGenotypeName, genotypeId);
				selectElem.options.add(newOptElem);
				selectElem.value    = genotypeId;
				input.style.display = "none";
				ajaxFormSubmitEditNewMouse(formName);
			} else {
				console.log("error adding new genotype");
			}
		};
		xhr.onerror = function() {
			console.log("There was an error with CORS request");
		};
		xhr.send();
	};
}
function deceasedMouse(event) {
	var mouse      = getParentWithClassName(event.toElement, "mouse");
	if (mouse == null) {console.log("Error retrieving mouse.");return;};
	var mouseId    = mouse.id;
	var mouseNbr   = getNbrFromId(mouseId);
	console.log(mouseNbr);

	var uri = "/mouse/deceased_mouse/" + mouseNbr + window.location.search;
	var xhr    = createCORSRequest('GET', uri);
	xhr.setRequestHeader("Content-Type","application/json");
	if (!xhr) return;
	xhr.onload = function() {
		var response = JSON.parse(xhr.responseText);
		if (response.response) {
			mouse.parentNode.removeChild(mouse);
		} else {
			console.log("error removing mouse " + mouseNbr);
			console.log(response.errorMessage);
		};
		updateTree();
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}
function deleteMouse(event) {
	var mouse      = getParentWithClassName(event.toElement, "mouse");
	if (mouse == null) {console.log("Error retrieving mouse.");return;};
	var mouseId    = mouse.id;
	var mouseNbr   = getNbrFromId(mouseId);
	console.log(mouseNbr);

	var uri = "/mouse/delete_mouse/" + mouseNbr + window.location.search;
	var xhr    = createCORSRequest('GET', uri);
	xhr.setRequestHeader("Content-Type","application/json");
	if (!xhr) return;
	xhr.onload = function() {
		var response = JSON.parse(xhr.responseText);
		if (response.response) {
			mouse.parentNode.removeChild(mouse);
		} else {
			console.log("error removing mouse " + mouseNbr);
			console.log(response.errorMessage);
		};

	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}
function newLitter(event) {
	var cage     = getParentWithClassName(event.toElement, "cage");
	if (cage == null) {console.log("Error retrieving cage.");return;};
	var cageId   = cage.id;
	var cageNbr  = getNbrFromId(cageId);
	console.log("Adding new litter to cage " + cageNbr);

	var url = "/mouse/new_litter/" + cageNbr + "/" + window.location.search;
	var xhr = createCORSRequest('GET', url);
	if (!xhr) return;
	xhr.onload = function() {
		var popUp = new PopUp(event.x, event.y, xhr.responseText, "main");
		var form = document.getElementsByName("newLitterForm")[0];
		form.setAttribute('onSubmit', 'return ajaxFormSubmitCreateNewLitter("newLitterForm")');
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}

window.cageContMenu = new ContMenu("cageContMenu", 0, 0, "settings", false);
window.cageContMenu.connectElement("amcn", "addMouseCreateNew");
window.cageContMenu.connectElement("remCage", "deleteCage");
window.cageContMenu.connectElement("newLitter", "newLitter");

window.mcm = new ContMenu("mouseContMenu", 0, 0, "mouse");
var mcm_subCallback = function (event) {
	var mouse    = getParentWithClassName(event.toElement, "mouse");
	if (mouse == null) {console.log("Error retrieving mouse.");return;};
	var mouseId  = mouse.id;
	var mouseNbr = getNbrFromId(mouseId);
	console.log("moving mouse " + mouseNbr);

	var mcm_sub = new ContMenu("cageSelectionSubMenu", 0, 0, false);
	loadCagesForSubMenu(mcm_sub, mouseNbr);
	console.log(event);
	return mcm_sub;
};
window.mcm.connectSubHeaderElement("moveMouse", mcm_subCallback);
window.mcm.connectElement("deleteMouse", "deleteMouse");
window.mcm.connectElement("deceasedMouse", "deceasedMouse");

function loadCagesForSubMenu(subMenu, mouseId) {
	var uri = "/mouse/getCages/" + window.location.search;
	var xhr    = createCORSRequest('GET', uri);
	xhr.setRequestHeader("Content-Type","application/json");
	if (!xhr) return;
	xhr.onload = function() {
		var response = JSON.parse(xhr.responseText);
		for (var i = 0; i < response.response.length; i++) {
			console.log(response.response[i]);
			subMenu.addElement("mmc_" + response.response[i]["cage_id"], "moveMouse", response.response[i]["cage_name"]);
		};
		subMenu.build();
		subMenu.hide(false);
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}



function updateTree(event) {
	var uri = "/mouse/get_all_mice/" + window.location.search;
	var xhr    = createCORSRequest('GET', uri);
	xhr.setRequestHeader("Content-Type","application/json");
	if (!xhr) return;
	xhr.onload = function() {
		var response = JSON.parse(xhr.responseText);
		var mice = response.response;
		window.familyTree.setGrid(mice);
		window.familyTree.build();
	};
	xhr.onerror = function() {
		console.log("There was an error with CORS request");
	};
	xhr.send();
}

var drawFunction = function(element, svgElement, width, height) {
	var birtDateLabel  = "Born";
	var deathDateLabel = "Died";
	var genotypeLabel  = "Genotype";
	var aliveLabel     = "alive";
	var headerHeight   = 12;
	var color          = "white";
	if (element["birth_date"]["month"]) {
		var birtDateString = element["birth_date"]["month"] + "/" + element["birth_date"]["day"] + "/" + element["birth_date"]["year"];
	} else {
		var birtDateString = "?";
	};
	if (element["time_deceased"]["month"]) {
		var deathDateString = element["time_deceased"]["month"] + "/" + element["time_deceased"]["day"] + "/" + element["time_deceased"]["year"];
	} else {
		var deathDateString = aliveLabel;
	};

	var rect  = document.createElementNS(this.ns,"rect");
	rect.setAttribute('x',      0);
	rect.setAttribute('y',      0);
	rect.setAttribute('width',  width);
	rect.setAttribute('height', height);
	rect.setAttribute('class', 'svgSexId' + element["sex"]);
	svgElement.appendChild(rect);

	var headerRect  = document.createElementNS(this.ns,"rect");
	headerRect.setAttribute('x',      0);
	headerRect.setAttribute('y',      0);
	headerRect.setAttribute('width',  width);
	headerRect.setAttribute('height', headerHeight);
	headerRect.setAttribute('class', 'svgSexId' + element["sex"]);
	svgElement.appendChild(headerRect);

	var text = document.createElementNS(this.ns,"text");
	text.setAttribute('x', 5);
	text.setAttribute('y', headerHeight - 2);
	text.setAttribute('font-family', 'Verdana');
	text.setAttribute('font-size', headerHeight-2);
	var textNode = document.createTextNode(element["mouse_id"]);
	text.appendChild(textNode);
	svgElement.appendChild(text);

	var text = document.createElementNS(this.ns,"text");
	text.setAttribute('x', 5);
	text.setAttribute('y', headerHeight + 12);
	text.setAttribute('font-family', 'Verdana');
	text.setAttribute('font-size', headerHeight-2);
	var textNode = document.createTextNode(birtDateLabel + ": " + birtDateString);
	text.appendChild(textNode);
	svgElement.appendChild(text);

	var text = document.createElementNS(this.ns,"text");
	text.setAttribute('x', 5);
	text.setAttribute('y', headerHeight + 22);
	text.setAttribute('font-family', 'Verdana');
	text.setAttribute('font-size', headerHeight-2);
	var textNode = document.createTextNode(deathDateLabel + ": " + deathDateString);
	text.appendChild(textNode);
	svgElement.appendChild(text);

	var text = document.createElementNS(this.ns,"text");
	text.setAttribute('x', 5);
	text.setAttribute('y', headerHeight + 32);
	text.setAttribute('font-family', 'Verdana');
	text.setAttribute('font-size', headerHeight-2);
	var textNode = document.createTextNode(genotypeLabel + ": ");
	text.appendChild(textNode);
	svgElement.appendChild(text);

	if (element["genotype_color"] != null) {
		color = element["genotype_color"];
	};
	if (element["genotype_name"] != null) {
		var genName     = element["genotype_name"];
		var genNameNode =  document.createElementNS(this.ns,"text");
		genNameNode.setAttribute('x',      5);
		genNameNode.setAttribute('y',      height - 5);
		genNameNode.setAttribute('font-family', 'Verdana');
		genNameNode.setAttribute('font-size', '.7em');
		genNameNode.setAttribute('style', 'color"rgb(' + color + ')"');
		genNameNode.setAttribute('fill', 'rgb(' + color + ')');
		var textNode = document.createTextNode(genName);
		genNameNode.appendChild(textNode);
		svgElement.appendChild(genNameNode);
	};
}

function highlightMouse(mouse) {
	window.cageContMenu.hide();
	if (mouse.classList.contains("mouseSelected")) return;
	var selected = document.getElementsByClassName('mouseSelected');
	for (var j = 0; j < selected.length; j++) {
		var old_element = selected[j]
		old_element.classList.remove("mouseSelected");
		// var new_element = old_element.cloneNode(true);
		// old_element.parentNode.replaceChild(new_element, old_element);
		// addMouseInspectorCallback(new_element);
	};
	mouse.className += " mouseSelected";

	var mouseId  = mouse.id;
	var mouseNbr = getNbrFromId(mouseId);
	window.familyTree.focusOnId(mouseNbr, true);

	// var mcm = new ContMenu("mouseContMenu", 0, 0, "mouseSelected", false);
	// var mcm_subCallback = function (event) {
	// 	var mouse    = getParentWithClassName(event.toElement, "mouse");
	// 	if (mouse == null) {console.log("Error retrieving mouse.");return;};
	// 	var mouseId  = mouse.id;
	// 	var mouseNbr = getNbrFromId(mouseId);
	// 	console.log("moving mouse " + mouseNbr);

	// 	var mcm_sub = new ContMenu("cageSelectionSubMenu", 0, 0, false);
	// 	loadCagesForSubMenu(mcm_sub, mouseNbr);
	// 	console.log(event);
	// 	return mcm_sub;
	// };
	// mcm.connectSubHeaderElement("moveMouse", mcm_subCallback);
	// mcm.connectElement("deleteMouse", "deleteMouse");
	// mcm.connectElement("deceasedMouse", "deceasedMouse");
	// mcm.build();
	// mcm.hide();
}


var addMouseInspectorCallback = function(mouseNode) {
    (function(obj) {
    	var mouseInspectorNode = document.getElementById("inspector");
        EventUtil.addHandler(obj, "click", function(event) {
            event = EventUtil.getEvent(event);
            EventUtil.stopPropagation(event);
            var mouse = getParentWithClassName(obj, "mouse");
            highlightMouse(mouse);
            if (mouse == null) {console.log("Error retrieving mouse.");return;};
            var mouseId  = mouse.id;
            var mouseNbr = getNbrFromId(mouseId);
            var uri = "/mouse/mouse_inspector/" + mouseNbr + window.location.search;
            var xhr = createCORSRequest('GET', uri);
			xhr.setRequestHeader("Content-Type","text/html-fragment");
            if (!xhr) return;
            xhr.onload = function() {
            	mouseInspectorNode.innerHTML = xhr.responseText;
            	var form       = document.getElementsByName("editMouseForm")[0];
            	var currentSex = form.current_sex.value;
            	var currentGenotypeId = form.current_genotype_id.value;
            	if (currentSex == 1) {
            		document.getElementById("rbMale").checked = true;
            	} else if (currentSex == 2) {
            		document.getElementById("rbFemale").checked = true;
            	}
            	var genOptionNode = document.getElementById("gen_" + currentGenotypeId);
            	if (genOptionNode != null) {
            		genOptionNode.selected = true;
            	};
            };
            xhr.onerror = function() {
            	console.log("There was an error with CORS request");
            };
            xhr.send();
        });
    })(mouseNode);
}
var addMouseInspectorCallbacks = function() {
	var mouseNodes = document.getElementsByClassName("mouse");
	for (var i = 0; i < mouseNodes.length; i++) {
		addMouseInspectorCallback(mouseNodes[i]);
	}
}

window.globalOnLoadQueue.push(addMouseInspectorCallbacks);
window.familyTree = new FamilyTree("familyTreeViewer", drawFunction, "mouse_id", "father_id", "mother_id");
window.familyTree.setGridLayout({gridResolutionX:120,gridResolutionY:120});
updateTree();

function openFamilyTreePopup() {
	var svg = window.familyTree.getSvg();
	console.log(svg);
	var popUp = new PopUp(0,0, svg, "main");
}
