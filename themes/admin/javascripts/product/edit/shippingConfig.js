var editShippingConfigCtrl = function( $scope, $rootScope, $route, $http, ProductEditFactory ) {
  $scope.shippingRule = $route.current.locals.loadData;

  $scope.check_boxs = {
    confirmation_type         : false,
    need_supplier_booking_ref : false
  };

  $scope.check_boxs.confirmation_type = ($scope.shippingRule.confirmation_type > 0 );

  $scope.check_boxs.need_supplier_booking_ref = ($scope.shippingRule.need_supplier_booking_ref > 0);

  var _shipping_styles = {
    'EMAIL'  : 'Email',
    'B2B'    : 'B2B下单',
    'HITOUR' : 'Hitour直接发货',
    'EXCEL'  : '整理Excel',
    'STOCK'  : '批量预购'
  };

  var _email_feedback_type = {
    '1' : 'Code确认码',
    '2' : 'PDF Voucher',
    '3' : '预约结果'
  };

  var _B2B_feedback_type = {
    '1' : 'Code确认码',
    '2' : 'PDF Voucher',
    '3' : '预约结果'
  };

  $scope.radio_options = {
    confirmation_type : {
      name  : 'confirmation_type',
      items : {
        '1' : '只有一个',
        '2' : '每人一个'
      }
    },

    shipping_type        : {
      name     : 'booking_type',
      class    : 'redeem with-notice',
      items    : _shipping_styles,
      notice   : true,
      comments : {
        'EMAIL'  : '*需要给供应商发EMAIL,等待供应商返回确认信息。',
        'B2B'    : '*需要在供应商B2B平台下单，获取确认信息。',
        'HITOUR' : '*HITOUR自动生成HITOUR BOOKINGID，用户付款后直接发货',
        'EXCEL'  : '*定期整理订单，发送EXCEL给供应商，获取批量的确认信息',
        'STOCK'  : '*批量预购VOUCHER PDF,上传商品VOUCHER PDF库存'
      }
    },
    feedback_type        : {},
    code_style           : {
      name  : 'confirmation_display_type',
      items : {
        "1" : '字符串',
        "2" : '条形码'
      }
    },
    need_additional_info : {
      name  : 'display_additional_info',
      items : {
        '0' : '不需要',
        '1' : '需要'
      }
    },
    need_notify_supplier : {
      name  : 'need_notify_supplier',
      items : {
        '0' : '不通知',
        '1' : '通知'
      }
    },
    email_language       : {
      name  : 'language_id',
      items : {
        '1' : '英文',
        '2' : '中文'
      }
    }
  };

  $scope.switchReturnType = function() {
    if( $scope.shippingRule.booking_type == "EMAIL" ||
        $scope.shippingRule.booking_type == "B2B" ||
        $scope.shippingRule.booking_type == "EXCEL" ) {
      $scope.radio_options.feedback_type = {
        name     : 'supplier_feedback_type',
        class    : 'redeem with-notice',
        items    : _email_feedback_type,
        notice   : true,
        comments : {
          '1' : '*供应商返回Code，需要OP手动上传',
          '2' : '*供应商返回PDF Voucher，可以直接发送给用户',
          '3' : '*供应商直接返回一个预定结果'
        }
      }
    }

    $scope.radio_options.feedback_type.onchange = function() {
      console.log( 'feedback_type changed' );
      if( $scope.shippingRule.supplier_feedback_type == '2' ) {
        $scope.radio_options.confirmation_type.items = {
          '1' : '只有一个',
          '2' : '每人一个',
          '3' : '不限制数量'
        }
        if( $scope.shippingRule.confirmation_type == '0' ) {
          $scope.shippingRule.confirmation_type = '1';
        }
      } else {
        $scope.radio_options.confirmation_type.items = {
          '1' : '只有一个',
          '2' : '每人一个'
        }
        if( $scope.shippingRule.confirmation_type == '3' ) {
          $scope.shippingRule.confirmation_type = '1';
        }
      }
    };
  };


  $scope.init = function() {
    $scope.switchReturnType();
    if( $scope.shippingRule.supplier_feedback_type == 2 ) {
      $scope.radio_options.confirmation_type.items = {
        '1' : '只有一个',
        '2' : '每人一个',
        '3' : '不限制数量'
      }
    }
  }

  $scope.init();

  $scope.isEmail = function( email ) {
    var reMail = /^(.+)@(.+)$/; // 只做最简单的校验 -- 含有@

    return reMail.test( email );
  };

  $scope.confirmationTypeChanged = function() {
    if( !$scope.check_boxs.confirmation_type ) {
      $scope.shippingRule.confirmation_type = '0';
    } else {
      $scope.shippingRule.confirmation_type = '1';
    }
  };

  $scope.submitChanges = function() {
    if( $scope.check_boxs.need_supplier_booking_ref ) {
      $scope.shippingRule.need_supplier_booking_ref = '1';
    } else {
      $scope.shippingRule.need_supplier_booking_ref = '0';
    }

    if( $scope.shippingRule.booking_type == 'EMAIL' ) {
      if( $scope.shippingRule.supplier_email.trim().length == 0 ) {
        alert( '请填写供应商Email.' );
        return;
      }
      var emails = $scope.shippingRule.supplier_email.split( ',' );
      for( var i = 0; i < emails.length; i++ ) {
        if( !$scope.isEmail( emails[i] ) ) {
          alert( 'Email "' + emails[i] + '" 格式不正确，请修改供应商Email地址后再试。' );
          return;
        }
      }
    }

    if( $scope.shippingRule.booking_type == 'B2B' || $scope.shippingRule.booking_type == 'EMAIL' ) {
      if( $scope.shippingRule.supplier_feedback_type == '1' ) {
        if( $scope.shippingRule.need_supplier_booking_ref == '0' && $scope.shippingRule.confirmation_type == '0' ) {
          alert( '"包含Supplier_BookingID"和"包含Supplier_ConfirmCode"至少选一个。' );
          return;
        }
      } else if( $scope.shippingRule.supplier_feedback_type == '3' ) {
        $scope.shippingRule.need_supplier_booking_ref = 0;
        $scope.shippingRule.confirmation_type = 0;
        $scope.shippingRule.need_hitour_booking_ref = 1;
      }
    }

    if( $scope.shippingRule.booking_type == 'HITOUR' ) {
        $scope.shippingRule.supplier_feedback_type = '3';
    }

    var postData = angular.copy( $scope.shippingRule );

    $http.post( request_urls.updateShippingConfigurations, postData ).success( function( data ) {
      alert( data.msg );

      if( data.code == 401 ) {
        window.location.reload();
      }
    } );
  }

  $scope.$watch( 'shippingRule.booking_type', function() {
    $scope.switchReturnType();
  } );

  $scope.is_GTA = ProductEditFactory.is_GTA;
  $scope.is_combo = ProductEditFactory.is_combo;
  $scope.is_CPIC = ProductEditFactory.is_CPIC;
}

angular.module( 'ProductEditApp' ).controller( 'editShippingConfigCtrl', [
  '$scope', '$rootScope', '$route', '$http', 'ProductEditFactory', editShippingConfigCtrl
] );
