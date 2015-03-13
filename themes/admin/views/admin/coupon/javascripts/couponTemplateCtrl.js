controllers.CouponTemplateCtrl = function($scope, $http, $rootScope, $filter) {
    $scope.data = {
        coupon   : {},
        template : {}
    };
    $scope.local = {
        overlay      : {
            has_overlay  : false,
            grid_options : {
                data    : [],
                label   : {
                    getHead : function(col) {
                        return col.label;
                    },
                    getBody : function(col, i, record) {
                        if(col.name == 'coupon') {
                            return '<a href="' + $request_urls.editCouponUrl + record.coupon_id + '">' +
                                   record.coupon_id + ' － ' +
                                   record.name + '</a>';
                        } else if(col.name == 'amount') {
                            return $filter('number')(record.discount, 2) + ( record.type == 'P' ? '%' : 'RMB' );
                        } else {
                            return record[col.name].toString();
                        }
                    }
                },
                request : {
                    api_url : $request_urls.templateInstances
                },
                columns : [
                    {
                        name  : 'order_id',
                        width : '10%',
                        label : '订单号'
                    },
                    {
                        name  : 'coupon',
                        width : '45%',
                        label : '优惠券'
                    },
                    {
                        name  : 'amount',
                        width : '15%',
                        label : '折扣金额'
                    },
                    {
                        name  : 'date_added',
                        width : '30%',
                        label : '使用日期'
                    }
                ]
            }
        },
        dropdown     : {
            start_offset : {
                values  : {
                    input  : '',
                    option : ''
                },
                options : {
                    placeholder : '下单起',
                    items       : {
                        'day'   : '日',
                        'month' : '月',
                        'year'  : '年'
                    }
                }
            },
            end_range    : {
                values  : {
                    input  : '',
                    option : ''
                },
                options : {
                    placeholder : '有效期至',
                    items       : {
                        'day'   : '日',
                        'month' : '月',
                        'year'  : '年'
                    }
                }
            }
        },
        breadcrumb   : {
            back : {},
            body : {
                content : '编辑优惠券模版'
            }
        },
        section_head : {
            info : {
                title    : '基本信息',
                is_edit  : false,
                updateCb : function() {
                    if($scope.template_info.$pristine && !$scope.local.new_template) {
                        $scope.local.section_head.info.is_edit = false;
                    } else if($scope.template_info.$valid) {
                        $scope.updateTemplate();
                    } else {
                        $rootScope.$emit('notify', {msg : '优惠券模版内容有误。请检查完再提交'});
                    }
                },
                editCb   : function() {
                    $scope.local.section_head.info.is_edit = true;
                }
            }
        },
        radio_switch : {
            status         : {
                name  : 'status',
                items : {
                    '0' : '无效',
                    '1' : '有效'
                }
            },
            date_type      : {
                name  : 'date_type',
                items : {
                    '0' : '绝对日期',
                    '1' : '自下单日期起'
                }
            },
            customer_limit : {
                name  : 'customer_limit',
                items : {
                    '0' : '不限制',
                    '1' : '下单用户使用'
                }
            }
        },
        edit_coupon  : $request_urls.editCouponUrl,
        new_template : false
    };

    function initDate(data) {
        $scope.data.template.date_start = data.date_start;
        $scope.data.template.date_end = data.date_end;
        $scope.data.template.dates = {
            start : $scope.data.template.date_start == '0000-00-00' ? new Date() :
                    new Date($scope.data.template.date_start),
            end   : $scope.data.template.date_end == '0000-00-00' ? new Date() :
                    new Date($scope.data.template.date_end)
        };
    }

    function initDropdown(item, value) {
        if(value) {
            item.values.input = parseInt(value);
            item.values.options = value.replace(/[0-9]/g, '');
            item.options.updateLabel();
        } else {
            item.values = {
                input  : '0',
                option : 'day'
            };
        }
    }

    function formatDropdown(values) {
        return values.input + values.option;
    }

    $scope.init = function() {
        $http.get($request_urls.productCouponTemplate).success(function(data) {
            $rootScope.$emit('setBreadcrumb', $scope.local.breadcrumb);

            if(data.code == 400) {
                var parts = window.location.search.substr(1).split('&');
                parts = parts.map(function(elem) {
                    var vals = elem.split('=');
                    return {
                        label : vals[0],
                        value : vals[1]
                    };
                });
                function findPart(label) {
                    for(var i = 0, len = parts.length; i < len; i++) {
                        if(parts[i].label == label) return parts[i].value;
                    }
                }

                $scope.data.coupon = false;
                $scope.data.template = {
                    id             : "",
                    status         : "1",
                    quantity       : "1",
                    product_id     : findPart('product_id'),
                    product_name   : findPart('product_name'),
                    customer_limit : "1",
                    //Date
                    date_type      : "0",
                    date_end       : formatDate(new Date()),
                    date_start     : formatDate(new Date()),
                    end_range      : "0day",
                    start_offset   : "0day"
                };

                $scope.local.section_head.info.is_edit = true;
                $scope.local.new_template = true;
            } else if(data.code == 200) {
                $scope.data.coupon = angular.copy(data.data.template_coupon);
                $scope.data.template = data.data;
                delete $scope.data.template.template_coupon;
            } else {
                $rootScope.$emit('errorStatus', true);
            }

            if(data.code == 200 || data.code == 400) {
                $scope.data.template.quantity = +$scope.data.template.quantity;
                initDate($scope.data.template);
                if($scope.data.template.start_offset) {
                    $scope.local.dropdown.start_offset.values.input = parseInt($scope.data.template.start_offset);
                    $scope.local.dropdown.start_offset.values.option = $scope.data.template.start_offset.replace(/[0-9]/g, '');
                    $scope.local.dropdown.start_offset.options.updateLabel();
                } else {
                    $scope.local.dropdown.start_offset.values.input = '0';
                    $scope.local.dropdown.start_offset.values.option = 'day';
                }
                if($scope.data.template.end_range) {
                    $scope.local.dropdown.end_range.values.input = parseInt($scope.data.template.end_range);
                    $scope.local.dropdown.end_range.values.option = $scope.data.template.end_range.replace(/[0-9]/g, '');
                    $scope.local.dropdown.end_range.options.updateLabel();
                } else {
                    $scope.local.dropdown.end_range.values.input = '0';
                    $scope.local.dropdown.end_range.values.option = 'day';
                }

                $rootScope.$emit('loadStatus', false);
            }
        });
    };

    $scope.updateTemplate = function() {
        var post_data = angular.copy($scope.data.template);
        post_data.date_start = formatDate(post_data.dates.start);
        post_data.date_end = formatDate(post_data.dates.end);
        post_data.start_offset = formatDropdown($scope.local.dropdown.start_offset.values);
        post_data.end_range = formatDropdown($scope.local.dropdown.end_range.values);

        $http.post($request_urls.productCouponTemplate, post_data).success(function(data) {
            if(data.code == 200) {
                if($scope.local.new_template) {
                    window.location = $request_urls.editCouponTemplateUrl + data.data;
                }

                initDate(data.data);
                $scope.data.coupon = data.data.template_coupon;
                $scope.local.section_head.info.is_edit = false;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.afterStart = function(date) {
        if(!$scope.data.template.date_start) return true;
        var start_date = new Date($scope.data.template.date_start);
        return date.getTime() > start_date.getTime();
    };

    $scope.toggleHistory = function(has_overlay) {
        $scope.local.overlay.has_overlay = has_overlay;
        $rootScope.$emit('overlay', has_overlay);
    };

    $scope.formatDropdown = function(item) {
        var dropdown = $scope.local.dropdown[item];
        return dropdown.values.input + dropdown.options.items[dropdown.values.option];
    };

    $scope.selectCoupon = function(use_new) {
        window.open(use_new ? $request_urls.editCouponUrl : $request_urls.listCouponUrl, '_blank');
    };

    $scope.init();
};

app.controller('CouponTemplateCtrl', ['$scope', '$http', '$rootScope', '$filter', controllers.CouponTemplateCtrl]);