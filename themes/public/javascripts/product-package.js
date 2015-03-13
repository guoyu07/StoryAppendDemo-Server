var packageModel = avalon.define("productPackage", function(vm) {
    vm.productData = {};//All Product Information
    vm.basic_info = {};//Basic Product Information
    vm.bundle_info = [];//Bundle Product Information
    vm.bundle = {};
    vm.basic_price = {};//Basic Product Price
    vm.hotel_info = [];//Hotel Special Code Map
    vm.tab_content = [];//tab almost includes all the bundle, "but bundle[1]"
    vm.selected_hotel = {hotel_item : {}};//Selected Hotel Information
    vm.sendData = {quantities : {}};
    vm.mapToggleFlag = [];//Hotel Map Toggle Flag
    vm.mapLoadFlag = [];//Hotel Map Load Flag
    vm.bundle_detail = {};
    vm.selectedHotelIndex = 0;
    vm.orig_total = 0;
    vm.rules = {};
    vm.how_it_works = {};
    vm.service_include = [];
    vm.pick_landinfo_groups = {};
    vm.quantity_tips = '';
    vm.quantity_error = '';
    vm.minAge=0;
    vm.maxAge=18;
    vm.bundle_one_location = {
        map             : '',
        current_markers : ''
    };
    vm.selector={
        plan:false,
        adult_num:false,
        child_num:false,
        child_age:[false,false,false,false,false,false,false,false,false,false,false],
        show:function(name,$event,index){

            if(typeof name=='string'){
                name!='plan'&&(packageModel.selector.plan=false);
                name!='adult_num'&&(packageModel.selector.adult_num=false);
                name!='child_num'&&(packageModel.selector.child_num=false);
                for(var i=0;i<packageModel.child_list.length;i++){
                    packageModel.child_list[i].show=false;
                }
                    packageModel.selector[name] = !packageModel.selector[name];
            }
            else {
                packageModel.selector.plan=false;
                packageModel.selector.adult_num=false;
                packageModel.selector.child_num=false;
                name.show=!name.show;
            }
            $event.stopPropagation();
        },
        select:function(name,root,value,onChange,$event,cur){
            if(name=='age'){
                cur.show=false;
            }
            else{
                packageModel.selector[name]=false;
            }
            root=root||packageModel;
            root[name]=value;
            onChange&&onChange.call(this);
            $event.stopPropagation();
        }
    }
    vm.hotel_list = [];
    vm.child_list = [];
    vm.max_num = [];
    vm.age_list = [];
    vm.adult_num = 0;
    vm.child_num = 0;
    vm.checkComplete = function() {
        if(checkFormCompleted()) {
            packageModel.selected_hotel.button_label = "确定";
            packageModel.selected_hotel.available = true;
        }
        else {
            packageModel.selected_hotel.button_label = "请完成出行信息";
            packageModel.selected_hotel.available = false;
        }
    };

    vm.selectQuantity = function(type) {
        checkQuantity.call(this,packageModel);
    };
    vm.renderCallback = function() {
        noticeModel.renderCallback();
    };

    vm.initTabComponents = function() {
        // init TAB 组件
        var HINavObject = [];
        var $hi_nav = $('.hi-nav');
        if($hi_nav) {
            $.each($hi_nav, function(i, val) {
                HINavObject.push(new HINav($hi_nav[i]));
            });
        }
        $('img').on('load', function() {
            if(HINavObject.length > 0) {
                for(var i = 0; i < HINavObject.length; i++) {
                    HINavObject[i].refreshToTop();
                }
                ;
            }
        });
    };

    vm.initDropMenuComponents = function(action) {
        //initialize dropmenu
        if(action == "index") {
            var dropmenu = $("#hitour_dropmenu");
            if(dropmenu) {
                var hiDropmenu = new Dropmenu(dropmenu);
                hiDropmenu.init();
            }
        }
    };

    vm.initSlider = function() {
        //initialize carousel
        new HiCarousel({
            dom  : '#base_carousel',
            type : 'fade'
        });

        tabScrollListener();
    }

    vm.priceChange = function(index) {

        selectedIndexChange(index);
        $(this).parent(".menu").slideToggle();
    };

    vm.changeSelectedIndex = function(index) {

        if(index != gl_selectedSpecialIndex) {
            packageModel.hotel_list[gl_selectedSpecialIndex].active = false;
            gl_selectedSpecialIndex = index;
            /* packageModel.selectedHotelIndex = index;//set selected index of bundle_info
             packageModel.basic_price.price = packageModel.hotel_info[index].price;
             packageModel.basic_price.orig_price = packageModel.hotel_info[index].orig_price;
             packageModel.basic_price.hotel_name = packageModel.hotel_info[index].cn_name;
             packageModel.selected_hotel = bindDataToSelectedHotel(packageModel.bundle_info[0]);*/
            packageModel.hotel_list[index].active = true;
            packageModel.selected_hotel = bindDataToSelectedHotel(packageModel.bundle.hotel, packageModel.selected_hotel);
            checkSelectedHotelInfo(packageModel.selected_hotel);
            /*var limits = getTourDateLimits(packageModel.bundle.hotel.products[packageModel.selectedHotelIndex].date_rule);
             gl_calendar.init({
             min: limits.start,
             max: limits.end,
             disable: limits.disable});*/
        }
    };

    var isOpen = false;
    vm.toggleCalendar = function() {
        if(!isOpen) {
            gl_calendar.open();
            isOpen = true;
        }
        else {
            isOpen = false;
        }
    };//must be deleted

    vm.toggleCalendar = function() {
        if(!isOpen) {
            gl_calendar.open();
            isOpen = true;
        }
        else {
            isOpen = false;
        }
    };//must be deleted

    vm.calculatePercent = function(number, cardinal) {
        number = (number == 0 ? cardinal : number);
        return (parseFloat(number, 10) / cardinal).toFixed(2) * 100;
    };//calculate the star length percent

    vm.showBuyDialog = function() {
        $(".buy-dialog-mask").addClass("show-buy-dialog");
        //    packageModel.selected_hotel = bindDataToSelectedHotel(packageModel.bundle_info[0]);
        packageModel.selected_hotel = bindDataToSelectedHotel(packageModel.bundle.hotel);
        var list = [];
        var select_mapping_product_id = packageModel.hotel_info[gl_selectedSpecialIndex].mapping_product_id;
        packageModel.hotel_info.$model.forEach(function(e, i) {
            list.push({
                active               : i == gl_selectedSpecialIndex,
                disable              : i != gl_selectedSpecialIndex &&
                                       e.mapping_product_id != select_mapping_product_id,
                cn_name              : e.cn_name,
                unit_price           : e.price,
                special_code         : e.special_code,
                mapping_product_id   : e.mapping_product_id,
                mapping_special_code : e.mapping_special_code
            })
        });

        console.log(list);
        packageModel.hotel_list = list;


        $("body").addClass("no-scroll");
    };//show buy dialog

    vm.closeBuyDialog = function() {
        $(".buy-dialog-mask").removeClass("show-buy-dialog");
        $("body").removeClass("no-scroll");
    };//hide buy dialog

    vm.setHotelMap = function(index, name, location) {
        //locate the node
        var target = $(this).parent().prev(".hotel-map");
        if(packageModel.mapToggleFlag[index]) {
            target.removeClass("show-map");
            $(this).html("显示地图");
        } else {
            if(!packageModel.mapLoadFlag[index]) {
                //initialize the map data. [hotel name, latitude, longitude, z-index]
                var viewPoints = [];
                var hotel = [];
                location = location.split(",");
                hotel.push(name, location[0], location[1], 1);
                viewPoints.push(hotel);

                //initialize the map entity
                var map = HImap.init(target.attr("id"));
                current_markers = HImap.drawMarkers(map, viewPoints);
                HImap.suitBounds(map, viewPoints);

                packageModel.mapLoadFlag[index] = !packageModel.mapLoadFlag[index];
            }
            //make the node show up
            target.addClass("show-map");
            $(this).html("关闭地图");
        }
        packageModel.mapToggleFlag[index] = !packageModel.mapToggleFlag[index];
    };

    // 弹出侧边窗，显示绑定商品详细信息
    vm.showBundleDetail = function(parent_id, product_id, product_type, e) {
        e.stopPropagation();
        var $bundle_detail = $('#bundle-detail')
        if(product_type == 3) $bundle_detail.addClass('type_3');
        else $bundle_detail.removeClass('type_3');

        $('body').addClass('no-scroll');
        $bundle_detail.addClass('show');
        setTimeout(function() {
            $('#full-mask').show();
            $bundle_detail.find('.spinner').show();
        }, 300)

        $.ajax({
            url      : $request_urls.bindingProduct + '?parent_id=' + parent_id + '&product_id=' + product_id,
            dataType : 'json',
            type     : 'get',
            success  : function(data) {

                var bundle = data.data;
                bundle.comments = {
                    total_score      : bundle.comments_state.avg_hitour_service_level * 0.7 +
                                       bundle.comments_state.avg_supplier_service_level * 0.3,
                    total_percent    : (bundle.comments_state.avg_hitour_service_level * 0.7 +
                                        bundle.comments_state.avg_supplier_service_level * 0.3) * 20,
                    hitour_score     : bundle.comments_state.avg_hitour_service_level,
                    hitour_percent   : bundle.comments_state.avg_hitour_service_level * 20,
                    supplier_score   : bundle.comments_state.avg_supplier_service_level,
                    supplier_percent : bundle.comments_state.avg_supplier_service_level * 20
                };
                bundle.tour_plan = bundle.tour_plan[0];
                vm.bundle_detail = bundle;

                if(bundle.hotel) {
                    if(vm.bundle_one_location.map == '') {
                        vm.bundle_one_location.map = HImap.init('bundle-map-location');
                        google.maps.event.clearListeners(vm.bundle_one_location.map, 'mouseover');
                    }
                    changeLocationMapMarkers(vm.bundle_one_location.map, bundle.description.name, bundle.hotel.latlng);
                }

                setTimeout(function() {
                    $bundle_detail.find('.modal-loading-mask').hide();
                }, 500);

            },
            error    : function(data) {
                console.log(data.msg);
            }
        });
    }

    vm.closeMask = function() {
        var $bundle_detail = $('#bundle-detail')
        $bundle_detail.removeClass('show');
        $('body').removeClass('no-scroll');
        setTimeout(function() {
            $bundle_detail.find('.modal-loading-mask').show();
            $bundle_detail.find('.spinner').hide();
            $('#full-mask').hide();
            $bundle_detail.scrollTop(0);
        }, 300);
    }

    vm.goBuy = function() {
        console.log(11111);
        $.ajax({
            url      : $request_urls.addCart,
            dataType : "json",
            type     : "post",
            data     : assembleSendData(),
            success  : function(res) {
                if(res.code == 200) {
                    location.href = res.data.checkout_url;
                }
                else {
                    alert(res.msg);
                }
            },
            error    : function(res) {
                alert('后台异常，请稍后再试，或联系客服');
            }
        });
    };
});
function checkQuantity(vm) {
    var max = vm.max_num.length - 1, sum = (vm.child_num | 0) + (vm.adult_num | 0);
    console.log(vm.child_num, '----', vm.adult_num)
    if(sum > max) {
        vm.quantity_error = '此房型最多只能住' + max + '人，请减少人数或增加房间数';
        $(this).parents('.select-holder').addClass('error');
        packageModel.selected_hotel.button_label = "请完成出行信息";
        packageModel.selected_hotel.available = false;
        //vm[type]=vm[type]-(parseInt(vm.child_num)+parseInt(vm.adult_num)-max);
        return;
    }
    else {
        $('#adult_num,#child_num').removeClass('error');
        vm.quantity_error = '';
    }
    if(sum > packageModel.selected_hotel.capacity * packageModel.selected_hotel.plan) {
        vm.quantity_tips = '*您的入住人数已经超过该房型的标准入住人数，请参看左边的加床政策，可能产生的费用，由您入住时在前台缴纳。'
    }
    else {
        vm.quantity_tips = '';
    }
    var dist = vm.child_num - vm.child_list.length;
    for(var i = 0; i < dist; i++) {
        vm.child_list.push({age : '',show:false});
    }
    for(i = 0; i > dist; i--) {
        vm.child_list.pop();
    }
    if(checkFormCompleted()) {
        packageModel.selected_hotel.button_label = "确定";
        packageModel.selected_hotel.available = true;
    }
    else {
        packageModel.selected_hotel.button_label = "请完成出行信息";
        packageModel.selected_hotel.available = false;
    }
}
function calcHotelPrice(date, specialCode) {
    var pricePlan = packageModel.productData.$model.price_plan;
    specialCode=findComboSpecialCode(specialCode)||0;
    for(var i = 0; i < pricePlan.length; i++) {
        var p = pricePlan[i];
        if(p.valid_region == '0' || (date >= p.from_date && date <= p.to_date)) {
            return (p.price_map[specialCode][1] || p.price_map[specialCode][99])['1']['price'];
        }
    }
}

function filterHotelList(date) {
    var dateStr = date.format();
    for(var i = 0; i < packageModel.hotel_list.length; i++) {
        var hotel = gl_HotelMapping[packageModel.hotel_list[i].mapping_product_id];
        var limits = getTourDateLimits(hotel.date_rule);
        packageModel.hotel_list[i].unit_price = calcHotelPrice(date.format(), packageModel.hotel_list[i].special_code);
        if(dateStr >= limits.start.format() && dateStr <= limits.end.format() && !limits.disable(date, dateStr)) {
            packageModel.hotel_list[i].disable = false;
        }
        else {
            packageModel.hotel_list[i].disable = true;
        }
    }


}

var assembleSendData = function() {
    //checkout pages need the following data
    //{id, tour date, special code, departure time and code, activity id, tour members, package number, ticket number}
    var result = {};
    result.product_id = packageModel.productData.product_id;
    result.tour_date = packageModel.selected_hotel.date.format('yyyy-mm-dd');
    result.special_code = findComboSpecialCode(packageModel.selected_hotel.special_code);
    result.departure_time = "";
    result.departure_code = "";
    result.adult_num = +packageModel.adult_num;
    result.child_num = +packageModel.child_num;
    for(var i = 0; i < packageModel.child_list.length; i++) {
        result['child_list[' + i + '][age]'] = packageModel.child_list[i].age;
    }
    if(packageModel.basic_info.activity_info.activity_id)
        result.activity_id = packageModel.basic_info.activity_info.activity_id;
    else
        result.activity_id = "";

    result.pax_num = (packageModel.adult_num | 0) + (packageModel.child_num | 0);

    result.quantities = {};
    result.quantities[packageModel.selected_hotel.ticket_id] = +packageModel.selected_hotel.plan;
    for(var k in result.quantities) {
        result['quantities[' + k + ']'] = result.quantities[k];
    }
    delete(result.quantities);
    return result;
};

var selectPlan = function() {
    var selector_elem = document.getElementById("plan_selector");
    var selected_hotel = packageModel.selected_hotel;
    packageModel.max_num.clear();

    for(var i = 0; i <= selected_hotel.plan * selected_hotel.max_capacity; i++) {
        packageModel.max_num.push(i);
    }
    if(packageModel.adult_num > selected_hotel.plan * selected_hotel.max_capacity) {
        packageModel.adult_num = selected_hotel.plan * selected_hotel.max_capacity;
    }
    if(packageModel.child_num > selected_hotel.plan * selected_hotel.max_capacity) {
        packageModel.child_num = selected_hotel.plan * selected_hotel.max_capacity;
    }
    checkSelectedHotelInfo(selected_hotel);
    //packageModel.adult_num = selected_hotel.plan * selected_hotel.capacity;
    checkQuantity(packageModel);
};

var selectMember = function() {
    var selector_elem = document.getElementById("member_selector");
    packageModel.selected_hotel.member = selector_elem.options[selector_elem.selectedIndex].text;
}

var selectedIndexChange = function(index) {
    var pid = packageModel.hotel_info[index].mapping_product_id;
    packageModel.bundle.hotel.products.forEach(function(e, i) {
        if(e.product_id == pid) {
            packageModel.selectedHotelIndex = i;
            return false;
        }
    });
    gl_selectedSpecialIndex = index;
    //packageModel.hotel_info[index].check=true;
    //packageModel.selectedHotelIndex = index;//set selected index of bundle_info
    packageModel.basic_price.price = packageModel.hotel_info[index].price;
    packageModel.basic_price.orig_price = packageModel.hotel_info[index].orig_price;
    packageModel.basic_price.hotel_name = packageModel.hotel_info[index].cn_name;
};
var gl_selectedSpecialIndex = 0;
var initSelectedHotel = function(model) {

}

function getHotelRomType(hotel, specialCode) {
    var roomType;
    hotel.hotel.room_types.forEach(function(e) {
        if(e.special_code == specialCode) {
            roomType = e;
            return false;
        }
    });
    return roomType;
}

var bindDataToSelectedHotel = function(model, oldSelect) {
    //model is the "packageModel.bundle_info[0]"
    var hotelinfo = packageModel.hotel_info[gl_selectedSpecialIndex];
    var hotel = gl_HotelMapping[hotelinfo.mapping_product_id];

    var result = {};
    var min_num = parseInt(hotel.sale_rule.min_num);
    var max_num = parseInt(hotel.sale_rule.max_num);

    core.forEach(packageModel.productData.ticket_types, function(key, value) {
        result.ticket_id = key;
        return false;
    });
    result.hotel_item = model.products[packageModel.selectedHotelIndex].$model;
    result.hotel_item.room_type = getHotelRomType(hotel, hotelinfo.mapping_special_code);


    result.special_code = hotelinfo.special_code;
    result.ticket_type = "";

    result.total_price = "";
    result.unit_price = "";
    result.date = oldSelect && oldSelect.date || "";
    result.date_str='';
    result.button_label = "请完成出行信息";
    result.plans_range = [];
    result.plan = oldSelect && oldSelect.plan || min_num;
    result.members_range = [];
    result.member = oldSelect && oldSelect.member || 1;
    result.capacity = result.hotel_item.room_type.capacity;
    result.max_capacity = result.hotel_item.room_type.max_capacity;
    result.date_limit = getTourDateLimits(hotel.date_rule);
    result.available = false;
    packageModel.max_num = [];
    for(var i = min_num; i <= result.max_capacity; i++) {
        result.plans_range.push(i);
    }
    for(i = 0; i <= result.plan * result.max_capacity; i++) {
        packageModel.max_num.push(i);
    }
    //update the data of the selected hotel's calendar
    updateCalendar(hotel, result.date_limit);
    //packageModel.adult_num = result.plan * result.capacity;
    return result;
};

var updateCalendar = function(model, limits) {
    window.x = limits;
    gl_calendar.init({min : limits.start, max : limits.end, disable : limits.disable});
    if(model.rules && model.rules.sale_desc) {
        $("#buy_rule").html(model.rules.sale_desc);
    }
};
function checkFormCompleted() {
    if(packageModel.selected_hotel.member && packageModel.selected_hotel.total_price &&
       parseInt(packageModel.adult_num) + parseInt(packageModel.child_num) > 0) {
        for(var i = 0; i < packageModel.child_list.length; i++) {
            if(packageModel.child_list[i].age == '') {
                return false;
            }
        }
        return true;
    }
    return false;
}
var checkSelectedHotelInfo = function(model) {
    //model is the "selected_hotel"
    if(model.date && model.plan) {
        model.unit_price = calcHotelPrice(model.date.format(), packageModel.hotel_list[gl_selectedSpecialIndex].special_code);
        model.total_price = model.unit_price * model.plan;

        //set the real unit price for each special code
        packageModel.hotel_info[packageModel.selectedHotelIndex].unit_price = model.unit_price;
        if(checkFormCompleted()) {
            model.button_label = "确定";
            model.available = true;
        }
        else {
            model.button_label = "请完成出行信息";
            model.available = false;
        }
    }
};

//Bind Price Data to the Basic Product Price
var bindDataToSelectedBasicPrice = function(model) {
    //model is "hotel_info", which is already sorted
    var result = {};
    result.price = model[0].price;
    result.orig_price = model[0].orig_price;
    result.hotel_name = model[0].cn_name;
    result.product_id = model[0].mapping_product_id;

    return result;
};

//Bind Data To the Basic Product Information
var bindDataToBasicInfo = function(model) {
    //model is "productData"
    var result = {};
    result.name = model.description.name;
    result.total = model.comment_stat.total;
    result.avg_hitour_service_level = model.comment_stat.avg_hitour_service_level;
    result.gallery = model.images.sliders.$model;
    result.price = model.show_prices.price;
    result.orig_price = model.show_prices.orig_price;
    result.buy_label = model.buy_label;
    result.available = model.available;
    result.is_favorite = model.is_favorite;
    result.activity_info = model.activity_info;
    result.service_included = [];

    var service = {};
    service.service_title = model.description.package_service_title;
    service.service_content = model.description.package_service;
    service.service_id = 0;
    result.service_included.push(service);

    var bundle = {};
    bundle.service_title = model.description.package_gift_title;
    bundle.service_content = model.description.package_gift;
    bundle.service_id = 1;
    result.service_included.push(bundle);

    var recommend = {};
    recommend.service_title = model.description.package_recommend_title;
    recommend.service_content = model.description.package_recommend;
    recommend.service_id = 2;
    result.service_included.push(recommend);

    return result;
};

var sortBundleInfoByPrice = function(model) {

    //  var bundle_hotel = packageModel.bundle_info[0].products;
    var bundle_hotel = packageModel.bundle.hotel.products;
    for(var i = 0; i < bundle_hotel.length; i++) {
        for(var j = 0; j < model.length; j++) {
            if(bundle_hotel[i].product_id == model[j].mapping_product_id) {
                bundle_hotel[i].basic_price = model[j].price;
            }
        }
    }
    bundle_hotel.sort(function(a, b) {
        return a.basic_price - b.basic_price;
    });

    bundle_hotel[0].basic_price_label = "+0";
    for(var i = 1; i < bundle_hotel.length; i++) {
        bundle_hotel[i].basic_price_label = "+" + (bundle_hotel[i].basic_price - bundle_hotel[0].basic_price);
    }
};

//Bind Data To the Hotel Special Codes
var bindDataToHotelInfo = function(model) {
    var specialCodes = [];
    var today = new Date();
    var result = {};

    //get special codes
    for(var i = 0; i < model.special_codes.length; i++) {
        var combo_special_code=findComboSpecialCode(model.special_codes[i].special_code);
        model.special_codes[i].combo_special_code=combo_special_code;
        specialCodes.push(combo_special_code);
    }

    var validPricePlan = getPricePlanByDate(today.format(), model.price_plan);

    result = setCodesPriceMap(validPricePlan, specialCodes);

    for(i = 0; i < result.length; i++) {
        for(var j = 0; j < model.special_codes.length; j++) {
            if(result[i].special_code == model.special_codes[j].combo_special_code) {
                result[i].cn_name = model.special_codes[j].cn_name;
                result[i].en_name = model.special_codes[j].en_name;
                result[i].mapping_product_id = model.special_codes[j].mapping_product_id;
                result[i].mapping_special_code = model.special_codes[j].mapping_special_code;
            }
        }
        result[i].unit_price = "";
    }//bind the necessary data to the hotel list

    result.sort(function(a, b) {
        return a.price - b.price;
    });
    sortBundleInfoByPrice(result);//link price to the hotel, then sort it
    setTimeout(function() {
        var dropmenu = $("#hitour_dropmenu");
        if(dropmenu) {
            var hiDropmenu = new Dropmenu(dropmenu);
            hiDropmenu.init();
        }
    }, 600);
    var pid = result[0].mapping_product_id;
    packageModel.bundle.hotel.products.forEach(function(hotel, i) {
        if(hotel.product_id == pid) {
            packageModel.selectedHotelIndex = i;
        }
        gl_HotelMapping[hotel.product_id] = hotel;
    });

    return result;

};
var gl_HotelMapping = {};
var setCodesPriceMap = function(validPricePlan, codes) {
    var codes_price_map = [];
    for(var i = 0; i < codes.length; i++) {
        var code=codes[i];
        if(validPricePlan.price_map.hasOwnProperty(code)) {
            codes_price_map.push(getMinPriceByTicketType(validPricePlan.price_map[code]));
        } else {
            continue;
        }
    }
    return codes_price_map;
};

var initNewCalendar = function(model) {

    if(model.rules && model.rules.sale_desc) {
        var sale_desc = '<div id="buy_rule" class="buy-rule">' + model.rules.sale_desc + '</div>'
    } else {
        sale_desc = '';
    }

    var limits = getTourDateLimits(model.date_rule);

    var calendar_elem = $("#tour_date");

    if(calendar_elem) {
        var calendar = new XCalendar($('#tour_date'), {
            width    : 245,
            min      : limits.start,
            max      : limits.end,
            disable  : limits.disable,
            partials : {
                'append' : '<div class="date-legend"><div class="legend-item" style="margin-right: 30px;width: 120px;"><div class="date-enable"></div>可选日期</div><div class="legend-item"><div class="date-disable"></div>关闭时间</div>' +
                           sale_desc + '</div><div class="calendar-arrow"></div>'
            },
            onClose  : function() {
                //      tourDate.close();
            },
            onSelect : function(date) {
                packageModel.selected_hotel.date = date;
                packageModel.selected_hotel.date_str=date.format();
                filterHotelList(date);
                checkSelectedHotelInfo(packageModel.selected_hotel);
            }
        });
        return calendar;
    }

};

var getTourDateLimits = function(date_rule) {
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
};

var getRealPriceByPlanAndTicket = function(onePriceMap, plan, code) {
    var ticket_types = [99, 1, 2];
    if(onePriceMap.price_map.hasOwnProperty(code)) {
        for(var i = 0; i < ticket_types.length; i++) {
            if(onePriceMap.price_map[code].hasOwnProperty(ticket_types[i])) {
                var price_change = onePriceMap.price_map[code][ticket_types[i]];
                for(var j = plan; j > 0; j--) {
                    if(price_change.hasOwnProperty(j)) {
                        //get the ticket type
                        packageModel.selected_hotel.ticket_type = ticket_types[i];
                        if(packageModel.productData.ticket_types.hasOwnProperty(ticket_types[i])) {
                            packageModel.selected_hotel.ticket_id = packageModel.productData.ticket_types[ticket_types[i]].ticket_id;
                        }
                        return price_change[j].price;
                    }
                }
            }
        }
    }
};

var getMinPriceByTicketType = function(onePriceMap) {
    var minPrice = 0;
    var ticket_types = [99, 1, 2];
    var result_price = {};
    var price_change = {};

    for(var j = 0; j < ticket_types.length; j++) {
        if(onePriceMap.hasOwnProperty(ticket_types[j])) {
            price_change = onePriceMap[ticket_types[j]].$model;
            for(var i in price_change) {

                if(minPrice > price_change[i]['price'] || minPrice == 0) {
                    minPrice = price_change[i]['price'];
                    result_price = price_change[i];
                }
            }
        }
    }
    return result_price;
};

var getPricePlanByDate = function(dateString, pricePlan) {
    var earliest_date = 0;
    for(var i = 0; i < pricePlan.length; i++) {
        if(pricePlan[i].valid_region == "0")
            return pricePlan[i];
        if(dateString >= pricePlan[i].from_date && dateString <= pricePlan[i].to_date)
            return pricePlan[i];
        if(pricePlan[i].from_date < pricePlan[earliest_date].from_date)
            earliest_date = i;
    }
    return pricePlan[earliest_date];
};

var bindDataToBundle = function(model) {
    var result = model;
    //model is the "productData.bundle"

    //initialize the hotel information - "bundle[0]"
    for(var j = 0; j < result.hotel.products.length; j++) {
        var product = result.hotel.products[j];
        product = setServiceIcon(product);
        product.hotel.room_types = setRoomPrice(product.hotel.room_types, product.price_plan_items);
        //set icons by facility types
        packageModel.mapToggleFlag[j] = false;//initialize Hotel Map Flags;
        packageModel.mapLoadFlag[j] = false;//initialize Hotel Map Load Flags;

        product.basic_price_label = "";
        product.basic_price = "";//initialize Hotel Bundle Price

        result.hotel.hotelPriceMap = packageModel.hotel_info;
    }

    //add the total original price value to the "bundle[1]"
    var total_orig_price = 0;
    if(!result.complimentary) {
        result.complimentary = {products : []};
    }
    if(!result.optional) {
        result.optional = {products : []};
    }
    var minAge=18,maxAge=0;
    for(var m = 0; m < result.complimentary.products.length; m++) {
        //included free products
        total_orig_price = total_orig_price + parseInt(result.complimentary.products[m].show_prices.price);
        if(result.complimentary.products[m].ticket_types[3]){
            var ageRange=result.complimentary.products[m].ticket_types[3].age_range.split('-');
            if(+ageRange[0]<minAge){
                minAge=+ageRange[0];
            }
            if(+ageRange[1]>maxAge){
                maxAge=+ageRange[1];
            }
        }
    }
    result.complimentary.total_orig_price = total_orig_price;

    //calculate the money customer can save in the "bundle[2]"
    var total_saved_price = 0;
    for(var k = 0; k < result.optional.products.length; k++) {
        //additional products
        setSavePriceByDiscountType(result.optional.products[k]);
        total_saved_price += parseInt(result.optional.products[k].saved_price);
        if(result.optional.products[k].ticket_types[3]){
            ageRange=result.optional.products[k].ticket_types[3].age_range.split('-');
            if(+ageRange[0]<minAge){
                minAge=+ageRange[0];
            }
            if(+ageRange[1]>maxAge){
                maxAge=+ageRange[1];
            }
        }
    }
    packageModel.minAge=0;
    packageModel.maxAge=maxAge==0?18:maxAge;
    for(var i=packageModel.minAge;i<=packageModel.maxAge;i++){
        packageModel.age_list.push(i);
    }

    result.optional.total_saved_price = total_saved_price;

    //set service facility icons of hotel, "result[0]" refers to hotel bundle
    return result;
};

var setSavePriceByDiscountType = function(product) {
    if(!product.bundle_info)
        return;
    //"F" is the symbol of discount straightly, "P" is the symbol of discount by percents
    if(product.bundle_info.discount_type == "F") {
        product.sold_price = product.show_prices.price - product.bundle_info.discount_amount;
        product.saved_price = product.bundle_info.discount_amount;
    } else if(product.bundle_info.discount_type == "P") {
        product.sold_price = product.show_prices.price * (100 - product.bundle_info.discount_amount);
        product.saved_price = product.show_prices.price * product.bundle_info.discount_amount;
    }
};

function setServiceIcon(product) {
    var map_service = {
        ID1 : ['tv', '有线电视'],
        ID2 : ['snowflake', '空调'],
        ID3 : ['wifi', '免费WIFI'],
        ID4 : ['wifi', '收费WIFI'],
        ID5 : ['diningware', '免费早餐'],
        ID6 : ['diningware', '收费早餐']
    };
    for(var j = 0; j < product.hotel.room_types.length; j++) {
        var room = product.hotel.room_types[j];
        if(typeof room.services != "object")
            return;
        for(var n = 0; n < room.services.length; n++) {
            var property = "ID" + room.services[n].service_id;
            if(map_service.hasOwnProperty(property)) {
                room.services[n].class_name = "icon-" + map_service[property][0];
                room.services[n].name = map_service[property][1];
            }
        }
        product.hotel.room_types[j] = room;
    }
    return product;
};

function setRoomPrice(room_array, price_plan) {
    for(var i = 0; i < room_array.length; i++) {
        for(var j = 0; j < price_plan.length; j++) {
            if(room_array[i].special_code == price_plan[j].special_code) {
                room_array[i].show_prices = price_plan[j];
            }
        }
    }
    return room_array;
}

var tabScrollListener = function() {

    //Set Fixed Nav
    window.onscroll = function() {
        var st = document.body.scrollTop || document.documentElement.scrollTop;
        if(st > $(".bundle-tab").offset().top) {
            $(".nav-content").addClass("fixed-nav");
            $("#fixed_tab_button").addClass("show-fixed-button");
            $("#fixed_fav").addClass("show-fixed-button");
        }
        else {
            $(".nav-content").removeClass("fixed-nav");
            $("#fixed_tab_button").removeClass("show-fixed-button");
            $("#fixed_fav").removeClass("show-fixed-button");
        }
    }
};

var bindBuyNoticeData = function(model) {
    var data = model;
    // service include
    var reg_serviceinclude = /<h2[^>]*>(.*?)<\/h2>[^<]*?(<ol>.*?<\/ol>)/ig, match;
    var res_serviceinclude = [];
    if(data.description.service_include) {
        while(match = reg_serviceinclude.exec(data.description.service_include.replace(/\n/g, ''))) {
            res_serviceinclude.push({key : match[1], val : match[2]});
        }
    }
    packageModel.service_include = res_serviceinclude;

    // how it works
    var reg_howitworks = /<h2[^>]*>(.*?)<\/h2>[^<]*?(<ol>.*?<\/ol>)/gi, match;
    var res_howitworks = {};
    if(typeof data.description.how_it_works=='string'&&data.description.how_it_works) {
        while(match = reg_howitworks.exec(data.description.how_it_works.replace(/\n/g, ''))) {
            res_howitworks[match[1]] = match[2];
        }
    }
    packageModel.how_it_works = res_howitworks;

    // rules
    packageModel.rules = packageModel.productData.rules.$model;
}

var changeLocationMapMarkers = function(map, name, location) {
    var coord = location.split(',');
    var viewpoints = [
        [name, coord[0], coord[1], 1]
    ];

    HImap.clearMarkers(packageModel.bundle_one_location.current_markers);
    packageModel.bundle_one_location.current_markers = HImap.drawMarkers(map, viewpoints);
    HImap.suitBounds(map, viewpoints);
}


var getAllData = (function() {
    $.ajax({
        url      : $request_urls.productData,
        dataType : "json",
        type     : "GET",
        cache    : true,
        success  : function(res) {
            if(res.code == 200) {
                res.data.special_codes=resolveSpecialInfo(res.data.special_info)[0].special_codes;

                packageModel.productData = res.data;

                var specialMap = {};
                /*res.data.special_codes.forEach(function(e) {
                 specialMap[e.mapping_product_id] = {specialCode : e.special_code, mappingSpecialCode : e.mapping_special_code};
                 });
                 res.data.bundle.hotel.products.forEach(function(e) {
                 e.specialCode = specialMap[e.product_id].specialCode;
                 e.mappingSpecialCode = specialMap[e.product_id].mappingSpecialCode;
                 });*/
                res.data.bundles.sort(function(a, b) {
                    return a.group_type - b.group_type;
                });
                console.log(res.data.bundles);
                packageModel.bundle = bindDataToBundle(res.data.bundle);

                packageModel.basic_info = bindDataToBasicInfo(packageModel.productData);

                packageModel.hotel_info = bindDataToHotelInfo(packageModel.productData);

                packageModel.basic_price = bindDataToSelectedBasicPrice(packageModel.hotel_info);

                bindBuyNoticeData(packageModel.productData);

                if(res.data.introduction) {
                    noticeModel.DataInitializer.setData(res.data.introduction, res.data.type);
                }

                $(function() {
                    //initialize the calendar
                    gl_calendar = initNewCalendar(packageModel.bundle.hotel.products[packageModel.selectedHotelIndex]);
                    $(document).on('click',function(){
                        packageModel.selector.plan=false;
                        packageModel.selector.adult_num=false;
                        packageModel.selector.child_num=false;
                        for(var i=0;i<packageModel.child_list.length;i++){
                            packageModel.child_list[i].show=false;
                        }
                    })
                    //initialize the button status
                    var status = packageModel.basic_info.available;
                    if(status != 1) {
                        $("#buy_btn").attr("disabled", true).addClass("buy-btn-disabled");
                        $("#fixed_tab_button").attr("disabled", true).addClass("buy-btn-disabled");
                    } else {
                        $("#buy_btn").attr("disabled", false).removeClass("buy-btn-disabled");
                        $("#fixed_tab_button").attr("disabled", false).removeClass("buy-btn-disabled");
                    }

                    $(".loading-mask").css("display", "none");
                });
            }
            else {
                alert(res.msg);
            }
        }
    });
})();

avalon.filters.seq = function() {
    var idx = 1;
    return function(str, l) {
        var ret = (str + 1) + "";
        if(ret.length == 1) {
            ret = '0' + ret;
        }
        return ret;
    }
}();
var gl_calendar;