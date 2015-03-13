/**
 * A simple customizable select component without css file.
 * @author: godsong
 * @version 0.7.8
 */
var SelectX = function (window, undefined) {

    var Cache = function () {//小型缓存系统 将selectx对象和生成的元素绑定在一起 旨在消除循环引用
        var uuid = 1,
            _cache = {},
            core = {},
            pid = 'selecx' + Math.random().toString().slice(-8);
        core.createCache = function (el, datas) {
            var cache = datas || {};
            _cache[uuid] = cache;
            el[pid] = uuid++;
            return cache;

        };
        core.share = function (host) {
            for (var i = 1; i < arguments.length; i++) {
                arguments[i][pid] = host[pid];
            }
        }
        core.getCache = function (el) {
            return _cache[el[pid]];
        };
        return core;
    }();
    /**
     * 索引值的公式匹配(实现nth-child(2a+b)的检测 比如判断一个索引值 2(第3个) 是否符合2n+1(符合))
     * 主要参数为四个
     * @expr 公式的表达式 格式为 an+b 或者 an  或者 b  例如 2n+1、3n-1、2n、3、-3(负数表示倒数 -1代表倒数第一个即最后一个 这功能必须制定索引值的范围 即length)
     * @n 待检测的数
     * @length 索引值的范围长度 比如 索引值的取值范围是 0-4 所以其length为5 提供此参数是为了判断 expr中的 -n 因为只有知道范围长度 我才能定位-n这种负偏移
     * @offset 索引值代表次序的偏移 比如程序中 索引值都是从0开始的 即  0代表 第1个 1代表第2个 这样的话他的偏移就是-1.为了符合大多数习惯 此参数默认即为-1 即默认索引值都是从0开始的
     * */
    var Formula=function(){
        var reg_Formula=/^(\d*n)*([\+\-]?)(\d*)$/gi;
        //此种方式可以将一个公式实例化一个对象 此后检测不需要重新解析公式了（拿空间换时间 坦白讲 这些逻辑的时间/空间复杂度都很低=。= ）
        function _Formula(fml,length,offset){
            var res=reg_Formula.exec(expr), a, b,opt;
            offset=offset||-1;
            reg_Formula.lastIndex=0;
            opt=res[2]=='-'?1:-1;
            if(res[1]===undefined){
                a=0;
            }
            else{
                a=isNaN(parseInt(res[1]))?1:parseInt(res[1]);
            }

            b=isNaN(parseInt(res[3]))?0:parseInt(res[3]);
            this.a=a;
            this.b=b;
            this.opt=opt;
            this.length=length;
            this.offset=offset;
        }
        _Formula.prototype={
            test:function(n){
                if(this.a>0){
                    return (n+this.b*this.opt)%this.a==Math.abs(this.offset);
                }
                else{
                    return (n+this.b*this.opt==this.offset)||(n+this.b*this.opt===this.length);
                }
            }
        }
        //当场解析公式 当场判断 参数详见前面注释
        _Formula.test=function (expr,n,length,offset){

            var res=reg_Formula.exec(expr), a, b,opt;
            offset=offset||-1;
            reg_Formula.lastIndex=0;
            opt=res[2]=='-'?1:-1;
            if(res[1]===undefined){
                a=0;
            }
            else{
                a=isNaN(parseInt(res[1]))?1:parseInt(res[1]);
            }

            b=isNaN(parseInt(res[3]))?0:parseInt(res[3]);
            if(a>0){
                return (n+b*opt)%a==Math.abs(offset);
            }
            else{
                return (n+b*opt==offset)||(n+b*opt===length);
            }
        };
        return _Formula;

    }();

    var head = document.getElementsByTagName('head')[0];
    var style = document.createElement('style');
    head.insertBefore(style, head.firstChild);
    var myStyleSheet = document.styleSheets[0];

    if(!document.querySelectorAll){
        document.querySelectorAll=function(selector){
            return $(selector);
        }
    }
    function addEventHandler(el,type,handler){
        if(el.addEventListener){
            el.addEventListener(type,handler);
        }
        else {
            el.attachEvent('on'+type,handler);
        }
    }
    function fireEvent(element,type){
        var evt;
        if (document.createEventObject){
            evt = document.createEventObject();
            return element.fireEvent('on'+type,evt)
        }
        else{
            evt = document.createEvent( 'HTMLEvents' );
            evt.initEvent(type, true, true);
            return !element.dispatchEvent(evt);
        }
    }
    function _eventFix(event){
        event.target=event.target||event.srcElement;
        event.preventDefault=event.preventDefault||function(){this.returnValue=false};
        event.stopPropagation=event.stopPropagation||function(){this.cancelBubble=true;}
        return event;
    }
    function _generateStyleClass(selector, css) {

        var cssText=_generateCSSText(css);
        // console.log(selector,'{'+cssText+'}');
        if(cssText.length>0){
            if(myStyleSheet.addRule){
                myStyleSheet.addRule(selector, _generateCSSText(css));
            }
            else{
                myStyleSheet.insertRule(selector+'{'+_generateCSSText(css)+'}',myStyleSheet.cssRules.length);
            }
        }
    }

    function structProxy(){

    }


    /**
     * 浅拷贝合并
     * 将参数target后面的参数合并到target中
     * 只用来合并css并将驼峰式的css名转换成连词形式
     * 可以在参数末尾加一个true来表示如果遇到target已存在的属性，则不覆盖 。默认覆盖
     *
     * */
    function _merge(target) {
        var len=arguments.length,flag;
        if(Object.prototype.toString.call(arguments[len-1])==='[object Boolean]'){
            flag=arguments[len-1];
            len-=1;
        }
        for (var i = 1; i < len; i++) {
            var args = arguments[i];
            for (var key in args) {
                var name=key.replace(/[A-Z]/g, function(item,i,word){
                    if(i>0){return '-'+item.toLowerCase()}
                    else {
                        return item.toLowerCase();
                    }

                });

                if(!flag||!target.hasOwnProperty(name)){
                    target[name] = args[key];
                }
            }
        }
        return target;
    }

    function _generateCSSText(styles) {//根据css样式对象生成css文本
        var cssText = '';
        for (var key in styles) {
            cssText += key + ':' + styles[key] + ';'
        }
        return cssText;
    }

    function _Hooker(type, selectx) {

        if (typeof selectx.hookers[type] === 'function') {
            return selectx.hookers[type].apply(selectx, Array.prototype.slice.call(arguments, 2));
        }
    }

    var containerStyle = {
            'position': 'relative'
        },
        selectStyle = {
            'border': '1px solid #000;',
            'display': 'block',
            'cursor': 'pointer',
            'background-color': '#fff',
            'white-space':'nowrap',
            'overflow':'hidden',
            'position':'relative'
        },
        selectboxStyle = {
            'border': '1px solid #000;',
            'position': 'absolute',
            'text-align': 'center',
            'display': 'none',
            'BackgroundColor': '#fff'
        },
        itemsStyle = {
            'display': 'block',
            'cursor': 'pointer',
            'white-space': 'nowrap',
            'overflow': 'hidden'
        },
        iconStyle = {

        },
        stripsStyle = {

        },

        triangleStyle = {
            'width':'0',
            'border-color':'red transparent',
            'border-style':'solid',
            'cursor':'pointer',
            'transition':'0.6s',
            '-webkit-transform-origin':'50% 30%',
            '-moz-transform-origin':'50% 30%',
            'transform-origin':'50% 30%'




        },
        itemsHoverStyle = {
            color: '#fff',
            backgroundColor: 'rgb(43,81,244)'
        },
        disableStyle={
            'background-color':'transparent',
            'border':'none'
        };

    function _resolveSelectTag(select) {
        var datas = [];
        var options = select.getElementsByTagName('option');
        for (var i = 0, l = options.length; i < l; i++) {
            var el = options[i];
            datas.push({value: el.value, text: options[i].innerHTML});
        }
        datas.selectedContent = '';
        if (select.selectedIndex >= 0) {
            datas.selectedContent = options[select.selectedIndex].innerHTML;
        }
        return datas;
    }

    function _generateIcon(w, h) {
        return _createElement('._xselect_container .triangle');
    }

    function _createStyle(className, styles, parentClass) {
        parentClass=parentClass?(parentClass+' .'):'';
        _generateStyleClass('.'+parentClass+ className, styles);

    }
    function _createElement(className,styles,tagName){
        var el = document.createElement(tagName || 'div');
        el.className = className;
        el.style.cssText=_generateCSSText(styles);
        return el;
    }


    function _getSelectx(event){
        var target=event.target;

        while(target.className.indexOf('_xs_container')==-1&&target.className.indexOf('_xs_selectbox')==-1){
            target=target.parentNode;
        }
        return Cache.getCache(target);
    }
    function _selectClickedHandler(evt) {
        var event = _eventFix(evt || window.event);
        var selectx = _getSelectx(event);

        if(selectx&&!selectx.disabled){
            var ret = _Hooker('selectClicked', selectx.host);
            if (ret !== false) {
                selectx.toggle(selectx);
                evt.stopPropagation();

            }
            _Hooker('selectClicked:after', selectx.host);
        }
        evt.preventDefault();
    }

    function _hoverHandler(evt) {
        var event = _eventFix(evt || window.event);
        var selectx = _getSelectx(event);


        var lastHoverEl = selectx.lastHoverEl;
        selectx.lastHoverEl = event.target;
        var ret = _Hooker('hover', selectx.host, event.target, lastHoverEl);
        if (ret !== false) {
            if (lastHoverEl) {
                lastHoverEl.className = lastHoverEl.className.replace(/ *hover/g, '');
            }
            event.target.className += ' hover';

        }
        _Hooker('hover:after', selectx.host, event.target, lastHoverEl);

    }

    function _itemsClickedHandler(evt) {
        var event = _eventFix(evt || window.event);
        var selectx = _getSelectx(event);

        var ret = _Hooker('itemsClicked', selectx.host, event.target);
        if (ret !== false) {
            selectx.select(event.target._index);
        }
        _Hooker('itemsClicked:after', selectx.host, event.target);

    }

    var _uuid=0;
    var reg_nthexpr=/items:nth-child\((.+)\)/gi;
    function _initStyles(uuid,usrStyles){
        //var styles={};
        var formula={items:{}},extClass={};

        for(var key in usrStyles){
            var res=reg_nthexpr.exec(key);
            reg_nthexpr.lastIndex=0;
            if(res!=null){
                _generateStyleClass('._xs_items_'+uuid+'._xs_nth_'+uuid+'_'+res[1],usrStyles[key]);
                formula.items[res[1]]=' _xs_nth_'+uuid+'_'+res[1]+' '+usrStyles[key]['@']||'';
            }
            if(key.indexOf(':')==-1){
                if(usrStyles[key]['@']){
                    extClass[key]=' '+usrStyles[key]['@'];
                    delete usrStyles[key]['@'];
                }
                else {
                    extClass[key]=''
                }
            }
        }

        _generateStyleClass('._xs_container_'+uuid,  _merge({}, containerStyle,  usrStyles.container));
        _generateStyleClass('._xs_select_'+uuid, _merge({}, selectStyle, usrStyles.select));
        _generateStyleClass('._xs_selectbox_'+uuid, _merge({}, selectboxStyle, usrStyles.selectbox));
        _generateStyleClass('._xs_items_'+uuid,  _merge({}, itemsStyle, usrStyles.items));
        _generateStyleClass('._xs_strips_'+uuid, _merge({}, stripsStyle, usrStyles.strips));
        _generateStyleClass('._xs_select_disabled_'+uuid, _merge({}, disableStyle, usrStyles['disabled']));
        _generateStyleClass('._xs_select_disabled_'+uuid+' ._xs_icon', {display:'none'});
        _generateStyleClass('._xs_container_'+uuid+' .triangle',_merge({},triangleStyle,usrStyles.triangle));
        _generateStyleClass('._xs_selectbox_'+uuid+' .hover', _merge({}, itemsHoverStyle, usrStyles['items:hover']));
        _generateStyleClass('._xs_container_'+uuid+' ._xs_select_disabled_'+uuid+' .triangle',{
            display:'none'
        });
        _generateStyleClass('._xs_items_'+uuid+'.selected',  _merge({}, itemsStyle, usrStyles['items:selected']));


        return {formula:formula,extClass:extClass};
        /* styles.containerStyle =;
         styles.selectStyle = _merge({}, selectStyle, usrStyles.select);
         styles.selectboxStyle = _merge({}, selectboxStyle, usrStyles.selectbox);
         styles.iconStyle = _merge({}, iconStyle, usrStyles.icon);
         styles.triangleStyle=_merge({},triangleStyle,usrStyles.triangle);
         styles.itemsStyle = _merge({}, itemsStyle, usrStyles.items);
         styles.stripsStyle = _merge({}, stripsStyle, usrStyles.strips);
         styles.itemHover = _merge({}, itemsHoverStyle, usrStyles['items:hover']);
         styles.disableStyle=_merge({}, disableStyle, usrStyles['disabled']);*/
        // return styles;

    }
    function _adjustStyles(w,h,size,marginV,marginH,paddingV,paddingH){
        var styles={};
        /* marginH = marginH || 10;
         marginV = marginV || 0;*/
        paddingH = paddingH || 0;
        paddingV = paddingV || 5;
        var a = (h - size) / 2;
        if (a < 0)a = 0;
        styles.containerStyle=_merge({}, {
            'font-size': size + 'px',
            'line-height': size + 'px'

        });
        styles.selectStyle= _merge({}, {
            'width': (w ) + 'px',
            //'height': size + 'px',
            'padding': a + 'px '+(marginH+paddingH)+'px'
        });

        styles.selectboxStyle=_merge({}, {
            //'width': w + 'px',
            'top': (h + 1) + 'px'
        });
        styles.iconStyle=_merge({}, {

        });

        styles.itemsStyle= _merge({},  {
            'padding': paddingV + 'px ' + paddingH + 'px',
            'height': size + 'px',
            'margin': marginV + 'px ' + marginH + 'px'

        });
        styles.stripsStyle=_merge({},  {
            //'width': (w - marginH * 2 - 10) + 'px',
            'margin':'0 '+ marginH + 'px'
        });
        styles.triangleStyle=_merge({},{
            'border-width': (h / 5) + 'px ' + (h / 5) * 0.7 + 'px 0px ' + (h / 5) * 0.7 + 'px'

        });
        styles.triangleSize={
            width:(h / 5) * 1.4,
            height:h/5
        };
        styles.widthData=w;
        styles.selectSize={
            width:w,
            height:h
        };
        return styles;
    }
    function _computeWidth(w,elements){

        if(w<20||w=='auto'){
            var el=elements.selectbox.cloneNode(true);
            el.style.visibility='hidden';
            el.style.display='block';
            el.style.position='absolute';
            document.body.appendChild(el);
            w=el.offsetWidth+30;
            document.body.removeChild(el);
        }
        var styles=elements.select.currentStyle||getComputedStyle(elements.select);
        elements.select.style.width=(w-(parseInt(styles.paddingLeft)||0)-(parseInt(styles.paddingRight)||0))+'px';
        elements.selectbox.style.width=w+'px';
        elements.selectbox.style.display='none';
        elements.selectbox.style.visibility='visible';
        console.log(w);

    }
    var _allSelectx=[];
    function initialize(uuid,styles,extStyles,configs,hookers){
        this.styles=styles;
        this.xselects=[];
        this.hookers = hookers||{};
        this.uuid=uuid;
        this.extStyles=extStyles;
        this.configs=configs||{};
        var triangle = _createElement('triangle'+(extStyles.extClass.triangle||''),styles.triangleStyle);
        this.icon=_createIcon(styles.selectSize,styles.triangleSize.width,styles.triangleSize.height,triangle);
        _allSelectx.push(this);

    }
    function factory(usrStyles,configs,hookers){

        var extStyles=_initStyles(_uuid,usrStyles);
        function newClass(w, h, size, marginV, marginH, paddingV, paddingH){
            var styles=_adjustStyles(w,h,size,marginV,marginH,paddingV,paddingH);
            initialize.call(this,arguments.callee.uuid,styles,extStyles,configs,hookers)
        }
        newClass.uuid=_uuid;
        newClass.prototype=_prototype;

        _uuid++;
        return newClass;
    }
    //
    function SelectX(w, h, size, marginV, marginH, paddingV, paddingH, usrStyles,configs) {

        var extStyles=_initStyles(_uuid,usrStyles);
        var styles=_adjustStyles(w,h,size,marginV,marginH,paddingV,paddingH);
        initialize.call(this,_uuid,styles,extStyles,configs);
        _uuid++;


    }
    function dealScroll(up,down,viewBox){
        addEventHandler(upArrow,'mousedown')
    }
    function _Scroll(scroll){
        if(scroll){
            var bodyBox=document.createElement('div'),scrollBox=document.createElement('div');
            var upArrow=document.createElement('div'),downArrow=document.createElement('div');
            upArrow.innerHTML=scroll.upArrowHtml;
            downArrow.innerHTML=scroll.downArrowHtml;
            bodyBox.appendChild(scrollBox);
            bodyBox.style.cssText='height:'+scroll.height+'px;overflow:hidden;';
            this.start=scroll.start>0?scroll.start:0;
            this.end=scroll.end>0?scroll.end:0;

            this.bodyBox=bodyBox;
            this.scrollBox=scrollBox;
            this.upArrow=upArrow;
            this.downArrow=downArrow;
            this.active=true;
        }
    }
    //todo 思考一下这里的代理扩展 应该怎么做
    _Scroll.prototype={
        delegate:function(selectbox,item,strips,i,length){
            if(this.active){
                this.proxy.apply(this,Array.prototype.slice.call(arguments));

            }
            else {
                this.origin.apply(this,Array.prototype.slice.call(arguments));
            }
        },
        proxy:function(selectbox,item,strips,i,length){
            if(i==this.start){

                selectbox.appendChild(this.upArrow);
                selectbox.appendChild(this.bodyBox);
            }
            if(i>=this.start&&i<=length-1-this.end){
                this.scrollBox.appendChild(item);
                this.scrollBox.appendChild(strips);
            }
            else{
                selectbox.appendChild(item);
                selectbox.appendChild(strips);
            }
            if(i==length-1-this.end){
                selectbox.appendChild(this.downArrow);
            }
        },
        origin:function(selectbox,item,strips,i,length){
            selectbox.appendChild(item);
            if(i!=length-1)selectbox.appendChild(strips);
        }
    };
    function _createIcon(size,w,h,html,marginRight){
        var iconCtn=document.createElement('div');
        iconCtn.className='_xs_icon';
        if(html&&html.tagName!=undefined&&html.nodeType!==undefined){
            iconCtn.appendChild(html);
        }
        else if(typeof html=='string'){
            iconCtn.innerHTML=html;
        }
        else {
            return iconCtn;
        }
        var top=(size.height-h)/2;
        if(top<0)top=0;

        var right=marginRight||(w/size.width)>0.3?5:10;
        iconCtn.style.cssText="top:"+top+'px;right:'+right+'px;width:'+w+'px;height:'+h+'px;position:absolute;';

        return iconCtn;

    }
    function _reRender(selectx){
        selectx.datas=_resolveSelectTag(selectx.bindSelect);

        selectx.elements.select.firstChild.innerHTML =selectx.datas.selectedContent;
        selectx.elements.selectbox.innerHTML='';
        var scroll=new _Scroll(selectx.host.configs.scroll);
        for (var i = 0; i < selectx.datas.length; i++) {
            var item = selectx.elements.items.cloneNode(true);
            item.innerHTML = '' + selectx.datas[i].text + '';
            item._index = i;
            scroll.delegate(selectx.elements.selectbox,item,selectx.elements.strips.cloneNode(true),i,selectx.datas.length);
            var formula=selectx.host.extStyles.formula.items;
            for(var key in formula){
                if(Formula.test(key,i,selectx.datas.length)){
                    item.className+=formula[key];
                }
            }
            addEventHandler(item,'mouseover',_hoverHandler);
            addEventHandler(item,'click',_itemsClickedHandler);


        }

        _computeWidth(selectx.host.styles.selectSize.width,selectx.elements);
    }
    function _render(selectx) {

        selectx.elements.container.appendChild(selectx.elements.select);

        selectx.elements.select.innerHTML = '<span>' + selectx.datas.selectedContent||'' + '</span>';
        selectx.elements.select.appendChild(selectx.elements.icon);
        selectx.elements.container.appendChild(selectx.elements.selectbox);


        var scroll=new _Scroll(selectx.host.configs.scroll);
        for (var i = 0; i < selectx.datas.length; i++) {
            var item = selectx.elements.items.cloneNode(true);
            item.innerHTML = '' + selectx.datas[i].text + '';
            item._index = i;
            scroll.delegate(selectx.elements.selectbox,item,selectx.elements.strips.cloneNode(true),i,selectx.datas.length);
            var formula=selectx.host.extStyles.formula.items;
            for(var key in formula){
                if(Formula.test(key,i,selectx.datas.length)){
                    item.className+=formula[key];
                }
            }
            addEventHandler(item,'mouseover',_hoverHandler);
            addEventHandler(item,'click',_itemsClickedHandler);


        }
        addEventHandler(selectx.elements.select,'click',_selectClickedHandler);
        _computeWidth(selectx.host.styles.selectSize.width,selectx.elements);

        addEventHandler(document,'click',function (evt) {
            selectx.collapse();
        });
        _Hooker('render:after',selectx.host,selectx);

    }

    function _Select(host){
        this.elements={};
        this.host=host;
        var extClass=host.extStyles.extClass;
        this.elements.container = _createElement('_xs_container_'+host.uuid+(extClass.container||''),host.styles.containerStyle);
        this.elements.select = _createElement('_xs_select_'+host.uuid+(extClass.select||''),host.styles.selectStyle);
        this.elements.selectbox = _createElement('_xs_selectbox_'+host.uuid+(extClass.selectbox||''),host.styles.selectboxStyle);
        this.elements.items = _createElement('_xs_items_'+host.uuid+(extClass.items||''),host.styles.itemsStyle);
        this.elements.strips = _createElement('_xs_strips_'+host.uuid+(extClass.strips||''),host.styles.stripsStyle);

        this.elements.icon=host.icon.cloneNode(true);
        this.selectedIndex = -1;
        Cache.createCache(this.elements.container, this);
        Cache.share(this.elements.container, this.elements.selectbox);

    }

    function searchItem(selectbox,index){

        var divs=selectbox.getElementsByTagName('div'),searchIdx=0;
        for(var i=0;i<divs.length;i++){

            if(/_xs_items/.test(divs[i].className)){
                searchIdx++;
            }
            if(searchIdx>index){
                return divs[i];
            }

        }
        return null;
    }
    _Select.prototype={
        select: function (index) {

            var ret = _Hooker('select', this.host, index);
            var lastHoverEl = this.lastHoverEl;
            if (ret !== false) {
                if (lastHoverEl) {
                    lastHoverEl.className = lastHoverEl.className.replace(/hover/g, '');
                }
                var span = this.elements.select.firstChild;
                var oldV = span.innerHTML;
                var newV = index<0?'':this.datas[index].text;

                if(this.selectedItem){

                    this.selectedItem.className=this.selectedItem.className.replace(/ *selected/g,'');
                }
                var item=searchItem(this.elements.selectbox,index);
                item.className+=' selected';
                this.selectedItem=item;
                if (this.bindSelect) {
                    this.bindSelect.selectedIndex = index;
                    fireEvent(this.bindSelect,'change');
                }
                this.selectedIndex = index;
                if (newV != oldV) {
                    span.innerHTML = newV;
                    span.setAttribute('title',newV);
                    _Hooker('change', this.host, this,index, item);

                }


                this.collapse();
            }
            _Hooker('select:after', this.host, index);
            this.clearError();
            return this;

        },
        setError:function(css){
            if(css){
                this.elements.className+=' '+css;

            }
            else {
                this.elements.select.style.border='1px solid #f00';
            }
            this._errorCss=css;
            this.hasError=true;

        },
        clearError:function(){
            if(this.hasError){
                if(this._errorCss){
                    this.elements.className=this.elements.className.replace(this._errorCss,'');
                }
                else{
                    this.elements.select.style.cssText=this.elements.select.style.cssText.replace(/border:[^;]*/g,'');
                }
                this.hasError=false;
            }
        },
        show:function(){
            this.elements.container.style.display='';
        },
        hide:function(){
            this.elements.container.style.display='none';
        },
        disable:function(){

            this.elements.select.className+=' _xs_select_disabled_'+this.host.uuid;
            this.disabled=true;

        },
        enable:function(){
            this.elements.select.className=this.elements.select.className.replace(' _xs_select_disabled_'+this.host.uuid,'');
            this.disabled=false;

        },
        collapse:function () {
            this.elements.selectbox.style.display = 'none';
            this.isShow = false;

        },
        expand:function (){
            this.elements.selectbox.style.display = 'block';
            this.isShow = true;
        },
        toggle:function (){
            if (this.isShow) {
                this.collapse();
            }
            else {
                this.expand();
            }

        }
    };

    var _prototype = {
        applyStyle: function (styles) {
            for(var key in styles){
                _merge(this.styles[key+'Style'],styles[key]);

            }
            return this;
        },
        loadData: function (data) {
            if(Object.prototype.toString.call(data)=='[object String]'){
                data=document.querySelectorAll(data)[0];
            }
            if (data.tagName&&data.tagName==='SELECT') {
                this.bindSelect = data;
                this.datas = _resolveSelectTag(data);
                //autoWidth(this.elements,this.datas);
            }


            else {
                this.datas={};
            }
            return this;
        },
        bindTo: function (selector) {

        },
        createIcon:function(w,h,html,marginRight){
            this.icon=_createIcon(this.styles.selectSize,w,h,html,marginRight);
            return this;
        },
        renderTo: function (selector,idx,isHide) {

            var index = selector.indexOf('@');
            var position;
            if (index == -1) {
                position = 'Append'
            }
            else {
                position = selector.substr(0, index);
                position = position.replace(/^./, function (m) {
                    return m.toUpperCase();
                });

                selector = selector.slice(index + 1);
            }
            var elems = document.querySelectorAll(selector);

            if(elems&&elems.length>0){
                for(var i=0;i<elems.length;i++){
                    var el=elems[i];
                    var _xselect=new _Select(this);
                    _xselect.widthData=this.styles.widthData;

                    this.xselects.push(_xselect);
                    if(el.tagName==="SELECT"){
                        this.loadData.call(_xselect,el);
                        el.style.display='none';
                        el._bindSelectX=_xselect;
                    }
                    var fn = this['render' + position];
                    fn && fn.call(_xselect, el,isHide);
                }

            }
            if(typeof idx=='number'){

                this.select(idx);
            }
            return this;

        },
        renderAppend: function (el, isHide) {
            if (el) {
                el.appendChild(this.elements.container);
                isHide && (el.style.display = 'none');
                _render(this);

            }
            else {
                console.error('function renderAppend need a htmlelement as its first argument');
            }
            return this;
        },
        renderPrepend: function (el, isHide) {
            if (el) {
                el.insertBefore(this.elements.container, el.firstChild);
                isHide && (el.style.display = 'none');
                _render(this);
            }
            else {
                console.error('function renderAppend need a htmlelement as its first argument');
            }
            return this;
        },
        renderBefore: function (el, isHide) {
            if (!el) {
                el = this.bindSelect;
                isHide = isHide || true;
            }
            else {
                var parent = el.parentNode;
                if (parent) {
                    parent.insertBefore(this.elements.container, el);
                    isHide && (el.style.display = 'none');
                    _render(this);
                }

            }
            return this;
        },
        renderAfter: function (el, isHide) {
            if (!el) {
                el = this.bindSelect;
                isHide = isHide || true;
            }
            else {

                var parent = el.parentNode;
                if (parent) {
                    if (el.nextSibling) {
                        parent.insertBefore(this.elements.container, el.nextSibling);
                    }
                    else {
                        parent.appendChild(this.elements.container);
                    }

                    isHide && (el.style.display = 'none');
                    _render(this);
                }
            }
            return this;
        },

        hooker: function (type, fn) {

            if (typeof type === 'object' && !(type instanceof String)) {

                for (var key in type) {
                    console.log(key)
                    this.hookers[key] = type[key];
                    console.log(this);
                }
            }
            else {
                this.hookers[type] = fn;
            }
            return this;
        },

        disable:function(index){
            if(index>=0){
                this.xselects[index].disable();
            }
            else{
                for(var i=0;i<this.xselects.length;i++){
                    this.xselects[i].disable();
                }
            }
        },
        enable:function(index){
            if(index>=0){
                this.xselects[index].enable();
            }
            else{
                for(var i=0;i<this.xselects.length;i++){
                    this.xselects[i].enable();
                }
            }
        },
        show:function(index){
            if(index>=0){
                this.xselects[index].show();
            }
            else{
                for(var i=0;i<this.xselects.length;i++){
                    this.xselects[i].show();
                }
            }
        },
        hide:function(index){
            if(index>=0){
                this.xselects[index].hide();
            }
            else{
                for(var i=0;i<this.xselects.length;i++){
                    this.xselects[i].hide();
                }
            }
        },
        expand:function(index){
            if(index>=0){
                this.xselects[index].expand();
            }
            else{
                for(var i=0;i<this.xselects.length;i++){
                    this.xselects[i].expand();
                }
            }
        },
        collapse:function(index){
            if(index>=0){
                this.xselects[index].collapse();
            }
            else{
                for(var i=0;i<this.xselects.length;i++){
                    this.xselects[i].collapse();
                }
            }
        },
        select:function(index,idx){
            if(idx!=undefined){
                this.xselects[idx].select(index);
            }
            else{
                for(var i=0;i<this.xselects.length;i++){
                    this.xselects[i].select(index);
                }
            }
        },
        toggle:function(index){
            if(index>=0){
                this.xselects[index].toggle();
            }
            else{
                for(var i=0;i<this.xselects.length;i++){
                    this.xselects[i].toggle();
                }
            }
        }
    };

    SelectX.prototype=_prototype;
    SelectX.factory=factory;
    SelectX.reRender=function(el){
        if(el._bindSelectX){
            _reRender(el._bindSelectX);
        }
    }
    SelectX.all=function(action){
        if(arguments.length==0){
            return _allSelectx;
        }
        else if(typeof action=='object'&&action.tagName=='SELECT'){
            if(arguments.length>1){
                action._bindSelectX[arguments[1]].apply(action._bindSelectX,Array.prototype.slice.call(arguments,2));
            }
            else {
                return action._bindSelectX;
            }
        }
        else{
            for(var i=0;i<_allSelectx.length;i++){
                if(_prototype.hasOwnProperty(action)){
                    _allSelectx[i][action].apply(_allSelectx[i],Array.prototype.slice.call(arguments,1));
                }
            }
        }
    }
    return SelectX;
}(window);


window.HitourSelect=SelectX.factory({

    container:{
        color:'#707070',
        display:'inline-block',
        '*display':'inline',
        '*zoom':1,
        '-webkit-user-select':'none',
        'vertical-align':'middle',
        '@':'hitour-select'

    },
    selectbox:{
        'border':'1px solid #d2d2d2',
        'box-shadow':'2px 2px 8px #BEBEBE',
        'max-height':'200px',
        'overflow-x':'hidden',
        'overflow-y':'auto',
        'z-index':1001
    },
    items:{
        'text-align':'left',
        color:'#707070'
    },
    select:{
        'border-color':'#D2D2D2'
    },
    triangle:{
        'border-top-color':'#00B98B',
        'display':'none'
    },
    strips:{
        'border-color':'#eee'

    },
    'items:hover':{
        color:'#00b98b',
        backgroundColor:'#fff'
    },
    'items:selected':{
        color:'#000'
    }
});

window.CircleSelect=SelectX.factory({

        selectbox:{

            'overflow-x':'hidden',
            'overflow-y':'auto',
            'z-index':1001,
            'border':'0',
            'background-color':'transparent',
            'box-shadow':'2px 2px 8px #BEBEBE',
            'border-radius':'17px'
        },
        items:{
            'text-align':'center',
            color:'#707070',
            'background-color':'#fff'
        },
        select:{

            '@':'circle-select'
        },
        triangle:{
            'border-top-color':'#00B98B',
            display:'none'
        },
        strips:{
            'border-bottom':'1px solid #BEBEBE'
        },
        'items:hover':{
            'background-color':'#ECECE7',
            'color':'#000'
        },
        'items:selected':{
            'background-color':'#00b98b',
            'color':'#fff'
        },
        'items:nth-child(1)':{
            '@':'selectbox-top'
        },
        'items:nth-child(-1)':{
            '@':'selectbox-bottom'

        }
    },{
        scroll:{
            height:400,
            start:1,
            end:1,
            upArrowHtml:'',
            downArrowHtml:''
        }
    }


);