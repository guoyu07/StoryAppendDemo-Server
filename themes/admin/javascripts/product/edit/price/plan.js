var editPricePlanCtrl = function( $scope, $rootScope, $route, $http, $q, commonFactory ) {
  var watchCodeFirstRun = true;

  $scope.data = {};
  $scope.local = {
    this_plan_index  : -1,
    this_plan        : {},
    weekdays         : {
      'wd1'   : '周一',
      'wd2'   : '周二',
      'wd3'   : '周三',
      'wd4'   : '周四',
      'wd5'   : '周五',
      'wd6'   : '周六',
      'wd7'   : '周日',
      'wdall' : '全部'
    },
    product_info     : {},
    has_special      : false,
    is_new           : false,
    is_special_price : false,
    price_plan_name  : '价格计划'
  };
  $scope.radio_options = {
    'valid_region'      : {
      name  : 'valid_region',
      items : {
        '0' : '整个区间生效',
        '1' : '自定义生效区间'
      }
    },
    'need_tier_pricing' : {
      name  : 'need_tier_pricing',
      items : {
        '0' : '不需要',
        '1' : '需要'
      }
    }
  };

  $scope.sortItems = function( items, has_quantity ) {
    //Sort -- special code, quantity, ticket_id
    items.sort( function( a, b ) {
      if( $scope.local.has_special ) {
        if( a.special_code != b.special_code ) {
          return a.special_code > b.special_code ? 1 : -1;
        } else if( has_quantity ) {
          if( a.quantity != b.quantity ) {
            return a.quantity - b.quantity;
          } else {
            return a.ticket_id - b.ticket_id;
          }
        }
      } else if( has_quantity ) {
        if( a.quantity != b.quantity ) {
          return a.quantity - b.quantity;
        } else {
          return a.ticket_id - b.ticket_id;
        }
      }
    } );
    return items;
  };

  $scope.updateItems = function( product_info, plan_info, has_quantity, has_special, codes ) {
    //Init Data
    var min = 1, max = 1, i;
    var new_set = [];
    var ticket_types = [];
    if( $scope.sale_in_package ) {
      angular.forEach( product_info.ticket_types, function( value, key ) {
        if( 99 == value.ticket_id ) {
          ticket_types.push( value );
        }
      } );
    } else {
      ticket_types = product_info.ticket_types;
    }

    if( has_quantity ) {
      min = ticket_types.length == 1 ? product_info.min_num : 1;
      max = product_info.max_num;
    }

    var result = [];

    //Add
    if( !has_quantity && !has_special && result.length == 0 ) {

      new_set = ticket_types.map( function( elem ) {
        return {
          price      : 0,
          frequency  : [],
          ticket_id  : elem.ticket_id, //Looping ticket type
          cost_price : 0,
          orig_price : 0
        };
      } );

      result = result.concat( new_set );

    } else if( has_quantity && !has_special && result.length == 0 ) {

      for( i = min; i <= max; i++ ) {

        new_set = ticket_types.map( function( elem ) {
          return {
            price      : 0,
            quantity   : i,
            frequency  : [],
            ticket_id  : elem.ticket_id, //Looping ticket type
            cost_price : 0,
            orig_price : 0
          };
        } );

        result = result.concat( new_set );

      }

    } else if( has_special ) {

      console.log( 'codes: ' + codes );
      for( var key in codes ) {
        for( i = min; i <= max; i++ ) {
          new_set = ticket_types.map( function( elem ) {
            return {
              price        : 0,
              quantity     : i,
              frequency    : [],
              ticket_id    : elem.ticket_id, //Looping ticket type
              cost_price   : 0,
              orig_price   : 0,
              special_code : codes[key] //Same key as loop
            };
          } );

          result = result.concat( new_set );
        } //End for

      } //End for

    } //End if

    //Sort -- special code, quantity, ticket_id
    result = $scope.sortItems( result, has_quantity );

    return result;
  };

  $scope.updateItemsByCodesChange = function( items, codes, has_quantity ) {
    // handle item changes caused by codes change
    // consider ticket type, quantity;


    if( items.length > 0 ) {
      var template_item_set = [];
      var special_code = items[0].special_code;
      angular.forEach( items, function( item, key ) {
        if( special_code == item.special_code ) {
          template_item_set.push( angular.copy( item ) );
        }
      } );

      var results = items.filter( function( item ) {
        return codes.indexOf( item.special_code ) >= 0;
      } );

      var exists_codes = results.map( function( item ) {
        return item.special_code;
      } );

      angular.forEach( codes, function( value, key ) {
        if( exists_codes.indexOf( value ) == -1 ) {
          angular.forEach( template_item_set, function( item, kk ) {
            var new_item = angular.copy( item );
            new_item.special_code = value;
            delete new_item['item_id'];
            results.push( new_item );
          } );
        }
      } );

      return $scope.sortItems( results, has_quantity );

    } else {
      return $scope.updateItems( $scope.local.product_info, $scope.local.this_plan, has_quantity, codes.length >
                                                                                                  0, codes );
    }
  };

  $scope.getTicketName = function( ticket_id ) {
    var all_tickets = $scope.local.product_info.ticket_types;
    var length = all_tickets.length;
    for( var i = 0; i < length; i++ ) {
      if( all_tickets[i].ticket_id == ticket_id ) return all_tickets[i].cn_name;
    }
    return false;
  };
  $scope.getCodeName = function( code ) {
    var all_codes = $scope.local.product_info.special_codes;
    var length = all_codes.length;
    for( var i = 0; i < length; i++ ) {
      if( all_codes[i].special_code == code ) return all_codes[i].cn_name;
    }
    return false;
  };
  $scope.getFrequencyLabel = function( str ) {
    if( !str || str.length == 0 ) return '';
    if( angular.isArray( str ) ) {
      var parts = [];
      for( var i = 0; i < str.length; i++ ) {
        parts.push( $scope.local.weekdays[str[i]] );
      }
      return parts.join( ', ' );
    } else {
      var parts = str.split( ';' );
      for( var key in parts ) {
        parts[key] = $scope.local.weekdays[ parts[key] ];
      }
      return parts.join( ', ' );
    }
  };

  $scope.getRowSpan = function( need_tier_pricing ) {
    if( !$scope.local.has_special ) return 0;

    var row_span = $scope.sale_in_package ? 1 : $scope.local.product_info.ticket_types.length;
    if( need_tier_pricing == 1 ) {
      var min = $scope.local.product_info.ticket_types.length == 1 ? $scope.local.product_info.min_num : 1;

      var count = $scope.local.product_info.max_num - min + 1;
      row_span *= count;
    }
    return row_span;
  }

  $scope.init = function() {
    var current_page = $route.current.params['current_page'] ? $route.current.params['current_page'] : 1;
    if( current_page != 3 && current_page != 5 ) return;

    $scope.local.is_special_price = ($route.current.params.current_page == 3) ? false : true;
    if( $scope.local.is_special_price ) {
      $scope.local.price_plan_name = '特价计划';
    }

    $scope.price_plan_url = request_urls.productPricePlan;
    if( $scope.local.is_special_price ) {
      $scope.price_plan_url = request_urls.productPricePlanSpecial;
    }

    var basic_info = $http.get( request_urls.productPricePlanBasicInfo );
    var all_plans = $http.get( request_urls.productPricePlans );
    if( $scope.local.is_special_price ) {
      all_plans = $http.get( request_urls.productPricePlanSpecials );
    }
    var product_info = $http.get( request_urls.getProduct );
    $q.all( [basic_info, all_plans, product_info] ).then( function( values ) {
      if( values[0].data.code == 200 ) {
        $scope.local.product_info = values[0].data.data;
        $scope.min_date = $scope.local.product_info.from_date;
        $scope.max_date = $scope.local.product_info.to_date;
        $scope.local.has_special = $scope.local.product_info.special_codes.length > 0;

        $scope.sale_in_package = false;
        angular.forEach( $scope.local.product_info.ticket_types, function( value, key ) {
          if( 99 == value.ticket_id ) {
            $scope.sale_in_package = true;
          }
        } );
      }

      if( values[1].data.code == 200 ) {
        $scope.data = values[1].data.data;
        $scope.data.forEach( function( elem ) {
          //判断价格计划是否过期
          var to_date_time = new Date( elem.to_date ).getTime() + 24 * 60 * 60 * 1000;
          var now = new Date();
          var now_time = now.getTime();
          if( now_time > to_date_time ) {
            elem.out_of_date = 1;
          }

          elem.colspan = 4;
          if( $scope.local.has_special ) elem.colspan += 2;
          if( elem.need_tier_pricing == 1 ) elem.colspan++;
          elem.row_span = $scope.getRowSpan( elem.need_tier_pricing );
          $scope.sortItems( elem.items, elem.need_tier_pricing == 1 );
        } );
        //        console.log( $scope.data );
      }

      $scope.product_basic_info = values[2].data.code == 200 ? values[2].data.data : {};
      $scope.is_GTA = $scope.product_basic_info.supplier_id == 11;
      //TODO: error handling
    } );
  };

  $scope.getDisplayDate = function( index ) {
    if( index < 0 || index >= $scope.data.length ) return '';
    var price_plan = $scope.data[index];

    return  (price_plan.valid_region == 1) ?
        commonFactory.formatDate( price_plan.from_date ) + ' -- ' + commonFactory.formatDate( price_plan.to_date ) :
        '整个区间';
  }

  $scope.getSpecialCodesAll = function() {
    var special_codes_all = [];
    if( $scope.local.product_info.special_codes ) {
      var length = $scope.local.product_info.special_codes.length;
      for( var i = 0; i < length; i++ ) {
        special_codes_all.push( $scope.local.product_info.special_codes[i].special_code );
      }
    }

    return special_codes_all;
  }

  $scope.addPlan = function() {
    if( $scope.product_basic_info.status == 3 || ($scope.is_GTA && !$scope.local.is_special_price) ) {
      $scope.editNotAllowedWarning( $scope.is_GTA );
      return;
    }
    if( $scope.data.length == 1 && $scope.data[0].valid_region == 0 ) {
      alert( '生效区间为整个区间的价格计划只能有一份。' );
      return;
    }

    var special_codes = '';
    if( $scope.local.has_special ) {
      special_codes = $scope.getSpecialCodesAll().join( ';' );
    }

    var data = {
      items             : [],
      to_date           : "",
      from_date         : "",
      valid_region      : "0",
      special_codes     : special_codes,
      price_plan_id     : "",
      need_tier_pricing : "0"
    };

    if( $scope.local.is_special_price ) {
      data.reseller = '';
      data.slogan = '';
    }

    var items = $scope.updateItems( $scope.local.product_info, data, 0, $scope.local.has_special, $scope.getSpecialCodesAll() );
    data.items = items;

    $scope.doEditPlan( $scope.data.length, data );
  };

  $scope.doEditPlan = function( index, data ) {
    $scope.local.this_plan_index = index;
    watchCodeFirstRun = true;
    $scope.local.this_plan = data;

    //Process codes
    if( $scope.local.has_special ) {
      $scope.local.this_plan.row_span = $scope.getRowSpan( $scope.local.this_plan.need_tier_pricing );

      var codes = [];
      if( data && !!data.special_codes ) {
        codes = data.special_codes.split( ';' );
      }

      var result = {};
      var length = $scope.local.product_info.special_codes.length;
      var current_code;
      for( var i = 0; i < length; i++ ) {
        current_code = $scope.local.product_info.special_codes[i].special_code;
        result[ current_code ] = codes.indexOf( current_code ) > -1;
      }

      $scope.local.this_plan.special_codes = result;
    }

    //Process frequency
    $scope.local.this_plan.special_code_frequency = {};
    var len = $scope.local.this_plan.items.length;
    for( var i = 0; i < len; i++ ) {
      if( angular.isString( $scope.local.this_plan.items[i].frequency ) ) {
        $scope.local.this_plan.items[i].frequency = $scope.local.this_plan.items[i].frequency.split( ';' );
        $scope.local.this_plan.special_code_frequency[$scope.local.this_plan.items[i].special_code] = $scope.local.this_plan.items[i].frequency;
      }
    }
    $scope.sortItems( $scope.local.this_plan.items, $scope.local.this_plan.need_tier_pricing == 1 );
  };

  $scope.editPlan = function( index ) {
    if( $scope.product_basic_info.status == 3 ) {
      $scope.editNotAllowedWarning( false );
      return;
    }

    $http.get( $scope.price_plan_url + $scope.data[index].price_plan_id ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.doEditPlan( index, data.data );
      } else {
        alert( data.msg + "\n请刷新页面后再试。" );
      }
    } );
  };
  $scope.updatePlan = function() {
    var postData = angular.copy( $scope.local.this_plan );
    if( postData.valid_region == 1 ) {

      //   var plan_all = angular.copy( $scope.data );
      //   plan_all.splice( $scope.local.this_plan_index, 1, postData );


      var result = commonFactory.validateDurations( [postData], $scope.local.product_info );
      if( result.code > 0 ) {
        alert( result.msg );
        if( result.code < 200 ) {
          return;
        }
      }
    }

    //  check all prices and no 0 allowed
    for( var key in postData.items ) {
      if( postData.items[key].price == 0 || postData.items[key].orig_price == 0 ||
          postData.items[key].cost_price == 0 ) {
        if( postData.items[key].ticket_id < 3 || postData.items[key].ticket_id == 99 ) {
          alert( '所有的价格都不能为0。请填写后再保存。' );
          return;
        }
      }
      if( $scope.local.has_special ) {
        postData.items[key].frequency = postData.special_code_frequency[postData.items[key].special_code];
        $scope.local.this_plan.items[key].frequency = postData.items[key].frequency;
      }
      if( angular.isArray( postData.items[key].frequency ) && postData.items[key].frequency.length > 0 ) {
        postData.items[key].frequency = postData.items[key].frequency.join( ';' );
      } else {
        postData.items[key].frequency = '';
      }
    }

    if( $scope.local.has_special ) {
      var tmp = Object.keys( postData.special_codes ).filter( function( elem ) {
        return postData.special_codes[ elem ] == true;
      } );
      postData.special_codes = tmp.join( ';' );
    }

    var id = $scope.local.this_plan.price_plan_id;

    $http.post( $scope.price_plan_url + id, postData ).success( function( data ) {
      alert( data.msg );
      if( data.code == 200 ) {
        //判断价格计划是否过期
        var to_date_time = new Date( $scope.local.this_plan.to_date ).getTime() + 24 * 60 * 60 * 1000;
        var now = new Date();
        var now_time = now.getTime();
        if( now_time > to_date_time ) {
          $scope.local.this_plan.out_of_date = 1;
        }
        var colspan = 4;
        if( $scope.local.has_special ) colspan += 2;
        if( $scope.local.this_plan.need_tier_pricing == 1 ) colspan++;

        $scope.data[ $scope.local.this_plan_index ] = angular.copy( $scope.local.this_plan );

        $scope.sortItems( $scope.data[ $scope.local.this_plan_index ].items, $scope.data[ $scope.local.this_plan_index ].need_tier_pricing ==
                                                                             1 );

        $scope.data[ $scope.local.this_plan_index ].colspan = colspan;
        $scope.data[ $scope.local.this_plan_index ].row_span = $scope.getRowSpan( $scope.data[ $scope.local.this_plan_index ].need_tier_pricing );

        $scope.data[ $scope.local.this_plan_index ].price_plan_id = data.data.price_plan_id;

        $scope.local.this_plan_index = -1;
      }
      //TODO: error handling
    } );

  };
  $scope.deletePlan = function( index ) {
    if( !$scope.local.is_special_price && ($scope.product_basic_info.status == 3 || $scope.is_GTA ) ) {
      $scope.editNotAllowedWarning( $scope.is_GTA );
      return;
    }
    if( !confirm( '删除后数据不可恢复。\n点击“确定”来删除。' ) ) return;

    $http.delete( $scope.price_plan_url + $scope.data[index].price_plan_id ).success( function( data ) {
      if( data.code == 200 ) {
        alert( '删除成功！' );
        // TODO refresh UI
        $scope.data.splice( index, 1 );
      } else {
        alert( data.msg );
      }
    } );
  }
  $scope.radio_options.need_tier_pricing.onchange = function() {
    //Init
    var codes;
    if( $scope.local.has_special ) {
      codes = Object.keys( $scope.local.this_plan.special_codes ).filter( function( elem ) {
        return $scope.local.this_plan.special_codes[ elem ] == true;
      } );
    }

    $scope.local.this_plan.items = $scope.updateItems( $scope.local.product_info, $scope.local.this_plan, $scope.local.this_plan.need_tier_pricing ==
                                                                                                          '1', $scope.local.has_special, codes );
    $scope.local.this_plan.row_span = $scope.getRowSpan( $scope.local.this_plan.need_tier_pricing );
  };

  $scope.$watch( 'local.this_plan.special_codes', function( newVal, oldVal ) {
    if( watchCodeFirstRun ) {
      watchCodeFirstRun = !watchCodeFirstRun;
      return;
    }

    //Init
    var codes;
    var old_codes;
    var has_quantity = $scope.local.this_plan.need_tier_pricing == '1';

    if( $scope.local.has_special ) {
      codes = $scope.analysisSpecialCode( newVal );
      old_codes = $scope.analysisSpecialCode( oldVal );
    }

    if( codes.length == 0 && old_codes.length > 0 ) {
      alert( "请至少选择一个special code." );
      $scope.local.this_plan.special_codes = oldVal;
      return;
    }

    $scope.local.this_plan.items = $scope.updateItemsByCodesChange( $scope.local.this_plan.items, codes, has_quantity );
  }, true );

  $scope.editNotAllowedWarning = function( is_GTA ) {
    if( is_GTA ) {
      alert( 'GTA商品不允许编辑这些信息。' );
    } else {
      alert( '上架状态无法编辑该信息。' );
    }
  };

  $scope.analysisSpecialCode = function( specialVal ) {
    return Object.keys( specialVal ).filter( function( elem ) {
      return specialVal[ elem ] == true;
    } );
  };

  $scope.init();

};

angular.module( 'ProductEditApp' ).controller( 'editPricePlanCtrl', [
  '$scope', '$rootScope', '$route', '$http', '$q', 'commonFactory', editPricePlanCtrl
] );
