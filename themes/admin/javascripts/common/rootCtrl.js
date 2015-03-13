controllers.rootCtrl = function($scope, $rootScope, $location, $sce) {
    $scope.local = {
        menu               : [
            {
                link_url : pathinfo.base_dir + 'product/index',
                label    : '商品'
            },
            {
                link_url : pathinfo.base_dir + 'order/index',
                label    : '订单'
            },
            {
                link_url : pathinfo.base_dir + 'article/index',
                label    : '文章'
            },
            {
                link_url : pathinfo.base_dir + 'statistics/index',
                label    : '统计'
            },
            {
                label : '运营',
                items : [
                    {
                        link_url : pathinfo.base_dir + 'home/index',
                        label    : '首页管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'country/index',
                        label    : '国家管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'city/index',
                        label    : '城市管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'promotion/index',
                        label    : '活动管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'operation/todo',
                        label    : 'SEO待办'
                    },
                    {
                        link_url : pathinfo.base_dir + 'edm/index',
                        label    : 'EDM管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'errorPage/index',
                        label    : '错误页面管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'expert/index',
                        label    : '专家管理'
                    }
                ]
            },
            {
                label : '其他',
                items : [
                    {
                        link_url : pathinfo.base_dir + 'supplier/index',
                        label    : '供应商管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'coupon/index',
                        label    : '优惠券管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'stock/index',
                        label    : '库存管理'
                    },
                    {
                        link_url : pathinfo.base_dir + 'invoice/index',
                        label    : '对账管理'
                    }
                ]
            }
        ],
        home_url           : pathinfo.base_dir + 'product/index',
        is_test            : $fe_options.is_test,
        show_error         : false,
        show_splash        : false,
        has_overlay        : false,
        overlay_options    : {
            type         : 'alert',
            message      : '',
            buttons      : '',
            buildOverlay : {}
        },
        breadcrumb_options : {
            back : {
                content      : false,
                part_content : false
            },
            body : {
                content : '我是默认的breadcrumb'
            }
        }
    };

    $rootScope.$on('notify', function(e, data) {
        //触发默认的overlay，目前只限制于alert
        $scope.local.overlay_options.message = $sce.trustAsHtml(data.msg);
        $scope.local.overlay_options.buildOverlay();
    });
    $rootScope.$on('overlay', function(e, data) {
        $scope.local.has_overlay = !!data;
    });
    $rootScope.$on('loadStatus', function(e, is_loading) {
        $scope.local.has_overlay = !!is_loading;
        $scope.local.show_splash = !!is_loading;
    });
    $rootScope.$on('errorStatus', function(e, is_error) {
        $scope.local.has_overlay = !!is_error;
        $scope.local.show_error = !!is_error;
    });
    $rootScope.$on('setBreadcrumb', function(e, data) {
        data.back.content = data.back.content || '<span class="i i-arrow-left"></span>';
        data.back.content = $sce.trustAsHtml(data.back.content);
        data.back.part_content = data.back.part_content || '';
        data.back.part_content = $sce.trustAsHtml(data.back.part_content);
        data.body.content = data.body.content || '';
        data.body.content = $sce.trustAsHtml(data.body.content);
        data.body.right_content = data.body.right_content || '';
        data.body.right_content = $sce.trustAsHtml(data.body.right_content);
        data.back.clickCb = data.back.clickCb || function() {
            window.history.back();
        };

        $scope.local.breadcrumb = data;
    });


    $rootScope.$emit('loadStatus', true);
};
factories.requestRejector = function($q) {
    var white_list = [
        //Product
        '/product/changeEditingState',
        // Article
        '/article/articles',
        //EDM
        '/EDM/getEDMList',
        //Error Page
        '/errorPage/getErrorPageList',
        //Promotion
        '/promotion/getPromotionList', '/promotion/updatePromotionBanner',
        //Coupon
        '/coupon/coupons', '/coupon/coupon', '/coupon/getCouponHistory', '/coupon/couponProduct',
        '/coupon/getCouponInstances', '/coupon/getLimitIdName', '/coupon/getCouponInstances',
        '/coupon/productGiftCoupon', '/coupon/productCouponRule', '/coupon/getProductGiftCouponList',
        '/coupon/template',
        //Order
        '/order/getOrderTotals', '/order/getOrderList', '/order/getOrderTotalsAndSupplierTotals',
        '/order/uploadVoucher', '/order/deleteVoucher', '/order/doShipping', '/order/rebookingOrder',
        '/order/returnOrder', '/order/saveRefund', '/order/refuseReturn', '/order/getDeparturesByDate',
        '/order/saveShippingInfo', '/order/saveTourDate', '/order/savePassengers', '/order/saveContacts',
        '/order/OrderComments', '/order/refundOrder', '/order/bookingOrder',
        //Stock
        '/stock/stockList',
        //Statistics
        '/statistics/orderSummary', '/statistics/orderSearch', '/statistics/orderListByDate',
        //Invoice
        '/invoice/getSupplierInvoiceList', '/invoice/getInvoiceOperationList', '/invoice/searchInvoiceOrder'
    ];
    var requestRejector = {
        request : function(config) {
            var url = ( config.url.indexOf('/hitour') == 0 ) ? config.url.substr('/hitour'.length) : config.url;
            url = url.split('?')[0].substr('/admin'.length);
            if(['post', 'delete'].indexOf(config.method.toLowerCase()) > -1 && white_list.indexOf(url) == -1) {
                return $q.reject('requestRejector');
            } else {
                return config;
            }
        }
    };

    return requestRejector;
};
interceptors.testRequestRejector = function($httpProvider) {
    $httpProvider.interceptors.push('requestRejector');
};
factories.responseError = function($q, $rootScope) {
    var responseError = {
        response      : function(response) {
            if(response.status == 200) { //Only test for HTTP status
                return response;
            } else {
                $rootScope.$emit('errorStatus', true);
                return $q.reject(response);
            }
        },
        responseError : function(response) {
            $rootScope.$emit('errorStatus', true);
            $q.reject(response);
        }
    };

    return responseError;
};
interceptors.requestError = function($httpProvider) {
    $httpProvider.interceptors.push('responseError');
};
helpers.getRouteTemplateName = function(route) {
    return route.loadedTemplateUrl.substr(0, route.loadedTemplateUrl.length - '.html'.length);
};

app.controller('rootCtrl', ['$scope', '$rootScope', '$location', '$sce', controllers.rootCtrl]);
app.factory('responseError', ['$q', '$rootScope', factories.responseError]);
app.config(['$httpProvider', interceptors.requestError]);
if(!!$fe_options.is_test) {
    app.factory('requestRejector', ['$q', factories.requestRejector]);
    app.config(['$httpProvider', interceptors.testRequestRejector]);
}