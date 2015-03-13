(function() {
    angular_routes.product = function($routeProvider) {
            //商品规则
        $routeProvider.when('/ProductBasicInfo/:info_type?', { //基本信息
            resolve     : p_resolve.ProductBasicInfo,
            controller  : 'ProductBasicInfoCtrl',
            templateUrl : 'ProductBasicInfo.html'
            //商品描述
        }).when('/ProductService/:service_type?', { //服务说明
            resolve     : p_resolve.ProductService,
            controller  : 'ProductServiceCtrl',
            templateUrl : 'ProductService.html'
        }).when('/ProductNotice/:notice_type?', { //购买须知
            resolve     : p_resolve.ProductNotice,
            controller  : 'ProductNoticeCtrl',
            templateUrl : 'ProductNotice.html'
        }).when('/ProductRedeem/:redeem_type?', { //兑换及使用
            resolve     : p_resolve.ProductRedeem,
            controller  : 'ProductRedeemCtrl',
            templateUrl : 'ProductRedeem.html'
        }).when('/ProductQna', { //常见问题
            resolve     : p_resolve.ProductQna,
            controller  : 'ProductQnaCtrl',
            templateUrl : 'ProductQna.html'
        }).when('/ProductComment', { //商品评论
            resolve     : p_resolve.ProductComment,
            controller  : 'ProductCommentCtrl',
            templateUrl : 'ProductComment.html'
        }).when('/ProductBundle', { //商品评论
            resolve     : p_resolve.ProductBundle,
            controller  : 'ProductBundleCtrl',
            templateUrl : 'ProductBundle.html'
        }).when('/HotelRoom', { //商品评论
            resolve     : p_resolve.HotelRoom,
            controller  : 'HotelRoomCtrl',
            templateUrl : 'HotelRoom.html'
            //商品价格
        }).when('/ProductFeedback', { //商品回访
            resolve     : p_resolve.ProductFeedback,
            controller  : 'ProductFeedbackCtrl',
            templateUrl : 'ProductFeedback.html'
        }).when('/ProductPrice/:price_type?/:price_plan_id?', { //价格体系
            resolve     : p_resolve.ProductPrice,
            controller  : 'ProductPriceCtrl',
            templateUrl : 'ProductPrice.html'
            //商品运营
        }).when('/ProductSeo', {
            resolve     : p_resolve.ProductSeo,
            controller  : 'ProductSeoCtrl',
            templateUrl : 'ProductSeo.html'
        }).when('/RelatedProduct', {
            resolve     : p_resolve.RelatedProduct,
            controller  : 'RelatedProductCtrl',
            templateUrl : 'RelatedProduct.html'
        }).when('/CouponTemplate', {
            resolve     : p_resolve.CouponTemplate,
            controller  : 'CouponTemplateCtrl',
            templateUrl : 'CouponTemplate.html'
        }).otherwise({
            redirectTo : '/ProductBasicInfo/name'
        });
    };

    var initProductEdit = function($rootScope, $http) {
        $rootScope.product = $root_product;
        $rootScope.finished = true;
        //$http.get($request_urls.getProductSummary).success(function(data) {
        //    $rootScope.finished = true;
        //    if(data.code == 200) {
        //        $rootScope.product = data.data;
        //    } else {
        //        $rootScope.$emit('errorStatus', true);
        //    }
        //});
    };

    app.config(['$routeProvider', angular_routes.product]).run(['$rootScope', '$http', initProductEdit]);
})();