/**
 * Created by godsong on 14-5-28.
 */
var FieldCache = Class({
    $init: function (name) {
        this.name = name;
        var data = localStorage.getItem(name);
        if (!data) {
            this.cache = {};
        }
        else {
            try {
                this.cache = JSON.parse(data);
            } catch (e) {
                this.cache = {};
            }
        }
    },
    get: function (id, f) {

        var curObj = this.cache;
        for (var i = 0; i < arguments.length; i++) {
            var key = arguments[i];
            curObj = curObj[key];
            if (core.typeOf(curObj) != 'object') {
                break;
            }
        }
        if (curObj == 'null') {
            curObj = null;
        }
        return curObj;
    },
    set: function () {
        var curObj = this.cache;
        for (var i = 0; i < arguments.length - 1; i++) {
            var key = arguments[i];

            if (core.typeOf(curObj[key]) != 'object') {
                curObj[key] = {};
            }
            if (i < arguments.length - 2) {
                curObj = curObj[key];
            }
            else {
                curObj[key] = arguments[arguments.length - 1];
            }
        }
    },
    save: function () {
        localStorage.setItem(this.name, JSON.stringify(this.cache));
    }
});

var paxCache = new FieldCache('passengers');
function genDateRange(field) {
    var tok = field.range.split(',');
    var from = new Date(), to = new Date(), defVal = new Date();
    if (tok[0] != 0) {
        from = DateUtil.strtotime(tok[0]);
    }
    if (tok[1] != 0) {
        to = DateUtil.strtotime(tok[1]);
    }
    if (field.default_value) {
        if (/Y|M|D/i.test(field.default_value)) {
            defVal = DateUtil.strtotime(field.default_value);
            field.defaultVal = DateUtil.format(defVal);
        }
        else {
            field.defaultVal = field.default_value;
        }

    }
    else {
        field.defaultVal = '';
    }

    field.from = DateUtil.format(from);
    field.to = DateUtil.format(to);
}
function genList(range) {
    var tok = range.split(',');
    var list = [], ctx = {};
    for (var i = 0; i < tok.length; i++) {
        var num = tok[i];
        if (isNaN(+num)) {
            var tk = num.split('-');
            for (var j = +tk[0]; j <= +tk[1]; j++) {
                ctx[j] = true;
            }
        }
        else {
            ctx[num] = true;
        }
    }
    for (var k in ctx) {
        list.push({title: k + '岁', value: k});
    }
    return list;
}
function genContactField() {
    var fields = [
        {
            name: '姓名',
            label: '姓名',
            input_type: 'text',
            active: false,
            closeBtn: false,
            showList: false,
            storage_field: 'firstname',
            status: paxCache.get('a', 'firstname') ? 'filled' : 'empty',
            value: paxCache.get('a', 'firstname') || '',
            formVal: '',
            regex: '',
            options: []
        },
        {
            name: '联系电话',
            label: '联系电话',
            input_type: 'text',
            active: false,
            closeBtn: false,
            showList: false,
            storage_field: 'telephone',
            status: paxCache.get('a', 'telephone') ? 'filled' : 'empty',
            value: paxCache.get('a', 'telephone') || '',
            formVal: '',
            regex: '^\\d{7,14}$',
            options: []
        },
        {
            name: '邮箱地址',
            label: '邮箱地址',
            input_type: 'text',
            active: false,
            closeBtn: false,
            showList: false,
            storage_field: 'email',
            status: paxCache.get('a', 'email') ? 'filled' : 'empty',
            value: paxCache.get('a', 'email') || '',
            formVal: '',
            regex: '^[-.\\w]+@[\\w-]+(\\.[a-zA-Z0-9]+)*\\.[a-zA-Z]{2,9}$',
            options: []
        }
    ];
    return fields;
}
function setSpecialName(bundle, append) {
    bundle.specialCode=bundle.special_code;
    bundle.specialName=bundle.special_name;
    /*if(append) {
        for (var i = 0; i < append.length; i++) {
            if (append[i].product_id == bundle.bundle_info.binding_product_id) {
                bundle.specialCode = append[i].special_code;
                break;
            }
        }

        for (i = 0; i < bundle.special_codes.length; i++) {
            if (bundle.specialCode == bundle.special_codes[i].special_code) {
                bundle.specialName = bundle.special_codes[i].cn_name;
                break;
            }
        }
    }*/
}
var glUniTicketId, glLeadMap = {}, glOtherMap = {},isBundleProduct=false;
var cartDataFactory = new HitourDataFactory($request_urls.checkoutData, function (data) {
    var fieldsets = [];
    if (data.bundles) {
        if(data.bundles.length>0){
            data.isHotel=true;
        }
        else {
            data.isHotel=false;
        }
        data.bundles.sort(function(a,b){
            if(a.bundle_info.group_type> b.bundle_info.group_type){
                return 1;
            }
            else if(a.bundle_info.group_type> b.bundle_info.group_type){
                return -1;
            }
            else{
                return 0;
            }
        });
    }
    if (core.isEmpty(data)) {
        window.location.href = $request_urls.home;
        return;
    }
    if (data.product.tour_date_title) {
        data.product.rule_desc.redeem_desc = data.product.rule_desc.redeem_desc.replace('使用日期', data.product.tour_date_title);
    }
    data.product.tour_date_title = data.product.tour_date_title || '使用日期';
    var idx = 0;
    data.quantities = [];
    for (var tid in data.raw_data.quantities) {
        if (data.raw_data.quantities.hasOwnProperty(tid)) {
            data.quantities.push({
                ticket_id: tid,
                quantity: data.raw_data.quantities[tid]
            });
        }
    }
    if (data.quantities.length == 1) {
        glUniTicketId = data.quantities[0].ticket_id;
    }
    data.append = {};
    if (data.raw_data.append) {

        data.raw_data.append.forEach(function (append) {
            data.append[append.product_id] = append;
        });
    }
    if (data.bundles) {
        var leadHidden = data.product.pax_rule.lead_hidden_fields.split(','),
            otherHidden = data.product.pax_rule.hidden_id_map[glUniTicketId]||data.product.pax_rule.hidden_id_map[1];
        console.log(leadHidden, otherHidden)
        for (var i = 0; i < data.bundles.length; i++) {
            isBundleProduct=true;
            var bundle = data.bundles[i];
            if (bundle.bundle_info.group_type == 1 && bundle.product_id != data.raw_data.special[0].items[0].mapping_product_id || bundle.bundle_info.group_type == 3 && !data.append[bundle.product_id]) {
                data.bundles.splice(i, 1);
                i--;
                continue;
            }
            core.forEach(data.append[bundle.product_id],function(key,value){
                bundle[key]=value;
            });
            bundle.pax_rule.other_fields.forEach(function (e) {
                if (otherHidden.indexOf(e) == -1) {
                    glOtherMap[e] = 1;
                }
            });
            bundle.pax_rule.lead_fields.forEach(function (e) {
                if (leadHidden.indexOf(e) == -1 && !glOtherMap[e]) {
                    glLeadMap[e] = 1;
                }
            });


        }
        var leadList = [], otherList = [];
        core.forEach(glLeadMap, function (k, v) {
                leadList.push(k);
        });
        core.forEach(glOtherMap, function (k, v) {
                otherList.push(k);
        });

        console.log(glLeadMap, glOtherMap);
        data.product.pax_rule.id_map = {};
        data.product.pax_rule.id_map[glUniTicketId] = otherList;
        data.product.pax_rule.lead_ids = [];
        var merge=otherList.concat(leadList);
        merge.forEach(function(e){
            if(data.product.pax_rule.lead_ids.indexOf(e)==-1){
                data.product.pax_rule.lead_ids.push(e);
            }
        })
    }
    console.log(data.product.pax_rule);
    var quantities = data.quantities;
    var packege_rules = data.product.ticket_types[data.quantities[0].ticket_id].package_rule, baseQuantity = data.quantities[0].quantity;
    if (packege_rules) {
        quantities = [];
        for (var k = 0; k < packege_rules.length; k++) {
            var pr = packege_rules[k];
            quantities.push({ticket_id: pr.ticket_id, quantity: baseQuantity * pr.quantity});
            data.product.ticket_types[pr.ticket_id] = pr;
        }
    }
    var adult_num=data.raw_data.adult_num||0,child_num=data.raw_data.child_num||0;
    for (i = 0; i < quantities.length; i++) {

        var quantity = quantities[i].quantity;
        if (data.raw_data.pax_num) {
            quantity = data.raw_data.pax_num;
        }
        tid = quantities[i].ticket_id;
        while (quantity-- > 0) {
            var fields = [], id_map, fieldset = {};
            fieldset.ticket_id=tid;
            fieldset.isChild=false;
            if (data.product.pax_rule.need_passenger_num != 0 && idx > data.product.pax_rule.need_passenger_num - 1) {
                break;
            }
            if (data.product.pax_rule.need_lead == 1) {
                id_map = data.product.pax_rule.lead_ids;
                fieldset.name = '（当日联系人）';
                fieldset.isLead = true;
                data.product.pax_rule.need_lead = 0;
                fieldset.age=30;
                adult_num--;
            }
            else {
                id_map = data.product.pax_rule.id_map[tid]||data.product.pax_rule.id_map[99];
                if (Object.keys(data.product.ticket_types).length > 1) {
                    fieldset.name = data.product.ticket_types[tid].ticket_type.cn_name;
                    if (fieldset.name == '成人') {
                        fieldset.name = '';
                    }
                    else {
                        fieldset.name = '（' + fieldset.name + '）';
                    }
                }
                else {
                    if(adult_num>0){
                        fieldset.name = '（成人）';
                        adult_num--;
                        fieldset.age=30;

                    }
                    else if(child_num>0){
                        var age=data.raw_data.child_list[data.raw_data.child_list.length-child_num].age;
                        fieldset.name = '（儿童 '+age+'岁）';
                        fieldset.age=age;
                        fieldset.isChild=true;
                        child_num--;
                    }
                    else{
                        fieldset.name = '';
                    }
                }
                fieldset.isLead = false;
            }

            for (var j = 0; j < id_map.length; j++) {
                fields.push(core.clone(data.product.pax_meta[id_map[j]]));
                var field = fields[fields.length - 1];
                field.name = fields[fields.length - 1].label;
                //field.label=field.name+(field.hint?' ('+field.hint+')':'');
                field.active = false;
                field.closeBtn = false;
                field.value = paxCache.get(idx, field.storage_field) || '';
                field.formVal = '';
                if (field.value != '') {
                    field.status = 'filled';
                }
                else {
                    field.status = 'empty';
                }
                field.ticket_id = tid;

                if (field.input_type == 'age') {
                    if(fieldset.isChild){
                        field.value=fieldset.age;
                        field.status='filled';
                    }
                    field.range = data.product.ticket_types[field.ticket_id].age_range;
                    if (field.range) {
                        field.options = genList(field.range);
                        for (var _i = 0; _i < field.options.length; _i++) {
                            if (field.value == field.options[_i].title) {
                                field.formVal = field.options[_i].value;
                            }
                        }
                    }
                    else {
                        field.input_type = 'text';
                        field.regex = '\\d+';
                        field.options = [];
                    }
                }
                else if (field.input_type == 'enum') {
                    field.options = new Function('return ' + field.range)();
                    for (_i = 0; _i < field.options.length; _i++) {
                        if (field.value == field.options[_i].title) {
                            field.formVal = field.options[_i].value;
                        }
                    }
                }
                else if (field.input_type == 'date') {
                    genDateRange(field);
                    field.options = [];
                }
                else {
                    field.options = [];
                }
                field.showList = false;
            }
            idx++;
            fieldset.fields = fields;
            fieldsets.push(fieldset);
        }
    }
    data.fieldsets = fieldsets;
    var paymentMethods = [];
    for (var key in data.payment_methods) {
        var pm = data.payment_methods[key];
        if (pm.mobile == 0) {
            pm.checked = false;
            paymentMethods.push(pm);
        }
    }
    var visibleBundleNum=0;
    for (i = 0; data.bundles && i < data.bundles.length; i++) {

        bundle = data.bundles[i];
        bundle.departures = [];
        if (bundle.bundle_info.group_type == 1 || bundle.bundle_info.group_type == 3 && !data.append[bundle.product_id]) {

            data.bundles.splice(i, 1);
            i--;
            continue;
        }

        bundle.show=true;
        if (bundle.bundle_info.group_type == 2) {
            bundle.price = '套餐包含';
            if(bundle.departure_rule.length==0&&bundle.date_rule.need_tour_date==0){
                bundle.show=false;
            }

            if (bundle.bundle_info.count_type == 1) {
                bundle.quantity = data.raw_data.quantities[glUniTicketId];
            }
            else if (bundle.bundle_info.count_type == 2) {
                bundle.quantity = bundle.bundle_info.count;
            }
            else {
                bundle.quantity = data.raw_data.pax_num;
            }
            bundle.num=bundle.quantity;
        }
        else {
            bundle.price = 0;
            bundle.quantity = data.append[bundle.product_id].pax_num;
            bundle.num=bundle.quantity;
        }
        var qindex = fieldsets.length;
        bundle.pax = [];
        bundle.origPrice=0;
        while (qindex-- > 0) {
            bundle.pax.push({select: false,ticketId:null});
            bundle.pax[bundle.pax.length - 1].enable = true;
        }

        bundle.departures = [];
        setSpecialName(bundle, data.raw_data.append);
        if(bundle.sale_rule.sale_in_package=='1'){
            bundle.ticket_types={99:bundle.ticket_types[99]};
        }
        if(!bundle.specialCode&&bundle.special_codes&&bundle.special_codes.length>0){
            bundle.specialCode=bundle.special_codes[0].special_code;
        }

        core.forEach(bundle.ticket_types, function (key, v) {
            v.quantity = 0;
            var minPrice = calcPrice(bundle, bundle.specialCode, bundle.tour_date,v.ticket_id);
            v.price = minPrice.price;
            v.origPrice = minPrice.origPrice;
            if (v.age_range) {
                if(v.age_range.split('-')[1] == 100||v.age_range.charAt(v.age_range.length-1)=='+') {
                    v.range = '(' + parseInt(v.age_range.split('-')[0]) + '岁以上)';
                }
                else {
                    v.range = '(' + v.age_range + '岁)'
                }
            }
        });
        if(bundle.bundle_info.group_type == 2) {
            core.forEach(bundle.ticket_types, function(tid, ticket) {
                if(ticket.ticket_id == 2) {
                    ticket.quantity = data.raw_data.adult_num;
                }
                else if(ticket.ticket_id == 1) {
                    ticket.quantity = (data.raw_data.adult_num | 0) + (data.raw_data.child_num | 0);
                }
                else {
                    ticket.quantity = 0;
                }
                ticket.quantifier = ticket.ticket_type.cn_name.replace('出行人', '人').replace('票', '');


            });
            data.raw_data.child_list.forEach(function(c, i) {
                core.forEach(bundle.ticket_types, function(tid, ticket) {
                    var range = ticket.age_range.split('-');
                    if((+c.age) >= (+range[0]) && (+c.age) <= (+range[1])) {
                        ticket.quantity++;
                        if(ticket.ticket_id == 2) {
                            ticket.tips = '您的其中一个儿童（' + c.age + '岁）不符合该商品的儿童票年龄范围 只能购买成人票';
                        }
                    }

                });

            });
            calcBundlePrice(bundle);
        }


        if(bundle.show){
            visibleBundleNum++;
        }
    }
    //paymentMethods[0].checked = true;
    data.visibleBundleNum=visibleBundleNum;
    data.paymentMethods = paymentMethods;

    return data;
});
var contactInfo = new ViewModel('contactInfo', 'onFocus,onBlur,onKeyup,onClear,contactFieldset|[],contactList|[],curId|0,changeContact');
var passenger = new ViewModel('passenger', 'fieldsets|[],onFocus,onBlur,onClear,clickList');

var cartInfo = new ViewModel('cartInfo', 'flag,productName,toBuy,beforePay,buyLabel|"确认订单",toPay,subTotal,cover,rule_desc|{},tour_date,finalPrice,departure,special_info,quantity|[],tour_date_title,special_title,departure_title');
var hotelInfo = new ViewModel('hotelInfo', 'productName,flag,hotelName,quantity,tour_date,pax_num,subTotal,hotelPrice,hotelOrigPrice,adult_num,child_num');
var payment = new ViewModel('payment', 'paymentMethods|[],changePayment');
var subProducts = new ViewModel('subProducts', 'hotelName,visibleBundleNum,bundles|[],getTicketType,selectPax,afterRendered');
contactInfo.bindData(cartDataFactory.getData());
payment.bindData(cartDataFactory.getData({}));
hotelInfo.bindData(cartDataFactory.getData({
    productName: 'product.name',
    hotelName: 'raw_data.special_name',
    quantity: function (src, data) {
        this.flag=!!data.isHotel;
        return data.raw_data.quantities[glUniTicketId];//fixme 硬写的 ticketid=1
    },
    hotelPrice: 'product.sub_total',
    hotelOrigPrice:'product.orig_sub_total',
    pax_num: 'raw_data.pax_num',
    adult_num:'raw_data.adult_num',
    child_num:'raw_data.child_num',
    tour_date: 'raw_data.tour_date',
    subTotal: 'product.sub_total'

}));
function resolveDiscount(origPrice, discount) {
    if (isFinite(discount)) {//如果没有折扣类型字母作为后缀 则当做直接减
        var result = origPrice - discount;
    }
    else {
        var match = /^(\d+)(P|F)/i.exec(discount);
        if (match) {
            if (match[2].toUpperCase() == 'P') {
                result = origPrice - origPrice * match[1] / 100;
            }
            else {
                result = origPrice - match[1];
            }
        }
        else {
            console.error('can not resolve discount!');
            return origPrice;
        }
    }
    return Math.floor(Math.max(result, 0));
}
function resolveBundlePrice(bundle, dateStr) {
    var now = dateStr || new Date().format(), result = {price: 99999, origPrice: 0};
    var specialCode = bundle.specialCode || 0;
    core.forEach(bundle.ticket_types, function (k, ticket) {
        for (var i = 0; i < bundle.price_plan.length; i++) {
            var p = bundle.price_plan[i];
            if (p.valid_region == '0' || now >= p.from_date && now <= p.to_date) {
                var curPrice = p.price_map[specialCode][ticket.ticket_id][1];
                ticket.price = resolveDiscount(curPrice.price, bundle.bundle_info.discount_amount + bundle.bundle_info.discount_type);
                ticket.origPrice = curPrice.price;
            }

        }
    });

}
function calcMinPrice(bundle, specialCode, ticketId) {
    var now = new Date().format(), result = {price: 99999, origPrice: 0};
    specialCode = specialCode || 0;
    for (var i = 0; i < bundle.price_plan.length; i++) {
        var p = bundle.price_plan[i];
        if (p.valid_region == '0' || now <= p.to_date) {
            var curPrice = p.price_map[specialCode][ticketId][1];
            if (curPrice.price < result.price) {
                result.price = resolveDiscount(curPrice.price, bundle.bundle_info.discount_amount + bundle.bundle_info.discount_type);
                result.origPrice = curPrice.price;
            }

        }
    }
    return result;
}
function calcPrice(bundle,specialCode,dateStr,ticketId){
    var result={},qtf;
    dateStr=dateStr||new Date().format('yyyy-mm-dd');
    var pricePlan=bundle.price_plan;
    for (var i = 0; i < pricePlan.length; i++) {
        var p = pricePlan[i];
        if (p.valid_region == '0' || dateStr>= p.from_date&&dateStr <= p.to_date) {
            var curPrice = p.price_map[specialCode||0][ticketId][1];
                result.price = resolveDiscount(curPrice.price, bundle.bundle_info.discount_amount + bundle.bundle_info.discount_type);
                result.origPrice = curPrice.orig_price;
        }
    }
    console.log(bundle.name,result);
    return result;
}
function calcBundlePrice(bundle) {
    var price = 0, origPrice = 0,num=0;

    core.forEach(bundle.ticket_types, function (key, ticket) {
        price += ticket.quantity * ticket.price;
        origPrice += ticket.quantity * ticket.origPrice;
        num+=+ticket.quantity;
    });
    if(bundle.bundle_info.group_type == 3) {
        bundle.price = price;
        bundle.num=num;
    }

    bundle.origPrice = origPrice;

    calcSum();

}
function calcSum(){
    var sum = 0, subTotal = 0;
    subProducts.viewModel.bundles.forEach(function (e) {
        if (e.price && isFinite(e.price)) {
            sum += e.price;
        }
        subTotal += (e.origPrice||0);
    });
    sum+=hotelInfo.viewModel.hotelPrice;
    hotelInfo.viewModel.subTotal = hotelInfo.viewModel.hotelOrigPrice + subTotal;
    if (Coupon.coupons[0] && Coupon.coupons[0].name == '为您节省') {
        if (subTotal - sum == 0) {
            Coupon.coupons.splice(0, 1);
        }
        else {
            Coupon.coupons[0].show = '&yen;' + (hotelInfo.viewModel.subTotal - sum);
            Coupon.coupons[0].discount = (hotelInfo.viewModel.subTotal - sum) + 'F';
        }

    }
    else {
        Coupon.coupons = [{
                              name: '为您节省',
                              show: '&yen;' + (hotelInfo.viewModel.subTotal - sum),
                              discount: (hotelInfo.viewModel.subTotal - sum) + 'F'
                          }].concat(Coupon.coupons);
    }
    Coupon.sumPrice = hotelInfo.viewModel.subTotal;
    Coupon.resolveFinalPrice();
}
function getTourDateLimits(date_rule) {
    if (date_rule.need_tour_date == 0) {
        return;
    }
    if (date_rule.operations.length > 0) {
        var mcloses = [];
    }
    else {
        mcloses = date_rule.close_dates.split(';');
    }

    var start = DateUtil.parse(date_rule.start), end = DateUtil.parse(date_rule.end);

    function disable(date, dateStr) {
        for (var i = 0; i < date_rule.operations.length; i++) {
            var operation = date_rule.operations[i];
            if (dateStr >= operation.from_date && dateStr <= operation.to_date) {
                if (operation.close_dates) {
                    var closes = mcloses.concat(operation.close_dates.split(';'));
                }
                else {
                    closes = mcloses;
                }

                for (var j = 0; j < closes.length; j++) {
                    var close = closes[j];
                    if (close.indexOf('周') != -1) {
                        if (date.getDay() == (+close.slice(1) % 7)) {
                            return true;
                        }
                    }
                    else if (close.indexOf('/') != -1) {
                        var ft = close.split('/');
                        if (dateStr >= ft[0] && dateStr <= ft[1]) {
                            return true;
                        }
                    }
                    else if (close) {
                        if (dateStr == close) {
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
        start: start,
        end: end,
        disable: disable
    };
}
function genDepartures(data, dateStr) {
    var departures = [];
    for (i = 0; i < data.departure_rule.length; i++) {
        var dep = data.departure_rule[i];

        if (dep.valid_region == 0 || (dateStr >= dep.from_date && dateStr <= dep.to_date)) {
            if (dep.time == '00:00:00') {
                var showTime = '';
            }
            else {
                showTime = dep.time.slice(0, -3)
            }
            if (dep.additional_limit) {
                var limits = dep.additional_limit.split(';');
                for (var j = 0; j < limits.length; j++) {
                    var limit = limits[j];
                    if (limit.indexOf('周') != -1) {
                        if (DateUtil.parse(dateStr).getDay() == (+limit.slice(1) % 7)) {
                            departures.push({
                                time: dep.time,
                                showTime: showTime,
                                departure_point: dep.departure.departure_point,
                                code: dep.departure.departure_code
                            });
                        }
                        else if (dateStr == limit) {
                            departures.push({
                                time: dep.time,
                                showTime: showTime,
                                departure_point: dep.departure.departure_point,
                                code: dep.departure.departure_code
                            });
                        }
                    }
                }
            }
            else {
                departures.push({
                    time: dep.time,
                    showTime: showTime,
                    departure_point: dep.departure.departure_point,
                    code: dep.departure.departure_code
                });
            }
        }
    }
    return departures;

}
function getPricePlan(pricePlan, dateStr) {
    for (var i = 0; i < pricePlan.length; i++) {
        priceSection = pricePlan[i];

        if (priceSection.valid_region == '0' || dateStr >= priceSection.from_date && dateStr <= priceSection.to_date) {
            return priceSection.price_map;
        }
    }
    return null;
}
function reComputePrice(bundle, dateStr) {
    core.forEach(bundle.ticket_types, function (key, ticket) {
        var price = getPricePlan(bundle.price_plan, dateStr)[bundle.specialCode || 0][ticket.ticket_id][1].price;
        ticket.origPrice = price;
        ticket.price = resolveDiscount(price, bundle.bundle_info.discount_amount + bundle.bundle_info.discount_type);
    });
}
subProducts.canlendars = {};

subProducts.viewModel.afterRendered = function (action) {
    if (action == 'index') {
        var data = cartDataFactory.dataPromise.cur.value(0);
        $('.sub-ctn').each(function (idx, e) {
            /*var limits = getTourDateLimits(data.bundles[i].date_rule);
            var tourDate = $(e).find('.sub-tour-date');
            if (tourDate.length > 0) {
                subProducts.canlendars[i] = new XCalendar(tourDate, {
                    width: 296,
                    min: limits.start,
                    max: limits.end,
                    disable: limits.disable,
                    partials: {
                        'append': '<div class="date-legend"><div class="legend-item" style="margin-right: 30px;width: 120px;"><div class="date-enable"></div>可选日期</div><div class="legend-item"><div class="date-disable"></div>关闭时间</div></div>'
                    },
                    onSelect: function (date) {
                        var bundle = subProducts.viewModel.bundles[this.index];
                        var dateStr = date.format('yyyy-mm-dd');
                        tourDate[0].firstChild.innerHTML = dateStr;
                        tourDate.removeClass('error');
                        resolveBundlePrice(this.bundle,dateStr);
                        if (this.bundle.bundle_info.group_type == 3) {
                            reComputePrice(this.bundle, dateStr);
                            subProducts.viewModel.bundles[this.index].departures = genDepartures(this.bundle, dateStr);
                            calcBundlePrice(bundle);
                        }
                        else if(this.bundle.bundle_info.group_type == 2){
                            subProducts.viewModel.bundles[this.index].departures = genDepartures(this.bundle, dateStr);
                        }


                    }
                });
                subProducts.canlendars[i].bundle = data.bundles[i];
                subProducts.canlendars[i].index = i;
            }*/
            $(e).find('.sub-passenger-wrap').each(function(i,radio){
                _selectPax(subProducts.viewModel.bundles[idx],i,true);
            });
            subProducts.viewModel.bundles.forEach(function(e){
                if(e.bundle_info.group_type==2){
                    calcBundlePrice(e);
                }
            });
            calcSum();
        });
    }
};
subProducts.bindData(cartDataFactory.getData({

    'getTicketType' : function(src, data) {
        return function(pax, bundle, $index) {
            var age = pax.age;
            var selectTicket;
            console.log(age)
            var count = 0, firstKey;
            if(bundle.pax[$index].select) {
                bundle.ticket_types[bundle.pax[$index].ticketId].quantity--;
            }
            core.forEach(bundle.ticket_types, function(key, ticket) {
                count++;
                if(firstKey === undefined) {
                    firstKey = key;
                }
                if(ticket.age_range) {
                    if(/\+$/.test(ticket.age_range)) {
                        var range = [parseInt(ticket.age_range), 100];
                    }
                    else {
                        range = ticket.age_range.split('-');
                    }
                    if(age >= +range[0] && age <= +range[1]) {
                        selectTicket = ticket;
                        if(!bundle.pax[$index]) {
                            bundle.pax[$index] = {};
                        }
                        bundle.pax[$index].ticketId = key;
                    }
                }

            });
            if(bundle.pax[$index].ticketId == undefined) {
                bundle.pax[$index].ticketId = firstKey;
            }
            if(bundle.pax[$index].select) {
                bundle.ticket_types[bundle.pax[$index].ticketId].quantity++;
            }

            if(count == 1) {
                return '';
            }
            else {

                if(!selectTicket) {
                    /*bundle.pax.forEach(function(p){
                     p.select=false;
                     })*/
                    if(bundle.pax[$index].enable) {
                        bundle.pax[$index].enable = false;
                        setTimeout(function() {
                            var modified = false;
                            bundle.pax.forEach(function(p) {
                                if(p.select) {
                                    p.select = false;
                                    bundle.ticket_types[p.ticketId].quantity--;
                                    bundle.quantity--;
                                    modified = true;
                                }

                            });
                            modified && calcBundlePrice(bundle);
                        }, 200);
                    }
                    return '(年龄不符合该商品要求)';
                }
                else {
                    bundle.pax[$index].enable = true;
                    return '(' + selectTicket.ticket_type.cn_name + '票)';
                }
            }


            return '';
        }

    },
    selectPax: function (src, data) {
        return _selectPax;
    }
})).then(function () {

});
function _selectPax(bundle, pid,disableTest) {
    if (!bundle.pax[pid]) {
        bundle.pax[pid] = {};
    }
    var ticket = bundle.ticket_types[bundle.pax[pid].ticketId];
    if (!bundle.pax[pid].select) {
        if (!bundle.pax[pid].enable) {
            return;
        }
        if (ticket.is_independent == 0&&!disableTest) {
            var flag = true;
            for (var i = 0; i < bundle.pax.length; i++) {
                if (i != pid && bundle.pax[i].select) {
                    flag = false;
                    break;
                }
            }
            if (flag) {
                alert('对不起，该商品无法单独购买' + ticket.ticket_type.cn_name + '票,请先购买勾选一张其他类型的票');
                return;
            }
        }
        bundle.pax[pid].select = true;
        ticket.quantity++;
        //bundle.quantity++;
        calcBundlePrice(bundle);
        console.log(bundle.pax, bundle.ticket_types)
    }
    else {
        flag = false;
        for (i = 0; i < bundle.pax.length&&!disableTest; i++) {
            if (i != pid && bundle.pax[i].select && bundle.ticket_types[bundle.pax[i].ticketId].is_independent == 0) {
                flag = true;
                var tname = bundle.ticket_types[bundle.pax[i].ticketId].ticket_type.cn_name;
            }
            else if(i != pid && bundle.pax[i].select && bundle.ticket_types[bundle.pax[i].ticketId].is_independent == 1){
                flag=false;
                break;
            }
        }
        if (flag) {
            alert('对不起，该商品无法单独购买' + tname + '票。您不能取消这张' + ticket.ticket_type.cn_name + '票');
            return;
        }
        bundle.pax[pid].select = false;
        ticket.quantity--;
        //bundle.quantity--;
        calcBundlePrice(bundle);
    }


}
var lastChecked;
contactInfo.viewModel.contactFieldset = genContactField();
contactInfo.viewModel.changeContact = function (index) {
    index = index || 0;
    var address = contactInfo.viewModel.contactList[index];
    lastChecked.checked = false;
    address.checked = true;
    lastChecked = address;
    if (address.address_id == 0) {
        contactInfo.viewModel.contactFieldset[0].value = '';
        $('#fd_a_0').focus();
    }
    else {
        contactInfo.viewModel.contactFieldset[0].value = address.firstname;
    }


    contactInfo.viewModel.contactFieldset[1].value = address.telephone;
    contactInfo.viewModel.contactFieldset[2].value = address.email;
    contactInfo.viewModel.contactFieldset[0].status = 'filled';
    contactInfo.viewModel.contactFieldset[1].status = 'filled';
    contactInfo.viewModel.contactFieldset[2].status = 'filled';
    contactInfo.viewModel.curId = address.address_id;
};

var changePayment=payment.viewModel.changePayment = function (index) {
    if(typeof index=='number'){
        idx=index;
    }
    else {
        idx = this.getAttribute('data-index');
    }
    for (var i = 0; i < payment.viewModel.paymentMethods.length; i++) {
        if (i == idx) {
            if(!payment.viewModel.paymentMethods[idx].checked) {
                var discount = payment.viewModel.paymentMethods[idx].discount;
                if (discount) {
                    var cp = {
                        name: payment.viewModel.paymentMethods[idx].title,
                        show: '&yen;' + discount.discount_amount + (discount.discount_type == 'P' ? '%' : ''),
                        discount: discount.discount_amount + discount.discount_type,
                        code: ''
                    };
                    if (Coupon.payment) {
                        Coupon.payment = cp;
                    }
                    else {
                        Coupon.coupons.push(cp);
                        Coupon.payment = cp;
                    }
                    Coupon.resolveFinalPrice();

                }
                else if (Coupon.payment) {
                    Coupon.coupons.forEach(function (e, i) {
                        if (e.$model === Coupon.payment) {
                            Coupon.coupons.splice(i, 1);
                            return false;
                        }
                    });
                    Coupon.payment = null;
                    Coupon.resolveFinalPrice();
                }
                payment.viewModel.paymentMethods[idx].checked = true;
            }
        }
        else {
            payment.viewModel.paymentMethods[i].checked = false;
        }
    }
};
var origPrice;
cartInfo.bindData(cartDataFactory.getData({
    productName: 'product.name',
    'rule_desc': 'product.rule_desc',
    'subTotal':'product.sub_total',
    departure: function (val, data) {

        if (!data.raw_data.departure_code) {
            return null;
        }
        var showTime = data.raw_data.departure_time.slice(0, -3);
        if (showTime == '00:00') {
            showTime = '';
        }
        return showTime + data.raw_data.departure_point;
    },
    tour_date: 'raw_data.tour_date',
    special_info: 'raw_data.special_info',
    quantity: function (val, data) {
        var q = [];
        for (var key in data.raw_data.quantities) {
            if (data.raw_data.quantities.hasOwnProperty(key)) {
                var quantity = {};
                quantity.name = data.product.ticket_types[key].ticket_type.cn_name;
                quantity.quantity = data.raw_data.pax_num||data.raw_data.quantities[key];
                q.push(quantity);
            }
        }
        Coupon.sumPrice = data.product.sub_total;
        Coupon.resolveFinalPrice();
        if (!data.bundles) {
            this.flag = true;
        }
        else {
            this.flag = false;
        }
        return q;
    },
    departure_title: 'product.departure_title',
    special_title: 'product.special_title',
    tour_date_title: 'product.tour_date_title'
})).then(function (data) {
    $(function () {
        if (data.coupon_title) {
            Coupon.coupon_title = data.coupon_title;
        }

        Coupon.flag = +data.allow_use_coupon;
        /* if(data.coupon_total>0){
         Coupon.state='complete';
         Coupon.save=data.coupon_total;
         Coupon.origPrice=data.sub_total;
         if(Coupon.flag==1){
         if(data.coupon_title=='使用优惠券'||data.coupon_title==''){
         Coupon.couponCode=data.coupon.code;
         }
         else{
         Coupon.couponCode=data.coupon_title;
         }
         }
         if(data.coupon_title=='微信扫码优惠'){
         $('.scan-ctn').hide();
         $('.scan-success').show();
         }
         Coupon.btnText = '取消';

         }*/
        $(document.body).scrollTop = 0;

        var $orderPanel = $('#right_aside'), $footer = $('.main-footer'), $pos = $('.payment-method:eq(0)');
        var posCache = 0;
        checkContactComplete();
        window.onscroll = function () {
            var pos2 = $pos.height() + $pos.offset().top;
            var h = $orderPanel.height();
            var st = document.body.scrollTop || document.documentElement.scrollTop;
            if (h + 50 >= window.innerHeight) {
                $orderPanel.removeClass('fixed');
                var pos1 = posCache || (h + $orderPanel.offset().top);
                var bh = window.innerHeight - (pos1 - st);
                var bh2 = window.innerHeight - (pos2 - st);
                if (bh >= 20 && bh2 < 150) {
                    $orderPanel.addClass('fixed2');
                    $orderPanel.removeClass('fixed-bottom');
                    posCache = pos1;
                }
                else if (bh2 >= 150) {
                    $orderPanel.removeClass('fixed2');
                    $orderPanel.addClass('fixed-bottom');
                }
                else {
                    posCache = 0;
                    $orderPanel.removeClass('fixed2');
                    $orderPanel.removeClass('fixed-bottom');
                    // $orderPanel.css('position','static');
                }
            }
            else {

                $orderPanel.removeClass('fixed2');
                var dst = $footer.offset().top - st;
                if (st >= 70 && dst > (40 + h)) {
                    $orderPanel.addClass('fixed');
                    $orderPanel.removeClass('fixed-bottom');
                }
                else if (dst <= (40 + h)) {
                    $orderPanel.removeClass('fixed');
                    $orderPanel.addClass('fixed-bottom');
                }

                else {
                    $orderPanel.removeClass('fixed');
                    $orderPanel.removeClass('fixed-bottom');
                }
            }
            lst = st;
        };


        $('.loading-mask').hide();
        $(".coupon-result").hide();
        //$('.aside-bottom').css('height','2%');
        changePayment(0);
    });

});
function getField(el) {
    var idx = el.getAttribute('data-index').split(',');
    if (idx[0] == 'a') {
        return contactInfo.viewModel.contactFieldset[idx[1]];
    }
    else {
        return passenger.viewModel.fieldsets[idx[0]].fields[idx[1]];
    }
}
passenger.bindData(cartDataFactory.getData()).then(function () {
    $('.date').each(function (i, e) {
        new XCalendar(e, {
            min: e.getAttribute('from'),
            max: e.getAttribute('to'),
            defaultDate: e.getAttribute('default'),
            offsetY: 6,
            onSelect: function (date) {
                //e.focus();
                //e.blur();
                var field = getField(e);
                field.value = DateUtil.format(date);
                var idx = e.getAttribute('data-index');
                validate(field, $.trim(field.value));
                paxCache.set(idx[0], field.storage_field, $.trim(field.value));
                paxCache.save();
            }
        });
    });
    for (var i = 0; i < passenger.viewModel.fieldsets.length; i++) {
        for (var j = 0; j < passenger.viewModel.fieldsets[i].fields.length; j++) {
            var field = passenger.viewModel.fieldsets[i].fields[j];
            if (field.storage_field == 'birth_date') {
                field.$watch('value', function () {
                    subProducts.viewModel.bundles.forEach(function (bundle) {
                        if (bundle.bundle_info.group_type == 3) {
                            calcBundlePrice(bundle);
                        }
                    })

                })
            }
        }
    }


});

contactInfo.viewModel.onFocus = passenger.viewModel.onFocus = function (evt) {
    var field = getField(evt.target);
    field.status = 'filled';
    field.active = true;
    field.label = field.name;
    if (evt.target.value != '') {
        field.closeBtn = true;
    }
    if(curShowList&&curShowList!==field){
        doBlur(curShowList,curShowList.value,true);
        curShowList=null;
    }
    if (field.options.length > 0) {
        field.showList = true;
        curShowList=field;
    }
};

passenger.viewModel.clickList = function (evt) {
    var idx = evt.target.getAttribute('data-index').split(',');
    var field = getField(evt.target);
    field.value = $.trim(evt.target.innerHTML);
    field.formVal = evt.target.getAttribute('data-val');
    field.showList = false;
    paxCache.set(idx[0], field.storage_field, field.value);
    paxCache.save();
    doBlur(field,field.value,true);
};
var blurTm = {}, keyTm,curShowList;
function checkContactComplete() {
    var hasError = false;
    for (var i = 0; i < contactInfo.viewModel.contactFieldset.length; i++) {
        var field = contactInfo.viewModel.contactFieldset[i];
        validate(field, field.value);
        if (field.value == '' || field.status == 'error') {
            hasError = true;
            break;
        }
    }
    if (!hasError) {
        $('section.mask').hide();
        $('#qrcode').addClass('show');
    }
    else {
        $('section.mask').show();
        $('#qrcode').removeClass('show');
    }
}
contactInfo.viewModel.onKeyup = function (evt) {
    var field = getField(evt.target);
    field.status = 'filled';
    field.label = field.name;
    clearTimeout(keyTm);
    keyTm = setTimeout(function () {
        validate(field, $.trim(evt.target.value));
        checkContactComplete();
    }, 1000);
};
contactInfo.viewModel.onBlur = function (evt) {
    var field = getField(evt.target);
    // field.label=field.name;
    var idx = evt.target.getAttribute('data-index');
    paxCache.set(idx[0], field.storage_field, $.trim(evt.target.value));
    paxCache.save();
    clearTimeout(keyTm);
    blurTm[idx] = setTimeout(function () {
        if (evt.target.value != '') {
            field.status = 'filled';
        }
        validate(field, $.trim(evt.target.value));
        field.active = false;
        field.showList = false;
        field.closeBtn = false;
        checkContactComplete();
    }, 200)

};
function doBlur(field,value,flag){
    // field.label=field.name;
    if(field.options.length==0||flag){
        clearTimeout(keyTm);
        blurTm[idx] = setTimeout(function () {
            if (value != '') {
                field.status = 'filled';
            }
            validate(field, $.trim(value));
            field.active = false;
            field.showList = false;
            field.closeBtn = false;
        }, 200)
    }
}
passenger.viewModel.onBlur = function (evt) {
    var field = getField(evt.target);
    var idx = evt.target.getAttribute('data-index');
    paxCache.set(idx[0], field.storage_field, $.trim(evt.target.value));
    paxCache.save();
    doBlur(field,evt.target.value,false);
};
var closeBlur = false;
contactInfo.viewModel.onClear = passenger.viewModel.onClear = function (evt) {

    var idx = evt.target.getAttribute('data-index').split(',');
    clearTimeout(blurTm[idx]);
    var field = getField(evt.target);
    field.closeBtn = false;
    field.value = '';
    var ipt = document.getElementById('fd_' + idx[0] + '_' + idx[1]);
    ipt.value = '';
    ipt.focus();
}
function validate(field, value) {
    if (value == '') {
        field.status = 'error';
        field.label = field.name + '不能为空';
    }
    else if (!new RegExp(field.regex).test(value)) {
        field.status = 'error';
        field.label = field.hint || (field.name + '格式不正确');
    }
    else {
        field.status = 'filled';
        field.label = field.name;
    }
}
cartInfo.viewModel.beforePay = function () {
    $('.dialog-mask').show();
};
cartInfo.viewModel.toBuy = function () {
    var ipts = $('.left-content input').each(function (i, e) {
        if (e.getAttribute("id") != "coupon_ipt") {
            var field = getField(e);
            validate(field, e.value);
            //passenger.viewModel.onBlur({target:e});
        }
    });
    var param = {}, isvalid = true, fieldsets,paxCount=0;

    for (var i = 0; i < passenger.viewModel.fieldsets.length; i++) {
        if (isBundleProduct&&i > 0&&passenger.viewModel.fieldsets[i].fields.length <passenger.viewModel.fieldsets[0].fields.length) {
            fieldsets = passenger.viewModel.fieldsets[i].fields.concat(passenger.viewModel.fieldsets[0].fields.slice(passenger.viewModel.fieldsets[i].fields.length));
        }
        else {
            fieldsets = passenger.viewModel.fieldsets[i].fields;
        }
        param['passengers[' + i + '][ticket_id]'] = passenger.viewModel.fieldsets[i].ticket_id;
        for (var j = 0; j < fieldsets.length; j++) {
            var field = fieldsets[j],
                ipt = document.getElementById('fd_' + i + '_' + j);
            if (ipt) {
                validate(field, ipt.value);
            }
            if (field.status == 'error') {
                if (isvalid) {
                    $('body,html').animate({scrollTop: $(ipt).offset().top - 50});
                }
                isvalid = false;
            }
            else {

                var val = field.formVal === "" ? field.value : field.formVal;
                if (val === '' || val === undefined || val === null) {
                    field.status = 'error';
                    field.label = field.name + '不能为空';
                    field.value = '';
                    $('body,html').animate({scrollTop: $(ipt).offset().top - 50});
                    return;
                }
                param['passengers[' + i + '][' + field.storage_field + ']'] = val;
                paxCount++;
            }
        }
    }
    if(paxCount==0){
        alert('您的出行人信息填写不完整，无法下单！如有疑问请拨打客服电话400-010-1900');
        return;
    }
    for (i = 0; i < contactInfo.viewModel.contactFieldset.length; i++) {

        field = contactInfo.viewModel.contactFieldset[i];
        ipt = document.getElementById('fd_a_' + i);
        validate(field, ipt.value);
        if (field.status == 'error') {
            isvalid = false;
        }
        else {
            param['address[' + field.storage_field + ']'] = ipt.value;
        }

    }
    param['address[address_id]'] = contactInfo.viewModel.curId;
    for (i = 0; i < payment.viewModel.paymentMethods.length; i++) {
        var p = payment.viewModel.paymentMethods[i];
        if (p.checked) {
            param['payment_method'] = p.payment_method;
            param['bank_payment'] = p.bank;
        }
    }
    var productIdx = 0;
    $('.sub-ctn').each(function (i, e) {

            var bundle = subProducts.viewModel.bundles[i];
            var passengerIdx = {},paxNum=0;
            bundle.pax.forEach(function (e, pid) {
                if (e.select) {
                    if (passengerIdx[e.ticketId] == undefined) {
                        passengerIdx[e.ticketId] = 0;
                    }
                    param['products[' + productIdx + '][passengers][' + e.ticketId + '][' + passengerIdx[e.ticketId] + ']'] = pid;
                    passengerIdx[e.ticketId] += 1;
                    paxNum++;
                }

            });

            if (paxNum ==0 && bundle.bundle_info.group_type == 3) {
                /*alert('请为您选择的商品【' + bundle.name + '】匹配' + bundle.quantity + '个出行人');
                isvalid = false;
                return false;*/
                return true;
            }

            param['products[' + productIdx + '][product_id]'] = bundle.bundle_info.binding_product_id;
            param['products[' + productIdx + '][bundle_product_id]'] = bundle.bundle_product_id;
            param['products[' + productIdx + '][tour_date]'] = bundle.tour_date;
            param['products[' + productIdx + '][special_code]'] = bundle.special_code;
            param['products[' + productIdx + '][bundle_id]'] = bundle.bundle_info.bundle_id;
            if (bundle.departure_code) {
                param['products[' + productIdx + '][departure_code]'] = bundle.departure_code;
                param['products[' + productIdx + '][departure_time]'] = bundle.departure_time;
            }
            core.forEach(bundle.ticket_types, function (key, ticket) {
                param['products[' + productIdx + '][quantities][' + ticket.ticket_id + ']'] = ticket.quantity;
            });
            productIdx++;
    });
    if (isvalid) {
        cartInfo.viewModel.buyLabel = '订单处理中...';
        $.ajax({
            url: $request_urls.addOrder,
            type: 'post',
            dataType: 'json',
            data: param,
            success: function (res) {
                if (res.code == 200) {
                    if (res.data.total == 0) {
                        location.href = res.data.success_url;
                    } else {
                        $('.passengers').addClass('disabled').find('input').attr('disabled', 'disabled');
                        $('.contact').addClass('disabled').find('input').attr('disabled', 'disabled');
                        $('.payment-method').hide();
                        $('.payment h3').hide();
                        $('#payment_unit_'+param['bank_payment']).show();
                        cartInfo.viewModel.toPay = res.data.payment_url;
                    }
                }
                else {
                    cartInfo.viewModel.buyLabel = '确认订单';
                    alert(res.msg);
                }
            },
            error: function () {
                cartInfo.viewModel.buyLabel = '确认订单';
                alert('后台异常！');
                console.log(arguments);
            }
        });
    }
    console.log(param)
};
LoginCallback.register(function () {
    $.ajax({
        url: $request_urls.customerInfo,
        dataType: 'json',
        success: function (res) {
            if (res.code == 200) {
                res.data.addresses.push({
                    address_id: "0",
                    email: "",
                    firstname: "新建联系人",
                    passport_number: "",
                    telephone: ""
                });
                for (var i = 0; i < res.data.addresses.length; i++) {
                    res.data.addresses[i].checked = i == 0;
                }

                contactInfo.viewModel.contactList = res.data.addresses;

                lastChecked = contactInfo.viewModel.contactList[0];
                $('.contact-wrap:eq(0)').trigger('click');
                checkContactComplete();
            }
        }
    });
    return false;
});
var Coupon = avalon.define('coupon', function (vm) {
    vm.btnText = '使用';
    vm.realCode='';
    vm.state = '';
    vm.flag = 1;
    vm.couponCode = '';
    vm.coupon_title = '使用优惠券';
    vm.coupons = [];
    vm.sumPrice = 0;
    vm.finalPrice = 0;
    vm.save = 0;
    vm.origPrice = 0;
    vm.msg = '';
    vm.useMark = -1;
    vm.resolveFinalPrice = function () {
        var price = vm.sumPrice;
        for (var i = 0; i < vm.coupons.length; i++) {
            var cp = vm.coupons[i];
            price = resolveDiscount(price, cp.discount);
        }
        Coupon.finalPrice = price;
    };
    vm.onBlur = function () {
        if (vm.couponCode == '') {
            vm.state = '';
        }
    };
    vm.onFocus = function () {
        if (vm.state == '') {
            vm.state = "active";
        }
    };
    vm.applyCoupon = function (realCode,name) {

        if (vm.couponCode != '' && !/中/.test(vm.btnText)) {

            if (vm.state == 'active') {
                vm.realCode=realCode;
                if (vm.couponCode != '微信扫码优惠') {
                    vm.btnText = '使用中..';
                    if(!vm.realCode){
                        vm.realCode=vm.couponCode;
                    }
                }

                vm.useMark = 1;
                $.ajax({
                    url: $request_urls.validateCoupon,
                    data: {coupon: realCode || vm.couponCode, coupon_title: vm.coupon_title},
                    dataType: 'json',
                    type: 'post',
                    success: function (res) {

                        if (res.code == 200) {
                            if (vm.couponCode != '微信扫码优惠') {
                                vm.msg = "优惠券使用成功";
                            }

                            vm.state = 'complete';
                            vm.btnText = '取消';
                            var insert=0;
                            if(vm.coupons[0]&&vm.coupons[0].name == '为您节省'){
                                insert=1;
                            }
                            vm.coupons.splice(insert,0,{
                                name: name||'使用优惠券',
                                show: (res.data.type == 'P' ? '' : '&yen;') + (res.data.discount | 0) + (res.data.type == 'P' ? '%' : ''),
                                discount: (res.data.discount | 0) + res.data.type,
                                code: res.data.code
                            });

                            Coupon.resolveFinalPrice();
                            //$(".coupon-result").slideDown();
                        }
                        else {
                            vm.msg = res.msg;
                            vm.useMark = 0;
                            vm.btnText = '使用';

                        }

                    }
                });

            }
            else {
                vm.btnText = '取消中..';
                $.ajax({
                    url: $request_urls.clearCoupon,
                    dataType: 'json',
                    data: {coupon: vm.couponCode},
                    type: 'post',
                    success: function (res) {
                        if (res.code == 200) {
                            vm.msg = "";
                            vm.state = 'active';
                            vm.btnText = '使用';
                            vm.useMark = 1;
                            if (vm.couponCode == '微信扫码优惠') {
                                vm.couponCode = '';
                                vm.state = '';
                                vm.coupon_title = '使用优惠券';

                            }
                            for (var i = 0; i < vm.coupons.length; i++) {
                                if (vm.coupons[i].code == vm.couponCode||vm.coupons[i].code==vm.realCode) {
                                    vm.coupons.splice(i, 1);
                                    break;
                                }
                            }
                            Coupon.resolveFinalPrice();
                            //$(".coupon-result").slideUp();
                            $('.scan-success').hide();
                            $('.scan-ctn').show();
                        }
                        else {
                            vm.msg = res.msg;
                            vm.useMark = 0;
                            vm.btnText = '取消';
                        }
                    }
                });

            }
        }
    }
})

var wechat;
$(function () {
    LoginCallback.execute();
    var retry = 5;
    $('.try-again').on('click', function () {
        $('.scan-error').hide();
        $('.scan-ctn').show();
        $('.qr-code-ctn').removeClass('error');
    });
    var socket = io(location.protocol + '//' + location.hostname + ':60001/wechat/qrcode',{
        'reconnect': true,
        'reconnectionDelay': 500,
        'reconnectionAttempts': 10
    });
    socket.on('qrcode', function (msg) {
        if (msg) {
            $('#qrcode img').attr('src', msg)
        }
        else {
            if (retry-- > 0) {
                setTimeout(function () {
                    socket.emit('qrcode', navigator.userAgent);
                }, 1000);
            }
        }
    });
    socket.on('finish', function (data) {
        wechat = data;
        console.log(data);
        $.ajax({
            url: $request_urls.weixinScan,
            type: 'post',
            dataType: 'json',
            data: {
                email: contactInfo.viewModel.contactFieldset[2].value,
                telephone: contactInfo.viewModel.contactFieldset[1].value,
                openid: wechat.data.openId,
                nickname: wechat.data.nick_name,
                avatar_url: wechat.data.avatar_url,
                unionid: wechat.data.unionid
            },
            success: function (res) {
                if (res.code == 200) {
                    $('.scan-ctn').hide();
                    $('.scan-success').show();
                    $('.qr-code-ctn').removeClass('error');
                    Coupon.state = 'active';
                    Coupon.couponCode = "微信扫码优惠";
                    Coupon.coupon_title = '微信扫码优惠';
                    Coupon.btnText = '取消';
                    Coupon.applyCoupon(res.data.coupon,'微信扫码优惠');
                    /*Coupon.state = 'complete';
                     Coupon.save = 10;
                     Coupon.origPrice = cartInfo.viewModel.sumPrice;
                     cartInfo.viewModel.sumPrice = cartInfo.viewModel.sumPrice - 10;
                     $(".coupon-result").slideDown();*/
                }
                else {
                    $('.qr-code-ctn').addClass('error');
                    $('.scan-ctn').hide();
                    $('.scan-error').show().find('p').html(res.msg);
                }
            }
        });

    });
    socket.emit('qrcode', navigator.userAgent);
    socket.on('connect_error', function () {
        $('#qrcode .qr-mask').css('background', '#6db381');
    })
    socket.on('reconnect', function () {
        $('#qrcode .qr-mask').css('background', 'transparent');
        socket.emit('qrcode', navigator.userAgent);
    });
});
