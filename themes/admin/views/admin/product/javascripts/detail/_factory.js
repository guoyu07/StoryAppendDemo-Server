factories.productEdit = function($rootScope, commonFactory) {
    var factory = {};

    //商品信息
    factory.product_status = [
        {
            value      : '1',
            label      : '编辑中',
            class_name : 'edit'
        },
        {
            value      : '2',
            label      : '待审核',
            class_name : 'review'
        },
        {
            value      : '3',
            label      : '已上架',
            class_name : 'onsale'
        },
        {
            value      : '4',
            label      : '禁用',
            class_name : 'offsale'
        }
    ];

    //菜单
    factory.menu_items = [
        {
            label : '商品说明'
        },
        {
            value : 'ProductBasicInfo',
            label : '基本信息'
        },
        {
            value : 'ProductService',
            label : '服务说明'
        },
        {
            value : 'ProductNotice',
            label : '购买提醒'
        },
        {
            value : 'ProductRedeem',
            label : '兑换及使用'
        },
        {
            value : 'ProductQna',
            label : '常见问题'
        },
        {
            value : 'ProductComment',
            label : '用户点评'
        },
        {
            value  : 'editProductHotel',
            label  : '酒店详情',
            is_old : true
        },
        {
            value  : 'HotelRoom',
            label  : '房型介绍'
        },
        {
            value  : 'editProductBundle',
            label  : '商品挂接',
            is_old : true
        },
        {
            value  : 'editProductTripPlan',
            label  : '行程安排',
            is_old : true
        },
        /*{ 到服务说明
         value : 'TripPlan',
         label : '行程安排'
         },
         {
         value : 'ProductBundle',
         label : '商品挂接'
         },
         {
         value : 'Hotel', 到服务说明
         label : '酒店详情'
         },
         {
         value : 'HotelRoom',
         label : '房型介绍'
         },*/
        {
            label : '价格'
        },
        {
            value : 'ProductPrice',
            label : '价格体系'
        },
        {
            label : '供应商要求'
        },
        {
            value  : 'editPassengerInfo',
            label  : '出行人信息',
            is_old : true
        },
        {
            value  : 'editVoucherRule',
            label  : 'Voucher',
            is_old : true
        },
        {
            value  : 'editShippingConfig',
            label  : '发货方式',
            is_old : true
        },
        {
            label : '商品运营'
        },
        {
            value : 'ProductSeo',
            label : '商品SEO'
        },
        {
            value : 'RelatedProduct',
            label : '相关商品'
        },
        {
            value : 'CouponTemplate',
            label : '优惠券挂接'
        }
    ];

    factory.default_list = [
        'editProductHotel', 'HotelRoom', 'editProductBundle', 'editProductTripPlan', 'productTourPlan'
    ];
    factory.type_blacklist = {
        '1'  : factory.default_list,
        '2'  : factory.default_list,
        '3'  : factory.default_list,
        '4'  : factory.default_list,
        '5'  : factory.default_list,
        '6'  : factory.default_list,
        '7'  : [
            'editProductBundle', 'editProductTripPlan', 'ProductService', 'ProductNotice', 'ProductRedeem',
            'ProductQna', 'ProductComment', 'ProductSeo', 'RelatedProduct', 'CouponTemplate'
        ],
        '8'  : ['editProductHotel', 'HotelRoom', 'editShippingConfig', 'editVoucherRule'],
        '9'  : factory.default_list,
        '10' : factory.default_list
    };


    factory.getProductStatus = function() {
        return factory.product_status;
    };
    factory.getMenuItems = function() {
        return factory.menu_items;
    };
    factory.isInBlacklist = function(menu_value) {
        if(!$rootScope.product) return;
        var product_type = $rootScope.product.type;

        return (product_type in factory.type_blacklist && factory.type_blacklist[product_type].indexOf(menu_value) > -1);
    };

    return factory;
};

app.factory('ProductEditFactory', ['$rootScope', 'commonFactory', factories.productEdit]);
