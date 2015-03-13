var productEditFactory = function( $http, $q ) {
  var factory = {};

  factory.initial_menu = 'editProductInfo';

  factory.product_type = {
    single_ticket : '1',
    combo         : '2',
    city_pass     : '3',
    easy_pass     : '4',
    tour          : '5',
    coupon        : '6',
    hotel         : '7',
    hotel_package : '8',
    multi_days    : '9',
    car           : '10'
  };

  factory.menu_type = {
    all            : "all",
    normal_product : [
      factory.product_type.single_ticket, factory.product_type.city_pass, factory.product_type.easy_pass,
      factory.product_type.tour, factory.product_type.coupon, factory.product_type.combo, factory.product_type.multi_days,
      factory.product_type.multi_days
    ],
    hotel          : factory.product_type.hotel,
    hotel_package  : factory.product_type.hotel_package
  };

  factory.menu = [
    {
      id    : 'productRules',
      group : true,
      label : '商品规则'
    },
    {
      id       : 'editProductInfo',
      group    : false,
      label    : '基本信息',
      status   : 'green',
      type     : [factory.menu_type.all],
      sections : ['editProductInfo']
    },
    {
      id       : 'editProductRules',
      group    : false,
      label    : '商品主要规则',
      status   : 'green',
      type     : [factory.menu_type.all],
      sections : ['editProductRules']
    },
    {
      id       : 'editProductPrice',
      group    : false,
      label    : '价格体系',
      status   : 'green',
      type     : [factory.menu_type.all],
      sections : ['editProductPrice']
    },
    {
      id    : 'orderRules',
      group : true,
      label : '下单'
    },
    {
      id       : 'editPassengerInfo',
      group    : false,
      label    : '出行人信息',
      status   : 'green',
      type     : [factory.menu_type.all],
      sections : ['selectPassengerInfoType', 'editPassengerInfo']
    },
    {
      id    : 'ShippingRules',
      group : true,
      label : '发货'
    },
    {
      id       : 'editVoucherRule',
      group    : false,
      label    : 'Voucher',
      status   : 'green',
      type     : [factory.menu_type.normal_product, factory.menu_type.hotel],
      sections : ['editVoucherRule']
    },
    {
      id       : 'editShippingConfig',
      group    : false,
      label    : '发货方式',
      status   : 'green',
      type     : [factory.menu_type.normal_product, factory.menu_type.hotel],
      sections : ['editShippingConfig']
    },
    {
      id    : 'productDesc',
      group : true,
      label : '商品信息'
    },
    {
      id       : 'editProductDesc',
      group    : false,
      label    : '商品描述',
      status   : 'green',
      type     : [factory.menu_type.normal_product, factory.menu_type.hotel_package],
      sections : ['editProductDesc']
    },
    {
      id       : 'editProductHotel',
      group    : false,
      label    : '酒店详情',
      status   : 'green',
      type     : [factory.menu_type.hotel],
      sections : ['editProductHotel']
    },
    {
      id       : 'editHotelRoomType',
      group    : false,
      label    : '房型介绍',
      status   : 'green',
      type     : [factory.menu_type.hotel],
      sections : ['editHotelRoomType']
    },
    {
      id       : 'editProductImage',
      group    : false,
      label    : '图片上传',
      type     : [factory.menu_type.all],
      sections : ['editProductImage']
    },
    {
      id       : 'productTourPlan',
      group    : false,
      label    : '商品图文',
      status   : 'green',
      type     : [factory.menu_type.all],
      sections : ['productTourPlan']
    },
    {
      id       : 'editProductAlbum',
      group    : false,
      label    : '地点关联',
      type     : [factory.menu_type.normal_product, factory.menu_type.hotel_package],
      sections : ['editProductAlbum']
    },

    {
      id       : 'editProductQna',
      group    : false,
      label    : '常见问题',
      status   : 'green',
      type     : [factory.menu_type.normal_product, factory.menu_type.hotel_package],
      sections : ['editProductQna']
    },
    {
      id       : 'editProductComment',
      group    : false,
      label    : '商品评论',
      status   : 'green',
      type     : [factory.menu_type.all],
      sections : ['editProductComment']
    },
    {
      id       : 'editProductBundle',
      group    : false,
      label    : '商品挂接',
      status   : 'green',
      type     : [factory.menu_type.hotel_package],
      sections : ['editProductBundle']
    },

    {
      id       : 'editProductTripPlan',
      group    : false,
      label    : '行程安排',
      status   : 'green',
      type     : [factory.menu_type.hotel_package],
      sections : ['editProductTripPlan']
    },
    {
      id    : 'productOperation',
      group : true,
      label : '运营'
    },
    {
      id       : 'editProductRelated',
      group    : false,
      label    : '相关商品',
      type     : [factory.menu_type.all],
      sections : ['editProductRelated']
    },
    {
      id       : 'editProductCoupon',
      group    : false,
      label    : '优惠券挂接',
      status   : 'green',
      type     : [factory.menu_type.all],
      sections : ['editProductCoupon']
    },
    {
      id       : 'editProductSeo',
      group    : false,
      label    : '商品SEO',
      status   : 'green',
      type     : [factory.menu_type.all],
      sections : ['editProductSeo']
    }
  ];
  factory.sections = {
    'selectPassengerInfoType'   : {
      title       : '出行人信息',
      description : '* 请选择一种出行人收录方式'
    },
    'editProductSaleRange'      : {
      title       : '日期规则',
      description : '* 请按照步骤填写'
    },
    'editProductRedeem'         : {
      title       : '有效期规则',
      description : '* 请按照步骤填写'
    },
    'editProductReturn'         : {
      title       : '退款规则',
      description : '* 请按照步骤填写'
    },
    'editProductName'           : {
      title       : '基本信息',
      description : '* 请填写完整并仔细检查'
    },
    'editProductDesc'           : {
      title       : '商品描述',
      description : '* 请填写完整并仔细检查'
    },
    'editPackageDesc'           : {
      title       : '包含简介',
      description : ''
    },
    'editProductLandsAlbum'     : {
      title       : '景点关联',
      description : '* 您所关联的景点图片将会直接显示在商品图片区域，以供编辑。'
    },
    'editProductSpecialAlbum'   : {
      title       : '特殊点关联',
      description : '* 您所关联的接送点，将会显示在分组中，请您对其进行命名。'
    },
    'editProductLandsList'      : {
      title       : '文字景点列表',
      description : '* 你可以添加不同的分组。'
    },
    'editProductRelated'        : {
      title       : '相关商品',
      description : '* 填写商品ID进行关联为用户提供更多选择'
    },
    'editVoucherRule'           : {
      title       : '基本信息',
      description : '* 请填写完整并仔细检查'
    },
    'editVoucherOthers'         : {
      title       : '其他显示信息',
      description : ''
    },
    'editShippingConfig'        : {
      title       : '发货方式',
      description : ''
    },
    'editShippingConfigDetails' : {
      title       : '发货方式配置',
      description : ''
    }
  };

  factory.passenger_info_types = {
    'leader'              : {
      title       : '领队',
      description : '不论几人出行，只需填写一个人的信息',
      need_info   : {
        need_passenger_num : "1",
        need_lead          : "1"
      }
    },
    'everyone'            : {
      title       : '所有人',
      description : '每位出行人都需要填写信息',
      need_info   : {
        need_passenger_num : "0",
        need_lead          : "0"
      }
    },
    'leader_and_everyone' : {
      title       : '领队 ＋ 所有人',
      description : '需填写领队人信息加上每位出行人信息',
      need_info   : {
        need_passenger_num : "0",
        need_lead          : "1"
      }
    }
  };

  factory.getNeed = function( arg1 ) {
    return this.passenger_info_types[arg1].need_info;
  };

  factory.getRoute = function( arg1, arg2 ) {
    var result = '';
    angular.forEach( this.passenger_info_types, function( value, key ) {
      if( value.need_info.need_passenger_num == arg1 && value.need_info.need_lead == arg2 ) result = key;
    } );

    return result;
  };

  factory.getCurrentMenu = function( ctrlName ) {
    for( var index in this.menu ) {
      if( this.menu[index].group ) continue;
      if( index == ctrlName || this.menu[index].sections.indexOf( ctrlName ) !== -1 ) {
        return this.menu[index].id;
      }
    }
  };

  factory.getPassengerRule = function() {
    var defer = $q.defer();

    $http.get( request_urls.getProductPassengerRule ).success( function( data ) {
      defer.resolve( data );
    } );

    return defer.promise;
  };

  factory.getProductPassengerRule = function() {
    var that = this;
    var defer = $q.defer();
    var separator = ',';

    var processAllRules = function( data ) {

      if( data.code == 200 ) {

        var allRules = [];
        var currentRule, lastTitle = '';

        for( var key in data.data ) {
          currentRule = data.data[key];
          if( currentRule.group_title != lastTitle ) {
            allRules.push( {
                             'group'       : true,
                             'group_title' : currentRule.group_title
                           } );
            lastTitle = currentRule.group_title;
          }

          currentRule.group = false;
          allRules.push( currentRule );
        }

        that.getPassengerRule().then( function( data ) {

          if( data.code == 200 ) {

            data.data.lead_field_items = data.data.lead_fields.split( separator );
            for( var i = 0, len = data.data.rule_item.length; i < len; i++ ) {
              data.data.rule_item[i].field_items = data.data.rule_item[i].fields.split( separator );
            }

            defer.resolve( {
                             all_criteria : allRules,
                             other_rules  : data.data
                           } );

          }

        } );

      }

    };

    $http.get( request_urls.getPassengerMetaData, {cache : true} ).success( processAllRules );

    return defer.promise;
  };

  factory.is_GTA = false;
  factory.is_combo = false;
  factory.is_CPIC = false;

  $http.get( request_urls.getProduct ).success( function( data ) {
    factory.product_info = data.code == 200 ? data.data : {};
    factory.is_GTA = factory.product_info.supplier_id == 11;
    factory.is_CPIC = factory.product_info.supplier_id == 89;
    factory.is_combo = factory.product_info.is_combo == 1;
  } );

  return factory;
};


angular.module( 'ProductEditApp' ).factory( 'ProductEditFactory', ['$http', '$q', '$location', productEditFactory] );