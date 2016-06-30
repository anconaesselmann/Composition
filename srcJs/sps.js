
// @author Axel Ancona Esselmann

function getChildNumber(node) {
  return Array.prototype.indexOf.call(node.parentNode.childNodes, node) / 2;
}
function getStyle(el, styleName) {
    var style    = window.getComputedStyle(el, null).getPropertyValue(styleName);
    var fontSize = parseFloat(style);
    return fontSize;
}

function loadXMLDoc(uri) {
    console.log(uri);
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
    } else {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            //document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
            console.log("request successful");
            console.log(xmlhttp.responseText);
            var cell = document.getElementById("activeCell").parentNode;
            // cell.style.background = "green";
            removeInput();
        }
    }
    xmlhttp.open("POST",uri,true);
    xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xmlhttp.send(JSON.stringify(arguments));
}



function enterColChange(e) {
    var charCode = (typeof e.which === "number") ? e.which : e.keyCode;
    var enterKey = 13;
    if (charCode == enterKey){
        var input          = e.target;
        var cell           = input.parentNode;
        var row            = cell.parentNode;
        var cellIndex      = getChildNumber(cell);
        var rowIndex       = getChildNumber(row);
        var cellText       = input.value;
        var uri            = window.location.pathname;
        var jsonString     = JSON.stringify({cell: cellIndex, row: rowIndex, value: cellText});
        var uriWithRequest = uri + "?ajaxRequest=" + jsonString;
        var arguments      = {ajaxRequest:jsonString}
        var apiUrl         = window.location.protocol + "//www.api." + window.location.host.replace('www.','') + window.location.pathname;
        console.log("enter pressed on cell " + cellIndex + " in row " + rowIndex + ": " + cellText);
        loadXMLDoc(uriWithRequest, arguments);
    }
}
function removeInput() {
    var activeInput = document.getElementById("activeCell");
    if (activeInput !== null) {
        var cellText = activeInput.value;
        var parentNode = activeInput.parentNode;
        parentNode.removeChild(activeInput);
        parentNode.innerText = cellText;
    };
}

function cellClicked(e) {


    var cell      = e.toElement;
    var tagName   = cell.tagName.toLowerCase();
    if (tagName === 'td') {
        removeInput();
        var row       = cell.parentNode;
        var cellIndex = getChildNumber(cell);
        var rowIndex  = getChildNumber(row);
        var cellPaddingLeft = getStyle(cell, 'padding-left');
        var cellPaddingRight = getStyle(cell, 'padding-right');
        var inputWidth = (cell.offsetWidth - 1 - cellPaddingLeft - cellPaddingRight);
        cell.innerHTML = "<input id=\"activeCell\" type=\"text\" name=\"activeInput\" value=\"" + cell.innerText +"\" style=\"width:" + inputWidth + "px\" onkeyup=\"enterColChange(event)\" autofocus />";
        setTimeout(function() {
            document.getElementById("activeCell").focus();
        }, 10);
    };
}

function registerTable(table) {
    table.addEventListener('mousedown',cellClicked,false);
}