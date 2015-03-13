/**
 * Created by admin on 14-5-17.
 */

var editDeparturePointCtrl = function( $scope, $route, $http, $q, commonFactory, $fileUploader ) {

  $scope.departure_point_editing = false;
  $scope.departure_limits_editing = false;

  $scope.local = {
    weekday: {
      '周1' : {
        key : '周1',
        name : '1'
      },
      '周2' : {
        key : '周2',
        name : '2'
      },
      '周3' : {
        key : '周3',
        name : '3'
      },
      '周4' : {
        key : '周4',
        name : '4'
      },
      '周5' : {
        key : '周5',
        name : '5'
      },
      '周6' : {
        key : '周6',
        name : '6'
      },
      '周7' : {
        key : '周7',
        name : '7'
      }
    }
  }

  $scope.time_picker_rule = {
    hstep      : 1,
    mstep      : 10,
    ismeridian : false
  };

  $scope.departure_status = {
    "needed" : '0'
  };

  $scope.radio_options = {
    need_departure   : {
      name  : 'needed',
      items : {
        '0' : '不需要',
        '1' : '需要'
      }
    },
    effects_duration : {
      name  : 'valid_region',
      items : {
        '1' : '自定义生效区间',
        '0' : '整个生效区间'
      }
    }
  }

  $scope.show_upload_button = 1;
  $scope.valid_region = {valid_region : '0'}; // 默认为整个区间
  $scope.need_departure_point_display = "不需要";
  $scope.title_str = "Departure Point";
  $scope.departure_status.needed = '0';
  $scope.planList = [];
  $scope.departure_list = [];
  $scope.departure_point_list = {};

  $scope.init = function() {
    var current_page = $route.current.params['current_page'] ? $route.current.params['current_page'] : 1;
    if(current_page != 4) return;

    var depature_plan = $http.get( request_urls.departurePlans );
    var product_tour_date = $http.get( request_urls.getProductSaleDateRule );
    var product_info = $http.get( request_urls.getProduct );

    var all_requests = [depature_plan, product_tour_date, product_info];
    $q.all( all_requests ).then( function( values ) {
      $scope.initData( values[0].data );
      var date_rule = values[1].data.code == 200 ? values[1].data.data : {};
      $scope.min_date = date_rule.from_date;
      $scope.max_date = date_rule.to_date;
      $scope.product_info = values[2].data.code == 200 ? values[2].data.data : {};
      $scope.is_GTA = $scope.product_info.supplier_id == 11;
    } );
  };

  $scope.initData = function( data ) {
    $scope.titles = {
      title_zh : data.data.cn_departure_title,
      title_en : data.data.en_departure_title
    };

    if( data.data.has_departure == 1 ) {
      $scope.need_departure_point_display = "需要";
      $scope.departure_status.needed = '1';

      $scope.title_str = $scope.titles.title_zh + "(" + $scope.titles.title_en + ")";

      $scope.valid_region.valid_region = data.data.valid_region.toString();
      $scope.planList = data.data.plan_list;

      for (var duration in $scope.planList) {
        for (var plan in $scope.planList[duration].plans) {
          $scope.planList[duration].plans[plan].time = new Date("1970-01-01T" + $scope.planList[duration].plans[plan].time);
        }
      }
    }
  }

  $scope.uploader = $fileUploader.create( {
                                            url     : request_urls.uploadDeparturePoints,
                                            scope   : $scope,
                                            filters : []
                                          } );

  $scope.uploader.filters.push( function( item ) {
    if( item.size / 1024 / 1024 >= 5 ) {
      alert( '上传文件不能超过5MB' );
      return false;
    } else {
      return true;
    }
  } );

  $scope.uploader.bind( 'afteraddingfile', function( event, item ) {
    item.upload();
  } );

  $scope.uploader.bind( 'success', function( event, xhr, item, response ) {
    if( response.code == 200 ) {
      var data = response.data;

      for (var i in data) {
        data[i].departure_time = data[i].departure_time.trim();
        var tmp = data[i].departure_time.split(":");
        if (tmp[0] < 10) {
          data[i].departure_time = "0" + data[i].departure_time;
        }
        var date_time = new Date("2000-01-01T" + data[i].departure_time);
        var newItem = {
          departure_plan_id : "",
          product_id        : "",
          departure_code    : "",
          valid_region      : "1",
          from_date         : $scope.current_plan.from_date,
          to_date           : $scope.current_plan.to_date,
          time              : date_time,
          additional_limit  : [
            '周1', '周2', '周3', '周4', '周5', '周6', '周7'
          ],
          short_time        : "",
          departures        : getNewDepartures(data[i].en_name, data[i].en_name)
        };

        $scope.current_plan.plans.push( newItem );
      }

      alert( response.msg );
    } else {
      alert( response.msg );
    }

    $scope.uploader.queue.pop();
  } );

  $scope.triggerUpload = function(current_plan) {
    $scope.current_plan = current_plan;

    $( '#excel-upload' ).trigger( 'click' );
  };

  $scope.toggleItem = function( id, items ) {
    id = id.toString();
    var index = items.indexOf( id );
    if( index === -1 ) {
      items.push( id );
    } else {
      items.splice( index, 1 );
    }
  };

  $scope.addDeparturePlanItem = function( departurePlan ) {
    var newItem = {
      departure_plan_id : "",
      product_id        : "",
      departure_code    : "",
      valid_region      : "1",
      from_date         : departurePlan.from_date,
      to_date           : departurePlan.to_date,
      time              : new Date("1970-01-01T00:00"),
      additional_limit  : [
        '周1', '周2', '周3', '周4', '周5', '周6', '周7'
      ],
      short_time        : "",
      departures        : getNewDepartures("", "")
    };

    departurePlan.plans.push( newItem );
  }

  $scope.deleteDeparturePlanItem = function( departurePlan, plan ) {
    if( departurePlan.plans.length < 2 ) {
      alert( "至少要有一个departure point在此生效区间中。" );
      return;
    }

    if( !confirm( '删除后不可恢复。\n点击“确定”来删除。' ) ) return;

    $scope.removeLocalPlanItem( departurePlan, plan );
  }

  $scope.removeLocalPlanItem = function( departurePlan, plan ) {
    var index = departurePlan.plans.indexOf( plan );
    if( index >= 0 ) {
      departurePlan.plans.splice( index, 1 );
    }
  }

  $scope.addDeparturePlan = function( valid_region ) {
    if( valid_region == "1" ) {
      var n = 0;
      for( var i in $scope.planList ) {
        n++;
      }
      var key = $scope.min_date + '_' + $scope.max_date + '_' + n.toString();
      $scope.planList[key] = {
        from_date    : $scope.min_date,
        to_date      : $scope.max_date,
        valid_region : valid_region,
        plans        : []
      };

      for( var i = 0; i < 4; i++ )
        $scope.addDeparturePlanItem( $scope.planList[key] );
    } else {
      $scope.planList[0].from_date = $scope.min_date;
      $scope.planList[0].to_date = $scope.max_date;
      $scope.planList[0].valid_region = valid_region;
      $scope.plans = [];
      for( var i = 0; i < 4; i++ )
        $scope.addDeparturePlanItem( $scope.planList[0] );
    }
  }

  $scope.deleteOneDuration = function( index ) {
    var i = 0;
    for( var key in $scope.planList ) {
      i++;
    }
    if( i < 2 ) {
      alert( "至少包含一个生效区间" );
      return;
    }

    i = 0;
    for( var key in $scope.planList ) {
      if( i == index ) {
        delete $scope.planList[key];
        break;
      }
      i++;
    }
  }

  $scope.radio_options.effects_duration.onchange = function() {
    console.log( 'changed!' + $scope.valid_region.valid_region );

    if( $scope.valid_region.valid_region == "0" ) {
      var index = 0;
      var first_plan;
      for( var prop in $scope.planList ) {

        if( index == 0 && typeof ($scope.planList[prop]) == 'object' ) {
          // copy first plan.
          first_plan = new Object();
          for( var i in $scope.planList[prop] ) {
            first_plan[i] = $scope.planList[prop][i];
          }
        }

        delete $scope.planList[prop];
        index++;
      }
      $scope.planList = [];
      if( first_plan != null ) {
        $scope.planList.push( first_plan );
      } else {
        $scope.planList.push( {
                                from_date    : "",
                                to_date      : "",
                                valid_region : "",
                                plans        : []
                              } );
        $scope.addDeparturePlan( "0" );
      }
    } else if( $scope.valid_region.valid_region == "1" ) {
      var first_plan;
      if( typeof ($scope.planList[0]) == 'object' ) {
        // copy first plan.
        first_plan = new Object();
        for( var i in $scope.planList[0] ) {
          first_plan[i] = $scope.planList[0][i];
        }
        first_plan.from_date = $scope.min_date;
        first_plan.to_date = $scope.max_date;
      }

      delete $scope.planList[0];
      $scope.planList = new Object();
      if( first_plan.hasOwnProperty( "plans" ) && first_plan.plans != null ) {
        $scope.planList[first_plan.from_date + '_' + first_plan.to_date] = first_plan;
      } else {
        $scope.addDeparturePlan( "1" );
      }
    }
  };

  $scope.radio_options.need_departure.onchange = function() {
    console.log( 'changed!' + $scope.departure_status.needed );

    if( $scope.departure_status.needed == '1' ) {
      if( $scope.planList[0].plans.length == 0 ) {
        $scope.addDeparturePlan( "0" );
      }
      $scope.need_departure_point_display = "需要";
    }
  };

  $scope.editDeparturePlans = function() {
//    if( $scope.is_GTA || $scope.product_info.status == 3 ) {
//      $scope.editNotAllowedWarning();
//      return;
//    }
    $scope.departure_limits_editing = true;
    if( $scope.valid_region.valid_region == 0 ) {
      if( $scope.planList.length == 0 ) {
        $scope.planList.push( {
                                from_date    : "",
                                to_date      : "",
                                valid_region : "0",
                                plans        : []
                              } );
      }
    }
  }

  $scope.submitDeparturePlanChanges = function() {

    var en_departure_title = "";
    var cn_departure_title = "";
    var planList = [];
    var valid_region = "";

    if( $scope.departure_status.needed == '1' ) {
      if( $scope.valid_region.valid_region == '1' ) {
        var result = $scope.validateDurations();

        if( result.code > 0 ) {
          alert( result.msg );
          if( result.code < 200 ) {
            return;
          }
        }
      }

//      $scope.checkEmptyRecord();

      if( !$scope.checkDuplicateItem() ) {
        alert( "同一生效区间内有重复的departure point，请检查后再进行保存。" );
        return;
      }

      en_departure_title = angular.copy( $scope.titles.title_en );
      cn_departure_title = angular.copy( $scope.titles.title_zh );

      if( en_departure_title == '' || cn_departure_title == '' ) {
        alert( "显示名称不能为空" );
        return;
      }

      planList = angular.copy( $scope.planList );

      for( var duration in planList ) {
        var isEffectWhole = $scope.valid_region.valid_region;
        var fromDate = isEffectWhole == 0 ? $scope.min_date : planList[duration].from_date;
        var toDate = isEffectWhole == 0 ? $scope.max_date : planList[duration].to_date;

        for( var planIndex in planList[duration].plans ) {
          var planItem = planList[duration].plans[planIndex];
          planItem.from_date = fromDate;
          planItem.to_date = toDate;
          planItem.valid_region = isEffectWhole;
          planItem.additional_limit = planItem.additional_limit.sort().join( ";" );
          planItem.departure_code = planItem.departure_code.toString();
          planItem.time = $scope.getTimeFromDate(planItem.time);
        }
      }
      valid_region = angular.copy( $scope.valid_region.valid_region );
    }

    var postData = {
      has_departure      : angular.copy( $scope.departure_status.needed ),
      en_departure_title : en_departure_title,
      cn_departure_title : cn_departure_title,
      valid_region       : valid_region,
      plan_list          : planList
    };

    $http.post( request_urls.departurePlans, postData ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.initData( data );
        alert( "保存成功" );
      } else if( data.code == 400 ) {
        alert( data.msg );
      } else if( data.code == 401 ) {
        alert( data.msg );
      }
    } );

    $scope.departure_limits_editing = false;
  }


  $scope.validateDurations = function() {
    var limitation = {
      from_date : $scope.min_date,
      to_date   : $scope.max_date
    };
    var duration_list = [];
    for( var key in $scope.planList ) {
      duration_list.push( {
                            from_date : new Date( $scope.planList[key].from_date ),
                            to_date   : new Date( $scope.planList[key].to_date )
                          } );
    }
    return commonFactory.validateDurations( duration_list, limitation );
  }

  $scope.checkEmptyRecord = function() {
    var need_remove_duration = [];
    for( var duration in $scope.planList ) {
      var need_remove_plan = [];
      for( var planIndex in $scope.planList[duration].plans ) {
        var planItem = $scope.planList[duration].plans[planIndex];
        var tmp1 = planItem.departures[0].departure_point.trim();
        var tmp2 = planItem.departures[1].departure_point.trim();
        if( tmp1 == "" && tmp2 == "" ) {
          need_remove_plan.push( planIndex );
        }
      }

      var plan_shifting = 0; // 删除之后的元素偏移量。
      for( var p_index in need_remove_plan ) {
        $scope.planList[duration].plans.splice( need_remove_plan[p_index] - plan_shifting, 1 );
        plan_shifting++;
      }

      if( $scope.planList[duration].plans.length == 0 ) {
        need_remove_duration.push( duration );
      }
    }

    var duration_shifting = 0; // 删除之后的元素偏移量。
    for( var d_index in need_remove_duration ) {
      if( typeof ($scope.planList) != 'object' ) {
        $scope.planList.splice( need_remove_duration[d_index] - duration_shifting, 1 );
      } else {
        delete $scope.planList[need_remove_duration[d_index]];
      }

      duration_shifting++;
    }
  }

  $scope.checkDuplicateItem = function() {
    for( var duration in $scope.planList ) {
      var item_arr = [];
      for( var planIndex in $scope.planList[duration].plans ) {
        var plan_item = $scope.planList[duration].plans[planIndex];
        var item_info = plan_item.departures[1].departure_point + $scope.getTimeFromDate(plan_item.time);
        item_arr.push( item_info );
      }

      item_arr = item_arr.sort();
      for( var i = 0; i < item_arr.length; i++ ) {
        if( item_arr[i - 1] == item_arr[i] ) {
          return false;
        }
      }
    }

    return true;
  }

  $scope.editNotAllowedWarning = function() {
    if( $scope.is_GTA ) {
      alert( 'GTA商品不允许编辑这些信息。' );
    } else {
      alert( '上架状态无法编辑该信息。' );
    }
  }

  $scope.getTimeFromDate = function (date) {
    var time_str = "";
    if (typeof(date) == 'object')
    {
      if (date.getHours() < 10) {
        time_str = "0"+date.getHours();
      } else {
        time_str = date.getHours();
      }
      time_str += ":";

      if (date.getMinutes() < 10) {
        time_str += "0"+date.getMinutes();
      } else {
        time_str += date.getMinutes();
      }
    }
    return time_str;
  }

  $scope.init();
}

var getNewDepartures = function(en_name, cn_name) {
  var departure_en = {
    product_id      : "",
    departure_code  : "",
    departure_point : en_name,
    address_lines   : "",
    telephone       : "",
    description     : "",
    first_service   : "",
    last_service    : "",
    intervals       : "",
    language_id     : "1"
  };

  var departure_cn = {
    product_id      : "",
    departure_code  : "",
    departure_point : cn_name,
    address_lines   : "",
    telephone       : "",
    description     : "",
    first_service   : "",
    last_service    : "",
    intervals       : "",
    language_id     : "2"
  };

  var departures = new Array();
  departures.push( departure_cn, departure_en );

  return departures;
}

var SaveModalCtrl = function( $scope, $modalInstance ) {
  $scope.saveForm = function() { //Remain on page and trigger save
    $modalInstance.close( 'save' );
  };
  $scope.navigate = function() { //Move away
    $modalInstance.close( 'navigate' );
  };
};

module.controller( 'editDeparturePointCtrl', ['$scope', '$route', '$http', '$q', 'commonFactory', '$fileUploader', editDeparturePointCtrl] );
