controllers.OrderEditCtrl = function($scope, $rootScope, $http, $q, $sce, $timeout) {
    //region Helpers
    var separator = ' / ';

    function patchFullName(en_name, zh_name) {
        if(en_name == zh_name) return en_name;

        var parts = [];
        if(!!en_name.trim()) {
            parts.push(en_name);
        }
        if(!!zh_name.trim()) {
            parts.push(zh_name);
        }

        return parts.join(separator);
    }

    function formatTime(str) {
        var parts = [];
        if(str.indexOf('.') > -1) {
            parts = str.split('.');
        } else if(str.indexOf(':') > -1) {
            parts = str.split(':');
        }

        return parts[0] + ':' + parts[1];
    }

    function formatDeparture(point, time) {
        var label = point;
        if(label.length) label += ' ';
        if(time != '00:00:00') label += formatTime(time);
        return label;
    }

    function lookupArray(array, prop_name, value) {
        var result = array.filter(function(elem) {
            if(elem[prop_name] == value) return true;
        });
        return ( result && result[0] );
    }

    function fillPassengerData(pax_meta, meta_ids, current_passenger) {
        var key, tmp, result = [];

        for(var i = 0, len = meta_ids.length; i < len; i++) {
            //meta.value 为展示值
            meta = pax_meta[meta_ids[i]];

            if(meta.input_type == 'age') {
                var ticket_age = lookupArray($scope.parent_product.product.ticket_rule, 'ticket_id', current_passenger.ticket_id);
                meta.dropdown = [];
                tmp = ticket_age.age_range.split(',').filter(function(item) {
                    return item.trim().length > 0;
                });
                for(key in tmp) {
                    if(tmp[key].indexOf('-') > -1) { //range
                        for(var j = +(tmp[key].split('-')[0]), len1 = +(tmp[key].split('-')[1]); j <= len1; j++) {
                            meta.dropdown.push(j.toString());
                        }
                    } else { //single
                        meta.dropdown.push(tmp[key]);
                    }
                }
                meta.dropdown = meta.dropdown.sort(function(a, b) {
                    return a - b;
                });

                meta.value = current_passenger[meta.storage_field];
            } else if(meta.input_type == 'date') {
                meta.value = current_passenger[meta.storage_field];
                meta.date = new Date(current_passenger[meta.storage_field]);
            } else if(meta.input_type == 'enum') {
                meta.dropdown = JSON.parse(meta.range);

                tmp = lookupArray(meta.dropdown, 'value', current_passenger[meta.storage_field]);
                if(tmp) {
                    meta.value = tmp.title;
                    meta.select = tmp.value; //TODO：值选不上，为啥？
                }
            } else {
                meta.value = current_passenger[meta.storage_field];
            }

            result.push(meta);
        }

        return result;
    }

    function normalizePassengerData(metas, index) {
        var result = angular.copy($scope.shipping.passengerInfo.pax_data[index]);
        var meta, tmp;
        for(var key in metas) {
            meta = metas[key];
            result[meta.storage_field] = meta.value;

            if(meta.input_type == 'date') {
                result[meta.storage_field] = formatDate(meta.date);

                //修改metas无效，必须修改原始值
                updateSinglePassengerMeta(index, key, result[meta.storage_field]);
            } else if(meta.input_type == 'enum') {
                result[meta.storage_field] = meta.select;
                //更新展示态
                tmp = lookupArray(meta.dropdown, 'value', meta.select);
                updateSinglePassengerMeta(index, key, tmp.title);
            }
        }

        return result;
    }

    function updateSinglePassengerMeta(index, key, value) {
        if($scope.data.shipping.passenger_info.has_lead && index == 0) {
            $scope.data.shipping.passenger_info.lead[0].meta[key].value = value;
        } else if($scope.data.shipping.passenger_info.has_all) {
            var i = $scope.data.shipping.passenger_info.has_lead ? index - 1 : index;
            $scope.data.shipping.passenger_info.all[i].meta[key].value = value;
        }
    }

    function availableTourDate(date, price_plan, tour_operation, need_special, special_code) {
        var i, len;
        var day = +date.getDay();
        day = day == '0' ? '7' : day;
        var today = new Date();
        var date_str = formatDate(date);

        function isInFrequency(day, frequency) {
            if(frequency == 'wdall') return true;
            return frequency.indexOf('wd' + day) > -1;
        }

        // 过滤没有价格计划
        if(!price_plan) return false;

        // 过滤小于今天的日期
        if(date.getTime() < today.getTime()) return false;

        // 过滤小于价格计划开始日期的日期
        if(price_plan.from_date && price_plan.valid_region == 1 &&
           date_str < formatDate(price_plan.from_date)) return false;

        // 过滤大于价格计划结束日期的日期
        if(price_plan.to_date && price_plan.valid_region == 1 &&
           date_str > formatDate(price_plan.to_date)) return false;

        if(tour_operation) {
            // 过滤小于售卖时间开始日期的日期
            if(tour_operation.from_date && date_str < formatDate(tour_operation.from_date)) return false;
            // 过滤大于售卖时间结束日期的日期
            if(tour_operation.to_date && date_str > formatDate(tour_operation.to_date)) return false;

            // 过滤close_dates
            if(tour_operation.close_dates) {
                var close_dates = tour_operation.close_dates.split(';').filter(function(elem) {
                    return elem.trim().length;
                });
                for(i = 0, len = close_dates.length; i < len; i++) {
                    if(close_dates[i].indexOf('周') != -1) { //周几
                        if('周' + day == close_dates[i]) return false;
                    } else if(close_dates[i].indexOf('/') != -1) { //close_date范围
                        var parts = close_dates[i].split('/');
                        if(formatDate(date) >= parts[0] && formatDate(date) <= parts[1]) return false;
                    } else if(close_dates[i].trim()) { //单个close_date
                        if(formatDate(date) == close_dates[i]) return false;
                    }
                }
            }
        }

        // 过滤价格计划中的限制条件
        if(price_plan.items && price_plan.items.length > 0) {
            for(i = 0, len = price_plan.items.length; i < len; i++) {
                if(price_plan.items[i].frequency) {
                    if(need_special == '1') {
                        if(price_plan.items[i].special_code == special_code) {
                            // 只有这些日期可以使用，不可以使用则返回false
                            return isInFrequency(day, price_plan.items[i].frequency);
                        }
                    } else {
                        return isInFrequency(day, price_plan.items[i].frequency);
                    }
                }
            }
        }

        return true;
    }

    function updateDeparture(new_list, shipping_departure) {
        var list = [], label;

        for(var i = 0, len = new_list.length; i < len; i++) {
            label = formatDeparture(new_list[i].departure.departure_point, new_list[i].time);
            list.push(label);
        }

        shipping_departure.orig_list = angular.copy(new_list);
        shipping_departure.list = angular.copy(list);
    }

    function getOrigDeparture(shipping_departure, departure_string) {
        var dep_index = shipping_departure.list.indexOf(departure_string);
        if(dep_index > -1) {
            return shipping_departure.orig_list[dep_index];
        }
        return false;
    }
    //endregion

    //region Data Initialization
    $scope.timeLimit = /^[0-9]*$/;
    $scope.data = {
        shipping    : {
            return_info    : {
                'reason'        : '',
                'comment'       : '',
                'return_type'   : '',
                'refund_amount' : 0
            },
            shipping_info  : {},
            basic_info     : [],
            contact_info   : {},
            passenger_info : {}
        },
        dialog_user : {}
    };
    $scope.local = {
        //Global
        menus             : {
            '1' : {
                label   : '基本信息',
                alert   : false,
                loading : false
            },
            '2' : {
                label   : '客服备注',
                alert   : false,
                loading : false
            },
            '3' : {
                label   : '支付信息',
                alert   : false,
                loading : false
            }
        },
        current_menu      : '1',
        //Shipping
        shipping          : {
            date                  : formatDate(new Date()),
            progress              : {
                'ship'      : false,
                'return'    : false,
                'reship'    : false,
                'rebooking' : false,
                'ship_pdf'  : false
            },
            has_overlay           : false,
            product_link          : $request_urls.viewProductUrl,
            return_types          : [
                {
                    name       : 'return_and_refund',
                    label      : '退货退款',
                    is_visible : true
                },
                {
                    name       : 'partial_refund',
                    label      : '部分退款',
                    is_visible : true
                },
                {
                    name       : 'record_refund',
                    label      : '记录退款',
                    is_visible : true
                },
                {
                    name       : 'refuse_refund',
                    label      : '拒绝退款',
                    is_visible : true
                }
            ],
            refund_reason         : [
                {
                    label  : '押金',
                    reason : 3
                },
                {
                    label  : '理赔',
                    reason : 4
                },
                {
                    label  : '其他',
                    reason : 2
                }
            ],
            supplier_order_status : {
                '1' : '待确认',
                '2' : '已确认',
                '3' : '已取消'
            }
        },
        section_head      : {
            shipping_info  : {
                title : '订单号 － '
            },
            basic_info     : {
                title    : '基本信息',
                editCb   : function() {
                    $scope.local.section_head.basic_info.is_edit = true;
                },
                updateCb : function() {
                    $scope.updateShippingBasicData();
                }
            },
            passenger_info : {
                title    : '出行人信息',
                editCb   : function() {
                    $scope.local.section_head.passenger_info.is_edit = true;
                },
                updateCb : function() {
                    $scope.updatePassengerData();
                }
            },
            contact_info   : {
                title    : '联系人信息',
                editCb   : function() {
                    $scope.local.section_head.contact_info.is_edit = true;
                },
                updateCb : function() {
                    $scope.updateContactData();
                }
            }
        },
        uploader_options  : {
            voucher : {
                target      : $request_urls.uploadVoucher,
                input_id    : 'voucher_upload',
                accept_type : 'application/pdf,image/png,image/jpeg',
                filterCb    : function(item) {
                    var extension = item.type.toLowerCase().slice(item.type.lastIndexOf('/') + 1);
                    var type_condition = (extension == 'pdf' || extension == 'png' || extension == 'jpg' || extension == 'jpeg');

                    if(!$scope.canAddVoucher()) {
                        $rootScope.$emit('notify', {msg : 'PDF上传数量已经达到上限，请删除一些再上传。'});
                    } else {
                        return type_condition;
                    }

                    return false;
                },
                beforeCb    : function(event, item) {
                    item.formData = [
                        {order_id : $scope.shipping.baseInfo.order_id}
                    ];
                },
                successCb   : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    var current_pid = $scope.local.uploader_options.current_pid;
                    if(response.code == 200) {
                        $scope.$apply(function() {
                            $scope.data.shipping.shipping_info[current_pid].supplier_order.voucher_ref.push(response.data);
                        });
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            }
        },
        //Default
        generator         : {
            departure : function() {
                return {
                    list       : [], //储存展示使用的数据
                    orig_list  : [], //储存请求返回的数据
                    info_index : -1
                };
            },
            tour_date : function(product_id, current_shipping) {
                current_shipping = current_shipping || $scope.shipping.shippingInfo[product_id];

                return {
                    info_index                 : -1,
                    dateFilter                 : function(tour_date) {
                        var special_id = current_shipping.baseInfo.special_info ? current_shipping.baseInfo.special_info.special_id : false;
                        return availableTourDate(tour_date, current_shipping.pricePlanInfo.price_plan, current_shipping.pricePlanInfo.tour_operation, current_shipping.baseInfo.need_special, special_id);
                    },
                    fetchDepartureFromTourDate : function(tour_date) {
                        if(current_shipping.baseInfo.need_departure == 1) {
                            $http.post($request_urls.getDepartures, {
                                product_id : product_id,
                                tour_date  : formatDate(tour_date)
                            }).success(function(data) {
                                if(data.code == 200) {
                                    updateDeparture(data.data.departure_list, $scope.shipping_product_info[product_id].departure);
                                } else {
                                    $rootScope.$emit('notify', {msg : data.msg});
                                }
                            });
                        }
                    }
                };
            }
        },
        //Comments
        todo_status       : {
            '1' : {
                label : '待处理'
            },
            '2' : {
                label : '已处理'
            }
        },
        current_departure : 0,
        order_comment_type : [
            {
                value : '1',
                label : '记录'
            },
            {
                value : '2',
                label : '催单'
            },
            {
                value : '3',
                label : '开发票'
            },
            {
                value : '4',
                label : '结算'
            },
            {
                value : '5',
                label : '修改'
            },
            {
                value : '6',
                label : '回访'
            },
            {
                value : '7',
                label : '投诉'
            }
        ],
        complaint_model: [
            {
                detail_type : '1',
                detail_md   : '',
                use         : false
            },
            {
                detail_type : '1',
                detail_md   : '',
                use         : false
            },
            {
                detail_type : '1',
                detail_md   : '',
                use         : false
            },
            {
                detail_type : '1',
                detail_md   : '',
                use         : false
            },
            {
                complaint_md: '',
                use         : false
            }
        ],
        detail_type_customer : [
            {
                value : ''
            },
            {
                value : '1',
                label : '用户迟到'
            },
            {
                value : '2',
                label : '未打印兑换单'
            },
            {
                value : '3',
                label : '未按规定日期/时间使用'
            },
            {
                value : '4',
                label : '胡搅蛮缠'
            },
            {
                value : '5',
                label : '其他'
            }
        ],
        detail_type_hitour : [
            {
                value : ''
            },
            {
                value : '1',
                label : '商品调研信息错误/不全'
            },
            {
                value : '2',
                label : '商品下单、发货配置错误'
            },
            {
                value : '3',
                label : '商品信息更新不及时'
            },
            {
                value : '4',
                label : 'op人工操作失误'
            },
            {
                value : '5',
                label : '发货不及时（操作原因）'
            },
            {
                value : '6',
                label : '发货不及时（预存余额不足）'
            },
            {
                value : '7',
                label : '未按时结算，订单被拒'
            },
            {
                value : '8',
                label : '玩途的服务条款漏洞'
            },
            {
                value : '9',
                label : '其他'
            }
        ],
        detail_type_supplier : [
            {
                value : ''
            },
            {
                value : '1',
                label : '供应商预订失误'
            },
            {
                value : '2',
                label : '供应商未按订单提供服务'
            },
            {
                value : '3',
                label : '供应商未及时更新商品信息'
            },
            {
                value : '4',
                label : '供应商服务态度差'
            },
            {
                value : '5',
                label : '供应商服务流程不合理'
            },
            {
                value : '6',
                label : '其他'
            }
        ],
        detail_type_god : [
            {
                value : ''
            },
            {
                value : '1',
                label : '天气原因取消行程'
            },
            {
                value : '2',
                label : '自然灾害等不可预见原因'
            },
            {
                value : '3',
                label : '其他'
            }
        ],
        //Payment
        payment           : {
            has_info : false,
            message  : ''
        },
        coupon_edit_url   : $request_urls.editCouponUrl,
        product_url       : $request_urls.viewProductUrl,
        show_pax_dialog   : false
    };
    $scope.shipping = {}; //Shipping Data Local Copy
    $scope.shipping_product_info = {}; //Tour Date & Departure
    //endregion

    $scope.display = function(flag) {
      console.log(flag);
    };
    //region Global
    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {
                content : '<span class="i i-arrow-left"></span>',
                clickCb : function() {
                    var part = window.location.search.split("&").filter(function(elem) {
                        return elem.indexOf('search=') == 0;
                    })[0];
                    window.location = $request_urls.listResult + decodeURIComponent(part.split("=")[1]);
                }
            },
            body : {
                content : '编辑订单'
            }
        });

        var order_comments = $http.get($request_urls.orderComments);
        var order_payments = $http.get($request_urls.orderPayInfo);
        var order_general = $http.get($request_urls.getOrderDetail);

        $q.all([order_general, order_comments, order_payments]).then(function(values) {
            if(values[0] && values[0].status == 200 && values[0].data.code == 200 && values[1] &&
               values[1].status == 200 &&
               values[1].data.code == 200 && values[2] && values[2].status == 200 &&
               ( values[2].data.code == 200 || values[2].data.code == 400 )) {
                var i, len;
                $rootScope.$emit('loadStatus', false);

                //Shipping
                $scope.shipping = values[0].data.data;
                $scope.initShippingData();

                //Comments
                $scope.data.comments = values[1].data.data;
                $scope.updateAlert();
                for(i = 0, len = $scope.data.comments.length; i < len; i++) {
                    $scope.data.comments[i].editable = false;
                    $scope.data.comments[i].expected_hour = parseInt($scope.data.comments[i].date_proc.substring(11,13));
                    var current_complaint = angular.copy($scope.data.comments[i].complaint);
                    $scope.data.comments[i].complaint = angular.copy($scope.local.complaint_model);
                    for(var complaint_index in current_complaint) {
                        $scope.data.comments[i].complaint[current_complaint[complaint_index].complaint_type - 1] = current_complaint[complaint_index];
                        $scope.data.comments[i].complaint[current_complaint[complaint_index].complaint_type - 1].use = true;
                    }
                }

                //Payment
                $scope.local.payment = {
                    has_info : values[2].data.code == 200,
                    message  : values[2].data.msg
                };
                $scope.data.payment = values[2].data.data;
            } else {
                $rootScope.$emit('loadStatus', false);
                $rootScope.$emit('errorStatus', true);
            }
        });
    };

    $scope.changePage = function(key) {
        $scope.local.current_menu = key;
    };

    //Notify before leaving page
    window.onbeforeunload = function(e) {
        if(!( $scope.shipping_form.$pristine && $scope.basic_info.$pristine && $scope.passenger_info.$pristine &&
              $scope.contact_info.$pristine )) {
            if(!e) e = window.event;
            //e.cancelBubble is supported by IE - this will kill the bubbling process.
            e.cancelBubble = true;
            e.returnValue = '有内容未保存，是否确认离开？';

            if(e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            }
        }
    };
    //endregion

    //region 发货信息
    $scope.initShippingData = function() {
        //Init Shipping
        $scope.initShippingProductInfo();

        //Shipping Info
        $scope.initShippingShippingData();

        //Basic Info
        $scope.initShippingBasicData();

        //Passenger Info
        $scope.initPassengerData();

        //Contact Info
        $scope.initContactData();

        //History Info
        $scope.data.shipping.history = $scope.shipping.statusHistory;
    };
    $scope.renewShippingData = function() {
        //hack:  renewShippingData后把shipping_form的$pristine设为true;
        $scope.shipping_form.$pristine = true;
        $scope.local.menus['1'].loading = true;
        $scope.local.menus['3'].loading = true;

        var order_general = $http.get($request_urls.getOrderDetail);
        var order_payments = $http.get($request_urls.orderPayInfo);

        $q.all([order_general, order_payments]).then(function(values) {
            if(values[0] && values[0].status == 200 && values[0].data.code == 200 && values[1] &&
               values[1].status == 200 &&
               ( values[1].data.code == 200 || values[1].data.code == 400 )) {
                $scope.local.menus['1'].loading = false;
                $scope.local.menus['3'].loading = false;

                //Shipping
                $scope.shipping = values[0].data.data;
                $scope.initShippingData();

                //Payment
                $scope.local.payment = {
                    has_info : values[1].data.code == 200,
                    message  : values[1].data.msg
                };
                $scope.data.payment = values[1].data.data;
            } else {
                $rootScope.$emit('errorStatus', true);
            }
        });
    };

    //region Shipping Page - Init
    function formatSpecialInfo(special_info) {
        var new_special;

        return special_info.items.map(function(one_item) {
            new_special = {
                'value' : patchFullName(one_item.en_name, one_item.cn_name),
                'label' : patchFullName(one_item.group_en_title, one_item.group_cn_title)
            };

            if(one_item.status == '0') {
                new_special.value += ' （状态：已禁用）';
            }

            return new_special;
        });
    }
    $scope.addShippingProductInfo = function(pid) {
        var product_base, is_parent, shipping, shipping_rule, ticket_num;
        shipping = $scope.shipping;
        is_parent = !( pid in shipping.shippingInfo );
        if(is_parent) { //Combo或者Package的父商品
            ticket_num = $scope.parent_product.pax_num;
            product_base = shipping.baseInfo;
            var current_shipping = { //伪装成shippingInfo里面的一个商品
                baseInfo      : shipping.baseInfo,
                pricePlanInfo : shipping.pricePlanInfo
            };
        } else {
            ticket_num = shipping.shippingInfo[pid].ticket_num;
            product_base = shipping.shippingInfo[pid].baseInfo;
            shipping_rule = shipping.shippingInfo[pid].shipping_rule;
        }

        $scope.shipping_product_info[pid] = {
            shipping     : {},
            departure    : {},
            tour_date    : {},
            special_code : []
        };

        //Tour Date
        if(product_base.need_tour_date == 1) {
            $scope.shipping_product_info[pid]['tour_date'] =
            is_parent ? $scope.local.generator.tour_date(pid, current_shipping) : $scope.local.generator.tour_date(pid);
            $scope.shipping_product_info[pid]['tour_date']['value'] = new Date(product_base.tour_date);
            $scope.shipping_product_info[pid]['tour_date']['label'] = patchFullName(product_base.tour_date_title_en, product_base.tour_date_title_zh);
        }

        //Special Code
        if(product_base.need_special == 1) {
            if(product_base.special_info && product_base.special_info.items) {
                $scope.shipping_product_info[pid]['special_code'] = formatSpecialInfo(product_base.special_info);
            }
        }

        //Departure Point
        if(product_base.need_departure == 1) {
            $scope.shipping_product_info[pid]['departure'] = $scope.local.generator.departure(pid);
            $scope.shipping_product_info[pid]['departure']['value'] = formatDeparture(product_base.departure_point_zh, product_base.departure_time);
            $scope.shipping_product_info[pid]['departure']['label'] = patchFullName(product_base.departure_title_en, product_base.departure_title_zh);
            updateDeparture(shipping.shippingInfo[pid].pricePlanInfo.departure_list, $scope.shipping_product_info[pid]['departure']);
        }

        //Shipping Rule
        if(shipping_rule) {
            //PDF上传数量
            // confirmation_type == 0 不需要pdf
            if(shipping_rule.confirmation_type == 1) { //只要一个
                $scope.shipping_product_info[pid].shipping.max_count = 1;
            } else if(shipping_rule.confirmation_type == 2) { //每人一个
                $scope.shipping_product_info[pid].shipping.max_count = ticket_num;
            } else if(shipping_rule.confirmation_type == 3) { //不限制
                $scope.shipping_product_info[pid].shipping.max_count = 0;
            }
        }
    };
    $scope.initShippingProductInfo = function() {
        var shipping = $scope.shipping;

        //对每个子商品的shippingInfo进行操作
        for(var pid in shipping.shippingInfo) {
            $scope.addShippingProductInfo(pid);
        }

        $scope.data.shipping.product_type = ''; //只区分Combo，酒店套餐，和其他
        //父商品的shippingInfo
        if($scope.shipping.baseInfo.product) { //其他商品
            $scope.parent_product = shipping.baseInfo.product;
            $scope.parent_pid = $scope.parent_product.product_id;

            if($scope.parent_product.is_combo == 1) {
                $scope.data.shipping.product_type = 'combo';
            } else {
                $scope.data.shipping.product_type = 'single';
            }
        } else { //package
            $scope.parent_product = shipping.baseInfo.products['group_0']['0'];
            $scope.parent_pid = $scope.parent_product.product_id;
            $scope.data.shipping.product_type = 'package';
            $scope.addShippingProductInfo($scope.parent_pid);
        }
    };
    //endregion

    //region Voucher
    $scope.addVoucher = function(pid) {
        $scope.local.uploader_options.current_pid = pid;
        $('#' + $scope.local.uploader_options.voucher.input_id).trigger('click');
    };
    $scope.delVoucher = function(pid, index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        var shipping_product = $scope.shipping.shippingInfo[pid];
        var post_data = {
            order_id          : $scope.shipping.baseInfo.order_id,
            voucher_name      : shipping_product.supplier_order.voucher_ref[index].voucher_name,
            supplier_order_id : shipping_product.supplier_order.supplier_order_id
        };
        $http.post($request_urls.deleteVoucher, post_data).success(function(data) {
            if(data.code == 200) {
                shipping_product.supplier_order.voucher_ref.splice(index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.canAddVoucher = function(pid) {
        var current_pid = pid || $scope.local.uploader_options.current_pid;
        var voucher_count = $scope.shipping.shippingInfo[current_pid].supplier_order.voucher_ref.length;
        var confirmation_type = $scope.shipping.shippingInfo[current_pid].shipping_rule.confirmation_type;

        return (
                   confirmation_type != 3 &&
                   voucher_count < $scope.shipping_product_info[current_pid].shipping.max_count
                   ) || ( confirmation_type == 3 );
    };
    //endregion

    //region Handle Shipping
    $scope.verifyPassengerInfo = function(info_array) {
        for(var i in info_array) {
            if(!info_array[i].value || info_array[i].value.length == 0) {
                return false;
            }
        }

        return true;
    };
    $scope.handleShipping = function(type) {
        var i;
        var pax_info_valid = true;
        if(pax_info_valid && $scope.data.shipping.passenger_info.has_lead) {
            for(i in $scope.data.shipping.passenger_info.lead) {
                pax_info_valid = $scope.verifyPassengerInfo($scope.data.shipping.passenger_info.lead[i].meta);
                if(!pax_info_valid)
                    break;
            }
        }
        if(pax_info_valid && $scope.data.shipping.passenger_info.has_all) {
            for(i in $scope.data.shipping.passenger_info.all) {
                pax_info_valid = $scope.verifyPassengerInfo($scope.data.shipping.passenger_info.all[i].meta);
                if(!pax_info_valid)
                    break;
            }
        }
        if(!pax_info_valid) {
            alert("请将缺失的出行人信息补充完全再进行操作。");
            return;
        }

        function successCb(data) {
            if(data.code == 200) {
                $scope.local.shipping.progress[type] = false;
                $scope.renewShippingData();
            }
            $rootScope.$emit('notify', {msg : data.msg});
        }

        if(type == 'rebooking') {
            if(!window.confirm("确认要重新预定吗？\n点击'确认'预定。")) return;

            $scope.local.shipping.progress[type] = true;
            $http.post($request_urls.rebookingOrder, {
                order_id : $scope.shipping.baseInfo.order_id
            }).success(successCb);
        } else if(type == 'ship' || type == 'reship') {
            $scope.local.shipping.progress[type] = true;
            $http.post($request_urls.doShipping).success(successCb);
        } else if(type == 'booking') {
            $scope.local.shipping.progress[type] = true;
            $http.post($request_urls.bookingOrder, {
                order_id : $scope.shipping.baseInfo.order_id
            }).success(successCb);
        } else if(type == 'ship-pdf') {
            $scope.local.shipping.progress[type] = true;
            $http.get($request_urls.doShipping + '&with_pdf=true').success(successCb);
        }
    };
    //endregion

    //region Refund
    $scope.initOverlay = function() {
        var overlay_container = angular.element(document.getElementById('refund_overlay'));
        var refund_container = angular.element(document.getElementById('refund_container'));
        overlay_container.bind('click', function() {
            $scope.$apply(function() {
                $scope.toggleOverlay(false);
            });
        });
        refund_container.bind('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    };
    $scope.toggleOverlay = function(is_visible) {
        var result = !!is_visible;
        $scope.local.shipping.has_overlay = result;
        $rootScope.$emit('overlay', result);
    };
    $scope.returnOrder = function() {
        if($scope.data.shipping.return_info.return_type == 'return_and_refund') {
            if(!window.confirm("是否确认退货！\n 该操作会执行需要几秒钟，请耐心等待！")) return;
            $scope.data.shipping.return_info.reason = 1;
        }

        $scope.local.shipping.progress.return = true;

        $http.post($request_urls.refundOrder, $scope.data.shipping.return_info).success(function(data) {
            $scope.toggleOverlay(false);
            $scope.local.shipping.progress.return = false;
            alert(data.msg);
            if(data.code == 200) {
                $scope.renewShippingData();
            }
        });
    };
    //endregion

    //Misc
    $scope.$watch('data.shipping.return_info.return_type', function(new_val) {
        if(new_val == 'return_and_refund') {
            $scope.data.shipping.return_info.refund_amount = +$scope.data.payment.remain_refund_amount;
        }
    });
    $scope.setChosen = function() {
        //TODO: Hack
        $timeout(function() {
            angular.element(document.querySelector('.departure .chosen-container')).css('width', '400px');
        }, 1000);
    };

    //region Shipping Info
    $scope.initShippingShippingData = function() {
        var tmp, shipping, gta_id = 11;

        shipping = $scope.shipping;

        //Section Title
        tmp = '订单号 － ' + shipping.baseInfo.order_id;
        if($scope.parent_product.product.supplier_id == gta_id) {
            tmp += '／GTA订单号 － ' + shipping.shippingInfo[$scope.parent_pid].supplier_order.supplier_booking_ref;
        }
        tmp += ' （订单状态：' + shipping.baseInfo.status_name + '）';
        if(!!Object.keys(shipping.activityInfo).length) {
            tmp += ' ［来源活动：' + shipping.activityInfo.title + '］';
        }
        $scope.local.section_head.shipping_info.title = $sce.trustAsHtml(tmp);

        //Section Content
        $scope.data.shipping.shipping_info = shipping.shippingInfo;

        var in_return = ['9', '10', '11', '12'].indexOf(shipping.baseInfo.status_id) > -1;

        for(var pid in $scope.data.shipping.shipping_info) {
            tmp = $scope.data.shipping.shipping_info[pid];
            tmp.pid = tmp.baseInfo.product_id;
            tmp.is_edit = false; //每个子商品默认为预览态
            tmp.allow_edit = (tmp.shipping_rule.need_supplier_booking_ref == '1' ||
                              tmp.shipping_rule.need_hitour_booking_ref == '1' ||
                              ( tmp.shipping_rule.supplier_feedback_type == '1' &&
                                tmp.shipping_rule.confirmation_type != '0' ) ||
                              ( tmp.shipping_rule.booking_type != 'STOCK' &&
                                tmp.shipping_rule.supplier_feedback_type == '2' &&
                                tmp.shipping_rule.confirmation_type > 0 ) ||
                              tmp.baseInfo.need_departure == '1' || tmp.baseInfo.need_tour_date == '1')


            //Confirmation Code
            if(tmp.shipping_rule.supplier_feedback_type == '1' && tmp.shipping_rule.confirmation_type != '0') { //需要Confirmation Code
                if(tmp.shipping_rule.confirmation_type != 3) { //需要计算code数量
                    tmp.confirmation_ref = Array($scope.shipping_product_info[pid].shipping.max_count).join('a').split('a');
                    if(tmp.supplier_order.confirmation_ref.length == tmp.confirmation_ref.length) {
                        tmp.confirmation_ref = tmp.supplier_order.confirmation_ref;
                    }
                }
            }
        }

        $scope.local.shipping.status_name = shipping.baseInfo.status_name;
        $scope.local.shipping.return_expire_date = $scope.parent_product.return_expire_date;

        //Refund
        $scope.data.shipping.return_info.order_id = shipping.baseInfo.order_id;
        if(shipping.canReturn == 0 || in_return) { //已经完成或者进行退货退款，则不让进行任何退款／退货，只能记录
            $scope.local.shipping.return_types[0].is_visible = false;
            $scope.local.shipping.return_types[1].is_visible = false;
        }
        if(shipping.baseInfo.order.total == 0) { //如果总价等于0，则不能做记录
            $scope.local.shipping.return_types[2].is_visible = false;
        }
        $scope.local.shipping.return_types[3].is_visible = shipping.baseInfo.status_id == 8;
    };
    $scope.updateShippingShippingInfoData = function(pid) {

        //内容校验
        var supplier_order, shipping_rule, shipping_product;
        var post_data = {};
        shipping_product = $scope.data.shipping.shipping_info[pid];

        shipping_rule = shipping_product.shipping_rule;
        supplier_order = shipping_product.supplier_order;

        //不做PDF检测
        if(shipping_product.supplier_order.supplier_order_id) {
            //需要上传PDF而且数量不够
            if(( shipping_rule.booking_type != 'STOCK' && shipping_rule.supplier_feedback_type == '2' ) && (
                ( shipping_rule.confirmation_type == 3 && supplier_order.voucher_ref.length == 0 ) ||
                ( shipping_rule.confirmation_type != 3 &&
                  supplier_order.voucher_ref.length < $scope.shipping_product_info[pid].shipping.max_count )
                )) {
                if(!window.confirm('PDF文件数量不够，是否继续保存？')){
                    return;
                }
                //$rootScope.$emit('notify', {msg : 'PDF文件数量不够，请继续上传。'});
            }
        }

        post_data[pid] = angular.copy(supplier_order);
        if(shipping_product.confirmation_ref) {
            post_data[pid].confirmation_ref = angular.copy(shipping_product.confirmation_ref);
        }
        if($scope.shipping.shippingInfo[pid].baseInfo.need_tour_date == 1) {
            post_data[pid].tour_date = formatDate($scope.shipping_product_info[pid].tour_date.value);
        }
        if($scope.shipping.shippingInfo[pid].baseInfo.need_departure == 1) {
            var selected_dep = getOrigDeparture($scope.shipping_product_info[pid].departure, $scope.shipping_product_info[pid].departure.value);
            if(selected_dep) {
                post_data[pid].departure_time = selected_dep.time;
                post_data[pid].departure_code = selected_dep.departure_code;
            }
        }

        $http.post($request_urls.updateShipping, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.data.shipping.shipping_info[pid].is_edit = false;
                $scope.renewShippingData();
            } else if(data.code == 303 && window.confirm(data.msg)) {
                $http.post($request_urls.confirmUpdateShipping, post_data).success(function(data) {
                    data.code == 200 ? $scope.renewShippingData() : $rootScope.$emit('notify', {msg : data.msg});
                });
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });

    };
    $scope.operationSave = function(pid) {
        $scope.updateShippingShippingInfoData(pid);
    };
    $scope.operationEdit = function(pid) {
        var product_on_editing = false;
        for(var i in $scope.data.shipping.shipping_info) {
            var item = $scope.data.shipping.shipping_info[i];
            if(item.is_edit) {
                product_on_editing = true;
            }
        }
        if(product_on_editing) {
            alert('一次只能编辑一个商品！<br/> 请将当前商品编辑完成并保存后再编辑下一个商品！');
        } else {
            $scope.data.shipping.shipping_info[pid].is_edit = true;
        }
    };
    //endregion

    //region 弹窗查看出行人
    $scope.getPassengerInfo = function(product_id, passenger_id) {
        $scope.local.show_pax_dialog = true;
        $scope.local.dialog_loading = true;
        $http.post($request_urls.getPassenger, {
            'product_id'   : product_id,
            'passenger_id' : passenger_id
        }).success(function(data) {
            if(data.code == 200) {
                var current_passenger = data.data;
                var current_passenger_meta = angular.copy(fillPassengerData(current_passenger.pax_meta, current_passenger.pax_rule.id_map[current_passenger.pax_data[0].ticket_id], current_passenger.pax_data[0]));

                $scope.data.dialog_user = {
                    meta  : current_passenger_meta,
                    label : current_passenger.pax_ticket[current_passenger.pax_data[0].ticket_id].cn_name
                }
            } else {
                $scope.local.show_pax_dialog = false;
            }
            $scope.local.dialog_loading = false;
        });
    };
    $scope.dismissDialog = function() {
        $scope.local.show_pax_dialog = false;
        $scope.local.dialog_loading = false;
    };
    $scope.syncCommonInfo = function() {
        var i;
        var sync_info = {};
        var sync_ids = ['19','25','65','67','70','71'];
        for (i in $scope.data.shipping.passenger_info.lead[0].meta) {
            if (sync_ids.indexOf($scope.data.shipping.passenger_info.lead[0].meta[i].id) > -1) {
                sync_info[$scope.data.shipping.passenger_info.lead[0].meta[i].id] = $scope.data.shipping.passenger_info.lead[0].meta[i].value;
            }
        }

        for(i in $scope.data.shipping.passenger_info.all) {
            for (var j in $scope.data.shipping.passenger_info.all[i].meta) {
                if (sync_info.hasOwnProperty($scope.data.shipping.passenger_info.all[i].meta[j].id)) {
                    $scope.data.shipping.passenger_info.all[i].meta[j].value = sync_info[$scope.data.shipping.passenger_info.all[i].meta[j].id];
                }
            }
        }

        $scope.updatePassengerData();
    };
    //endregion
    //endregion


    //region 基本信息
    $scope.initShippingBasicData = function() {
        var tmp, shipping, parent_pid;

        //Shipping, var for fast access
        shipping = $scope.shipping;
        parent_pid = $scope.parent_pid;
        $scope.local.shipping.allow_edit_basicinfo = shipping.baseInfo.need_departure == 1 ||
                                                     shipping.baseInfo.need_tour_date == 1;

        $scope.data.shipping.basic_info = [
            {
                id         : 'product_info',
                data       : '<a href="' + $scope.local.product_url + parent_pid + '" target="_blank">【ID：' + $scope.parent_pid + '】 ' + $scope.parent_product.product_name + '</a>',
                label      : '商品信息',
                allow_edit : false
            },
            {
                id         : 'supplier',
                data       : patchFullName($scope.parent_product.supplier_name_en, $scope.parent_product.supplier_name_zh),
                label      : '供应商',
                allow_edit : false
            }
        ];

        //基本信息里（父）商品的Tour Date
        if(shipping.baseInfo.need_tour_date == 1) {
            shipping.baseInfo.tour_date = new Date(shipping.baseInfo.tour_date);
            tmp = {
                id         : 'tour_date',
                date       : shipping.baseInfo.tour_date,
                data       : formatDate(shipping.baseInfo.tour_date),
                label      : patchFullName(shipping.baseInfo.tour_date_title_en, shipping.baseInfo.tour_date_title_zh),
                comment    : $scope.parent_product.rule_desc.return_desc,
                allow_edit : true
            };

            if(!shipping.pricePlanInfo.price_plan) {
                //如果缺少，不让编辑
                tmp.allow_edit = false;
                $scope.local.shipping.allow_edit_basicinfo = false;
                $rootScope.$emit('notify', {msg : '此订单的出行日期缺少价格计划。请修改后再编辑。'});
            }

            $scope.shipping_product_info[parent_pid].tour_date.info_index = $scope.data.shipping.basic_info.push(tmp) - 1;
        }

        //基本信息里（父）商品的Departure Point
        if(shipping.baseInfo.need_departure == 1) {
            updateDeparture(shipping.pricePlanInfo.departure_list, $scope.shipping_product_info[parent_pid].departure);

            tmp = {
                id         : 'departure_point',
                data       : formatDeparture(shipping.baseInfo.departure_point_zh, shipping.baseInfo.departure_time),
                label      : patchFullName(shipping.baseInfo.departure_title_en, shipping.baseInfo.departure_title_zh),
                allow_edit : true
            };
            $scope.shipping_product_info[parent_pid].departure.info_index = $scope.data.shipping.basic_info.push(tmp) - 1;
        }

        //Special Code
        var product_info = $scope.data.shipping.product_type == 'package' ? shipping.baseInfo.products['group_0'][0] : shipping.baseInfo.product;
        if(product_info.need_special == 1 && product_info.special_info) {
            var special_index = getIndexByProp(product_info.special_info, 'special_id', product_info.special_code);

            if(special_index > -1) {
                $scope.data.shipping.basic_info = $scope.data.shipping.basic_info.concat(formatSpecialInfo(product_info.special_info[special_index]).map(function(one_special) {
                    one_special.id = 'special_code';
                    one_special.allow_edit = false;

                    return one_special;
                }));
            }
        }

        //Insurance
        if(shipping.baseInfo.insurance_code.length) {
            tmp = shipping.baseInfo.insurance_code.reduce(function(prev, curr, index) {
                return prev + curr + '\t\t' + ( index % 3 == 1 ? '\n' : '' );
            }, '');

            $scope.data.shipping.basic_info.push({
                id         : 'insurance',
                data       : tmp,
                label      : '保险码',
                allow_edit : false
            });
        }

        //Gift Coupon
        if(shipping.baseInfo.gift_coupon.length) {
            tmp = shipping.baseInfo.gift_coupon.reduce(function(prev, curr, index) {
                return prev + '<a href="' + $scope.local.coupon_edit_url + curr.coupon_id + '" target="_blank">' + curr.coupon.code + '</a>' + '\t\t' + ( index % 3 == 1 ? '\n' : '' );
            }, '');

            $scope.data.shipping.basic_info.push({
                id         : 'insurance',
                data       : tmp,
                label      : '赠送优惠券',
                allow_edit : false
            });
        }

        $scope.data.shipping.basic_info = $scope.data.shipping.basic_info.map(function(one_item) {
            one_item.html = (one_item.data && one_item.data.indexOf('<') != 0) ? '<pre>' + one_item.data + '</pre>' : one_item.data;
            one_item.html = $sce.trustAsHtml(one_item.html);

            return one_item;
        });
    };
    $scope.updateShippingBasicData = function() {
        if($scope.local.shipping.allow_edit_basicinfo) {
            var parent_id = $scope.parent_pid;
            var post_data = {
                'product_id'     : parent_id,
                'tour_date'      : '',
                'time'           : '',
                'departure_code' : ''
            };

            if($scope.shipping.baseInfo.need_tour_date == 1) {
                var tour_date = $scope.data.shipping.basic_info[$scope.tour_date.info_index];
                post_data.tour_date = tour_date.data = formatDate(tour_date.date);
            }
            if($scope.shipping.baseInfo.need_departure == 1) {
                var departure_index = $scope.shipping_product_info[parent_id].departure.info_index;
                var departure_string = $scope.data.shipping.basic_info[departure_index].data;
                var selected_dep = getOrigDeparture($scope.shipping_product_info[parent_id].departure, departure_string);
                if(selected_dep) {
                    post_data.time = selected_dep.time;
                    post_data.departure_code = selected_dep.departure_code;
                }
            }

            $http.post($request_urls.updateTourDate, post_data).success(function(data) {
                if(data.code == 200) {
                    $scope.basic_info.$pristine = true;
                    $scope.local.section_head.basic_info.is_edit = false;
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        } else {
            $scope.local.section_head.basic_info.is_edit = false;
        }
    };
    //endregion


    //region 出行人信息
    $scope.initPassengerData = function() {
        var i, len, key, elem, current_passenger, current_passenger_meta;

        var shipping = $scope.shipping;
        var has_all = shipping.passengerInfo.pax_rule.need_passenger_num == 0;
        var has_lead = shipping.passengerInfo.pax_rule.need_lead == 1;
        var summary_str = '';
        var passenger_map = {};
        $scope.data.shipping.passenger_info = {
            'all'      : [],
            'lead'     : [],
            'has_all'  : has_all,
            'has_lead' : has_lead
        };

        //数量摘要
        for(key in shipping.passengerInfo.pax_quantities) {
            elem = parseInt(shipping.passengerInfo.pax_quantities[key], 10);
            passenger_map[key] = 1;
            if(elem > 0) {
                summary_str += shipping.passengerInfo.pax_ticket[key].cn_name + 'x' + elem + '&nbsp;';
            }
        }
        summary_str = '出行人信息&nbsp;&nbsp;<span class="sub-title">（&nbsp;' + summary_str + '）</span>';
        $scope.local.section_head.passenger_info.title = $sce.trustAsHtml(summary_str);

        //旅客信息展示
        if(has_lead) {
            //angular.copy required!!!

            $scope.data.shipping.passenger_info.lead = [
                {
                    meta  : angular.copy(fillPassengerData($scope.shipping.passengerInfo.pax_meta, shipping.passengerInfo.pax_rule.lead_ids, $scope.shipping.passengerInfo.pax_data[0])),
                    label : '主要出行联系人'
                }
            ];
        }

        if(has_all) {
            $scope.data.shipping.passenger_info.all = [];
            for(i = ( has_lead ? 1 : 0 ), len = shipping.passengerInfo.pax_data.length; i < len; i++) {
                current_passenger = shipping.passengerInfo.pax_data[i];
                current_passenger_meta = angular.copy(fillPassengerData($scope.shipping.passengerInfo.pax_meta, shipping.passengerInfo.pax_rule.id_map[current_passenger.ticket_id], $scope.shipping.passengerInfo.pax_data[i]));
                $scope.data.shipping.passenger_info.all.push({
                    meta  : current_passenger_meta,
                    label : shipping.passengerInfo.pax_ticket[current_passenger.ticket_id].cn_name +
                            ( passenger_map[current_passenger.ticket_id]++ )
                });
            }
        }
    };
    $scope.updatePassengerData = function() {
        var all_pax, lead_pax, passenger_manifest;

        all_pax = angular.copy($scope.data.shipping.passenger_info.all);
        lead_pax = angular.copy($scope.data.shipping.passenger_info.lead);
        passenger_manifest = lead_pax.concat(all_pax).map(function(elem, index) {
            return normalizePassengerData(elem.meta, index);
        });

        $http.post($request_urls.updatePassengers, passenger_manifest).success(function(data) {
            if(data.code == 200) {
                $scope.passenger_info.$pristine = true;
                $scope.local.section_head.passenger_info.is_edit = false;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //endregion


    //region 联系人信息
    $scope.initContactData = function() {
        var user_type = '';
        $scope.shipping.contactsInfo.customer_type.forEach(function(elem) {
            if(elem.toLowerCase() == 'email') {
                user_type += separator + '邮箱注册(' + $scope.shipping.contactsInfo.customer_account + ')';
            } else if(elem.toLowerCase() == 'qq') {
                user_type += separator + 'QQ绑定';
            } else if(elem.toLowerCase() == 'weibo') {
                user_type += separator + '微博绑定';
            } else if(elem.toLowerCase() == 'phone') {
                user_type += separator + '手机注册(' + $scope.shipping.contactsInfo.customer_account + ')';
            } else if(elem.toLowerCase() == 'weixin') {
                user_type += separator + '微信绑定';
            }
        });

        $scope.data.shipping.contact_info = [
            {
                id         : 'contacts_name',
                data       : $scope.shipping.contactsInfo.contacts_name,
                label      : '姓名',
                allow_edit : true
            },
            {
                id         : 'contacts_telephone',
                data       : $scope.shipping.contactsInfo.contacts_telephone,
                label      : '电话',
                allow_edit : true
            },
            {
                id         : 'contacts_email',
                data       : $scope.shipping.contactsInfo.contacts_email,
                label      : '邮箱',
                allow_edit : true
            },
            {
                id         : 'account',
                data       : $scope.shipping.contactsInfo.customer_account,
                label      : '付款账号',
                allow_edit : false
            },
            {
                id         : 'user_type',
                data       : user_type.substring(3),
                label      : '账号类型',
                allow_edit : false
            }
        ];
    };
    $scope.updateContactData = function() {
        var post_data = {};
        for(var key in $scope.data.shipping.contact_info) {
            if($scope.data.shipping.contact_info[key].allow_edit == true) {
                post_data[$scope.data.shipping.contact_info[key].id] = $scope.data.shipping.contact_info[key].data;
            }
        }
        $http.post($request_urls.updateContact, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.contact_info.$pristine = true;
                $scope.local.section_head.contact_info.is_edit = false;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.orderProcessingMail = function() {
        $http.post($request_urls.sendOrderProcessing, {
            'order_id' : $scope.shipping.baseInfo.order_id
        }).success(function(data) {
            if(data.code == 200) {
                alert('发送邮件成功');
            } else {
                alert('发送邮件失败');
            }
        });
    };
    //endregion


    //region Comments
    $scope.addTodo = function() {
        var default_status = 1; //Default to unprocessed
        var default_time = new Date();
        default_time = formatDate(default_time) + default_time.toTimeString().substr(0, 8);

        $scope.data.comments.unshift({
            editable      : true, //Only allow new comments to be edited
            comment       : "",
            comment_id    : "",
            proc_status   : default_status,
            date_added    : default_time,
            date_modified : "0000-00-00 00:00:00",
            status_name   : $scope.local.todo_status[default_status].label,
            user_name     : "",
            type          : "1",
            expected_hour : 17,
            date_proc     : new Date(),
            complaint     : angular.copy($scope.local.complaint_model)
        });
        $scope.updateAlert(true);
    };

    $scope.toggleCheck = function(todo_index, complaint_index) {
        $scope.data.comments[todo_index].complaint[complaint_index].use = !$scope.data.comments[todo_index].complaint[complaint_index].use;
    };

    $scope.updateTodo = function(todo_index) {
        var target_todo = angular.copy($scope.data.comments[todo_index]);

        if(target_todo.editable){
            if(!target_todo.comment.trim().length) {
                $rootScope.$emit('notify', {msg : '备注不能为空'});
                return;
            }
            if(!target_todo.date_proc || !target_todo.expected_hour || target_todo.expected_hour < 0 || target_todo.expected_hour > 23 ) {
                $rootScope.$emit('notify', {msg : '请正确输入处理时间'});
                return;
            }
            target_todo.date_proc = formatDate(target_todo.date_proc);
            if(target_todo.expected_hour < 10){
                target_todo.date_proc = target_todo.date_proc + ' 0'+ target_todo.expected_hour + ':00:00';
            } else {
                target_todo.date_proc = target_todo.date_proc + ' '+ target_todo.expected_hour + ':00:00';
            }
            var complaint = [];
            if(target_todo.type == '7') {
                for(var type_index in target_todo.complaint){
                    if(target_todo.complaint[type_index].use) {
                        complaint.push({
                            complaint_id:target_todo.complaint[type_index].complaint_id?target_todo.complaint[type_index].complaint_id:'',
                            detail_type:target_todo.complaint[type_index].detail_type?target_todo.complaint[type_index].detail_type:'',
                            detail_md:target_todo.complaint[type_index].detail_md?target_todo.complaint[type_index].detail_md:'',
                            complaint_type:parseInt(type_index)+1,
                            complaint_md:target_todo.complaint[type_index].complaint_md?target_todo.complaint[type_index].complaint_md:'',
                        });
                    }
                }
            }
            target_todo.complaint = complaint;
            $http.post($request_urls.orderComments, target_todo).success(function(data) {
                if(data.code == 200) {
                    $scope.data.comments[todo_index].editable = false;
                    $scope.data.comments[todo_index].date_proc = target_todo.date_proc;
                    $scope.data.comments[todo_index].user_name = data.data.user_name;
                    $scope.data.comments[todo_index].comment_id = data.data.comment_id;
                    for(var complaint_index in data.data.complaint) {
                        $scope.data.comments[todo_index].complaint[parseInt(data.data.complaint[complaint_index].complaint_type) - 1].complaint_id = data.data.complaint[complaint_index].complaint_id;
                    }
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        } else {
            $scope.data.comments[todo_index].editable = true;
        }
    };

    $scope.toggleSelection = function(item, collection) {
        var index = collection.indexOf(item);
        if(index > -1) {
            collection.splice(index, 1);
        } else {
            collection.push(item);
        }
    };

    $scope.deleteTodo = function(todo_index) {
        var target_todo = $scope.data.comments[todo_index];

        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        if(target_todo.comment_id != ''){
            $http.delete($request_urls.orderComments + '&comment_id=' + target_todo.comment_id).success(function(data) {
                if(data.code == 200) {

                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
        $scope.data.comments.splice(todo_index,1);
    };

    $scope.updateAlert = function(use_alert) {
        if(typeof use_alert == "undefined") {
            var resolved = true;
            if($scope.data.comments.length > 0) {
                resolved = false;
            }

            $scope.local.menus['2'].alert = !resolved;
        } else {
            $scope.local.menus['2'].alert = use_alert;
        }
    };

    $scope.changeTodoStatus = function(todo_index) {
        var target_todo = $scope.data.comments[todo_index];
        var process_status = 1;
        var finished_status = 2; //设置成已处理

        if(target_todo.editable) {
            $rootScope.$emit('notify', {msg : '请保存后再修改状态'});
            return;
        }

        if(target_todo.proc_status == process_status) { //Process only if status is process
            target_todo.proc_status = finished_status;
        } else if(target_todo.proc_status == finished_status) {
            target_todo.proc_status = process_status;
        }
        $http.post($request_urls.orderComments, target_todo).success(function(data) {
            if(data.code == 200) {
                //If everything is resolved
                $scope.updateAlert();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
                //If error, revert back to process status
                target_todo.proc_status = process_status;
            }

            target_todo.status_name = $scope.local.todo_status[target_todo.proc_status].label;
        });
    };
    //endregion


    $scope.init();
};
directives.refundAmount = function() {
    var linkFunc = function(scope, elm, attrs, ctrl) {
        ctrl.$parsers.unshift(function(viewValue) {
            var result;
            viewValue = parseFloat(viewValue);
            if(scope.return.return_type == 'return_and_refund') {
                result = viewValue <= parseFloat(scope.refundable) ? viewValue : undefined;
            } else if(scope.return.return_type == 'partial_refund') {
                result = viewValue < parseFloat(scope.refundable) ? viewValue : undefined;
            } else if(scope.return.return_type == 'record_refund') {
                result = viewValue > 0 ? viewValue : undefined;
            } else { //如果没有选择类型，永远是校验失败
                result = undefined;
            }

            ctrl.$setValidity('amount', !!result);
            return result;
        });
    };

    return {
        link    : linkFunc,
        scope   : {
            'return'     : '=',
            'refundable' : '='
        },
        require : 'ngModel'
    };
};

app.controller('OrderEditCtrl', [
    '$scope', '$rootScope', '$http', '$q', '$sce', '$timeout', controllers.OrderEditCtrl
]);
app.directive('refundAmount', directives.refundAmount);