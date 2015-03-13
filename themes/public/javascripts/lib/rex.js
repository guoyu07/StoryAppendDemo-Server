/**
 * Created by godsong on 14-5-8.
 */

if(typeof window.console=='undefined'){
    window.console={
        log:function(){},
        error:function(){}
    }
}
if(typeof Object.keys!='function'){
    Object.keys=function(obj){
        var keys=[];
        for(var key in obj){
            if(obj.hasOwnProperty(key)){
                keys.push(key);
            }
        }
        return keys;
    }
}

window.core = function () {
    function _reveal() {
        for (var i = 0; i < arguments.length; i++) {
            var func = arguments[i];
            if (func.name) {
                window[func.name] = func;
            }
        }
    }

    var Slice = Array.prototype.slice;
    var typeOf = function () {
        var class2type = {}, toString = Object.prototype.toString, split = 'Boolean Number String Function Array Date RegExp Object Error'.split(' ');
        for (var i = 0; i < split.length; i++) {
            class2type[ "[object " + split[i] + "]" ] = split[i].toLowerCase();
        }
        return function (obj) {
            if (obj == null) {
                return obj + "";
            }
            return typeof obj === "object" || typeof obj === "function" ?
                class2type[ toString.call(obj) ] || "object" :
                typeof obj;
        }
    }();

    function clone(src, deep) {
        if (typeOf(src) === 'object') {
            var dest = {};
            for (var key in src) {
                if (src.hasOwnProperty(key)) {
                    var val = src[key];
                    if (typeOf(val) === 'object' && deep) {
                        dest[key] = clone(val, true);
                    }
                    else {
                        dest[key] = val;
                    }
                }
            }
            return dest;
        }
        else {
            return src;
        }

    }

    function merge(dest) {
        if (arguments.length > 1) {
            var deepCopy = arguments[arguments.length - 1] === true;
            for (var i = 1, _l = arguments.length - (deepCopy ? 1 : 0); i < _l; i++) {
                var src = arguments[i];
                if (typeof src == 'object') {
                    for (var key in src) {
                        if (src.hasOwnProperty(key)) {
                            var val = src[key];
                            if (typeof val === 'object' && deepCopy) {
                                dest[key] = merge(dest[key] || (val instanceof Array ? [] : {}), val, true);
                                if (dest[key] instanceof Array && val instanceof Array) {
                                    dest[key].length = Math.max(dest[key].length, val.length);
                                }
                            }
                            else {
                                dest[key] = val;
                            }
                        }
                    }
                }
            }
            return dest;
        }
        else {
            throw new Error('merge need at least 2 arguments');
        }
    }

    merge.ex = function (dest) {
        if (arguments.length > 1) {
            var deepCopy = arguments[arguments.length - 1] === true;

            for (var i = 1, _l = arguments.length - (deepCopy ? 1 : 0); i < _l; i++) {
                var src = arguments[i];
                if (typeof src == 'object') {

                    for (var key in src) {
                        if (src.hasOwnProperty(key)) {
                            var val = src[key];
                            if (typeof val == 'object' && deepCopy) {
                                if (!dest[key]) {
                                    dest[key] = val instanceof Array ? [] : {};
                                }
                                if (dest[key] instanceof Array && val instanceof Array) {
                                    dest[key].push.apply(dest[key], val);
                                }
                                else {
                                    dest[key] = merge.ex(dest[key] || {}, val, true);
                                }

                            } else {


                                if (dest instanceof Array) {
                                    dest.length++;
                                }
                                dest[key] = val;
                            }
                        }
                    }
                }
            }
            return dest;
        }
        else {
            throw new Error('merge need at least 2 arguments');
        }
    };

    /*=========== 一个简单的类系统==========*/

    function ObjectX() {
    }//基类
    ObjectX.prototype = {
        $super: function (name) {
            var curClass = this.constructor, value;
            if (!this._constructor)this._constructor = curClass;
            //在非构造函数下使用的分支
            if (arguments.callee.caller != this._constructor) {
                name = name || "";
                if (name.length > 0) {
                    while (true) {
                        this._constructor = this._constructor.$parent;
                        if (this._constructor === ObjectX || this._constructor == null) break;
                        value = this._constructor.prototype[name];
                        if (value !== undefined)break;
                    }
                    if (typeof value == 'function') value = value.apply(this, [].slice.call(arguments, 1));
                    delete this._constructor;
                    return value;
                }
                else {
                    delete this._constructor;
                    return curClass.$parent.prototype;
                }
            }
            //在构造函数下使用的分支
            else {
                //获得父类构造器
                var superConstructor = this._constructor.$parent;
                //_constructor指针移到父类 保存当前的调用状态 使得父类构造函数中的super能正确获得其父类构造函数
                this._constructor = superConstructor;
                //执行父类构造函数（其中若父类构造函数依旧有super则构成了一个隐式的递归）
                if (superConstructor)superConstructor.apply(this, arguments);
                //删除这个_constructor指针，不留痕迹。
                delete this._constructor;
            }
        }
    };
    function _addAnAspect(target, pos, aspect) {
        targetFn = this.prototype[target];
        if (!targetFn || typeof targetFn != 'function') {
            throw new Error('target of addAspect must be a function!["' + target + '"]');
        }

        var host = this;
        var targetFn = this.prototype[target];
        if (pos == 'before') {
            this.prototype[target] = function () {
                var advice = {
                    host: this,
                    methodName: target,
                    pos: pos
                };
                aspect.apply(advice, Array.prototype.slice.call(arguments));
                return targetFn.apply(advice, Array.prototype.slice.call(arguments))
            };
        }
        else if (pos == 'after') {
            this.prototype[target] = function () {
                var advice = {
                    host: this,
                    methodName: target,
                    pos: pos
                };
                var args = Array.prototype.slice.call(arguments);
                var ret = targetFn.apply(this, args);
                advice.ret = ret;
                var aRet = aspect.call(advice, args);
                if (aRet === undefined) {
                    return ret;
                }
                else {
                    return aRet;
                }

            };
        }
        else if (pos == 'around') {
            this.prototype[target] = function () {
                var args = Array.prototype.slice.call(arguments);
                aspect.apply({
                    host: this,
                    methodName: target,
                    pos: 'before'
                }, args);
                var ret = targetFn.apply(this, args);
                var aRet = aspect.call({
                    host: this,
                    methodName: target,
                    pos: 'after',
                    ret: ret
                }, args);
                if (aRet === undefined) {
                    return ret;
                }
                else {
                    return aRet;
                }

            };
        }
        else {
            return;
        }

        /*this.prototype[target].toString=function(){
         return targetFn.toString();
         }*/
    }

    function addAspect(aspect) {
        for (var key in aspect) {
            if (aspect.hasOwnProperty(key)) {
                var tok = key.split('@');
                if (tok.length == 1) {
                    var pos = 'after';
                    target = tok[0];
                }
                else {
                    pos = tok[0];
                    target = tok[1];
                }
                if (target.charAt(0) == '+') {
                    this.prototype[target.slice(1)] = aspect[key];
                }
                else {
                    if (target == '*') {
                        for (var k in this.prototype) {
                            if (this.prototype.hasOwnProperty(k) && typeof this.prototype[k] == 'function' && !(k in ObjectX.prototype)) {
                                _addAnAspect.call(this, k, pos, aspect[key]);
                            }
                        }
                    }
                    else {
                        _addAnAspect(target, pos, aspect[key]);
                    }
                }
            }
        }
        return this;
    }

    function Class(main) {
        var parent = main.$extend || ObjectX;
        var newClass = main.$init || function () {
        };
        delete main.$init;
        delete main.$extend;
        function Parent() {
        }

        Parent.prototype = parent.prototype;
        newClass.prototype = new Parent();
        if (!newClass.prototype.$super) {
            newClass.prototype.$super = ObjectX.prototype.$super;
        }
        newClass.prototype.constructor = newClass;
        newClass.$parent = parent;
        newClass.addAspect = addAspect;
        for (var key in main) {
            if (main.hasOwnProperty(key)) {
                var member = main[key];
                if (typeof member == 'function') {

                    if (key.charAt(0) == '@') {
                        newClass[key.slice(1)] = member;
                    }
                    else {
                        newClass.prototype[key] = member;
                    }
                }
                else {
                    newClass[key] = member;
                }
            }

        }
        return newClass;
    }

    function isEmpty(obj) {
        if (obj == null || obj == undefined) {
            return true;
        }
        else if (typeof obj == 'string') {
            return obj.length == 0;
        }
        else if (typeOf(obj) == 'array') {
            return obj.length == 0;
        }
        else if (typeOf(obj) == 'object') {
            var flag = true;
            for (var k in obj) {
                if (obj.hasOwnProperty(k)) {
                    flag = false;
                    break;
                }
            }
            return flag;
        }
        else {
            return false;
        }
    }
    function forEach(obj,callback){
        if(obj) {
            for(var key in obj) {
                if(obj.hasOwnProperty(key) && key != 'hasOwnProperty') {
                    callback.call(obj, key, obj[key]);
                }
            }
        }
    }
    Class.Base = ObjectX;
    window.Class = Class;

    _reveal(merge, Class, ObjectX);
    window.typeOf = typeOf;
    return {
        typeOf: typeOf,
        merge: merge,
        clone: clone,
        isEmpty: isEmpty,
        forEach:forEach
    }
}();
!function () {
    Date.lang = {
        monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
        monthNamesShort: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
        weekDayNames: ['星期日', '星期一', '星期二', '星期四', '星期五', '星期六'],
        weekDayNamesShort: ['周日', '周一', '周二', '周三', '周四', '周五', '周六']
    };
    var _regFormat = /y+|m+|d+|Y+|M+|D+/g;
    var _matchType = [];

    function _dateStrResolver(flag) {
        if (flag == 'yyyy' || flag == 'yy' || flag == 'y') {
            _matchType.push('year');
            return '\\d{4}';
        }
        else if (flag == 'dd') {
            _matchType.push('day');
            return '\\d{2}';
        }
        else if (flag == 'd') {
            _matchType.push('day');
            return '\\d{1,2}';
        }
        else if (flag == 'm') {
            _matchType.push('month');
            return '\\d{1,2}';
        }
        else if (flag == 'mm') {
            _matchType.push('month');
            return '\\d{2}';
        }
        else return flag;
    }
    function _dateResolver(date, flag) {
        var year = date.getFullYear(), month = date.getMonth() + 1, day = date.getDate(), weekday = date.getDay();
        if (flag == 'yyyy' || flag == 'YYYY') {
            return year;
        }
        else if (flag == 'yy' || flag == 'y') {
            return ('' + year).slice(2);
        }
        else if (flag == 'dd') {
            return day < 10 ? '0' + day : day;
        }
        else if (flag == 'd') {
            return day;
        }

        else if (flag == 'm') {
            return month;
        }
        else if (flag == 'mm') {
            return month < 10 ? '0' + month : month;
        }
        else if (flag == 'D') {
            return Date.lang.weekDayNamesShort[weekday];
        }
        else if (flag == 'DD') {
            return Date.lang.weekDayNames[weekday];
        }
        else if (flag == 'M') {
            return Date.lang.monthNamesShort[month - 1];
        }
        else if (flag == 'MM') {
            return Date.lang.monthNames[month - 1];
        }
        else return flag;
    }
    var _dateConver = {
        'day': function (date, num) {
            date.setDate(date.getDate() + num);
        },
        'year': function (date, num) {
            var d = date.getDate();
            date.setFullYear(date.getFullYear() + num);
            if (date.getDate() != d) {
                date.setDate(0);
            }
        },
        'month': function (date, num) {
            var d = date.getDate();
            date.setMonth(date.getMonth() + num);
            if (date.getDate() != d) {
                date.setDate(0);

            }
        }
    };

    function _convert(str, date, num) {
        str = str.toLowerCase();
        if (str == 'd') {
            str = 'day';
        }
        else if (str == 'y') {
            str = 'year';
        }
        else if (str == 'm') {
            str = 'month';
        }
        _dateConver[str](date, num);
    }

    Date.prototype.format = function (formatStr) {
        formatStr = formatStr || 'yyyy-mm-dd';
        var date=this;
        return formatStr.replace(_regFormat, function (m) {
            return _dateResolver(date, m);
        })
    };


    Date.parse = function (dateStr, formatStr) {
        _matchType = [];
        if (dateStr) {
            if (formatStr) {
                var date = new Date(1,0,1);
                date.setHours(0, 0, 0, 0);

                var reg = new RegExp(formatStr.replace(_regFormat, function (m) {
                    return '(' + _dateStrResolver(m.toLowerCase()) + ')';
                }), 'g');
                var res = reg.exec(dateStr);

                if (res) {
                    for (var i = 1; i < res.length; i++) {
                        var v = res[i], type = _matchType[i - 1];

                        if (type == 'year') {
                            date.setFullYear(+v)
                        }
                        else if (type == 'month') {
                            date.setMonth(v - 1);
                        }
                        else if (type == 'day') {
                            date.setDate(+v);
                        }
                    }
                    return date;


                } else {
                    return null;

                }
            }
            else {
                var tok = dateStr.split('-');
                if (isNaN(+tok[0])) {
                    return null;
                }
                else {
                    return new Date(+tok[0], +tok[1] - 1, +tok[2]);
                }

            }
        } else {
            return null;
        }
    };
    Date.prototype.strtotime=function (str) {

        var date=new Date(this);
        var reg = /([\+\-])(\d+)\s*([a-zA-Z]+)/g;
        var token;
        while (token = reg.exec(str)) {
            var opt = token[1] == '-' ? -1 : 1;
            _convert(token[3], date, +token[2] * opt);
        }
        return date;
    };
}();
if(typeof Array.prototype.forEach!='function'){
    Array.prototype.forEach=function(callback){
        for(var i=0;i<this.length;i++){
            if(callback(this[i],i,this)===false)break;
        }
    }
}
if(typeof Array.prototype.indexOf!='function'){
    Array.prototype.indexOf=function(v){
        for(var i=0;i<this.length;i++){
            if(this[i]===v){
                return i;
            }
        }
        return -1;
    }
}

//基于链式的Promise实现
!function () {
    var _Slice = Array.prototype.slice;
    var _when = function () {
        var count = arguments.length;
        var args = [], p = this;

        function sandGlass() {//计时器 作为when里所有promise的then 每个promise 完成时 count递减 当count为0时代表when里所有的promise都完成了 此时resolve when这个promise
            count--;
            args[this.index] = _Slice.call(arguments);
            if (count == 0) {
                console.warn(args);
                p.resolve.apply(p, args);
            }
        }

        for (var i = 0; i < arguments.length; i++) {
            var pro = arguments[i];
            if (typeof pro === 'function') {
                var ret = pro();
                if (ret instanceof Promise) {
                    ret.then(sandGlass);
                    ret.index = i;
                }
                else {
                    count--;
                    args[i] = ret;
                }
            }
            else {
                args[i] = pro;
                count--;
            }
        }
        return p;
    };
    var _any = function () {

        var args = [], p = this;

        function sandGlass() {//计时器 作为when里所有promise的then 每个promise 完成时 count递减 当count为0时代表when里所有的promise都完成了 此时resolve when这个promise
            if (p.state == 'pending') {

                p.resolve.apply(p, args);
            }
        }

        for (var i = 0; i < arguments.length; i++) {
            var pro = arguments[i];
            if (typeof pro === 'function') {
                var ret = pro();
                if (ret instanceof Promise) {
                    ret.then(sandGlass);
                    ret.index = i;
                }
                else {
                    count--;
                    args[i] = ret;
                }
            }
            else {
                args[i] = pro;
                count--;
            }
        }
        return p;

    };
    var _cache = {}, _uuid = 0;

    function Promise(func) {
        this.state = 'pending';
        if (typeof func === 'function') {
            var p = this;
            func(function () {
                p.resolve.apply(p, _Slice.call(arguments));
            }, function () {
                p.reject.apply(p, _Slice.call(arguments));
            })
        }
        this.uuid = _uuid++;
    }

    Promise.prototype = {
        then: function (done, fail) {
            this.done = done;
            this.fail = fail;
            this.next = new Promise();
            if (this.state == 'fulfilled') {
                this.resolve.apply(this, _cache[this.uuid]);
            }
            if(this.state=='reject'){
                this.reject.apply(this, _cache[this.uuid]);
            }
            return this.next;
        },
        resolve: function () {
            if (this.next) {
                if (this.done) {
                    this.state = 'fulfilled';
                    var ret = this.done.apply(this, _Slice.call(arguments));
                    if (ret instanceof Promise) {
                        //移花接木 将当前promise done函数中生成的promise转化成当前的后续promise(该后续promise本身就是为它预生成的)
                        ret.done = this.next.done;
                        ret.fail = this.next.fail;
                        ret.uuid = this.next.uuid;
                        ret.next = this.next.next;
                        if (ret.next == null) {
                            //借尸还魂 当前promise的done函数已经是最后一个函数 生成的promise无法被用户引用，因此需要把fulfill状态置回为它预生成的那个promise上一遍后续的then
                            ret.proto = this.next;
                        }
                    }
                    else {
                        var p = new Promise();
                        p.done = this.next.done;
                        p.fail = this.next.fail;
                        p.uuid = this.next.uuid;
                        p.next = this.next.next;
                        if (p.next == null) {
                            p.proto = this.next;
                        }
                        p.resolve(ret);
                    }
                }
                else {
                    this.next.resolve();
                }
            }
            else {
                if (this.proto) {
                    this.proto.state = 'fulfilled';
                    _cache[this.proto.uuid] = _Slice.call(arguments);
                }
                else {
                    this.state = 'fulfilled';
                    _cache[this.uuid] = _Slice.call(arguments);
                }

            }
            return this;
        },
        reject: function () {
            this.state='reject';
            if (this.fail) {
                this.fail.apply(this, _Slice.call(arguments));
            }
            else{
                if(this.next){
                    this.next.reject.apply(this.next,_Slice.call(arguments));//错误冒泡
                }
                else{
                    _cache[this.uuid] = _Slice.call(arguments);
                }

            }
        },
        when: function () {
            _when.apply(this, _Slice.call(arguments));
            return this;
        },
        crossFire:function(){

        },
        value: function (n) {
            if (typeof n == 'number') {
                return _cache[this.uuid][n];
            }
            return _cache[this.uuid];
        }
    };

    Promise.when = function () {
        var p = new Promise();
        _when.apply(p, _Slice.call(arguments));
        return p;
    };
    window.Promise = Promise;
}();

/*function a1() {
 var p = new Promise();
 console.log('run', arguments.callee.name);
 setTimeout(function () {
 p.resolve(1);
 }, 1000);
 return p;
 }
 function a2() {
 var p = new Promise();
 console.log('run', arguments.callee.name);
 setTimeout(function () {
 p.resolve('b', 'c');
 }, 2000);
 return p;
 }
 function a3() {
 console.log('run', arguments.callee.name);
 var p = new Promise();
 setTimeout(function () {
 p.resolve({x: 1});
 }, 5000);
 return p;
 }*/
/*var l = a1().then(a2).then(a3);
 var async1=function(){
 var p = new Promise();
 setTimeout(function () {
 p.resolve(1);
 }, 5000);
 return p;
 };
 async1.then(function(){
 var p = new Promise();

 setTimeout(function () {
 p.resolve(2);
 }, 2000);
 return p;

 }).then(function(){
 var p = new Promise();
 setTimeout(function () {
 p.resolve(3);
 }, 2000);
 return p;
 });*/

/*
 Promise.when(a1,function () {
 return 111;
 }, a2, a3).then(function () {
 console.log(+new Date() - t1);
 console.timeEnd(1);
 console.log(arguments)
 });*/


/*var o1 = {
 a: 1,
 b: [1, 2]
 }, o2 = {
 b: [3, 4],
 c: {x: 1, y: 2}
 }, o3 = {
 c: [4, 5],
 d: [6, 7]
 }, o4 = {
 d: {x: 4, y: 5}
 };
 console.log(o1, o2, o3, o4)
 var n1 = merge({}, o1, o2, o3, o4), n2 = merge({}, o1, o2, o3, o4, true);*/


window.DataAdapter = (function (clone) {

    function _mapping(token) {
        var map = {};
        var splits = token.split(',');
        for (var i = 0; i < splits.length; i++) {
            map[splits[i]] = true;
        }
        return map;

    }

    function _propertyLocate(src, path, create) {
        var splits = path.split('.'), cur = src;
        for (var i = 0; i < splits.length; i++) {
            var prop = splits[i];
            if (prop) {
                if (cur) {
                    if (typeof cur == 'object' && !cur[prop] && create) {
                        cur[prop] = {};
                    }
                    cur = cur[prop];
                }
                else break;
            }
        }
        return cur;
    }

    function DataAdapter(adapter, selector) {

        if (typeof adapter == 'string' && arguments.length == 1) {
            selector = adapter;
            adapter = null;
        }
        if (!selector) {
            selector = '*';
        }
        this.others = -1;
        var idx = selector.indexOf('//');
        if (idx != -1) {
            this.path = selector.substring(0, idx);
            selector = selector.slice(idx + 2);
        }
        if (selector == '*') {
            this.others = 1;
        }
        else if (selector == '^') {
            this.others = 0;
        }
        else if (selector.charAt(0) == '^') {
            this.reduce = _mapping(selector.slice(1));
        }
        else {
            this.map = _mapping(selector);
        }
        this.adapter = adapter;
        this.keys = {};
        for (var k in adapter) {
            if (adapter.hasOwnProperty(k)) {
                this.keys[k] = true;
            }
        }
    }


    DataAdapter.prototype = {
        apply: function (dest, src, deep) {
            var self = false, fn;
            if (!dest) {
                dest = src;
                self = true;
            }
            if (this.path) {
                src = _propertyLocate(src, this.path);
            }

            /*if(core.typeOf(src)!='array'){
             src=[src];
             }

             for(var i=0;i<src.length;i++){
             var srcObj=src[i];
             var keys=clone(this.keys);*/
            var srcObj = src;
            for (var key in srcObj) {

                if (srcObj.hasOwnProperty(key)) {
                    if (this.adapter && (fn = this.adapter[key])) {

                        if (typeof fn == 'function') {
                            dest[key] = fn.call(dest, srcObj[key], srcObj);
                        }
                        else if (typeof fn == 'string') {
                            if (fn == '*') {
                                dest[key] = srcObj;
                            }
                            else if (deep) {
                                dest[key] = clone({}, _propertyLocate(srcObj, fn), true);
                            }
                            else {
                                dest[key] = _propertyLocate(srcObj, fn);
                            }
                        }


                        else {
                            fn = fn instanceof String ? fn.valueOf() : fn;
                            dest[key] = fn;
                        }
                        delete this.keys[key];
                    }
                    else if (this.others !== 0 && !self) {

                        if (this.others == 1 || (this.reduce && !this.reduce[key]) || (this.map && this.map[key])) {
                            if (deep) {
                                dest[key] = clone({}, srcObj[key], true);
                            }
                            else {
                                dest[key] = srcObj[key];
                            }

                        }

                    }
                }
            }
            for (key in this.keys) {
                if (this.keys.hasOwnProperty(key)) {
                    fn = this.adapter[key];
                    if (typeof fn == 'function') {
                        dest[key] = fn.call(dest, srcObj[key], srcObj)
                    }
                    else if (typeof fn == 'string') {
                        if (fn == '*') {
                            dest[key] = srcObj;
                        }
                        else if (deep) {
                            dest[key] = clone({}, _propertyLocate(srcObj, fn), true);
                        }
                        else {
                            dest[key] = _propertyLocate(srcObj, fn);
                        }

                    }
                    else {
                        dest[key] = fn;
                    }
                }
            }
            //}
            return dest;
        }

    };
    DataAdapter.setup = function (dest, map, src) {
        map = map.split(/,| /);
        for (var i = 0; i < map.length; i++) {
            var res = map[i].split('|'), def = null;
            if (res[1]) {
                def = JSON.parse(res[1]);
            }
            if (src) {
                dest[res[0]] = src[res[0]] || def;
            }
            else {
                dest[res[0]] = def;
            }
        }
        return dest;
    };
    DataAdapter.clone = clone;
    new DataAdapter('*', '^foo,bar,ttt', {
        'props': function () {

        },
        'props2': 'abc.foo'

    });


    return DataAdapter;
}(core.clone));

/*var a = {};
 var b = {
 p1: [1, 2],
 p2: 'abc',
 p3: {
 x: {
 e: '123'
 }
 },
 p4: {
 d: 321
 }
 };
 var da = new DataAdapter({
 a:function(val,src){
 return val+1;
 },
 b:'p3.x.e'
 },'^');
 console.time(2);

 c = da.apply({}, b);
 console.timeEnd(2);*/

window.DataFactory = (function () {
    _defaultConfig = {
        type: 'get',
        dataType: 'json'
    };
    function _resolveDomData(root,data){

        for(var i= 0,l=root.childNodes.length;i<l;i++){
            var node=root.childNodes[i];
            if(node.nodeType==1){
                if(node.tagName=='UL'){
                    data[node.getAttribute('data-n')]=_resolveDomData(node,[]);
                }
                else if(node.tagName=='DIV'||node.tagName=='LI'){
                    data[node.getAttribute('data-n')]=_resolveDomData(node,{});
                }
                else if(node.tagName=='SPAN'){
                    data[node.getAttribute('data-n')]=node.innerText||node.textContent||"";
                }
                else if(node.tagName=='A'){
                    data[node.getAttribute('data-n')]=node.getAttribute('href');
                }
            }
        }
        return data;
    }
    return Class({
        $init: function DataFactory(address, config, resolver) {
            this.url = address;
            this.config = core.merge({url: address}, _defaultConfig, config);
            var dataPromise = new Promise();
            var self=this;
            this.resolver=resolver;
            this.dataPromise = {cur: dataPromise};
            this.headPromise=dataPromise;
            if(address===null){
                var res = {
                    code:200,
                    data:{}
                };
                var data=resolver(res);
                dataPromise.resolve(data);
                return;
            }
            $(function(){
                var dataDom=$('#'+address.replace(/[/?&=]/g,'_'))[0],typeMap={'UL':[],'DIV':{}};
               if(dataDom){
                   res = {
                       code:200,
                       data:_resolveDomData(dataDom.firstChild,typeMap[dataDom.firstChild.tagName])
                   };
                   var data=resolver(res);
                   dataPromise.resolve(data);
               }
                else{
                   self.config.success = function (response) {
                       if (resolver) {
                           var data = resolver(response);
                           dataPromise.resolve(data);
                       }
                       else {
                           dataPromise.resolve(response);
                       }

                   };
                   self.config.error = function (xhr, msg, error) {
                       dataPromise.reject(msg, error);
                   };
                   $.ajax(self.config);

               }

            });


        },
        reload:function(data){
            this.config.data=data;
            var self=this;
            this.config.success = function (response) {
                if (self.resolver) {
                    var data = self.resolver(response);
                    self.headPromise.resolve(data);
                }
                else {
                    self.headPromise.resolve(response);
                }

            };
            this.config.error = function (xhr, msg, error) {
                self.headPromise.reject(msg, error);
            };
            $.ajax(self.config);
          self.dataPromise.cur.state='pending';
            return self.dataPromise.cur;
        },
        getData: function (dataAdapter) {
            if (typeOf(dataAdapter) == 'object' && !(dataAdapter instanceof DataAdapter)) {
                dataAdapter = new DataAdapter(dataAdapter);
            }
            return {
                promise: this.dataPromise,
                adapter: dataAdapter
            };

        },
        saveData: function (data) {

        }
    });

}());

var HitourDataFactory = Class({
    $init: function (address, config, resolver) {
        if (typeof config == 'function') {
            resolver = config;
        }
        this.$super(address, config, function (response) {
            if (response.code == 200) {

                return resolver ? (resolver(response.data) || response.data) : response.data;
            }
            else {
                throw new Error(response.msg);
            }
        });
    },
    $extend: DataFactory
});
window.HitourDataFactory=HitourDataFactory;
window.ViewModel=function(name, props) {
    var self = this;
    this.name = name;
    this.propMap = {};
    this.props = props;
    var splits = props.split(/,| /);
    for (var i = 0; i < splits.length; i++) {
        var res = splits[i].split('|');
        this.propMap[res[0]] = true;
    }
    self.viewModel = avalon.define(name, function (vm) {

        DataAdapter.setup(vm, props);
    });
    ViewModel.all.push(this);
}
ViewModel.all = [];
ViewModel.prototype = {
    bindData: function (promiseData) {
        if (!promiseData.adapter) {
            promiseData.adapter = new DataAdapter(this.props)
        }
        var p = new Promise();
        var vm = this.viewModel;
        this.promise = promiseData;
        promiseData.adapter.others = -1;
        promiseData.adapter.map = this.propMap;
        promiseData.adapter.reduce = null;
        if (promiseData.promise.cur.state == 'fulfilled') {
            promiseData.adapter.apply(vm, promiseData.promise.cur.value(0));
            p.resolve(promiseData.promise.cur.value(0));
        }
        else {
            promiseData.promise.cur = promiseData.promise.cur.then(function (data) {

                promiseData.adapter.apply(vm, data);
                var dump = {};
                for (var k in vm) {
                    if (vm.hasOwnProperty(k)) {
                        if (k.charAt(0) != '$') {
                            dump[k] = vm[k];
                        }
                    }
                }
                p.resolve(data);
                return data;
            });
        }
        return p;
    }
};
ViewModel.render = function () {
    avalon.ready(function () {
        avalon.scan();
    });
};
var VM=Class({
    $init:function(name){

    }
});
function scanElement(elem){
    var attrs=elem.attributes;

}
function Module(name, executor){
    return new _Module(name,executor);

}
function _Module(name,executor){
    if(typeof name === 'function') {
        this.executor = name;
    }
    else {
        this.name = name;
        this.executor = executor;
    }
}
_Module.prototype.load=function(dataFactory) {
    var promiseData = dataFactory.getData();
    if(promiseData.promise.cur.state == 'fulfilled') {
        this.executor(promiseData.promise.cur.value(0));
    }
    else {
        var self = this;
        promiseData.promise.cur = promiseData.promise.cur.then(function(data) {
            self.executor(data);
            return new Promise().resolve(data);

        });
    }
};

window.define=undefined;
window.ViewModel=ViewModel;
window.Module=Module;


