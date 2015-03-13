//TODO: notification
//TODO: Age Validation

var editPriceAttributeCtrl = function( $scope, $rootScope, $route, $http, $q, commonFactory ) {
  $scope.radio_options = {
    'ticket_type'       : {
      name  : 'ticket_type',
      class : 'inline',
      items : {
        '1' : '一种票',
        '2' : '区分成人／儿童',
        '3' : '多种票'
      }
    },
    'need_tour_date'    : {
      name  : 'need_tour_date',
      class : 'inline',
      items : {
        '1' : '需要',
        '0' : '不需要'
      }
    },
    'need_special_code' : {
      name  : 'need_special_code',
      class : 'inline',
      items : {
        '1' : '需要',
        '2' : '不需要'
      }
    }
  };
  $scope.data = {
    'edit_label'          : {
      '1' : '编辑',
      '2' : '保存'
    },
    'in_edit'             : {
      tour_date    : '1',
      ticket_type  : '1',
      special_code : '1'
    },
    'tour_date'           : {},
    'ticket_type'         : {},
    'special_code'        : {},
    'bundle_hotels'       : {},
    'hotel_special_codes' : {}
  };
  $scope.local = {
    ticket_type         : {
      map : {}
    },
    close_ranges        : {
      'singleday' : '单独固定日期',
      'weekday'   : '按周期循环',
      'range'     : '时间段'
    },
    current_range       : 'range',
    added_field         : {
      'singleday' : '',
      'weekday'   : '',
      'range'     : {
        from_date : '',
        to_date   : ''
      }
    },
    special_status      : "1",
    on_special_codes    : [],
    off_special_codes   : [],
    special_status_menu : [
      {
        key  : '1',
        item : '生效套餐'
      },
      {
        key  : '0',
        item : '禁用套餐'
      }
    ]
  };

  $scope.init = function() {
    var current_page = $route.current.params['current_page'] ? $route.current.params['current_page'] : 1;
    if( current_page != 1 ) return;

    var special_code = $http.get( request_urls.getProductSpecialCodes );
    var ticket_type = $http.get( request_urls.ticketTypes );
    var ticket_rule = $http.get( request_urls.ticketRules );
    var date_rule = $http.get( request_urls.getDateRule );
    var product_info = $http.get( request_urls.getProduct );
    var all_requests = [special_code, ticket_type, ticket_rule, date_rule, product_info];
    $q.all( all_requests ).then( function( values ) {
      var len;
      var i;
      var curr;
      var temp;

      //Special Code
      $scope.data.special_code = values[0].data.code == 200 ? values[0].data.data : {};

      if( values[4].data.data.type == '8' ) {
        $scope.getBundleHotels();
      }

      $scope.data.special_code.need_special_code = '2';
      $scope.local.on_special_codes = [];
      $scope.local.off_special_codes = [];
      for( var i in $scope.data.special_code.special_codes ) {
        if( $scope.data.special_code.special_codes[i].status == '1' ) {
          $scope.data.special_code.need_special_code = '1';
          $scope.local.on_special_codes.push( angular.copy( $scope.data.special_code.special_codes[i] ) );
        } else {
          $scope.local.off_special_codes.push( angular.copy( $scope.data.special_code.special_codes[i] ) );
        }
      }

      if( $scope.local.special_status == '1' ) {
        $scope.data.special_code.special_codes = $scope.local.on_special_codes;
      } else {
        $scope.data.special_code.special_codes = $scope.local.off_special_codes;
      }
      $scope.updateSpecialCodeTitle();

      //Ticket Type
      $scope.data.ticket_type = values[2].data.code == 200 ? values[2].data.data : {};
      len = $scope.data.ticket_type.ticket_rules.length;
      for( i = 0; i < len; i++ ) {
        curr = $scope.data.ticket_type.ticket_rules[i];
        temp = curr.age_range.split( '-' );
        if( temp.length == 2 ) {
          curr.age = {
            begin : parseInt( temp[0], 10 ),
            end   : parseInt( temp[1], 10 )
          };
        } else {
          curr.age = {
            begin : '',
            end   : ''
          };
        }
      }

      $scope.local.ticket_type.all = values[1].data.code == 200 ? values[1].data.data : [];
      $scope.local.ticket_type.all.shift(); //Get rid of first one
      len = $scope.local.ticket_type.all.length;
      for( i = 0; i < len; i++ ) {
        curr = $scope.local.ticket_type.all[i];
        $scope.local.ticket_type.map[curr.ticket_id] = curr.cn_name;
      }

      //Tour Date
      $scope.data.tour_date = values[3].data.code == 200 ? values[3].data.data : {};
      var len = $scope.data.tour_date.product_tour_operation.length;
      for( var i = 0; i < len; i++ ) {
        var result = processCloseDate( $scope.data.tour_date.product_tour_operation[i].close_dates );
        $scope.data.tour_date.product_tour_operation[i].parts = result; //Close Dates
      }

      $scope.product_info = values[4].data.code == 200 ? values[4].data.data : {};
      $scope.is_GTA = $scope.product_info.supplier_id == 11;

      $scope.updateTourDateTitle();
    } );
  };

  $scope.addSpecial = function() {
    var new_code = {
      cn_name              : '',
      en_name              : '',
      description          : '',
      product_origin_name  : '',
      status               : $scope.local.special_status == "1" ? "1" : "0",
      mapping_product_id   : '0',
      mapping_special_code : '',
      hotel_specials       : {}
    };
    $scope.data.special_code.special_codes.push( new_code );
  };

  $scope.toggleSpecialStatus = function( index ) {
    if( $scope.local.special_status == "1" ) {
      if( !confirm( "你确定要禁用此套餐吗？" ) ) {
        return;
      }

      $scope.local.on_special_codes[index].status = '0';
      var code = angular.copy( $scope.local.on_special_codes[index] );

      $scope.local.on_special_codes.splice( index, 1 );
      $scope.local.off_special_codes.push( code );
    } else {
      $scope.local.off_special_codes[index].status = '1';
      var code = angular.copy( $scope.local.off_special_codes[index] );

      $scope.local.off_special_codes.splice( index, 1 );
      $scope.local.on_special_codes.push( code );
    }
  };

  $scope.toggleSpecial = function( target ) {
    if( $scope.product_info.status == 3 ) {
      $scope.editNotAllowedWarning();
      return;
    }
    if( target == '2' ) { //Edit to View
      if( $scope.data.special_code.need_special_code == '1' && $scope.local.on_special_codes.length == 0 ) {
        if( !confirm( "当前没有启用的Special Code, 确定要继续保存吗？" ) ) {
          return;
        }
      }

      var post_arr = [];
      var post_data = {};
      if( $scope.local.on_special_codes.length > 0 ) {
        post_arr = post_arr.concat( $scope.local.on_special_codes );
      }
      if( $scope.local.off_special_codes.length > 0 ) {
        post_arr = post_arr.concat( $scope.local.off_special_codes );
      }


      if ($scope.product_info.type == "8") {
        if( $scope.local.has_bundle ) {
          for( var i in post_arr ) {
            if( $scope.local.has_bundle && post_arr[i].mapping_product_id == '0' ) {
              alert( "请选择绑定的酒店" );
              return;
            }

            if( post_arr[i].bundle_has_special && post_arr[i].mapping_special_code.length == 0 ) {
              alert("请选择Special Code");
              return;
            }
          }
        }
      }

      post_data.need_special_code = $scope.data.special_code.need_special_code;
      post_data.special_codes = post_arr;
      post_data.cn_special_title = $scope.data.special_code.cn_special_title;
      post_data.en_special_title = $scope.data.special_code.en_special_title;

      $http.post( request_urls.saveProductSpecialCodes, post_data ).success( function( data ) {
        var result = false;
        if( data.code == 200 ) {
          $scope.data.in_edit.special_code = '1';
          $scope.init();
          result = true;
        }
        $scope.alertByResult( result );
      } );
    } else { //View to Edit
      $scope.data.in_edit.special_code = '2';
    }
  };
  $scope.radio_options.need_special_code.onchange = function() {
    if( $scope.data.special_code.need_special_code == '2' ) {
      for( var index in $scope.local.on_special_codes ) {
        $scope.local.on_special_codes[index].status = '0';
        var code = angular.copy( $scope.local.on_special_codes[index] );
        $scope.local.off_special_codes.push( code );
      }

      $scope.data.special_code.special_codes = [];
      $scope.local.on_special_codes = [];
    }
  };

  //Ticket Type
  $scope.addTicket = function() {
    var new_ticket = {
      age         : {
        begin : '',
        end   : ''
      },
      age_range   : '',
      ticket_id   : '4',
      description : ''
    };
    $scope.data.ticket_type.ticket_rules.push( new_ticket );
  };
  $scope.delTicket = function( index ) {
    if( $scope.data.ticket_type.ticket_rules.length > 3 ) {
      $scope.data.ticket_type.ticket_rules.splice( index, 1 );
    } else {
      alert( "票种不能小于3。" );
    }
  };
  $scope.toggleTicket = function( target ) {
    if( $scope.product_info.status == 3 ) {
      $scope.editNotAllowedWarning();
      return;
    }
    if( target == '2' ) { //Edit to View
      //Pre-process
      var i;
      var len = $scope.data.ticket_type.ticket_rules.length;
      var types = [];

      var prev_age_begin, prev_age_end;

      for( i = 0; i < len; i++ ) {
        //Prevent Duplicate Ticket Types
        if( $scope.data.ticket_type.ticket_type == '3' ) {
          if( types.indexOf( $scope.data.ticket_type.ticket_rules[i].ticket_id ) > -1 ) {
            alert( '不能有重复的票种' );
            return;
          } else {
            types.push( $scope.data.ticket_type.ticket_rules[i].ticket_id );
          }
        }

        //For Age Check
        var age_begin = $scope.data.ticket_type.ticket_rules[i].age.begin;
        var age_end = $scope.data.ticket_type.ticket_rules[i].age.end;
        if( age_begin > age_end ) {
          alert( '年龄范围，开始年龄不能大于结束年龄。' );
          return;
        }

        if( i == 0 ) {
          prev_age_begin = age_begin;
          prev_age_end = age_end;
        } else {
          if( !(prev_age_begin > age_end || prev_age_end < age_begin) ) {
            alert( '年龄范围不能重叠。' );
            return;
          }
        }

        //Handle Age Range
        if( age_begin == '' && age_end == '' ) {
          $scope.data.ticket_type.ticket_rules[i].age_range = '';
        } else {
          $scope.data.ticket_type.ticket_rules[i].age_range = age_begin + '-' + age_end;
        }

        if($scope.data.ticket_type.ticket_type == '1') {
          $scope.data.ticket_type.ticket_rules[i].is_independent = 1;
        }
      }

      $http.post( request_urls.ticketRules, $scope.data.ticket_type ).success( function( data ) {
        var result = false;
        if( data.code == 200 ) {
          $scope.data.in_edit.ticket_type = '1';
          result = true;
        }
        $scope.alertByResult( result );
      } );
    } else { //View to Edit
      $scope.data.in_edit.ticket_type = '2';
    }
  };
  $scope.getTicketRule = function( ticket_id ) {
    return {
      age         : {
        begin : '',
        end   : ''
      },
      age_range   : '',
      ticket_id   : ticket_id,
      description : ''
    };
  };

  $scope.radio_options.ticket_type.onchange = function() {
    var alert_msg = "更改票种后会导致之前设定的售卖方式和价格计划失效，请重新设定售卖方式和价格计划。";

    if( $scope.data.ticket_type.ticket_type == '1' ) {
      alert( alert_msg );
      $scope.data.ticket_type.reset_ticket_type = 1;
      $scope.data.ticket_type.ticket_rules = [
        $scope.getTicketRule( 1 )
      ];
    } else if( $scope.data.ticket_type.ticket_type == 2 &&
               (!$scope.data.ticket_type.ticket_rules || $scope.data.ticket_type.ticket_rules.length != 2) ) {
      alert( alert_msg );
      $scope.data.ticket_type.reset_ticket_type = 1;
      $scope.data.ticket_type.ticket_rules = [
        $scope.getTicketRule( 2 ), $scope.getTicketRule( 3 )
      ];
    } else if( $scope.data.ticket_type.ticket_type == 3 &&
               (!$scope.data.ticket_type.ticket_rules || $scope.data.ticket_type.ticket_rules.length < 3) ) {
      alert( alert_msg );
      $scope.data.ticket_type.reset_ticket_type = 1;
      $scope.data.ticket_type.ticket_rules = [
        $scope.getTicketRule( 2 ), $scope.getTicketRule( 3 ), $scope.getTicketRule( 5 )
      ];
    }
  };

  //Tour Date
  function processCloseDate( str ) {
    var result = {
      'range'     : [],
      'weekday'   : [],
      'singleday' : []
    };
    if( !str ) {
      return result;
    }
    var parts = str.split( ';' );
    var len = parts.length;


    for( var i = 0; i < len; i++ ) {
      if( parts[i].indexOf( '周' ) > -1 ) {
        result.weekday.push( parts[i] );
      } else if( parts[i].indexOf( '/' ) > -1 ) {
        result.range.push( parts[i].replace( '/', ' - ' ) );
      } else {
        if( parts[i].trim().length > 0 ) {
          result.singleday.push( parts[i] );
        }
      }
    }

    return result;
  };

  $scope.addTour = function() {
    $http.post( request_urls.productTourOperation ).success( function( data ) {
      if( data.code == 200 ) {
        data.data.parts = processCloseDate( data.data.close_dates );
        $scope.data.tour_date.product_tour_operation.push( data.data );
      }
      //TODO: error notification
    } );
  };

  $scope.delTour = function( index ) {
    var tour_id = $scope.data.tour_date.product_tour_operation[ index ].operation_id;
    $http.delete( request_urls.productTourOperation + tour_id ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.data.tour_date.product_tour_operation.splice( index, 1 );
      }
    } );
  };

  $scope.toggleTour = function( target ) {
    if( $scope.product_info.status == 3 || $scope.is_GTA ) {
      $scope.editNotAllowedWarning();
      return;
    }
    if( target == '2' ) { //Edit to View
      $scope.formatCloseDate();

      var result = commonFactory.validateDurations( $scope.data.tour_date.product_tour_operation );
      if( result.code > 0 ) {
        alert( result.msg );
        return;
      }

      if( $scope.validCloseDates() == false ) {
        alert( '关闭日期不能超出售卖日期范围。' );
        return;
      }

      $http.post( request_urls.saveDateRule, $scope.data.tour_date ).success( function( data ) {
        var result = false;
        if( data.code == 200 ) {
          var len = $scope.data.tour_date.product_tour_operation.length;
          for( var i = 0; i < len; i++ ) {

            $scope.data.tour_date.product_tour_operation[i].parts = processCloseDate( $scope.data.tour_date.product_tour_operation[i].close_dates ); //Close Dates
          }
          $scope.updateTourDateTitle();

          $scope.data.in_edit.tour_date = '1';
          result = true;
        }
        $scope.alertByResult( result );
      } );
    } else { //View to Edit
      $scope.data.in_edit.tour_date = '2';
    }
  };

  $scope.validCloseDates = function() {
    var len = $scope.data.tour_date.product_tour_operation.length;
    for( var i = 0; i < len; i++ ) {
      var close_dates = $scope.data.tour_date.product_tour_operation[i].close_dates;
      if( close_dates.length > 0 ) {
        var from_date = $scope.data.tour_date.product_tour_operation[i].from_date;
        var to_date = $scope.data.tour_date.product_tour_operation[i].to_date;

        var parts = close_dates.split( ';' );
        var parts_len = parts.length;
        for( var j = 0; j < parts_len; j++ ) {
          var part = parts[j];
          if( part.indexOf( '-' ) > 0 && part.indexOf( '/' ) == -1 ) {
            if( part < from_date || part > to_date ) {
              return false;
            }
          }
        }
      }
    }

    return true;
  }

  $scope.updateTourDateTitle = function() {
    if( $scope.data.tour_date.product_date_rule.need_tour_date == '1' ) {
      $scope.tour_date_title = $scope.data.tour_date.cn_tour_date_title + "(" +
                               $scope.data.tour_date.en_tour_date_title + ")";
    } else {
      $scope.tour_date_title = "使用日期";
    }
  };

  $scope.updateSpecialCodeTitle = function() {
    if( $scope.data.special_code.need_special_code == '2' ) {
      $scope.special_code_title = "Special Code";
    } else {
      $scope.special_code_title = $scope.data.special_code.cn_special_title + "(" +
                                  $scope.data.special_code.en_special_title + ")";

    }
  };

  $scope.editNotAllowedWarning = function() {
    $scope.alertByResult( $scope.is_GTA, 'GTA商品不允许编辑这些信息。', '上架状态无法编辑该信息。' );
  };

  $scope.alertByResult = function( result, true_msg, false_msg ) {
    alert( result ? (true_msg ? true_msg : '保存成功！') : (false_msg ? false_msg : '保存失败！') );
  };

  $scope.removeCloseItem = function( index, target_arr ) {
    if( confirm( "确定要删除此条不可购买日期吗？" ) ) {
      target_arr.splice( index, 1 );
    }
  };

  $scope.addCloseItem = function( current_tour_operation ) {
    var range = $scope.local.current_range;
    if( range == "range" ) {

      if( commonFactory.formatDate( $scope.local.added_field.range.from_date ).trim() == '' ||
          commonFactory.formatDate( $scope.local.added_field.range.to_date ).trim() == '' ) {
        alert( "请填写完整的日期区间后再添加" );
        return;
      }
      var str = commonFactory.formatDate( $scope.local.added_field.range.from_date ) + " - " +
                commonFactory.formatDate( $scope.local.added_field.range.to_date );
      current_tour_operation.parts.range.push( str );
      $scope.local.added_field.range.from_date = '';
      $scope.local.added_field.range.to_date = '';
    } else if( range == "weekday" ) {
      if( $scope.local.added_field.weekday.trim() == '' ) {
        alert( "请填写后再添加" );
        return;
      }
      var str = '';
      if( $scope.local.added_field.weekday.indexOf( '1' ) != -1 ||
          $scope.local.added_field.weekday.indexOf( '一' ) != -1 ) {
        str = '周1';
      } else if( $scope.local.added_field.weekday.indexOf( '2' ) != -1 ||
                 $scope.local.added_field.weekday.indexOf( '二' ) != -1 ) {
        str = '周2';
      } else if( $scope.local.added_field.weekday.indexOf( '3' ) != -1 ||
                 $scope.local.added_field.weekday.indexOf( '三' ) != -1 ) {
        str = '周3';
      } else if( $scope.local.added_field.weekday.indexOf( '4' ) != -1 ||
                 $scope.local.added_field.weekday.indexOf( '四' ) != -1 ) {
        str = '周4';
      } else if( $scope.local.added_field.weekday.indexOf( '5' ) != -1 ||
                 $scope.local.added_field.weekday.indexOf( '五' ) != -1 ) {
        str = '周5';
      } else if( $scope.local.added_field.weekday.indexOf( '6' ) != -1 ||
                 $scope.local.added_field.weekday.indexOf( '六' ) != -1 ) {
        str = '周6';
      } else if( $scope.local.added_field.weekday.indexOf( '7' ) != -1 ||
                 $scope.local.added_field.weekday.indexOf( '七' ) != -1 ) {
        str = '周7';
      }

      if( current_tour_operation.parts.weekday.indexOf( str ) == -1 ) {
        current_tour_operation.parts.weekday.push( str );
      }


      $scope.local.added_field.weekday = '';
    } else if( range == "singleday" ) {
      if( commonFactory.formatDate( $scope.local.added_field.singleday ).trim() == '' ) {
        alert( "请选择日期后再添加" );
        return;
      }
      var date_str = commonFactory.formatDate( $scope.local.added_field.singleday );
      if( current_tour_operation.parts.singleday.indexOf( date_str ) == -1 ) {
        current_tour_operation.parts.singleday.push( date_str );
      }
      $scope.local.added_field.singleday = '';
    }
  };

  $scope.formatCloseDate = function() {
    for( var i in $scope.data.tour_date.product_tour_operation ) {
      var operation = $scope.data.tour_date.product_tour_operation[i];
      var close_str = '';
      if( operation.parts.weekday.length > 0 ) {
        close_str += operation.parts.weekday.join( ";" );
        close_str += ";";
      }
      if( operation.parts.singleday.length > 0 ) {
        close_str += operation.parts.singleday.join( ";" );
        close_str += ";";
      }
      if( operation.parts.range.length > 0 ) {
        for( var j in operation.parts.range ) {
          var date = operation.parts.range[j].replace( ' - ', '/' );
          close_str += date + ";";
        }
      }
      operation.close_dates = close_str;
    }
  }

  $scope.changeSpecialStatusMenu = function( status ) {
    if( status == $scope.local.special_status )
      return;

    if( status == '1' ) {
      $scope.local.special_status = '1';
      $scope.data.special_code.special_codes = $scope.local.on_special_codes;
    } else {
      $scope.local.special_status = '0';
      $scope.data.special_code.special_codes = $scope.local.off_special_codes;
    }
  };

  $scope.getBundleHotels = function() {
    $http.get( request_urls.getBundleHotelSpecial ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.data.bundle_hotels = data.data.description;
        if( Object.keys( $scope.data.bundle_hotels ).length > 0 ) {
          $scope.local.has_bundle = true;
        } else {
          $scope.local.has_bundle = false;
        }

        $scope.data.hotel_special_codes = data.data.special_code;

        for( var i in $scope.data.special_code.special_codes ) {
          $scope.data.special_code.special_codes[i].hotel_specials = {};
          if( parseInt( $scope.data.special_code.special_codes[i].mapping_product_id ) > 0 &&
              $scope.data.hotel_special_codes[$scope.data.special_code.special_codes[i].mapping_product_id].need_special_code ) {
            $scope.data.special_code.special_codes[i].hotel_specials = $scope.data.hotel_special_codes[$scope.data.special_code.special_codes[i].mapping_product_id].special_codes;
            if( Object.keys( $scope.data.special_code.special_codes[i].hotel_specials ).length > 0 ) {
              $scope.data.special_code.special_codes[i].bundle_has_special = true;
            } else {
              $scope.data.special_code.special_codes[i].bundle_has_special = false;
            }
          }
        }
      }
    } );
  };

  $scope.changeHotel = function( index ) {
    var product_id = $scope.data.special_code.special_codes[index].mapping_product_id;
    var specials = $scope.data.hotel_special_codes[product_id].special_codes;
    var hotel = $scope.data.bundle_hotels[product_id];
    $scope.data.special_code.special_codes[index].hotel_specials = specials;
    $scope.data.special_code.special_codes[index].cn_name = hotel.product_name;
    $scope.data.special_code.special_codes[index].en_name = hotel.product_en_name;

    if( Object.keys( specials ).length > 0 ) {
      $scope.data.special_code.special_codes[index].bundle_has_special = true;
    } else {
      $scope.data.special_code.special_codes[index].bundle_has_special = false;
    }
    $scope.data.special_code.special_codes[index].mapping_special_code = '';
  };

  $scope.init();
};

angular.module( 'ProductEditApp' ).controller( 'editPriceAttributeCtrl', [
  '$scope', '$rootScope', '$route', '$http', '$q', 'commonFactory', editPriceAttributeCtrl
] );