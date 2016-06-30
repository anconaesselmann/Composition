
// @author Axel Ancona Esselmann

var CORSRequest = {
    create: function (method, url) {
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
}