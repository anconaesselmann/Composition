/*** include aae/_.js ***/

// @author Axel Ancona Esselmann

aae.ui = {
    lineClampAndLink: function(container, data, maxLine, link) {
        container.innerHTML = "";
        tokens              = data.split(" ");
        container           = new _Wrapper(container);
        var height, line    = 0;
        var space = "";
        var exerpt = [];
        var i;
        for (i = 0; i < tokens.length; i++) {
            if (i > 0) space = " ";
            var span  = container._('<span', space + tokens[i]);
            exerpt.push(tokens[i]);
            var bounds = span.bounds();
            var secondLast;
            if (height !== bounds.top) {
                height = bounds.top;
                line += 1;
                if (line > maxLine) {
                    if (link != undefined) container._('<span', '... ')._(link);
                    else container._('<span', '...');
                    var last = container.n.lastChild;
                    last.style.whiteSpace = 'nowrap';
                    do {
                        secondLast = last.previousSibling;
                        container.n.removeChild(secondLast);
                        bounds = last.getBoundingClientRect();
                        exerpt.pop();
                    } while (height == bounds.top);
                    secondLast = last.previousSibling;
                    var s = secondLast.innerHTML;
                    var lastChar = s[s.length - 1];
                    switch (lastChar) {
                        case ',':
                        case '.':
                            var shortened = s.substring(0, s.length - 1);
                            exerpt.pop();
                            exerpt.push(shortened);
                            break;
                    }
                    break;
                };
            };
        };
        container.n.innerHTML = exerpt.join(' ');
        if (i < tokens.length) {
            container.n.innerHTML += "&hellip; ";
            if (link != undefined) container._(link);
        };
    }
}
