var bundleModel = avalon.define("bundleCtrl", function(vm) {
    vm.bundle = {};
    vm.bundle_one_location = {
        map             : '',
        current_markers : ''
    };
    vm.bundle_local = {
        is_show_hotel_comments : false
    }

    // 弹出侧边窗，显示绑定商品详细信息
    vm.showBundleModal = function(product_id, product_type, parent_id) {
        var $bundle_detail = $('#bundle-detail');
        if(product_type == 3) {
            $bundle_detail.addClass('type_3');
            $('#full-mask .modal-close-btn').addClass('type_3');
        } else {
            $bundle_detail.removeClass('type_3');
            $('#full-mask .modal-close-btn').removeClass('type_3');
        }

        $('body').addClass('no-scroll');
        $bundle_detail.addClass('show');
        setTimeout(function() {
            $('#full-mask').show();
            $bundle_detail.find('.spinner').show();
        }, 300)

        getBundleData(product_id, parent_id);
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
    var getBundleData = function(product_id, parent_id) {
        $.ajax({
            url      : $request_urls.bindingProduct + '?parent_id=' + parent_id + '&product_id=' + product_id,
            dataType : 'json',
            type     : 'get',
            success  : function(data) {
                var bundle = data.data;
                bundle.tour_plan = bundle.tour_plan[0];

                if(bundle.hotel) {
                    bundle.hotel.room_types = HotelRoom.setServiceIcon(bundle.hotel.room_types);
                    bundle.hotel.room_types = HotelRoom.setRoomPrice(bundle.hotel.room_types, bundle.price_plan_items);
                    if(vm.bundle_one_location.map == '') {
                        vm.bundle_one_location.map = HImap.init('hotel-location');
                    }
                    changeLocationMapMarkers(vm.bundle_one_location.map, bundle.description.name, bundle.hotel.latlng);
                }

                vm.bundle = bundle;

                setTimeout(function() {
                    $('#bundle-detail').find('.modal-loading-mask').hide();
                }, 500);
            },
            error    : function(data) {
                console.log(data.msg);
            }
        });
    }


    // bundle section 酒店 头部模块
    vm.showAllComments = function() {
        vm.bundle_local.is_show_hotel_comments = true;
    }
    vm.hideAllComments = function() {
        vm.bundle_local.is_show_hotel_comments = false;
    }

    // bundle section 地图模块
    var changeLocationMapMarkers = function(map, name, location) {
        var coord = location.split(',');
        var viewpoints = [
            [name, coord[0], coord[1], 1]
        ];

        HImap.clearMarkers(packageModel.bundle_one_location.current_markers);
        packageModel.bundle_one_location.current_markers = HImap.drawMarkers(map, viewpoints);
        HImap.suitBounds(map, viewpoints);
    }

    // bundle section 酒店 房型模块 -----
    vm.showRoomMore = function(more_btn) {
        var $room = $(more_btn).parent();
        $room.find('.room-more-row').slideDown(300);
    };
    vm.hideRoomMore = function(more_btn) {
        var $room = $(more_btn).parent().parent();
        $room.find('.room-more-row').slideUp(300);
    };
    var HotelRoom = {
        'setServiceIcon' : function(room_array) {
            for(var i = 0; i < room_array.length; i++) {
                for(var j = 0; j < room_array[i].services.length; j++) {
                    var service_item = room_array[i].services[j];
                    switch(service_item.service_id) {
                        case '1':
                            service_item.name = '有线电视';
                            service_item.class_name = 'icon-tv';
                            break;
                        case '2':
                            service_item.name = '空调';
                            service_item.class_name = 'icon-snowflake';
                            break;
                        case '3':
                            service_item.name = '免费WIFI';
                            service_item.class_name = 'icon-wifi';
                            break;
                        case '4':
                            service_item.name = '收费WIFI';
                            service_item.class_name = 'icon-wifi';
                            break;
                        case '5':
                            service_item.name = '免费早餐';
                            service_item.class_name = 'icon-diningware';
                            break;
                        case '6':
                            service_item.name = '收费早餐';
                            service_item.class_name = 'icon-diningware';
                            break;
                    }
                    room_array[i].services[j] = service_item;
                }
            }
            return room_array;
        },
        'setRoomPrice'   : function(room_array, price_plan) {
            for(var i = 0; i < room_array.length; i++) {
                for(var j = 0; j < price_plan.length; j++) {
                    if(room_array[i].special_code == price_plan[j].special_code) {
                        room_array[i].show_prices = price_plan[j];
                    }
                }
            }
            return room_array;
        }
    };

});