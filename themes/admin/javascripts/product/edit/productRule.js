var editProductRulesCtrl = function( $scope, $rootScope, $http, $route ) {
  //Helper
  var _items_options = {
    'day'   : '日',
    'month' : '月',
    'year'  : '年'
  };
  var _redeem_options = {
    '3' : '固定日期',
    '2' : '一段时间',
    '1' : '使用日期当日',
    '4' : '使用日期后'
  };

  $scope.getDuration = function( duration_val, default_unit ) {
    var result = {};
    if( duration_val ) {
      duration_val = duration_val.toLowerCase();
    }

    if( !duration_val || duration_val == '0day' || duration_val == '0' ) {
      result.qty = 0;
      result.unit = default_unit || 'day';
    } else {
      result.qty = parseInt( duration_val, 10 );
      result.unit = duration_val.match( /[a-z]+/gi )[0];
    }
    if( result.unit !== 'day' && result.unit != 'month' && result.unit != 'year' ) {
      result.unit = 'day';
    }

    return result;
  }

  //Model

  $scope.checkLimit = function( limit ) {
    limit = limit.toLowerCase();
    if( limit == '0day' || limit == '0month' || limit == '0year' ) {
      return '0';
    }
    return '1';
  }

  $scope.date_limit = $route.current.locals.loadData.sale_date_rule;
  $scope.redeem_limit = $route.current.locals.loadData.redeem_limit;
  $scope.return_limit = $route.current.locals.loadData.return_limit;
  $scope.local_date_limit = {
    'day_type'           : $scope.date_limit.day_type,
    'shipping_day_type'  : $scope.date_limit.shipping_day_type,
    'buy_in_advance'     : $scope.checkLimit( $scope.date_limit.buy_in_advance ),
    'buy_in_advance_str' : parseInt( $scope.date_limit.buy_in_advance, 10 ),
    'lead_time'          : $scope.checkLimit( $scope.date_limit.lead_time ),
    'lead_time_str'      : parseInt( $scope.date_limit.lead_time, 10 ),
    'limit_sale_range'   : $scope.checkLimit( $scope.date_limit.sale_range ),
    'sale_range'         : $scope.getDuration( $scope.date_limit.sale_range, 'month' )
  };
  $scope.local_date_limit.sale_range.min_num = 0;

  //Options for directive
  $scope.radio_options = {
    day_type          : {
      name  : 'day_type',
      class : 'inline',
      items : {
        '0' : '自然日',
        '1' : '工作日'
      }
    },
    shipping_day_type : {
      name  : 'shipping_day_type',
      class : 'inline',
      items : {
        '0' : '自然日',
        '1' : '工作日'
      }
    },
    buy_in_advance    : {
      name  : 'buy_in_advance',
      items : {
        '0' : '不需要',
        '1' : '需要'
      }
    },
    lead_time         : {
      name  : 'lead_time',
      items : {
        '0' : '立刻发货',
        '1' : '不立刻发货'
      }
    },
    sale_type         : {
      name  : 'sale_type',
      //class: 'two-in-row',
      items : {
        '1' : '区分成人／儿童票',
        '2' : '只有一种票',
        '3' : '套票'
      }
    },
    child_only        : {
      name  : 'child_only',
      items : {
        '1' : '是',
        '0' : '否'
      }
    },
    redeem_type       : {
      name     : 'redeem_type',
      class    : 'redeem with-notice',
      items    : _redeem_options,
      notice   : true,
      comments : {
        '1' : '＊用户只能在自己填写的使用日期当日兑换。',
        '2' : '＊请指定一个时间段，用户在此时间段内可以兑换。',
        '3' : '＊请指定一个具体日期，用户在此日期前可以兑换。',
        '4' : '＊用户只能在自己填写的使用日期后一段时间内兑换。'
      }
    },
    return_type       : {
      name  : 'return_type',
      items : {
        "0" : '不可以退',
        "1" : '商品失效前可退',
        "2" : 'Tour Date 前可退'
      }
    },
    limit_sale_range  : {
      name  : 'limit_sale_range',
      items : {
        "0" : '不限制',
        "1" : '限制'
      }
    }
  };
  $scope.select_options = {
    buy_in_advance  : {
      name  : 'buy_in_advance',
      model : 'date_limit',
      items : _items_options
    },
    redeem_duration : {
      name  : 'duration',
      model : 'redeem_limit',
      items : _items_options
    },
    return_offset   : {
      name  : 'offset',
      model : 'return_limit',
      items : _items_options
    },
    sale_range      : {
      name  : 'sale_range',
      model : 'date_limit',
      items : _items_options
    }
  };

  $scope.redeem_duration_type2 = $scope.getDuration( $scope.redeem_limit.duration, 'month' );
  $scope.redeem_duration_type2.min_num = 1;
  $scope.redeem_duration_type4 = $scope.getDuration( $scope.redeem_limit.duration );
  $scope.redeem_duration_type4.min_num = 0;
  $scope.return_duration = $scope.getDuration( $scope.return_limit.offset );
  $scope.return_duration.min_num = 1;
  $scope.buy_in_advance_duration = $scope.getDuration( $scope.date_limit.buy_in_advance );
  $scope.local_date_limit.sale_range.min_num = 1;

  var isFirst = true;
  $scope.$watch( 'product_rules_form.$pristine', function() {
    if( isFirst ) {
      return isFirst = false;
    } else {
      $rootScope.$emit( 'dirtyForm', 'editProductRules' );
    }
  } );

  $scope.radio_options.limit_sale_range.onchange = function() {
    console.log( 'onchange...' );
    if( $scope.local_date_limit.limit_sale_range == 0 ) {
      $scope.date_limit.sale_range = '0Month';
    }
  }

  $scope.submitChanges = function() {
    var address = request_urls.postEditProductRule;

    if( $scope.local_date_limit.return_offset == 0 ) {
      $scope.return_limit.offset = '0day';
    }
    $scope.date_limit.day_type = $scope.local_date_limit.day_type;
    $scope.date_limit.shipping_day_type = $scope.local_date_limit.shipping_day_type;
    $scope.date_limit.buy_in_advance = (angular.isNumber( $scope.local_date_limit.buy_in_advance_str ) ?
        $scope.local_date_limit.buy_in_advance_str : 0) + $scope.buy_in_advance_duration.unit;
    $scope.date_limit.lead_time = (angular.isNumber( $scope.local_date_limit.lead_time_str ) ?
        $scope.local_date_limit.lead_time_str : 0) + $scope.buy_in_advance_duration.unit;

    //Validation
    var errorAlert = '';
    var defaultRange = '0day';

    if( $scope.local_date_limit.buy_in_advance == '1' &&
        $scope.date_limit.buy_in_advance.toLowerCase() == defaultRange ) {
      errorAlert += "提前购买时间段不能为0日。\n";
    }
    if( $scope.local_date_limit.buy_in_advance == '0' ) {
      $scope.date_limit.buy_in_advance = '0Day';
    }

    if( $scope.local_date_limit.lead_time == '1' && $scope.date_limit.lead_time.toLowerCase() == defaultRange ) {
      errorAlert += "不立即发货，提前时间不能为0日。\n";
    }
    if( $scope.local_date_limit.lead_time == '0' ) {
      $scope.date_limit.lead_time = '0Day';
    }

    if( ['2', '4'].indexOf( $scope.redeem_limit.redeem_type ) > -1 &&
        $scope.redeem_limit.duration.toLowerCase() == defaultRange ) {
      errorAlert += '兑换时间段不能为0日。\n';
    }
    if( $scope.redeem_limit.redeem_type == '3' ) {
      if( $scope.redeem_limit.expire_date < new Date() ) {
        errorAlert += '兑换固定日期不能小于今天。\n';
      }
    }
    if( $scope.return_limit.return_type != '1' && $scope.return_limit.offset.toLowerCase() == defaultRange ) {
      errorAlert += '退换时间段不能为0日。\n';
    }

    if( errorAlert.length > 0 ) {
      $rootScope.$emit( 'publishAlert', 400, errorAlert );
    } else {
      $http.post( address, {
        redeem_limit   : $scope.redeem_limit,
        return_limit   : $scope.return_limit,
        sale_date_rule : $scope.date_limit
      } ).success( function( data ) {
        $rootScope.$emit( 'clearDirty' );
        $rootScope.$emit( 'publishAlert', data.code, data.msg );
      } );
    }
  };
};


angular.module( 'ProductEditApp' ).controller( 'editProductRulesCtrl', [
  '$scope', '$rootScope', '$http', '$route', editProductRulesCtrl
] );