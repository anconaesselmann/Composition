/*** include aae/_.js ***/

// @author Axel Ancona Esselmann

aae.std = {
    inArray: function(needle, haystack){
        return (haystack.indexOf(needle) > -1);
    },
    arrayUnset: function(needle, haystack) {
        var i = haystack.indexOf(needle);
        if (i > -1) {
            haystack.splice(i, 1);
            return true;
        }
        return false;
    }
}