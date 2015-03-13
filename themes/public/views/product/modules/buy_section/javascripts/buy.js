/**
 * Created by godsong on 15-1-19.
 */



var OrderModel = {quantities : {}};
var buying = false;
//region Special Code ViewModel
var SpecialCode = new ViewModel('SpecialCode', 'flag|false,show,status|"init",special_codes|[],special_title,select,show_desc,preview_ticket_name,selected_index,special_code|{},mousein,mouseout,cur_special|{},ticket_prices|[],top');
SpecialCode.applyData = function(data) {
    var preview_ticket = data.ticket_types[2] || data.ticket_types[1] || data.ticket_types[99];
    //region 预处理special_codes
    var special_list = [];
    var pl = getPricePlan(data.price_plan, OrderModel.tour_date);
    var date = DateUtil.parse(OrderModel.tour_date)||new Date();
    var spcList = [];
    for (var spc in pl) {
        for (var k in pl[spc]) {
            for (var customer_count in pl[spc][k]) {
                var f = pl[spc][k][customer_count].frequency;
                if (f && f != 'wdall') {
                    f = f.split(';');
                    for (var j = 0; j < f.length; j++) {
                        if (date.getDay() == f[j].substr(2, 1) % 7) {
                            if (!data.spc || data.spc == spc) {
                                spcList.push(spc);
                            }
                            break;
                        }
                    }
                }
                else if(f=='wdall'){
                    if (!data.spc || data.spc == spc) {
                        spcList.push(spc);
                    }
                }
                break;
            }
            break;
        }
    }
    var sl = spcList.join(','),idx=0;
    for (var i = 0; i < data.special_codes.length; i++) {
        var special_code = data.special_codes[i];
        if (sl.indexOf(findComboSpecialCode(special_code.special_code)) != -1) {
            special_code.checked = false;
            special_code.index = idx++;
            special_code.show = true;
            special_code.z_index = 1;
            special_list.push(special_code);
            special_code.price = calcPrice(data.price_plan, findComboSpecialCode(special_code.special_code), OrderModel.tour_date, preview_ticket);
        }
    }

    special_list.sort(function(a,b){
        return a.price.price- b.price.price;
    });
    var isDiff=false,self=this;
    if(this.viewModel.special_codes.length==0&&special_list.length>0)isDiff=true;
    special_list.forEach(function(e,i){
        e.index=i;
        if(self.viewModel.special_codes[i]&&e.special_code!=self.viewModel.special_codes[i].special_code){
            isDiff=true;
        }
    });
    if(isDiff) {
        if (special_list.length > 3 && this.viewModel.selected_index == undefined) {

            this.viewModel.selected_index = this.viewModel.selected_index || 0;
            special_list[this.viewModel.selected_index].checked = true;
            OrderModel.special_code = special_list[this.viewModel.selected_index].special_code;
        }
        else {
            this.viewModel.selected_index = -1;
        }
        //endregion
        this.viewModel.special_codes = special_list;
        this.viewModel.special_code = special_list[0];
        this.viewModel.flag = true;
    }

};
//endregion
//region Departure ViewModel
var Departure = new ViewModel('Departure', 'status|"disable",departure_title,departure_text,departures|[],open,show|false,flag|false,selectDeparture');
Departure.applyData = function(data, date_str) {
    if(data.departure_rule.length > 0) {
        var departures = [];
        for(i = 0; i < data.departure_rule.length; i++) {
            var dep = data.departure_rule[i];

            if(dep.valid_region == 0 || (date_str >= dep.from_date && date_str <= dep.to_date)) {
                if(dep.time == '00:00:00') {
                    var showTime = '';
                }
                else {
                    showTime = dep.time.slice(0, -3)
                }
                if(dep.additional_limit) {
                    var limits = dep.additional_limit.split(';');
                    for(var j = 0; j < limits.length; j++) {
                        var limit = limits[j];
                        if(limit.indexOf('周') != -1) {
                            if(DateUtil.parse(date_str).getDay() == (+limit.slice(1) % 7)) {
                                departures.push({
                                    time            : dep.time,
                                    showTime        : showTime,
                                    departure_point : dep.departure.departure_point,
                                    code            : dep.departure.departure_code
                                });
                            }
                            else if(date_str == limit) {
                                departures.push({
                                    time            : dep.time,
                                    showTime        : showTime,
                                    departure_point : dep.departure.departure_point,
                                    code            : dep.departure.departure_code
                                });
                            }
                        }
                    }
                }
                else {
                    departures.push({
                        time            : dep.time,
                        showTime        : showTime,
                        departure_point : dep.departure.departure_point,
                        code            : dep.departure.departure_code
                    });
                }
            }
        }
        this.viewModel.departures = departures;
        this.viewModel.status = 'init';
        this.viewModel.departure_text = '';
        this.viewModel.flag = true;
    }
}
//endregion
//region TourDate ViewModel
var TourDate = new ViewModel('TourDate', 'tour_date_title,flag|false,status|"init",date_text,open');
TourDate.viewModel.open = function(evt) {

    doExceptThis(TourDate, closeField);
};
//endregion
//region TicketType ViewModel
var TicketType = new ViewModel('TicketType', 'ticket_types|[],addCart,show|false,show_panel|false,ticket_tips|"",sum_price,status|"disable",counterAdd,counterReduce,open');
TicketType.applyData = function(data, special_code, date_str) {
    //var pricePlan = getPricePlan(data.price_plan, dateStr);
    this.viewModel.status='init';
    this.viewModel.ticket_types = genTicketTypes(data, special_code, date_str);

};
//endregion
//region SpecialGroup ViewModel
var SpecialGroup=new ViewModel('SpecialGroup','special_groups|[],open,select');

SpecialGroup.initData=function(data){
    var groups=[];
    data.special_groups.forEach(function(e){
        groups.push({
            show:false,
            status:'disable',
            select:{},
            title: e.title,
            special_codes: e.special_codes.concat(),
            group_id: e.group_id
        });
    });

    this.viewModel.special_groups=groups;
    this.viewModel.open=function(gp,evt){
        evt.stopPropagation();
        doExceptThis(SpecialGroup,closeField,gp);
        if(gp.status=='disable'){
            animateTips();
        }
        else{
            gp.show=!gp.show;
        }
    };

    this.viewModel.select=function(group_index,sp){
        console.log(group_index);
        SpecialGroup.viewModel.special_groups[group_index].select=sp.$model;
        SpecialGroup.viewModel.special_groups[group_index].status='complete';
        var prevGroups=SpecialGroup.viewModel.special_groups.$model.slice(0,group_index+1);
        var afterGroups=data.special_groups.slice(group_index+1);
        var prefix='',i=0;
        if(sp.item_limit&&sp.item_limit.limit_pax_num==1) {
            data.sale_rule.min_num = sp.item_limit.min_pax_num||1;
            data.sale_rule.max_num = sp.item_limit.max_pax_num;
        }
        console.log(prevGroups,afterGroups);
        prevGroups.forEach(function(gp){
            prefix+=gp.group_id+':'+gp.select.special_code;
            prefix+='|';
        });
        if(afterGroups.length>0) {
            updateSpecialGroup(data,afterGroups,group_index,prefix);
        }
        else{
            var price=checkPrice(data.price_plan, findByGroupInfo(prefix.slice(0,-1)), DateUtil.parse(OrderModel.tour_date));
            OrderModel.special_code=prefix.slice(0,-1);
            TicketType.applyData(data, OrderModel.special_code);
            console.log(price);
        }
    }

};
function updateSpecialGroup(data,groups,index,prefix){
    var result=[],filter={};
        DProduct(groups, result, 0, prefix);
        result.forEach(function(r) {
            if(checkPrice(data.price_plan, findByGroupInfo(r), DateUtil.parse(OrderModel.tour_date))) {
                filter[r.split('|')[index + 1].split(':')[1]] = true;
            }
        });
        var filtered_special_codes = [];
        data.special_groups[index + 1].special_codes.forEach(function(sp) {
            if(filter[sp.special_code]) {
                filtered_special_codes.push(sp);

            }
        });
        SpecialGroup.viewModel.special_groups[index + 1].special_codes = filtered_special_codes;
        SpecialGroup.viewModel.special_groups[index + 1].status = 'init';
        for(var i=index+1;i<SpecialGroup.viewModel.special_groups.length;i++){
            SpecialGroup.viewModel.special_groups[i].select={};
            if(i>index+1){
                SpecialGroup.viewModel.special_groups[i].status='disable';
            }
        }
        TicketType.viewModel.status='disable';
        TicketType.viewModel.show_panel = false;
        doscroll();
        refreshNav();
}
function checkPrice(price_plan,combo_special_code,date){
    date=date||new Date();
    var plan=getPricePlan(price_plan,date.format());
    if(!plan[combo_special_code]){
        return false;
    }
    else{
        var price=(plan[combo_special_code][1]||plan[combo_special_code][99])[1];
        if(price&&price.frequency){
            if(price.frequency=='wdall'){
                return price;
            }
            else{
                var freq=price.frequency.split(';');
                for(var i=0;i<freq.length;i++){
                    if(date.getDay() == freq[i].substr(2, 1) % 7){
                        return price;
                    }
                }
                return false;
            }
        }
        else{
            return false;
        }
    }
}
function DProduct(groups,result,layer,cur){//计算笛卡尔积
    if(layer<groups.length-1) {
        for(var i = 0; i < groups[layer].special_codes.length; i++) {
            DProduct(groups, result, layer + 1, cur+groups[layer].group_id+':'+groups[layer].special_codes[i].special_code+'|');
        }
    }
    else{
        for(i = 0; i < groups[layer].special_codes.length; i++) {
            result.push(cur+groups[layer].group_id+':'+groups[layer].special_codes[i].special_code);
        }
    }
}


SpecialGroup.enable=function(id){
    for(var i=0;i<this.viewModel.special_groups.length;i++){
        var gp=this.viewModel.special_groups[i];
        if((id!==undefined&&i==id)||gp.status=='disable'){
            gp.status='enable';
            break;
        }
    }

};
SpecialGroup.closeExcept=function(group){
    for(var i=0;i<this.viewModel.special_groups.length;i++){
        var gp=this.viewModel.special_groups[i];
        if(gp!==group)gp.show=false;
    }
};
//endregion

//模块主体
Module('buysection', function(data) {
    Departure.viewModel.departure_title = data.description.departure_title;
    TourDate.viewModel.tour_date_title=data.description.tour_date_title;
    OrderModel.product_id = data.product_id;
    //SpecialCode.viewModel.preview_ticket_name=preview_ticket.ticket_id==2?'成人':'';
    var _tipTimer;
    if(data.special_info) {
        data.special_groups=resolveSpecialInfo(data.special_info);
        data.special_codes=data.special_groups.length>0?data.special_groups[0].special_codes:[];
        if(data.type==10){//商品分支处理 对包车商品单独处理
            SpecialGroup.initData(data);

            //TicketType.applyData(data, data.special_info && SpecialGroup.viewModel.special_codes[0].special_code);
        }
        else{
            SpecialCode.viewModel.special_title = data.special_groups[0].title;
            SpecialCode.applyData(data);
            TicketType.applyData(data, data.special_info && SpecialCode.viewModel.special_codes[0].special_code);
        }

    }
    else{
        TicketType.applyData(data, 0);
    }
    TicketType.viewModel.addCart = function(evt) {
        evt.stopPropagation();
        if(!TicketType.viewModel.show_panel) {
            var dest = $('.order-panel').offset().top, cur = $(document).scrollTop();
            if(!checkComplete(true) && cur > dest) {

                $('body,html').animate({scrollTop : dest}, 600);
            }
            return;
        }
        if(!buying && (this.innerHTML == '预订' || this.innerHTML == '购买')) {
            if(data.type==10){
                for(var k in OrderModel.quantities) {
                    OrderModel['quantities[' + k + ']'] =1;
                    OrderModel['pax_num']=OrderModel.quantities[k];
                }
                for(var i=0;i<SpecialGroup.viewModel.special_groups.length;i++){
                    var gp=SpecialGroup.viewModel.special_groups[i];
                    OrderModel['special_info['+i+'][title]']=gp.title;
                    OrderModel['special_info['+i+'][special_name]']=gp.select.cn_name;
                }
            }
            else {
                for(k in OrderModel.quantities) {
                    OrderModel['quantities[' + k + ']'] = OrderModel.quantities[k];
                }
                delete OrderModel.quantities;
                if(OrderModel.special_name){
                    OrderModel['special_info[0][title]']=data.description.special_title;
                    OrderModel['special_info[0][special_name]']=OrderModel.special_name;
                }
            }
            OrderModel.special_code = findComboSpecialCode(OrderModel.special_code||SpecialCode.viewModel.special_code.special_code)||undefined;
            OrderModel.activity_id=data.activity_info['activity_id'];
            var oldV = this.innerHTML;
            this.innerHTML = '下单中...';
            var self = this;
            buying = true;
            this.setAttribute('disabled', 'disabled');
            $.ajax({
                url      : $request_urls.addCart,
                dataType : 'json',
                type     : 'post',
                data     : OrderModel,
                success  : function(res) {
                    if(res.code == 200) {
                        location.href = res.data.checkout_url;
                    }
                    else {
                        alert(res.msg);
                        buying = false;
                        self.innerHTML = oldV;
                        self.removeAttribute('disabled');
                    }
                },
                error    : function() {
                    alert('后台异常，请稍后再试，或联系客服');
                    buying = false;
                    self.innerHTML = oldV;
                }
            });
        }
    }

    TicketType.viewModel.counterAdd = function(idx, evt) {
        TicketType.viewModel.status = 'complete';
        if(sumNum() == data.sale_rule.max_num) {
            TicketType.viewModel.ticket_tips = '您最多可以订' + data.sale_rule.max_num + '张票';
            clearTimeout(_tipTimer);
            _tipTimer = setTimeout(function() {
                TicketType.viewModel.ticket_tips = '';
            }, 4000);
        }
        else {
            TicketType.viewModel.ticket_types[idx].quantity = TicketType.viewModel.ticket_types[idx].quantity + 1;
            OrderModel.quantities[TicketType.viewModel.ticket_types[idx].ticketId] = TicketType.viewModel.ticket_types[idx].quantity;

        }
        calcSumPrice(data.type==10);
        evt.stopPropagation();

    };

    TicketType.viewModel.counterReduce = function(idx, evt) {
        var ticket = TicketType.viewModel.ticket_types[idx];
        TicketType.viewModel.status = 'complete';
        var sum = sumNum();
        if(ticket.quantity == 0) {

        } else if(sum == data.sale_rule.min_num) {
            clearTimeout(_tipTimer);
            TicketType.viewModel.ticket_tips = '请至少预订' + data.sale_rule.min_num + '张票';
            _tipTimer = setTimeout(function() {
                TicketType.viewModel.ticket_tips = '';
            }, 4000);
        }
        else if(ticket.quantity == data.ticket_types[ticket.ticketId].min_num) {
            clearTimeout(_tipTimer);
            TicketType.viewModel.ticket_tips = '请至少预订' + data.sale_rule.min_num + '张' +
                                               data.ticket_types[ticket.ticketId].ticket_type.cn_name + '票';
            _tipTimer = setTimeout(function() {
                TicketType.viewModel.ticket_tips = '';
            }, 4000);
        }

        else {
            if(dealIndependent(idx,data)) {
                OrderModel.quantities[TicketType.viewModel.ticket_types[idx].ticketId] = TicketType.viewModel.ticket_types[idx].quantity;

            }
        }
        calcSumPrice(data.type==10);
        evt.stopPropagation();
    };
    if(data.date_rule.need_tour_date == 1) {
        TourDate.viewModel.flag = true;
        var limits = getTourDateLimits(data.date_rule);
        var start = DateUtil.parse(data.date_rule.start), protect = 100000;
        while(limits.disable(start, DateUtil.format(start)) && protect-- > 0) {
            start.setDate(start.getDate() + 1);
            if(DateUtil.format(start) >= data.date_rule.end) {
                break;
            }
        }
        OrderModel.tour_date = DateUtil.format(start);
        if(data.rules.sale_desc) {
            var sale_desc = '<div class="buy-rule">' + data.rules.sale_desc + '</div>'
        }
        else {
            sale_desc = '';
        }
        if(data.departure_rule.length > 0) {
            Departure.viewModel.flag = true;
        }
        new XCalendar($('#tour_date'), {
            width       : 270,
            min         : limits.start,
            max         : limits.end,
            disable     : limits.disable,
            bindClick   : true,
            secondClick : true,
            partials    : {
                'append' : '<div class="date-legend"><div class="legend-item" style="margin-right: 30px;width: 120px;"><div class="date-enable"></div>可选日期</div><div class="legend-item"><div class="date-disable"></div>关闭时间</div>' +
                           sale_desc + '</div><div class="arrow-up"></div>'
            },
            onClose     : function() {
            },
            onSelect    : function(date, date_str) {
                OrderModel.tour_date = date_str;
                TourDate.viewModel.date_text = date_str;
                TourDate.viewModel.status = 'complete';
                Departure.applyData(data, date_str);
                if(data.type==10){
                    //SpecialGroup.enable(0);
                    updateSpecialGroup(data,data.special_groups,-1,'');
                }
                else {
                    data.special_info && SpecialCode.applyData(data);
                }
                showOrder();
            }
        });
    }
    else {

        $('#tour_date').parent().hide();
        if(data.type==10){
            SpecialGroup.enable();
        }
        else {
            data.special_info && SpecialCode.applyData(data);
        }
        Departure.applyData(data, new Date().format());
        TourDate.viewModel.status = 'complete';
        TourDate.viewModel.flag = false;
    }
    if(data.departure_rule.length == 0) {
        Departure.viewModel.status = 'complete';
    }
    var _scroll_timer;
    SpecialCode.viewModel.select = function(sp) {
        SpecialCode.viewModel.show_desc = false;
        _scroll_timer = setInterval(doscroll, 16.6)
        setTimeout(function() {
            clearInterval(_scroll_timer);
            refreshNav();
        }, 600);
        if(SpecialCode.viewModel.selected_index >= 0) {
            if(SpecialCode.viewModel.selected_index != sp.index) {
                SpecialCode.viewModel.special_codes[SpecialCode.viewModel.selected_index].checked = false;
                sp.checked = true;
                SpecialCode.viewModel.selected_index = sp.index;
                SpecialCode.viewModel.special_code = sp.$model;
                OrderModel.special_code=sp.special_code;
                OrderModel.special_name=sp.cn_name;
                calcSumPrice(data.type==10);
                //$('body,html').animate({ scrollTop: $('.order-panel').offset().top }, 600);
            }
            else {
                SpecialCode.viewModel.selected_index = -1;
                sp.checked = false;
            }

        }
        else {
            sp.checked = true;
            SpecialCode.viewModel.special_code = sp.$model;
            SpecialCode.viewModel.selected_index = sp.index;
            OrderModel.special_code=sp.special_code;
            OrderModel.special_name=sp.cn_name;
            calcSumPrice(data.type==10);
            //$('body,html').animate({ scrollTop: $('.order-panel').offset().top }, 600);
        }
        showOrder();

    };
    SpecialCode.viewModel.mousein = function(index, sp) {
        clearTimeout(_special_desc_timer);
        index = SpecialCode.viewModel.selected_index >= 0 ? 0 : index;
        SpecialCode.viewModel.cur_special = sp.$model;
        SpecialCode.viewModel.ticket_prices = genTicketTypes(data, sp.special_code, OrderModel.tour_date);
        SpecialCode.viewModel.top = 40 + 53 * index;
        SpecialCode.viewModel.show_desc = true;
    };
    var _special_desc_timer;
    SpecialCode.viewModel.mouseout = function() {
        clearTimeout(_special_desc_timer);
        _special_desc_timer = setTimeout(function() {
            SpecialCode.viewModel.show_desc = false;
        }, 200);

    }
    Departure.viewModel.open = function(evt) {
        evt.stopPropagation();
        doExceptThis(Departure, closeField);
        if(Departure.viewModel.status != 'disable') {
            Departure.viewModel.show = !Departure.viewModel.show;
        }
    };
    Departure.viewModel.selectDeparture = function(dp) {
        Departure.viewModel.departure_text = dp.showTime + ' ' + dp.departure_point;
        OrderModel.departure_time = dp.time;
        OrderModel.departure_code = dp.code;
        Departure.viewModel.show = false;
        Departure.viewModel.status = 'complete';
        showOrder();
    };
    /*if(SpecialCode.viewModel.special_codes.length>0&&SpecialCode.viewModel.selected_index!=-1) {
        //SpecialCode.viewModel.selected_index = 0;
        //SpecialCode.viewModel.special_codes[0].checked = true;
        //SpecialCode.viewModel.special_code = SpecialCode.viewModel.special_codes[0].$model;
        TicketType.applyData(data, SpecialCode.viewModel.special_codes[SpecialCode.viewModel.selected_index].special_code, OrderModel.tour_date);
    }*/
    $(document).on('click', function() {
        doExceptThis(null, function(gp) {
            if(this===SpecialGroup){
                SpecialGroup.closeExcept(gp);
            }
            else{
                this.viewModel.show=false;
            }
            if(this.name == 'TicketType') {
                showOrder();
            }
        });

    });

    $(window).on('scroll', doscroll);
    function showOrder() {
        if(SpecialCode.viewModel.selected_index >= 0 && checkComplete()) {
            calcSumPrice(data.type==10);
            TicketType.viewModel.show_panel = true;
            doscroll();
            //setTimeout(doscroll,50);\
            refreshNav();
            return true;
        }
        else {
            TicketType.viewModel.show_panel = false;
            refreshNav(data.type==10);
            return false;
        }

    }

    function calcSumPrice(fixed_price) {
        var sum = 0, orig_sum = 0;
        for(var i = 0; i < TicketType.viewModel.ticket_types.length; i++) {
            var ticket = TicketType.viewModel.ticket_types[i];
            var priceObj= calcPrice(data.price_plan, findComboSpecialCode(OrderModel.special_code || data.special_info &&
                                                                             data.special_codes[0].special_code), OrderModel.tour_date, ticket);
            ticket.price =priceObj.price;
            ticket.orig_price=priceObj.orig_price;
            sum += (fixed_price?1:ticket.quantity) * ticket.price;
            orig_sum += (fixed_price?1:ticket.quantity) * ticket.orig_price;
        }
        TicketType.viewModel.sum_price = sum;

    }

    TicketType.viewModel.open = function(evt) {
        evt.stopPropagation();
        doExceptThis(TicketType, closeField);
        if(TicketType.viewModel.status=='disable'){
            animateTips();
        }
        else{
            TicketType.viewModel.status = 'complete';
            TicketType.viewModel.show = !TicketType.viewModel.show;
            if(!TicketType.viewModel.show)showOrder();
        }


    };
}).load(productData);


//region 业务逻辑区



function getPricePlan(pricePlan, dateStr) {
    dateStr=dateStr||new Date().format();
    for (var i = 0; i < pricePlan.length; i++) {
        priceSection = pricePlan[i];

        if (priceSection.valid_region == '0' || dateStr >= priceSection.from_date && dateStr <= priceSection.to_date) {
            return priceSection.price_map;
        }
    }
    return null;
}
/**
 * 计算价钱
 * price_plan 价格计划
 * special_code rt
 * date_str日期字符串 yyyy-mm-dd
 * ticket 票种数据对象 至少要包含ticket_id和quantity
 */
function calcPrice(price_plan, special_code, date_str, ticket) {
    date_str = date_str || new Date().format();
    for(var i = 0; i < price_plan.length; i++) {
        var p = price_plan[i];
        if(p.valid_region == '0' || date_str >= p.from_date && date_str <= p.to_date) {
            var pm = p.price_map[special_code || 0];

            pm = (ticket && pm[ticket.ticketId] || pm[99] || pm[1] || pm[2]);
            var firstK;
            for(var k in pm){
                firstK=k;
                break;
            }
            return pm[ticket.quantity] || pm[1]||pm[firstK];
        }
    }
    return {price : -1, orig_price : -1};
}


function getTourDateLimits(date_rule) {
    if(date_rule.need_tour_date == 0) {
        return;
    }
    if(date_rule.operations.length > 0) {
        var mcloses = [];
    }
    else {
        mcloses = date_rule.close_dates.split(';');
    }

    var start = DateUtil.parse(date_rule.start), end = DateUtil.parse(date_rule.end);

    function disable(date, date_str) {
        for(var i = 0; i < date_rule.operations.length; i++) {
            var operation = date_rule.operations[i];
            if(date_str >= operation.from_date && date_str <= operation.to_date) {
                if(operation.close_dates) {
                    var closes = mcloses.concat(operation.close_dates.split(';'));
                }
                else {
                    closes = mcloses;
                }

                for(var j = 0; j < closes.length; j++) {
                    var close = closes[j];
                    if(close.indexOf('周') != -1) {
                        if(date.getDay() == (+close.slice(1) % 7)) {
                            return true;
                        }
                    }
                    else if(close.indexOf('/') != -1) {
                        var ft = close.split('/');
                        if(date_str >= ft[0] && date_str <= ft[1]) {
                            return true;
                        }
                    }
                    else if(close) {
                        if(date_str == close) {
                            return true;
                        }
                    }
                }
                return false;
            }
        }
        return true;
    }


    return {
        start   : start,
        end     : end,
        disable : disable
    };

}

var _tipTimer;
function dealIndependent(idx,data) {
    TicketType.viewModel.ticket_types[idx].quantity--;
    var sum = sumNum();
    if(TicketType.viewModel.ticket_types.length > 2) {
        for(var i = 0; i < TicketType.viewModel.ticket_types.length; i++) {
            var ticket = TicketType.viewModel.ticket_types[i];
            if(data.ticket_types[ticket.ticketId].is_independent == 0 && ticket.quantity > 0 &&
               sum == ticket.quantity) {
                TicketType.viewModel.ticket_tips = '不能单独购买' + data.ticket_types[ticket.ticketId].ticket_type.cn_name;
                clearTimeout(_tipTimer);
                _tipTimer = setTimeout(function() {
                    TicketType.viewModel.ticket_tips = '';
                }, 4000);
                TicketType.viewModel.ticket_types[idx].quantity++;
                return false;
            }
        }
    }
    return true;
}

function sumNum() {
    var sum = 0;
    for(var i = 0; i < TicketType.viewModel.ticket_types.length; i++) {
        var ticket = TicketType.viewModel.ticket_types[i];
        sum += ticket.quantity;
    }
    return sum;
}

var _fieldList = [SpecialCode, Departure, TicketType,SpecialGroup];
function doExceptThis(thisField, callback,args) {//对于除去指定的field的其他所有field执行回调 一般用于关闭下拉选择框
    _fieldList.forEach(function(e) {
        if(e !== thisField) {
            callback.apply(e,Array.prototype.slice.call(arguments,2));
        }
    });
}
function closeField(gp){
    if(this===SpecialGroup){
        SpecialGroup.closeExcept(gp);
    }
    else{
        this.viewModel.show=false;
    }
}
function checkComplete(showTips) {
    var flag = true;
    if(Departure.viewModel.status != 'complete') {
        if(Departure.viewModel.status != 'disable' && showTips) {
            Departure.viewModel.status = 'empty';
        }
        flag = false;
    }
    if(SpecialGroup.viewModel.special_groups.length>0){
        for(var i=0;i<SpecialGroup.viewModel.special_groups.length;i++){
            var sg=SpecialGroup.viewModel.special_groups[i];
            if(sg.status!='complete'){
                if(sg.status!='disable'&&showTips){
                    sg.status='empty';
                }
                flag=false;
            }
        }
    }
    if(TicketType.viewModel.status != 'complete') {
        if(showTips) {
            TicketType.viewModel.status = 'empty';
        }
        flag = false;
    }
    if(TourDate.viewModel.status != 'complete') {
        if(showTips) {
            TourDate.viewModel.status = 'empty';
        }
        flag = false;
    }


    return flag;

}

function genTicketTypes(data, special_code, date_str) {
    var ticket_types = [];
    date_str = date_str || new Date().format();
    var i = 0;
    for(var k in data.ticket_types) {
        var ticketType = data.ticket_types[k];
        var age_range = ticketType.age_range;
        var m = age_range.match(/^(\d+)-100$/);
        if(m) {
            age_range = m[1] + '+';
        }
        var ticket = {
            name        : ticketType.ticket_type.cn_name,
            description : ticketType.description,
            age_range   : age_range,
            ticketId    : ticketType.ticket_id
        };
        //ticket.price = pricePlan[specialCode][ticketType.ticket_id][1].price;
        //ticket.orig_price = pricePlan[specialCode][ticketType.ticket_id][1].orig_price;
        if(special_code&&special_code.indexOf('|')!=-1){
            special_code=findByGroupInfo(special_code);
        }
        else if(special_code){
            special_code=findComboSpecialCode(special_code);
        }
        var price = calcPrice(data.price_plan,special_code , date_str, ticket);
        ticket.price = price.price;
        ticket.orig_price = price.orig_price;
        ticket_types.push(ticket);
        ticket.quantity = OrderModel.quantities[ticketType.ticket_id] || i == 0 ?
                          Math.max(data.sale_rule.min_num, ticketType.min_num) :
                          0
        OrderModel.quantities[ticketType.ticket_id] === undefined &&
        (OrderModel.quantities[ticketType.ticket_id] = ticket.quantity);
        i++;
    }
    return ticket_types
}
function animateTips(){
    var $el=$('.field-wrap:not(.disable,.complete)').addClass('ani-blink');
    setTimeout(function(){
        $el.removeClass('ani-blink');
    },1000);
}

$(function() {
    var $base = $('#base_carousel'), $section = $('.buy-section'), $fix = $('.fix-ctn'), $op = $('.order-panel');
    window.doscroll = function() {
        if($op.height() > 459) {
            var st = $(document).scrollTop();
            var top = $base.offset().top - 20;
            var bottom = $section.offset().top + $section.height() - 479;
            if(st < top) {
                $fix.removeClass('fixed').removeClass('fixed-to-bottom');
            }
            else if(st < bottom) {
                $fix.removeClass('fixed-to-bottom').addClass('fixed');
            }
            else {
                $fix.removeClass('fixed').addClass('fixed-to-bottom');
            }
        }
    }

});
function refreshNav() {
    if(HINavObject.length > 0) {
        for(var i = 0; i < HINavObject.length; i++) {
            HINavObject[i].refreshToTop();
        }
        ;
    }
}
//endregion