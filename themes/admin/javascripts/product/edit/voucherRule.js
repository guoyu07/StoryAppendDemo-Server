var editVoucherRuleCtrl = function( $scope, $rootScope, $route, $http, FileUploader, ProductEditFactory ) {

    $scope.uploader = new FileUploader({
        url   : request_urls.uploadAttachedPdf,
        scope : $scope
    });
    $scope.uploader.filters.push({
        name : 'imagesOnly',
        fn   : function(item) {
            return item.type.toLowerCase().slice( item.type.lastIndexOf( '/' ) + 1 ) == 'pdf';
        }
    });
    $scope.uploader.onSuccessItem = function(item, response) {
        $scope.local.check_upload_progress = false;
        $scope.uploader.queue = [];
        if( response.code == 200 ) {
            $scope.voucherConfig.attached_pdf.push( response.data );
        } else {
            alert( response.msg );
        }
    };
    $scope.uploader.onBeforeUploadItem = function() {
        $scope.local.check_upload_progress = true;
    };
    $scope.uploader.onAfterAddingFile = function(item) {
        item.upload();
    };

  $scope.voucherConfig = $route.current.locals.loadData["configurations"][0];
  $scope.wholeRules = $route.current.locals.loadData["wholeRules"];

  $scope.rule_group = ProductEditFactory.getRoute( $scope.wholeRules["need_passenger_num"], $scope.wholeRules["need_lead"] );

  if( $scope.wholeRules.passenger_rule_item != null ) {
    $scope.other_rules = [];
    for( var i in $scope.wholeRules.passenger_rule_item ) {
      var arr = new Array();
      if($scope.wholeRules.passenger_rule_item[i].voucher_field.length != 0) {
        arr["current_rule"] = $scope.wholeRules.passenger_rule_item[i].voucher_field.split( "," );
      } else {
        arr["current_rule"] =[];
      }

      arr["origin_rule"] = $scope.wholeRules.passenger_rule_item[i].fields;
      arr["infos"] = $scope.wholeRules.passenger_rule_item[i].ticket_type;
      $scope.other_rules.push( arr );
    }
  }
  if( $scope.wholeRules.voucher_leader_field.length != 0 ) {
    $scope.curr_leader_rules = $scope.wholeRules.voucher_leader_field.split( "," );
  } else {
    $scope.curr_leader_rules = [];
  }

  $scope.leader_rules = $scope.wholeRules.lead_fields;

  $scope.radio_options = {
    language    : {
      name  : 'language_id',
      items : {
        '1' : '中文',
        '3' : '中/英双文'
      }
    },
    payable_by  : {
      name  : 'need_pay_cert',
      items : {
        '0' : '不显示',
        '1' : '显示'
      }
    },
    origin_name : {
      name  : 'need_origin_name',
      items : {
        '0' : '不显示',
        '1' : '显示'
      }
    },
    signature   : {
      name  : 'need_signature',
      items : {
        '0' : '不需要',
        '1' : '需要'
      }
    }
  }

  $scope.local = {
    check_upload_progress : false
  }

  $scope.triggerUpload = function() {
    $( '#pdf-upload' ).trigger( 'click' );
  };

  $scope.delPdf = function(product_id,pdf_name,index) {
    if( !window.confirm( "删除后不可恢复。\n点击'确认'删除。" ) ) return;
    $http.post( request_urls.deleteAttachedPdf, {
      product_id    : product_id,
      pdf_name      : pdf_name
    } ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.voucherConfig.attached_pdf.splice( index, 1 );
      } else {
        alert( data.msg );
      }
    } );
  }

  $scope.toggleItem = function( id, items ) {
    id = id.toString();
    var index = items.indexOf( id );
    if( index === -1 ) {
      items.push( id );
    } else {
      items.splice( index, 1 );
    }
  };


  $scope.submitChanges = function() {
    var voucherConfig = angular.copy( $scope.voucherConfig );

    var tmp_array = [];
    for (var index in $scope.other_rules) {
      var item = $scope.other_rules[index];
      var rule = {
        'ticket_id' : item["infos"]['ticket_id'],
        'fields' : item["current_rule"].toString()
      };

      tmp_array.push(rule);
    }

    var postData = {
      'voucherConfig' : voucherConfig,
      'passenger_rule_item' : tmp_array
    };

    postData["voucherConfig"]["lead_fields"] = $scope.curr_leader_rules.toString();
    if( $scope.rule_group == 'leader' || $scope.rule_group == 'leader_and_everyone') {
      if(  postData["voucherConfig"]["lead_fields"].length == 0 ) {
        alert( '领队信息不完整。' );
        return;
      }
    }

    $http.post( request_urls.updateVoucherRule, postData ).success( function( data ) {
      alert( data.msg );

      if( data.code == 401 ) {
        window.location.reload();
      }
    } );
  };

  $scope.is_GTA = ProductEditFactory.is_GTA;
  $scope.is_combo = ProductEditFactory.is_combo;
};

angular.module( 'ProductEditApp' ).controller( 'editVoucherRuleCtrl', [
  '$scope', '$rootScope', '$route', '$http', 'FileUploader', 'ProductEditFactory', editVoucherRuleCtrl
] );