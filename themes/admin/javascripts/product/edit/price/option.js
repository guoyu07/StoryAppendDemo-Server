var editPriceOptionCtrl = function( $scope, $rootScope, $route, $http, $q ) {

  $scope.priceOptionEditing = false;

  $scope.priceOptionEditClick = function() {
    if( $scope.product_info.status == 3 || $scope.product_info.supplier_id == 11 ) {
      $scope.editNotAllowedWarning();
      return;
    }
    $scope.priceOptionEditing = true;
  }

  $scope.submitPriceOptionChanges = function() {
    $scope.priceOptionEditing = false;
  }

  $scope.radio_options = {
    is_packaged : {
      name  : 'sale_in_package',
      items : {
        "0" : '不按套出售',
        "1" : '按套出售'
      }
    },
    child_only  : {
      name  : 'is_independent',
      items : {
        "0" : '否',
        "1" : '是'
      }
    },
     adult_only  : {
      name  : 'is_independent',
      items : {
        "0" : '否',
        "1" : '是'
      }
    }
  };

  $scope.init = function() {
    var current_page = $route.current.params['current_page'] ? $route.current.params['current_page'] : 1;
    if(current_page != 2) return;

    var sale_rule = $http.get( request_urls.getSaleRule );
    var product_info = $http.get( request_urls.getProduct );
    var all_requests = [sale_rule, product_info];
    $q.all( all_requests ).then( function( values ) {
      $scope.option_rule = values[0].data.code == 200 ? values[0].data.data : [];
      $scope.initialOptionsDescription();

      $scope.product_info = values[1].data.code == 200 ? values[1].data.data : {};
      $scope.is_GTA = $scope.product_info.supplier_id == 11;
    } );
  }

  $scope.initialOptionsDescription = function() {
    var resultStr = "";
    var unit = "";
    var isPackaged = "";
    var typeAmount = 0;

    if( $scope.option_rule.sale_rule.sale_in_package == "1" ) {
      unit = "套";
      isPackaged = "按套";
      for( var index in $scope.option_rule.package_rule ) {
        resultStr += $scope.option_rule.package_rule[index].ticket_type.cn_name + "票" +
                     $scope.option_rule.package_rule[index].quantity + "张     ";
      }
    } else {
      unit = "人";
      isPackaged = "不按套";
      var hasSingle = "";
      var hasCantSingle = "";
      var single = "";
      var cantSingle = "";

      if( $scope.option_rule.ticket_rule.length == 1 ) {
        resultStr = "可单独购买  " + $scope.option_rule.ticket_rule[0].ticket_type.cn_name;
        typeAmount = 1;
      }
      if( $scope.option_rule.ticket_rule.length == 2 ) {
        for( var index in $scope.option_rule.ticket_rule ) {
          if( $scope.option_rule.ticket_rule[index].ticket_type.ticket_id == "2" ) {
            $scope.adult_index = index;
            $scope.min_adult_amount = $scope.option_rule.ticket_rule[index].min_num;
          }
          if( $scope.option_rule.ticket_rule[index].ticket_type.ticket_id == "3" ) {
            $scope.child_index = index;
            $scope.min_child_amount = $scope.option_rule.ticket_rule[index].min_num;
            if( $scope.option_rule.ticket_rule[index].is_independent == "1" ) {
              resultStr = "可单独购买";
            } else {
              resultStr = "不可单独购买";
            }
          }
        }

        typeAmount = 2;
      }
      if( $scope.option_rule.ticket_rule.length > 2 ) {
        for( var index in $scope.option_rule.ticket_rule ) {
          if( $scope.option_rule.ticket_rule[index].is_independent == "1" ) {
            single += $scope.option_rule.ticket_rule[index].ticket_type.cn_name + "票" + "/"
            $scope.option_rule.ticket_rule[index].is_independent = true;
          } else {
            cantSingle += $scope.option_rule.ticket_rule[index].ticket_type.cn_name + "票" + "/"
            $scope.option_rule.ticket_rule[index].is_independent = false;
          }
        }
        if( single.length > 0 ) {
          hasSingle = 1;
          single = single.substring( 0, single.length - 1 );
        }
        if( cantSingle.length > 0 ) {
          hasCantSingle = 1;
          cantSingle = cantSingle.substring( 0, cantSingle.length - 1 );
        }

        resultStr = single + "         " + cantSingle;
        typeAmount = 3;
      }
    }

    $scope.rulesDescription = {
      isPackaged       : isPackaged,
      unit             : unit,
      resultStr        : resultStr,
      ticketTypeAmount : typeAmount,
      single           : single,
      cantSingle       : cantSingle,
      hasSingle        : hasSingle,
      hasCantSingle    : hasCantSingle
    };

  }

  $scope.submitPriceOptionChanges = function() {
    if( $scope.rulesDescription.ticketTypeAmount == "2" && $scope.option_rule.sale_rule.sale_in_package == "0" ) {
      if( $scope.option_rule.ticket_rule[$scope.adult_index].is_independent == "1" ) {
        $scope.option_rule.ticket_rule[$scope.child_index].min_num = '0';
      } else if( $scope.option_rule.ticket_rule[$scope.child_index].min_num == '0' ) {
        alert( "成人票不可单独售卖，最少包含儿童数不能为0" );
        return;
      }

      if( $scope.option_rule.ticket_rule[$scope.child_index].is_independent == "1" ) {
        $scope.option_rule.ticket_rule[$scope.adult_index].min_num = '0';
      } else if( $scope.option_rule.ticket_rule[$scope.adult_index].min_num == '0' ) {
        alert( "儿童票不可单独售卖，最少包含成人数不能为0" );
        return;
      } else if( $scope.option_rule.ticket_rule[$scope.adult_index].min_num > $scope.option_rule.sale_rule.min_num ) {
        alert( "最少包含成人数不能大于起定人数" );
        return;
      }
    } else if( $scope.option_rule.sale_rule.sale_in_package == "1" ) {
      var total_pticket = 0;
      for( var index in $scope.option_rule.package_rule ) {
        total_pticket += $scope.option_rule.package_rule[index].quantity;
      }
      if( total_pticket == 0 ) {
        alert( "请填写票种数量" );
        return;
      }
    }

    var priceOptionRule = angular.copy( $scope.option_rule );
    delete priceOptionRule.editing;
    delete priceOptionRule.work_time_start;
    delete priceOptionRule.work_time_stop;

    $http.post( request_urls.saveSaleRule, priceOptionRule ).success( function( data ) {
      $scope.priceOptionEditing = false;

      if( data.code == 200 ) {
        alert( data.msg );
        $scope.option_rule = data.data;
        $scope.initialOptionsDescription();
      }
      if( data.code == 401 ) {
        window.location.reload();
      }
    } );
  }

  $scope.init();

  $scope.radio_options.is_packaged.onchange = function() {
    if( $scope.option_rule.sale_rule.sale_in_package == '1' ) {
      if( $scope.option_rule.package_rule == null ) {
        var newPackageRules = new Array();
        for( var index in $scope.option_rule.ticket_rule ) {
          newPackageRules.push( {
                                  product_id      : $scope.option_rule.ticket_rule[index].product_id,
                                  base_product_id : "0",
                                  ticket_id       : $scope.option_rule.ticket_rule[index].ticket_id,
                                  quantity        : "0",
                                  ticket_type     : {
                                    ticket_id : $scope.option_rule.ticket_rule[index].ticket_type.ticket_id,
                                    cn_name   : $scope.option_rule.ticket_rule[index].ticket_type.cn_name,
                                    en_name   : $scope.option_rule.ticket_rule[index].ticket_type.en_name
                                  }
                                } );
        }
        $scope.option_rule.package_rule = newPackageRules;
      }
    } else {
      if( $scope.option_rule.ticket_rule == null ) {
        var newTicketRules = new Array();
        for( var index in $scope.option_rule.package_rule ) {
          newTicketRules.push( {
                                 product_id     : $scope.option_rule.package_rule[index].product_id,
                                 ticket_id      : $scope.option_rule.package_rule[index].ticket_id,
                                 age_range      : "",
                                 description    : null,
                                 is_independent : "0",
                                 min_num        : "0",
                                 ticket_type    : {
                                   ticket_id : $scope.option_rule.package_rule[index].ticket_type.ticket_id,
                                   cn_name   : $scope.option_rule.package_rule[index].ticket_type.cn_name,
                                   en_name   : $scope.option_rule.package_rule[index].ticket_type.en_name
                                 }
                               } );
        }
        $scope.option_rule.ticket_rule = newTicketRules;
      }

    }

    $scope.initialOptionsDescription();
  };

  $scope.editNotAllowedWarning = function() {
    if( $scope.is_GTA ) {
      alert( 'GTA商品不允许编辑这些信息。' );
    } else {
      alert( '上架状态无法编辑该信息。' );
    }
  };
}


angular.module( 'ProductEditApp' ).controller( 'editPriceOptionCtrl', [
  '$scope', '$rootScope', '$route', '$http', '$q', editPriceOptionCtrl
] );