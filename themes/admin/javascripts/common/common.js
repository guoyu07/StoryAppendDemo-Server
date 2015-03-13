function randomStr(len, tough) {
    len = len || 8;
    var selection = tough ?
                    '~`!@#$%^&*()_qwertyuioplkjhgfdsazxcvbnm0192837465QWERTYUIOPLKJHGFDSAZXCVBNM+-={}|:?><[];/.,' :
                    'qwertyuioplkjhgfdsazxcvbnm0192837465QWERTYUIOPLKJHGFDSAZXCVBNM';
    var char_span = selection.length;
    var result = '';

    for(var i = 0; i < len; i++) {
        result += selection[Math.floor(Math.random() * char_span)];
    }

    return result;
}
function imageFilter(item) {
    var type = '|' + item.type.toLowerCase().slice(item.type.lastIndexOf('/') + 1) + '|';
    var result = '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
    return result;
}
var typeOf = function() {
    var class2type = {}, toString = Object.prototype.toString, split = 'Boolean Number String Function Array Date RegExp Object Error'.split(' ');
    for(var i = 0; i < split.length; i++) {
        class2type["[object " + split[i] + "]"] = split[i].toLowerCase();
    }
    return function(obj) {
        if(obj == null) {
            return obj + "";
        }
        return typeof obj === "object" || typeof obj === "function" ? class2type[toString.call(obj)] || "object" :
               typeof obj;
    }
}();
function isEmpty(obj) {
    if(obj == null || obj == undefined) {
        return true;
    } else if(typeof obj == 'string') {
        return obj.length == 0;
    } else if(typeOf(obj) == 'array') {
        return obj.length == 0;
    } else if(typeOf(obj) == 'object') {
        return !!Object.keys(obj).length;
    } else {
        return false;
    }
}
function getGroupBy(arr, prop_name, default_group, group_name) {
    group_name = !!group_name ? group_name : 'group';
    default_group = !!default_group ? default_group : ' ';
    var tmp;

    arr.sort(function(a, b) {
        var a_group = !!a[prop_name] ? a[prop_name].toLowerCase().charCodeAt(0) : default_group.charCodeAt(0);
        var b_group = !!b[prop_name] ? b[prop_name].toLowerCase().charCodeAt(0) : default_group.charCodeAt(0);
        return a_group - b_group;
    });

    for(var i = 0, len = arr.length; i < len; i++) {
        tmp = !!arr[i][prop_name] ? arr[i][prop_name] : default_group;
        arr[i][group_name] = tmp[0].toUpperCase();
    }

    return arr;
}
function formatDate(dateObj) {
    var seconds = Date.parse(dateObj);

    if(isNaN(seconds)) {
        return false;
    } else {
        seconds = new Date(seconds);
        var result = [seconds.getFullYear()];
        result.push(padTo(seconds.getMonth() + 1, 2));
        result.push(padTo(seconds.getDate(), 2));
        /*seconds = new Date(seconds);
        var now = new Date(seconds.getTime() - (seconds.getTimezoneOffset() * 60 * 1000));
        var result = [now.getFullYear()];
        result.push(padTo(now.getMonth() + 1, 2));
        result.push(padTo(now.getDate(), 2));*/
        return result.join('-');
    }
}
function formatTime(date) {
    if(typeof(date) == 'object') {
        date = padTo(date.getHours(), 2) + ':' + padTo(date.getMinutes(), 2);
    }

    return date;
}
function padTo(str, len, pad_char) {
    pad_char = pad_char || '0';
    str = str.toString();
    if(str.length < len) {
        return Array(len - str.length + 1).join(pad_char) + str;
    } else if(str.length == len) {
        return str;
    } else {
        return str.substr(0, len);
    }
}
function getIndexByProp(arr, prop_name, value) {
    for(var key in arr) {
        if(arr[key] && prop_name in arr[key] && arr[key][prop_name] == value) return key;
    }
    return -1;
}
function reOrder(items, one_indexed, prop_name) {
    prop_name = prop_name || 'display_order';
    items = items.map(function(elem, index) {
        elem[prop_name] = one_indexed ? +index + 1 : index;
        return elem;
    });
    return items;
}
function composeMarkdown(md) {
    return encodeURIComponent(JSON.stringify({md_text : md.md_text, md_html : !!md.md_html && md.md_text.length > 0 ? md.md_html.$$unwrapTrustedValue() : ''}));
}
function decomposeMarkdown(md) {
    try {
        md = JSON.parse(decodeURIComponent(md));
        if(typeOf(md.md_text) != 'string') {
            md.md_text = '';
        }
        if(typeOf(md.md_html) != 'string') {
            md.md_html = '';
        }
    } catch(e) {
        md = {md_text : '', md_html : ''};
    }

    return md;
}