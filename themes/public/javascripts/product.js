var baseDataFactory = new HitourDataFactory($request_urls.getProducts);
var productScenesFactory = new HitourDataFactory($request_urls.productScenes);
var bizDataFactory = new HitourDataFactory($request_urls.getBizData, null, function (data) {
    //data.special_codes=[];

});

var FlowlineViewModel = Class({
    $extend: ViewModel,
    $init: function (name, props) {
        this.$super(name, props);
        this.fulfilled = 0;
    },
    open: function () {
        this.viewModel.status = '';
        this.viewModel.flag = 1;
    },
    close: function () {
        this.viewModel.status = 'collapse';
    }
}).addAspect({
    'around@*': function (args) {
        if (this.host._hookList) {
            var callback = this.host._hookList[this.pos + '@' + this.methodName];
            callback && callback.apply(this.host, args);
        }
    },
    '+addHook': function (name, callback) {
        if (!this._hookList) {
            this._hookList = {};
        }
        this._hookList[name] = callback;
    }

});

var productInfo = new ViewModel('productInfo', 'country_name,country_url,city_name,city_url,description|{},sliders|[],rules|{},service_include');
var productScenes = new ViewModel('productScenes', 'description|{},qa,landinfo_groups|{},all_landinfo|[],communications|[],gmap_url,land_names|[]');
var buyNotice = new ViewModel('buyNotice', 'sample|{},gmap_url,benefit,how_it_works,pick_landinfo_groups|[]');

var subject = new ViewModel('subject', 'subjects|[],link_url,related|[]');
var order = new ViewModel('order', 'productId,closeSection,onHover,onLeave,show_prices|{},selectOption,openSection');
var specialTips = new ViewModel('specialTips', 'name,description,specialPrices|[],top,visible');
var tourDate = new FlowlineViewModel('tourDate', 'flag,status,needTourDate,selectedDate,openDatePicker');
var specialCode = new FlowlineViewModel('specialCode', 'flag,status,selectedSpecial,specialPrices|[],special_codes|{}')
var departure = new FlowlineViewModel('departure', 'departures|[],flag,status,selectedDeparture');
var ticketType = new FlowlineViewModel('ticketType', 'ticketTypes|[],closeCounter,ticketTips,flag,status,counterAdd,counterReduce,close,open');
var sumPrice = new FlowlineViewModel('sumPrice', 'sumPrice,toBuy,discount,reduce,flag,checkout,inCheckout|"购买"');
sumPrice.addHook('after@open', function () {
    calcSumPrice();
    sumPrice.viewModel.toBuy = true;
});
ticketType.addHook('after@open', function () {
    this.fulfilled = 1;
});
var infoAdapter = new DataAdapter({
    country_name: 'city.country.cn_name',
    country_url: 'city.country.link_url',
    city_name: 'city.cn_name',
    city_url: 'city.link_url',
    sliders: 'images.sliders'
});
var sceneAdapter = new DataAdapter({
    'gmap_url': function (src, data) {
        var markers = '';
        var c = 1;
        for (var key in data.landinfo_groups) {
            if (data.landinfo_groups.hasOwnProperty(key)) {
                for (var i = 0; i < data.landinfo_groups[key].length; i++) {
                    markers += '&markers=color:red%7Clabel:' +
                        c++ + '%7C' +
                        data.landinfo_groups[key][i].location_latlng;
                }
            }
        }

        return 'http://maps.googleapis.com/maps/api/staticmap?size=631x360' + markers;
    },
    'all_landinfo': function (all_landinfo) {
        var tmp;
        var len = all_landinfo.length;
        var pattern = /<h2>.*?<\/h2><ol>.*?<\/ol>/g;

        for (var i = 0; i < len; i++) {
            all_landinfo[i].lists = [];

            do {
                tmp = pattern.exec(all_landinfo[i].list);
                tmp && all_landinfo[i].lists.push(tmp[0]);
            } while (tmp);
        }

        return all_landinfo;
    },
    'communications': function (communications) {
        var result = [];
        if (communications) {
            var len = communications.length;
            for (var i = 0; i < len; i++) {
                result.push({
                    title: communications[i].title,
                    description: communications[i].description
                });
            }

        }
        return result;
    }
});
var bnAdapter = new DataAdapter({
    sample: 'images.sample',
    benefit: function (val, src) {
        return core.isEmpty(src.description.benefit) ? null : src.description.benefit
    },
    how_it_works: 'description.how_it_works',
    pick_landinfo_groups: function (val, src) {

        for (var i = 0; i < val.length; i++) {
            var obj = val[i];
            var makers = '';
            for (var j = 0; j < obj.landinfos.length; j++) {
                makers += '&markers=color:red%7Clabel:' + (i+''+(j + 1)) +
                    '%7C' + obj.landinfos[j].location_latlng;
            }
            obj.gmap_url = 'http://maps.googleapis.com/maps/api/staticmap?size=631x324' + makers;


        }
        return val;
    },
    gmap_url: function (val, src) {
        var makers = '';
        var charCode = 'A'.charCodeAt(0);
        for (var i = 0; i < src.pick_landinfo_groups.length; i++) {
            var placeGroup = src.pick_landinfo_groups[i];
            for (var j = 0; j < placeGroup.landinfos.length; j++) {
                makers += '&markers=color:red%7Clabel:' + String.fromCharCode(charCode++) +
                    '%7C' + placeGroup.landinfos[j].location_latlng;
            }
        }
        return 'http://maps.googleapis.com/maps/api/staticmap?size=631x324' + makers;
    }
});
var orderModel = {};

productInfo.bindData(baseDataFactory.getData(infoAdapter)).then(function () {
    $('.nivoSlider').nivoSlider({
        'pauseTime': 36000000
    });
    var $tabCtn = $('.tab-ctn');
    window.onscroll = function () {
        if ($(document).scrollTop() > $tabCtn.offset().top) {
            $tabCtn.addClass('fixed');
        }
        else {
            $tabCtn.removeClass('fixed');
        }
    }
    $('#fix_buy').click(function () {
        scrollTo($('.order-ctn').offset().top);
    });
    var $allTab = $tabCtn.find('.tab').on('click', function () {
        $allTab.removeClass('active');
        var target = $(this).addClass('active').attr('data-target');
        if (target) {
            scrollTo($(target).offset().top - 54);
        }
    })
});
productScenes.bindData(productScenesFactory.getData(sceneAdapter)).then(function () {
    var $list = $('.one-location-list');
    var $accr = $('.one-accordion');

    $list.find('h2').addClass('list-title');
    $list.find('ol').addClass('list-body');
    $list.find('li').addClass('one-list-item');
    $list.each(function () {
        var $oneList = $(this);
        $oneList.find('li').each(function () {
            var index = $oneList.find('li').index($(this));
            var className = ( index % 2 == 0 ) ? 'odd-row' : 'even-row';
            $(this).addClass(className);
        });
    });

    //Hide all and expand first
    $accr.find('.section-body').hide();
    $accr.first().addClass('expand').find('.section-body').slideDown();
    $accr.on('click', '.section-title', function () {
        var $oneAccr = $(this).parents('.one-accordion');
        if ($oneAccr.hasClass('expand')) {
            $oneAccr.removeClass('expand').find('.section-body').slideUp();
        }

        else {
            $accr.removeClass('expand').find('.section-body').slideUp();//Hide all other
            $oneAccr.addClass('expand').find('.section-body').slideDown(null, null, function () {
                window.scrollTo(0, $oneAccr.offset().top)
            }); //Show this
        }
    });
});
buyNotice.bindData(baseDataFactory.getData(bnAdapter));

subject.bindData(baseDataFactory.getData(new DataAdapter({
    subjects: 'city.product_groups',
    link_url: 'city.link_url'
})));

var Flowline = function () {
    var _flowline = [];
    var exports = {};
    exports.f = _flowline;
    exports.add = function () {
        for (var i = 0; i < arguments.length; i++) {
            _flowline.push(arguments[i]);
            arguments[i]._fIdx = i;
        }
    };
    exports.setRelate = function (a, b) {
        a.related = b;
    };
    exports.remove = function (a) {
        var idx = a._fIdx;
        _flowline.splice(idx, 1);
        for (var i = 0; i < _flowline.length; i++) {
            if(_flowline[i].related===a){
                delete _flowline[i].related;
            }
            _flowline[i]._fIdx = i;

        }


    };
    exports.openNext = function (cur) {
        var nextIdx = cur._fIdx;
        while (++nextIdx < _flowline.length) {
            if (_flowline[nextIdx].fulfilled == 0) {
                _flowline[nextIdx].open();
                exports.others(_flowline[nextIdx], function () {
                    this.close();
                });
                if (_flowline[nextIdx].bind) {
                    _flowline[nextIdx].bind.open();
                }
                return;
            }
        }
        if (_flowline[cur._fIdx].related !== undefined) {
            _flowline[cur._fIdx].related.open();
        }
        else {
            for (var i = 0; i < _flowline.length; i++) {
                _flowline[i].viewModel.flag = 1;
            }
            sumPrice.viewModel.flag = 1;
            sumPrice.viewModel.toBuy = true;
        }
    };
    exports.others = function (cur, action) {
        for (var i = 0; i < _flowline.length; i++) {
            if (cur._fIdx != i) {
                action.call(_flowline[i]);
            }

        }
    };
    exports.setStatus = function (cur, status) {
        _flowline[cur._fIdx] = status;
    };
    exports.setBind = function (target, bind) {
        _flowline[target._fIdx].bind = bind;
    };
    exports.start = function () {
        _flowline[0].viewModel.flag = 1;
    };
    return exports;

}();
function openSection() {
    this.viewModel.flag = 1;
    this.viewModel.status = '';
    if (this.name == 'sumPrice') {
        calcSumPrice();
    }
    sumPrice.viewModel.toBuy = false;

}
function closeSection(status) {
    if (this.fulfilled == 0) {
        this.viewModel.flag = 0;
    }
    this.viewModel.status = 'collapse';
}
Flowline.add(tourDate, specialCode, departure, ticketType);
Flowline.setRelate(tourDate, departure);
Flowline.setBind(ticketType, sumPrice);

sumPrice.viewModel.checkout = function () {
    for (var k in orderModel.quantity) {
        orderModel['quantity[' + k + ']'] = orderModel.quantity[k];
    }
    orderModel.product_id = order.viewModel.productId;
    delete orderModel.quantity;
    sumPrice.viewModel.inCheckout = '下单中...';
    $.ajax({
        url: $request_urls.addCart,
        dataType: 'json',
        type: 'post',
        data: orderModel,
        success: function (res) {
            console.log(res);
            if (res.code == 200) {
                location.href = res.data.checkout_url;
            }
        }
    });
};
orderModel.quantity = {};
function calcSumPrice() {
    var sum = 0, orig_sum = 0;
    for (var i = 0; i < ticketType.viewModel.ticketTypes.length; i++) {
        var ticket = ticketType.viewModel.ticketTypes[i];
        sum += ticket.quantity * ticket.price;
        orig_sum += ticket.quantity * ticket.orig_price;
    }
    sumPrice.viewModel.sumPrice = sum;
    sumPrice.viewModel.discount = ((orig_sum - sum) * 100 / orig_sum) | 0;
    sumPrice.viewModel.reduce = orig_sum - sum;

}
function sumNum() {
    var sum = 0;
    for (var i = 0; i < ticketType.viewModel.ticketTypes.length; i++) {
        var ticket = ticketType.viewModel.ticketTypes[i];
        sum += ticket.quantity;
    }
    return sum;
}
var _tipTimer;


ticketType.viewModel.close = function () {
    if (ticketType.viewModel.status != 'collapse') {
        //sumPrice.viewModel.toBuy = true;
        ticketType.viewModel.ticketTips = '';
        ticketType.close();
        Flowline.openNext(ticketType);
    }

};
ticketType.viewModel.open = function () {
    ticketType.viewModel.status = '';
    //sumPrice.viewModel.toBuy = false;
};
tourDate.bindData(bizDataFactory.getData(new DataAdapter({
    flag: function (val, data) {
        if (data.date_rule.need_tour_date == 0) {
            Flowline.remove(tourDate);
            return -1;
        }
        else return 0;
    },

    openDatePicker: function () {
        return function () {
            order.calendar.open(orderModel.tour_date);
            tourDate.viewModel.status = '';
            Flowline.others(tourDate, closeSection);
            sumPrice.viewModel.toBuy = false;
        }
    }

})));

specialCode.bindData(bizDataFactory.getData({
    flag: function (val, data) {
        if (core.isEmpty(data.special_codes)) {
            Flowline.remove(specialCode);
            return -1;
        }
        else return 0;
    }
}));

departure.bindData(bizDataFactory.getData({
        flag: function (val, data) {
            if (core.isEmpty(data.departure_rule)) {
                console.log(11212121, data.departure_rule)
                Flowline.remove(departure);
                return -1;
            }
            else {
                return 0
            }
        }
    })).then(function () {
    console.log(12212121);
    Flowline.start();
});

var x = order.bindData(bizDataFactory.getData(new DataAdapter({

        'productId': 'sale_rule.product_id',
        selectOption: function (val, data) {
            var lastSelectEl1 = null, lastSelectEl2 = null;

            return function (evt, index, type) {


                if (type == 'special_codes') {
                    if (lastSelectEl1) {

                        lastSelectEl1.className = lastSelectEl1.className.replace(' selected', '');
                    }
                    evt.target.className += ' selected';
                    lastSelectEl1 = evt.target;
                    specialCode.viewModel.status = 'collapse';
                    specialCode.viewModel.selectedSpecial = data['special_codes'][index].cn_name;

                    var spc = data.special_codes[index].special_code, specialPrices = [];
                    var pricePlan = getPricePlan(data.price_plan, orderModel.tour_date);
                    for (var i = 0; i < Math.min(2, data.ticket_types.length); i++) {
                        var prices = pricePlan[spc][data.ticket_types[i].ticket_type.ticket_id][1];

                        specialPrices.push({
                            ticketType: data.ticket_types[i].ticket_type.cn_name,
                            price: prices.price,
                            marketPrice: prices.orig_price
                        });
                    }
                    specialCode.viewModel.specialPrices = specialPrices;
                    orderModel.special_code = spc;
                    ticketType.viewModel.ticketTypes = genTicketTypes(data, orderModel.tour_date, orderModel.special_code);
                    specialCode.fulfilled = 1;
                    Flowline.openNext(specialCode);
                }
                else {
                    if (lastSelectEl2) {

                        lastSelectEl2.className = lastSelectEl2.className.replace(' selected', '');
                    }
                    evt.target.className += ' selected';
                    lastSelectEl2 = evt.target;
                    departure.viewModel.selectedDeparture = departure.viewModel.departures[index].time.slice(0, -3) + ' ' + departure.viewModel.departures[index].departure_point;
                    departure.viewModel.status = 'collapse';
                    orderModel.departure_time = departure.viewModel.departures[index].time;
                    orderModel.departure_code = departure.viewModel.departures[index].code;
                    departure.fulfilled = 1;
                    Flowline.openNext(departure);
                }
            }
        },
        openSection: function (val, data) {
            return function (type) {
                window[type].viewModel.status = '';
                Flowline.others(window[type], closeSection);
                if (type !== 'ticketType') {
                    sumPrice.viewModel.toBuy = false;
                }
                order.calendar.close();


            }
        },
        closeSection: function () {
            return function (type) {
                var vm = window[type];
                if (vm.viewModel.status != 'collapse') {
                    vm.close();
                    Flowline.openNext(vm);
                }
            }
        },
        onHover: function (val, data) {


            return function (evt) {
                var idx = evt.target.getAttribute('data-index');
                var desc = data.special_codes[idx].description;
                if (desc || true) {
                    specialTips.viewModel.visible = 'block';
                    specialTips.viewModel.top = (105 + idx * 70 - 73);
                    specialTips.viewModel.name = data.special_codes[idx].cn_name;
                    specialTips.viewModel.description = desc;
                    var spc = data.special_codes[idx].special_code, specialPrices = [];
                    var pricePlan = getPricePlan(data.price_plan, orderModel.tour_date);
                    for (var i = 0; i < Math.min(2, data.ticket_types.length); i++) {
                        var prices = pricePlan[spc][data.ticket_types[i].ticket_type.ticket_id][1];

                        specialPrices.push({
                            ticketType: data.ticket_types[i].ticket_type.cn_name,
                            price: prices.price,
                            marketPrice: prices.orig_price
                        });
                    }
                    specialTips.viewModel.specialPrices = specialPrices
                }
            }
        },
        onLeave: function () {
            return function (evt) {
                specialTips.viewModel.visible = 'none';
            }
        }

    }))).then(function (data) {


    function dealIndependent(idx) {
        ticketType.viewModel.ticketTypes[idx].quantity--;
        var sum = sumNum();
        if (data.ticket_types.length > 2) {
            for (var i = 0; i < ticketType.viewModel.ticketTypes.length; i++) {
                var ticket = ticketType.viewModel.ticketTypes[i];
                if (data.ticket_types[i].is_independent == 0 && ticket.quantity > 0 && sum == ticket.quantity) {
                    ticketType.viewModel.ticketTips = '不能单独购买' + data.ticket_types[i].ticket_type.cn_name;
                    clearTimeout(_tipTimer);
                    _tipTimer = setTimeout(function () {
                        ticketType.viewModel.ticketTips = '';
                    }, 4000);
                    ticketType.viewModel.ticketTypes[idx].quantity++;
                    return false;
                }
            }
        }
        return true;
    }

    ticketType.viewModel.counterAdd = function (idx) {
        console.log(sumNum(), data.sale_rule.max_num);
        if (sumNum() == data.sale_rule.max_num) {
            ticketType.viewModel.ticketTips = '您最多可以订' + data.sale_rule.max_num + '张票';
            clearTimeout(_tipTimer);
            _tipTimer = setTimeout(function () {
                ticketType.viewModel.ticketTips = '';
            }, 4000);
        }
        else {
            ticketType.viewModel.ticketTypes[idx].quantity++;
            orderModel.quantity[ticketType.viewModel.ticketTypes[idx].ticketId] = ticketType.viewModel.ticketTypes[idx].quantity;
            calcSumPrice();
        }

    };
    ticketType.viewModel.counterReduce = function (idx) {
        var ticket = ticketType.viewModel.ticketTypes[idx];
        var sum = sumNum();
        if (ticket.quantity == 0) {

        } else if (sum == data.sale_rule.min_num) {
            clearTimeout(_tipTimer);
            ticketType.viewModel.ticketTips = '请至少预订' + data.sale_rule.min_num + '张票';
            _tipTimer = setTimeout(function () {
                ticketType.viewModel.ticketTips = '';
            }, 4000);
        }
        else if (ticket.quantity == data.ticket_types[idx].min_num) {
            clearTimeout(_tipTimer);
            ticketType.viewModel.ticketTips = '请至少预订' + data.sale_rule.min_num + '张' + data.ticket_types[idx].ticket_type.cn_name + '票';
            _tipTimer = setTimeout(function () {
                ticketType.viewModel.ticketTips = '';
            }, 4000);
        }

        else {
            if (dealIndependent(idx)) {
                orderModel.quantity[ticketType.viewModel.ticketTypes[idx].ticketId] = ticketType.viewModel.ticketTypes[idx].quantity;
                calcSumPrice();
            }
        }

    };
    if (data.date_rule.need_tour_date == 1) {
        var limits = getTourDateLimits(data.date_rule);
        console.log(limits, data);
        order.calendar = new XCalendar(document.getElementById('tour_date'), {
            width: 320,
            min: limits.start,
            max: limits.end,
            disable: limits.disable,
            onSelect: function (date) {
                tourDate.viewModel.selectedDate = DateUtil.format(date, 'yyyy/mm/dd');
                orderModel.tour_date = DateUtil.format(date);
                tourDate.viewModel.status = 'collapse';
                departure.viewModel.departures = genDepartures(data, orderModel.tour_date);
                if (specialCode.viewModel.flag == -1 || orderModel.special_code) {
                    ticketType.viewModel.ticketTypes = genTicketTypes(data, orderModel.tour_date, orderModel.special_code);
                }
                tourDate.fulfilled = 1;
                Flowline.openNext(tourDate, openSection);
            }
        });
    }
});


function genTicketTypes(data, dateStr, specialCode) {
    specialCode = specialCode || 0;
    var pricePlan = getPricePlan(data.price_plan, dateStr);
    var ticketTypes = [];
    for (var i = 0; i < data.ticket_types.length; i++) {
        var ticketType = data.ticket_types[i];
        var ticket = {name: ticketType.ticket_type.cn_name, age_range: ticketType.age_range, ticketId: ticketType.ticket_id};
        ticket.price = pricePlan[specialCode][ticketType.ticket_id][1].price;
        ticket.orig_price = pricePlan[specialCode][ticketType.ticket_id][1].orig_price;

        ticketTypes.push(ticket);
        ticket.quantity = i == 0 ? Math.max(1, ticketType.min_num) : 0;
        orderModel.quantity[ticketType.ticket_id] = ticket.quantity;
    }
    return ticketTypes;
}

function genDepartures(data, dateStr) {
    var departures = [];
    for (i = 0; i < data.departure_rule.length; i++) {
        var dep = data.departure_rule[i];
        if (dep.valid_region == 0 || (dateStr >= dep.from_date && dateStr <= dep.to_date)) {
            if (dep.additional_limit) {
                var limits = dep.additional_limit.split(';');
                for (var j = 0; j < limits.length; j++) {
                    var limit = limits[j];
                    if (limit.indexOf('周') != -1) {
                        if (DateUtil.parse(dateStr).getDay() == (+limit.slice(1) % 7)) {
                            departures.push({time: dep.time, showTime: dep.time.slice(0, -3), departure_point: dep.departure.departure_point, code: dep.departure.departure_code});
                        }
                        else if (dateStr == limit) {
                            departures.push({time: dep.time, showTime: dep.time.slice(0, -3), departure_point: dep.departure.departure_point, code: dep.departure.departure_code});
                        }
                    }
                }
            }
            else {
                departures.push({time: dep.time, showTime: dep.time.slice(0, -3), departure_point: dep.departure.departure_point, code: dep.departure.departure_code});
            }
        }
    }
    return departures;

}
function getTourDateLimits(date_rule) {
    if (date_rule.need_tour_date == 0) {
        return;
    }
    var mcloses = date_rule.close_dates.split(';');
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
function getPricePlan(pricePlan, dateStr) {
    for (var i = 0; i < pricePlan.length; i++) {
        priceSection = pricePlan[i];

        if (priceSection.valid_region == '0' || dateStr >= priceSection.from_date && dateStr <= priceSection.to_date) {
            return priceSection.price_map;
        }
    }
    return null;
}
function scrollTo(y) {
    $('body,html').animate({ scrollTop: y }, 500);
}
$(function () {

})
