/**
 * Created by godsong on 14-10-24.
 */


window.productInfo = new ViewModel('productInfo', 'mainProduct|{},hotelInfo,selectedList|[],optionalList|[],sub_total_orig,sub_total,onHover,onMouseout,appendCart,selectOptional,removeOptional,modifyOptional,openDialog,add,reduce,showList,selectSpecial,selectDeparture,confirmBundle');
var moreInfo = new ViewModel('moreInfo', 'name,origPrice,price,contentList,index,show');
var productDialog = new ViewModel('productDialog', 'name,show,specialCodes|[],close,selectSpecial,calcPrice,confirm');
productInfo.viewModel.showList = function(el, name, evt) {

    for(var i = 0; i < productInfo.viewModel.selectedList.length; i++) {
        if(productInfo.viewModel.selectedList[i] !== el) {
            productInfo.viewModel.selectedList[i].show_special = false;
            productInfo.viewModel.selectedList[i].show_departure = false;
        }
    }
    el[name] = !el[name];
    evt.stopPropagation();
};
var calcBundlePrice = function(bundle) {
    var subtotal = 0, subtotal_orig = 0;
    core.forEach(bundle.ticket_types, function(tid, ticket) {
        ticket.prices = calcPrice(bundle.price_plan, discountMap, findComboSpecialCode(bundle.special.special_code), bundle.tour_date, tid);
        subtotal += ticket.quantity * ticket.prices.price;
        subtotal_orig += ticket.quantity * ticket.prices.origPrice;
    });
    bundle.subtotal = subtotal;
    bundle.subtotal_orig = subtotal_orig;
    calcSum();
}
productInfo.viewModel.selectSpecial = function(el, sp, evt) {
    el.special = sp.$model;
    el.show_special = false;
    el.error.special = false;

    calcBundlePrice(el);
    evt.stopPropagation();
};
productInfo.viewModel.selectDeparture = function(el, dp, evt) {
    el.departure = dp.$model;
    el.show_departure = false;
    el.error.departure = false;
    evt.stopPropagation();
};
productInfo.viewModel.confirmBundle = function(el) {
    var flag = true;
    if(el.date_rule.need_tour_date == 1 && !el.tour_date) {
        flag = false;
        el.error.date = true;

    }
    if(el.specialCodes.length > 0 && !el.special.special_code) {
        flag = false;
        el.error.special = true;
    }
    if(el.departures.length > 0 && !el.departure.departure_point) {
        flag = false;
        el.error.departure = true;
    }
    if(flag) {
        el.edit = false;
    }
}
productDialog.viewModel.close = function() {
    productDialog.viewModel.show = false;
};
productInfo.viewModel.onMouseout = function() {
    moreInfo.viewModel.show = false;
};
productInfo.viewModel.appendCart = function() {
    var params = {};
    /*if(productInfo.viewModel.selectedList.length==0){
     location.href = $request_urls.checkout;
     return;
     }*/
    for(var i = 0; i < productInfo.viewModel.selectedList.length; i++) {
        var selected = productInfo.viewModel.selectedList[i];
        if(selected.edit) {
            $('body,html').animate({scrollTop : $('.selected-list:eq(' + i + ')').offset().top - 50});
            return;
        }
        params['products[' + i + '][product_id]'] = selected.product_id;
        params['products[' + i + '][special_code]'] = findComboSpecialCode(selected.special.special_code);
        params['products[' + i + '][special_name]'] = selected.special.cn_name;
        params['products[' + i + '][tour_date]'] = selected.tour_date;
        params['products[' + i + '][departure_time]'] = selected.departure.time;
        params['products[' + i + '][departure]'] = selected.departure.show;
        params['products[' + i + '][departure_code]'] = selected.departure.code;
        var quantity = 0;
        core.forEach(selected.ticket_types, function(tid, ticket) {
            quantity += (ticket.quantity | 0);
        })
        params['products[' + i + '][pax_num]'] = quantity;

    }
    console.log(params);
    $.ajax({
        url      : $request_urls.appendCart,
        data     : params,
        dataType : 'json',
        type     : 'post',
        success  : function(res) {
            console.log(res);
            location.href = res.data.checkout_url;

        }
    })
};
var calcSum = function() {
    productInfo.viewModel.sub_total_orig = +productInfo.viewModel.mainProduct.origPrice;
    productInfo.viewModel.sub_total = +productInfo.viewModel.mainProduct.price;

    for(var i = 0; i < productInfo.viewModel.selectedList.length; i++) {
        var select = productInfo.viewModel.selectedList[i];
        if(!select.bundle_info) {

            productInfo.viewModel.sub_total += select.subtotal;
        }
        productInfo.viewModel.sub_total_orig += select.subtotal_orig;
    }
};
productInfo.viewModel.removeOptional = function(el) {
    for(var i = 0; i < productInfo.viewModel.selectedList, length; i++) {
        if(productInfo.viewModel.selectedList[i] === el) {
            productInfo.viewModel.selectedList.splice(i, 1);
            break;
        }
    }
    el.checked = false;
    calcSum();
};
productInfo.viewModel.modifyOptional = function(el) {

    el.edit = true;
    /* productInfo.viewModel.selectedList.splice(el)
     var select = productInfo.viewModel.selectedList[index];
     var product = productInfo.viewModel.optionalList[select.index];
     productDialog.product = product;
     productDialog.index = select.index;
     productDialog.modify = index;
     productDialog.viewModel.name = product.name;
     productDialog.viewModel.specialCodes = product.special_codes;

     productDialog.viewModel.show = true;*/
};
productDialog.viewModel.selectSpecial = function(index) {
    for(var i = 0; i < productDialog.viewModel.specialCodes.length; i++) {
        if(productDialog.viewModel.specialCodes[i].checked == true) {
            productDialog.viewModel.specialCodes[i].checked = false;
        }
    }
    productDialog.viewModel.specialCodes[index].checked = true;
    productDialog.curSpecial = productDialog.viewModel.specialCodes[index];
    productDialog.viewModel.show = false;
    productDialog.product.checked = true;
    if(productDialog.modify >= 0) {
        console.log(productDialog.modify);
        console.log(productDialog.curSpecial);
        var select = productInfo.viewModel.selectedList[productDialog.modify];
        select.price = productDialog.curSpecial.price;
        select.origPrice = productDialog.curSpecial.origPrice;
        select.specialCode = productDialog.curSpecial.special_code;
        select.specialName = productDialog.curSpecial.cn_name;
        calcSum();
    }
    else {
        productInfo.viewModel.selectedList.push(productDialog.product);
        calcSum();
    }
};
productDialog.viewModel.confirm = function() {
    if(productDialog.curSpecial) {

    }
}
var tipsTm = -1;
productInfo.viewModel.add = function(select) {
    if(select.quantity < Math.min(999, productInfo.viewModel.mainProduct.pax_num)) {
        select.quantity++;
    }
    else {
        select.showTips = true;
        if(tipsTm >= 0) {
            clearTimeout(tipsTm);
        }
        tipsTm = setTimeout(function() {
            select.showTips = false;
        }, 2000)
    }

    calcSum();
};
productInfo.viewModel.reduce = function(select) {
    if(select.quantity > 1) {
        select.quantity--;
    }

    calcSum();
};


var discountMap = {};
function calcHotelPrice(date, pricePlan, tid, quantity) {
    for(var i = 0; i < pricePlan.length; i++) {
        var p = pricePlan[i];
        if(p.valid_region == '0' || (date >= p.from_date && date <= p.to_date)) {
            for(var k in p.price_map) {
                if(p.price_map.hasOwnProperty(k)) {
                    return (p.price_map[k][1] || p.price_map[k][99])['1']['orig_price'];
                }
            }
        }
    }
}
function calcPrice(pricePlan, discountMap, specialCode, dateStr, ticketId) {
    var result = {}, qtf;
    dateStr = dateStr || new Date().format('yyyy-mm-dd');
    for(var i = 0; i < pricePlan.length; i++) {
        var p = pricePlan[i];
        if(p.valid_region == '0' || dateStr >= p.from_date && dateStr <= p.to_date) {
            var pm = p.price_map[specialCode || 0];
            var curPrice = (ticketId && pm[ticketId] || (qtf = '套', pm[99]) || (qtf = '人', pm[1]) ||
                            (qtf = '人', pm[2]))[1];
            result.price = resolveDiscount(curPrice.price, discountMap[p.product_id]);
            result.origPrice = curPrice.orig_price;
            result.quantifier = qtf;
        }
    }
    return result;
}
function calcMinPrice(pricePlan, discountMap, specialCode, ticket_id) {
    var now = new Date().format(), result = {price : 99999, origPrice : 0};
    for(var i = 0; i < pricePlan.length; i++) {
        var p = pricePlan[i];
        if(p.valid_region == '0' || now <= p.to_date) {
            if(specialCode) {
                var pm = p.price_map[specialCode],
                    qtf;//量词
                if(!pm) {
                    return false;
                }
                var curPrice = (ticket_id && pm[ticket_id] || (qtf = '套', pm[99]) || (qtf = '人', pm[1]) ||
                                (qtf = '人', pm[2]))[1];
                if(curPrice.price < result.price) {
                    result.price = resolveDiscount(curPrice.price, discountMap[p.product_id]);
                    result.origPrice = curPrice.orig_price;
                    console.log(p.product_id)
                }
            }
            else {
                for(var k in p.price_map) {
                    if(p.price_map.hasOwnProperty(k)) {
                        pm = p.price_map[k];
                        curPrice = (pm[99] || pm[1] || pm[2])[1];
                        if(curPrice.price < result.price) {
                            result.price = resolveDiscount(curPrice.price, discountMap[p.product_id]);
                            result.origPrice = curPrice.orig_price;
                            console.log(p.product_id)
                        }
                    }
                }
            }
        }
    }
    return result;
}
function resolveDiscount(origPrice, discount) {
    if(isFinite(discount)) {//如果没有折扣类型字母作为后缀 则当做直接减
        var result = origPrice - discount;
    }
    else {
        var match = /^(\d+)(P|F)/i.exec(discount);
        if(match) {
            if(match[2].toUpperCase() == 'P') {
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
    return Math.max(result, 0);
}
var uniTiketId;
var cartFactory = new HitourDataFactory($request_urls.cartData, function(data) {
    data.bundles.sort(function(a, b) {
        if(a.group_type > b.group_type) {
            return 1;
        }
        else if(a.group_type < b.group_type) {
            return -1;
        }
        else {
            return 0;
        }
    });
    if(data.bundles.length < 3) {
        for(var idx = 0; idx < 3; idx++) {
            if(!data.bundles[idx]) {
                data.bundles.push({items : [], products : []})
            }
            else if(data.bundles[idx].group_type > (idx + 1)) {
                data.bundles.splice(idx, 0, {items : [], products : []});
                idx++;
            }
        }
    }
    console.log(data.bundles);
    for(var k in data.raw_data.quantities) {
        if(data.raw_data.quantities.hasOwnProperty(k)) {
            uniTiketId = k;
            break;
        }
    }

    //data.product.origPrice=calcHotelPrice(data.raw_data.tour_date||new Date().format(),data.bundles[0].products[0].price_plan,uniTiketId,1);
    data.mainProduct = {
        name         : data.product.name,
        hotelName    : data.raw_data.special_name,
        packagedList : [{description : {name : data.raw_data.special_name}}].concat(data.bundles[1].products),
        num          : data.raw_data.quantities[uniTiketId],
        price        : data.product.sub_total,
        imgUrl       : data.product.cover_image_url,
        origPrice    : data.product.orig_total,
        pax_num      : data.raw_data.pax_num
    };
    data.selectedList = [];
    data.optionalList = [];
    var hotelInfo = data.raw_data.tour_date + '&nbsp;&nbsp;' + data.raw_data.quantities[uniTiketId] + '间房&nbsp;&nbsp;'
    if(data.raw_data.adult_num > 0) hotelInfo += data.raw_data.adult_num + '成人';
    if(data.raw_data.child_num > 0) {
        hotelInfo += '，' + data.raw_data.child_num + '儿童（';
        data.raw_data.child_list.forEach(function(e) {
            hotelInfo += e.age + '岁,';
        });
        hotelInfo = hotelInfo.replace(/,$/, ')');
    }
    data.hotelInfo = hotelInfo;

    var items = data.bundles[1].items.concat(data.bundles[2].items);
    for(var i = 0; i < items.length; i++) {
        var item = items[i];
        discountMap[item.binding_product_id] = item.discount_amount + item.discount_type;

    }
    for(i = 0; i < data.bundles[1].items.length; i++) {
        data.bundles[1].products[i].bundle_info = data.bundles[1].items[i];
    }
    var products = data.bundles[1].products.concat(data.bundles[2].products);
    for(i = 0; i < products.length; i++) {
        var product = products[i];
        if(product.special_info&&product.special_info.groups) {
            product.special_codes = resolveSpecialInfo(product.special_info)[0].special_codes;
        }
        else{
            product.special_codes=[];
        }
        for(var j = 0; j < product.special_codes.length; j++) {
            product.special_codes[j].price = 0;
            product.special_codes[j].origPrice = 0;
            product.special_codes[j].checked = false;
        }

        product.tour_date = '';
        product.edit = false;
        product.editable = false;
        product.error = {date : false, special : false, departure : false};
        product.departures = [];
        product.departure = {};
        product.specialCodes = [];
        product.special = {};
        product.show_special = false;
        product.show_departure = false;
        product.price = calcMinPrice(product.price_plan, discountMap);


        core.forEach(product.ticket_types, function(tid, ticket) {
            if(ticket.ticket_id == 2) {
                ticket.quantity = data.raw_data.adult_num;
            }
            else if(ticket.ticket_id == 1) {
                ticket.quantity = (data.raw_data.adult_num | 0) + (data.raw_data.child_num | 0);

            }
            else {
                ticket.quantity = 0;
            }
            ticket.prices = calcMinPrice(product.price_plan, discountMap, 0, tid);
            ticket.quantifier = ticket.ticket_type.cn_name.replace('出行人', '人').replace('票', '');
            if(product.bundle_info&&product.bundle_info.count_type==1){
                ticket.quantity=data.raw_data.quantities[uniTiketId]*product.bundle_info.count;
                if(ticket.ticket_id==1){
                    ticket.quantifier=''
                }
            }
            else if(product.bundle_info&&product.bundle_info.count_type==2){
                ticket.quantity=product.bundle_info.count;
                if(ticket.ticket_id==1){
                    ticket.quantifier=''
                }
            }

        });
        console.log(product.ticket_types);
        data.raw_data.child_list.forEach(function(c, i) {
            core.forEach(product.ticket_types, function(tid, ticket) {
                var range = ticket.age_range.split('-');
                if((+c.age) >= (+range[0]) && (+c.age) <= (+range[1])) {
                    ticket.quantity++;
                    if(ticket.ticket_id == 2) {
                        ticket.tips = '您的其中一个儿童（' + c.age + '岁）不符合该商品的儿童票年龄范围 只能购买成人票';
                    }
                }

            });

        });
        product.subtotal = 0;
        product.subtotal_orig = 0;
        core.forEach(product.ticket_types, function(tid, ticket) {
            product.subtotal += ticket.quantity * ticket.prices.price;
            product.subtotal_orig += ticket.quantity * ticket.prices.origPrice;
        });
        if(product.bundle_info) {
            if(product.bundle_info.count_type == 1) {
                product.quantity = data.raw_data.quantities[uniTiketId];
            }
            else if(product.bundle_info.count_type == 2) {
                product.quantity = product.bundle_info.count;
            }
            else {
                product.quantity = parseInt(data.raw_data.adult_num) + parseInt(data.raw_data.child_num);
            }
            product.removable = false;
            product.price.price = '0';
        }
        else {
            data.optionalList.push(product);
            product.removable = true;
            product.quantity = parseInt(data.raw_data.adult_num) + parseInt(data.raw_data.child_num);

        }
        if(product.ticket_types[1] && !product.ticket_types[99]) {
            product.price.quantifier = product.ticket_types[1].ticket_type.cn_name.replace('出行人', '人');
        }
        product.checked = false;

    }
    for(i = 0; i < data.bundles[1].products.length; i++) {
        product = data.bundles[1].products[i];
        if(product.date_rule.need_tour_date == 1 || product.special_codes.length > 0 ||
           product.departure_rule.length > 0) {
            product.edit = true;
            product.editable = true;
        }
        data.selectedList.push(product);
    }

    return data;
});

productInfo.bindData(cartFactory.getData({
    onHover        : function(src, data) {
        return function(i, product) {
            moreInfo.viewModel.index = i;
            moreInfo.viewModel.show = true;
            moreInfo.viewModel.name = product.name;
            moreInfo.viewModel.origPrice = product.price.origPrice;
            moreInfo.viewModel.price = product.price.price;
            moreInfo.viewModel.contentList = data.bundles[2].products[i].description.service_include;
        }
    },
    'sub_total'    : 'product.sub_total',
    sub_total_orig : 'product.orig_total',
    selectOptional : function(src, data) {
        return function(index, el) {
            if(!el.checked) {
                var product = data.bundles[2].products[index];
                el.checked = true;
                if(el.date_rule.need_tour_date == 1 || el.special_codes.length > 0 || el.departure_rule.length > 0) {
                    el.edit = true;
                    el.editable = true;
                }
                productInfo.viewModel.selectedList.push(el);
                if(el.date_rule.need_tour_date == 1) {
                    var limits = getTourDateLimits(el.date_rule);
                    setTimeout(function() {
                        new XCalendar($('.tour-date:last'), {
                            width    : 200,
                            min      : limits.start,
                            max      : limits.end,
                            disable  : limits.disable,
                            partials : {
                                'append' : '<div class="date-legend"><div class="legend-item" style="margin-right: 30px;width: 120px;"><div class="date-enable"></div>可选日期</div><div class="legend-item"><div class="date-disable"></div>关闭时间</div></div><div class="calendar-arrow"></div>'
                            },
                            onSelect : function(date) {
                                el.tour_date = date.format('yyyy-mm-dd');
                                el.departures = genDepartures(el, el.tour_date);
                                el.specialCodes = genSpecialCode(el, el.tour_date);
                                if(el.specialCodes.length == 0) {
                                    el.price = calcPrice(el.price_plan, discountMap, 0);
                                    calcBundlePrice(el);
                                }
                                el.error.date = false;


                            }
                        })
                    }, 500);
                    calcSum();
                }
                else {
                    calcBundlePrice(el);
                }


            }
            else {
                el.checked = false;
                for(var i = 0; i < productInfo.viewModel.selectedList.length; i++) {
                    if(productInfo.viewModel.selectedList[i] === el) {
                        productInfo.viewModel.selectedList.splice(i, 1);
                        calcSum();
                        break;
                    }
                }

            }
        }
    }
}));
moreInfo.bindData(cartFactory.getData({})).then(function() {
    $(function() {
        $('.loading-mask').hide();
        $('.tour-date').each(function(i, e) {
            var $element = $(e);
            var el = productInfo.viewModel.selectedList[i];
            if(el.date_rule.need_tour_date == 1) {
                var limits = getTourDateLimits(el.date_rule);
                new XCalendar($element, {
                    width    : 200,
                    min      : limits.start,
                    max      : limits.end,
                    disable  : limits.disable,
                    partials : {
                        'append' : '<div class="date-legend"><div class="legend-item" style="margin-right: 30px;width: 120px;"><div class="date-enable"></div>可选日期</div><div class="legend-item"><div class="date-disable"></div>关闭时间</div></div><div class="calendar-arrow"></div>'
                    },
                    onSelect : function(date) {
                        el.tour_date = date.format('yyyy-mm-dd');
                        el.departures = genDepartures(el, el.tour_date);
                        el.specialCodes = genSpecialCode(el, el.tour_date);
                        if(el.specialCodes.length == 0) {
                            el.price = calcPrice(el.price_plan, discountMap, 0);
                        }
                        el.error.date = false;
                    }
                })
            }
            else {
                calcBundlePrice(el);
            }
        });
        $(document).on('click', function() {
            for(var i = 0; i < productInfo.viewModel.selectedList.length; i++) {
                productInfo.viewModel.selectedList[i].show_special = false;
                productInfo.viewModel.selectedList[i].show_departure = false;
            }
        })
        calcSum();
    })
});


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

    function disable(date, dateStr) {
        for(var i = 0; i < date_rule.operations.length; i++) {
            var operation = date_rule.operations[i];
            if(dateStr >= operation.from_date && dateStr <= operation.to_date) {
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
                        if(dateStr >= ft[0] && dateStr <= ft[1]) {
                            return true;
                        }
                    }
                    else if(close) {
                        if(dateStr == close) {
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
function genDepartures(data, dateStr) {
    var departures = [];
    for(i = 0; i < data.departure_rule.length; i++) {
        var dep = data.departure_rule[i];

        if(dep.valid_region == 0 || (dateStr >= dep.from_date && dateStr <= dep.to_date)) {
            if(dep.time == '00:00:00') {
                var showTime = '';
            }
            else {
                showTime = dep.time.slice(0, -3) + ' '
            }
            if(dep.additional_limit) {
                var limits = dep.additional_limit.split(';');
                for(var j = 0; j < limits.length; j++) {
                    var limit = limits[j];
                    if(limit.indexOf('周') != -1) {
                        if(DateUtil.parse(dateStr).getDay() == (+limit.slice(1) % 7)) {
                            departures.push({
                                time            : dep.time,
                                showTime        : showTime,
                                show            : showTime + dep.departure.departure_point,
                                departure_point : dep.departure.departure_point,
                                code            : dep.departure.departure_code
                            });
                        }
                        else if(dateStr == limit) {
                            departures.push({
                                time            : dep.time,
                                showTime        : showTime,
                                show            : showTime + dep.departure.departure_point,
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
                    show            : showTime + dep.departure.departure_point,
                    departure_point : dep.departure.departure_point,
                    code            : dep.departure.departure_code
                });
            }
        }
    }
    return departures;

}
function genSpecialCode(data, tour_date) {
    var list = [];
    var pl = getPricePlan(data.price_plan, tour_date);
    var date = DateUtil.parse(tour_date);
    var spcList = [];
    for(var spc in pl) {
        if(pl.hasOwnProperty(spc)) {
            for(var k in pl[spc]) {
                if(pl[spc].hasOwnProperty(k)) {
                    for(var customer_count in pl[spc][k]) {
                        if(pl[spc][k].hasOwnProperty(customer_count)) {
                            var f = pl[spc][k][customer_count].frequency;
                            if(f && f != 'wdall') {
                                f = f.split(';');
                                for(var j = 0; j < f.length; j++) {
                                    if(date.getDay() == f[j].substr(2, 1) % 7) {
                                        if(!data.spc || data.spc == spc) {
                                            spcList.push(spc);
                                        }
                                        break;
                                    }
                                }
                            }
                            else if(f=='wdall'){
                                if(!data.spc || data.spc == spc) {
                                    spcList.push(spc);
                                }
                            }
                            break;
                        }

                    }
                    break;
                }
            }
        }
    }
    var sl = spcList.join(',');
    for(i = 0; i < data.special_codes.length; i++) {
        if(sl.indexOf(data.special_codes[i].special_code) != -1) {
            data.special_codes[i].price = calcPrice(data.price_plan, discountMap, findComboSpecialCode(data.special_codes[i].special_code), data.tour_date);
            list.push(data.special_codes[i]);

        }
    }
    return list;
}
function getPricePlan(pricePlan, dateStr) {
    for(var i = 0; i < pricePlan.length; i++) {
        priceSection = pricePlan[i];

        if(priceSection.valid_region == '0' || dateStr >= priceSection.from_date && dateStr <= priceSection.to_date) {
            return priceSection.price_map;
        }
    }
    return null;
}