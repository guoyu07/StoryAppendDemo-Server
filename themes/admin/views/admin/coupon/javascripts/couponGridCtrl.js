controllers.CouponGridCtrl = function($scope, $rootScope) {
    var paging = {
        start : 0,
        limit : 15
    };

    $scope.data = {};
    $scope.local = {
        search_text   : '',
        coupon_status : {
            '0' : {
                label      : '禁用',
                class_name : 'error'
            },
            '1' : {
                label      : '启用',
                class_name : 'default'
            }
        },
        use_type      : {
            '1' : '现金抵用',
            '2' : '渠道OP',
            '3' : '测试'
        },
        grid_options  : {
            data    : [],
            table   : {
                table_id : 'coupon_grid'
            },
            label   : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == 'name') {
                        return '<a href="' + $request_urls.editCouponUrl + record.coupon_id + '">' + record.name +
                               '</a>';
                    } else if(col.name == 'status') {
                        var status = $scope.local.coupon_status[record.status];
                        return '<span class="grid-status ' + status.class_name + '">' + status.label + '</span>';
                    } else if(col.name == 'use_type') {
                        return $scope.local.use_type[record.use_type];
                    } else {
                        return !!record[col.name] ? record[col.name].toString() : '';
                    }
                }
            },
            query   : {
                sort          : {
                    'date_added' : 0
                },
                paging        : angular.copy(paging),
                query_filter  : {},
                record_filter : ['name', 'code', 'date_added', 'use_type', 'status', 'coupon_id']
            },
            request : {
                api_url : $request_urls.fetchCoupons
            },
            columns : [
                {
                    name     : 'coupon_id',
                    width    : '10%',
                    label    : '优惠券ID',
                    use_sort : true
                },
                {
                    name     : 'name',
                    width    : '35%',
                    label    : '优惠券名称',
                    use_sort : true
                },
                {
                    name     : 'code',
                    width    : '15%',
                    label    : '优惠券代码',
                    use_sort : true
                },
                {
                    name     : 'date_added',
                    width    : '15%',
                    label    : '创建日期',
                    use_sort : true
                },
                {
                    name     : 'use_type',
                    width    : '8%',
                    label    : '使用类型',
                    use_sort : true
                },
                {
                    name     : 'status',
                    width    : '7%',
                    label    : '状态',
                    use_sort : true
                }
            ]
        }
    };

    $scope.init = function() {
        $rootScope.$emit('loadStatus', false);
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '查找优惠券'
            }
        });
    };
    $scope.goToCoupon = function(coupon_id) {
        coupon_id = coupon_id || '';
        window.location = $request_urls.editCouponUrl + coupon_id;
    };
    $scope.searchCoupon = function() {
        if(!!$scope.local.search_text) {
            $scope.local.grid_options.query.paging = angular.copy(paging);
            $scope.local.grid_options.query.query_filter = {
                search_text : angular.copy($scope.local.search_text)
            };
            $scope.local.grid_options.fetchData();
        }
    };

    $scope.init();
};

app.controller('CouponGridCtrl', ['$scope', '$rootScope', controllers.CouponGridCtrl]);