controllers.OrderSearchListCtrl = function ( $scope, $http, $rootScope, commonFactory ) {
  $scope.data = {};
  $scope.local = {
    chosen                : {
      supplier    : {
        supplier_id: '0'
      },
      order_status: {
        order_status_id: '0'
      }
    },
    search_criteria       : window.location.search && JSON.parse( decodeURIComponent( window.location.search.split( 'search=' )[1] ) ),
    show_advance_search   : false,
    visible_search_options: [
      {
        key   : 'order_id',
        value : '订单号',
        status: 0
      },
      {
        key   : 'contacts_name',
        value : '联系人',
        status: 0
      },
      {
        key   : 'contacts_telephone',
        value : '联系电话',
        status: 0
      },
      {
        key   : 'contacts_email',
        value : '联系人邮箱',
        status: 0
      },
    ],
    supplier_list         : [],
    grid_options          : {
      data    : [],
      table   : {
        table_id: 'order_grid'
      },
      label    : {
        getHead : function( col, i ) {
          return col.label;
        },
        getBody: function ( col, i, record, j ) {
          if( col.name == "order_id" || col.name == "name" || col.name == "contacts_name" ) {
            return "<a href='" + getDetailLink( record.order_id ) + "' target = '_blank'>" +
                                                                                           record[col.name].toString() + "</a>";
          } else if( col.name == 'tour_date' && record[col.name] == '0000-00-00' ) {
            return '';
          } else {
            return record[col.name]?record[col.name].toString():'';
          }
        }
      },
      query   : {
        sort        : {
          'date_added': 0
        },
        paging      : {
          start: 0,
          limit: 10
        },
        query_filter: {
          'search_text'           : '',
          'search_product_text'   : '',
          'search_field'          : 'order_id',
          'search_added_from_date': '',
          'search_added_to_date'  : '',
          'search_tour_from_date' : '',
          'search_tour_to_date'   : '',
          'search_passenger'      : '',
          'filter_supplier_id'    : 0,
          'filter_order_status_id': '',
          'filterNotShipped'      : 0,
          'filterNeedRefund'      : 0,
          'filterQuestion'        : 0,
          'filterToDo'            : 0,
          'notShipped_use_date':0,
          'notShipped_over_ship_date' :0,
          'notShipped_to_ship_date':0,
          'todo_complain':0,
          'todo_urge':0,
          'todo_bill':0,
          'todo_total':0,
          'todo_edit':0,
          'todo_feedback':0,
          'todo_date_today':0,
          'todo_date_tomorrow':0,
          'todo_date_after_tomorrow':0
        }
      },
      custom  : {
        filterOrderStatus: function () {
          $scope.filterOrderStatus();
        }
      },
      request : {
        api_url: $request_urls.getOrderList
      },
      columns : [
        {
          name     : 'order_id',
          width    : '5%',
          label    : '订单号',
          use_sort : false
        },
        {
          name     : 'supplier_name',
          width    : '7%',
          label    : '供应商',
          use_sort : false
        },
        {
          name     : 'name',
          width    : '18%',
          label    : '商品名称',
          use_sort : false
        },
        {
          name     : 'contacts_name',
          width    : '13%',
          label    : '联系人',
          use_sort : false
        },
        {
          name     : 'contacts_telephone',
          width    : '10%',
          label    : '联系电话',
          use_sort : false
        },
        {
          name     : 'contacts_email',
          width    : '10%',
          label    : '联系邮箱',
          use_sort : false
        },
        {
          name     : 'cn_name',
          width    : '12%',
          label    : '订单状态',
          use_sort : false
        },
        {
          name     : 'tour_date',
          width    : '10%',
          label    : '出行日期',
          use_sort : true
        },
        {
          name     : 'date_added',
          width    : '15%',
          label    : '下单日期',
          use_sort : true
        }
      ],
      customer: {
        status_list: []
      }
    },
    order_status          : [
      {
        label: '全部',
        name : 'filterAll',
        key  : 0,
        count: 0
      },
      {
        label: '待发货',
        name : 'filterNotShipped',
        key  : 1,
        count: 0
      },
      {
        label: '待退款',
        name : 'filterNeedRefund',
        key  : 2,
        count: 0
      },
      {
        label: '问题',
        name : 'filterQuestion',
        key  : 3,
        count: 0
      },
      {
        label: '待办',
        name : 'filterToDo',
        key  : 4,
        count: 0
      }
    ],
    search_order_status   : 0,
    no_shipped_status          : [
      {
        label: '全部',
        name : 'noShipped_all',
        key  : 0,
        count: 0
      },
      {
        label: '临近使用日期',
        name : 'notShipped_use_date',
        key  : 1,
        count: 0
      },
      {
        label: '已超发货时限',
        name : 'notShipped_over_ship_date',
        key  : 2,
        count: 0
      },
      {
        label: '临近发货时限',
        name : 'notShipped_to_ship_date',
        key  : 3,
        count: 0
      }
    ],
    search_no_sipped      : 0,
    todo_status          : [
      {
        label: '全部',
        name : 'todo_all',
        key  : 0,
        count: 0
      },
      {
        label: '记录',
        name : 'todo_record',
        key  : 1,
        count: 0
      },
      {
        label: '催单',
        name : 'todo_urge',
        key  : 2,
        count: 0
      },
      {
        label: '开发票',
        name : 'todo_bill',
        key  : 3,
        count: 0
      },
      {
        label: '结算',
        name : 'todo_total',
        key  : 4,
        count: 0
      },
      {
        label: '修改',
        name : 'todo_edit',
        key  : 5,
        count: 0
      },
      {
        label: '回访',
        name : 'todo_feedback',
        key  : 6,
        count: 0
      },
      {
        label: '投诉',
        name : 'todo_complain',
        key  : 7,
        count: 0
      },
    ],
    search_todo      : 0,
    todo_date_status          : [
      {
        label: '全部',
        name : 'todo_date_all',
        key  : 0
      },
      {
        label: '今天',
        name : 'todo_date_today',
        key  : 1
      },
      {
        label: '明天',
        name : 'todo_date_tomorrow',
        key  : 2
      },
      {
        label: '后天',
        name : 'todo_date_after_tomorrow',
        key  : 3
      }
    ],
    search_todo_date      : 0,
    cost_amount           : ''
  };

  $scope.init = function () {
    $rootScope.$emit( 'setBreadcrumb', {
      back: {},
      body: {
        content: '订单列表'
      }
    } );
    for( var key in $scope.local.search_criteria ) {
      if( key == "has_combination" ) {
        $scope.local.show_advance_search = $scope.local.search_criteria[key];
      }

      if( key == "filterNotShipped" && $scope.local.search_criteria[key] == 1 ) {
        $scope.local.search_order_status = 1;
      } else if( key == "filterNeedRefund" && $scope.local.search_criteria[key] == 1 ) {
        $scope.local.search_order_status = 2;
      } else if( key == "filterQuestion" && $scope.local.search_criteria[key] == 1 ) {
        $scope.local.search_order_status = 3;
      } else if( key == "filterToDo" && $scope.local.search_criteria[key] == 1 ) {
        $scope.local.search_order_status = 4;
      }
      $scope.local.grid_options.query.query_filter[key] = $scope.local.search_criteria[key];
    }

    for( var i in $scope.local.visible_search_options ) {
      if( $scope.local.visible_search_options[i].key == $scope.local.grid_options.query.query_filter.search_field ) {
        $scope.local.visible_search_options[i].status = 1;
      }
    }

    if( $scope.local.search_criteria.hasOwnProperty( 'supplier_id' ) &&
        $scope.local.grid_options.query.query_filter.filter_supplier_id == 13 ) {
      $http.get( $request_urls.getUnShippedOrderCostAmount +
                 $scope.local.grid_options.query.query_filter.filter_supplier_id ).success( function ( data ) {
                                                                                              if( data.code == 200 ) {
                                                                                                $scope.local.cost_amount = "还未发货订单总额：" +
                                                                                                                           data.data[0].total;
                                                                                              }
                                                                                            } );
    }

    $scope.local.chosen.supplier.supplier_id = "" + $scope.local.grid_options.query.query_filter.filter_supplier_id;
    $scope.local.chosen.order_status.order_status_id = "" + $scope.local.grid_options.query.query_filter.filter_order_status_id;

    $scope.getOrderTotals( $scope.local.grid_options.query.query_filter.filter_supplier_id );

    $http.get( $request_urls.getOrderStatusList ).success( function ( data ) {
      if( data.code == 200 ) {
        $scope.local.grid_options.customer.status_list = data.data.status;
      }
    } );

    commonFactory.getAjaxSearchSupplierList( true ).then( function ( data ) {
      $scope.local.supplier_list = data;
      $scope.local.grid_options.fetchData();
      $rootScope.$emit( 'loadStatus', false );
    } );
  };

  $scope.filterOrderStatus = function () {
    $scope.local.grid_options.query.paging.start = 0;
    $scope.getOrderTotals( $scope.local.grid_options.query.query_filter.filter_supplier_id );
    $scope.local.grid_options.fetchData();
  };

  $scope.hasFilter = function () {
    if( $scope.local.grid_options.query.query_filter['filterNotShipped'] == 0 &&
        $scope.local.grid_options.query.query_filter['filterNeedRefund'] == 0 &&
        $scope.local.grid_options.query.query_filter['filterQuestion'] == 0 &&
        $scope.local.grid_options.query.query_filter['filterToDo'] == 0 ) {
      return true;
    }
  };

  $scope.clearFilter = function () {
    $scope.local.search_order_status = 0;
    for( var key in $scope.local.order_status ) {
      $scope.local.grid_options.query.query_filter[ $scope.local.order_status[key].name ] = '0';
    }
    $scope.local.grid_options.query.paging.start = 0;
    $scope.local.grid_options.fetchData();
  };

  $scope.setFilter = function ( filter ) {
    if( filter == "filterAll" ) {
      $scope.clearFilter();
    } else {
      if( $scope.local.grid_options.query.query_filter[ filter ] == 0 ) {
        //Reset all others
        for( var key in $scope.local.order_status ) {
          $scope.local.grid_options.query.query_filter[ $scope.local.order_status[key].name  ] = 0;
        }
        if( filter != "filterNotShipped") {
          for( var key in $scope.local.no_shipped_status ) {
            $scope.local.grid_options.query.query_filter[ $scope.local.no_shipped_status[key].name  ] = 0;
          }
          $scope.local.search_no_sipped = 0;
        }
        if( filter != "filterToDo") {
          for( var key in $scope.local.todo_status ) {
            $scope.local.grid_options.query.query_filter[ $scope.local.todo_status[key].name  ] = 0;
          }
          for( var key in $scope.local.todo_date_status ) {
            $scope.local.grid_options.query.query_filter[ $scope.local.todo_date_status[key].name  ] = 0;
          }
          $scope.local.search_todo = 0;
          $scope.local.search_todo_date = 0;
        }
        $scope.local.grid_options.query.query_filter[ filter ] = 1;
        $scope.local.grid_options.query.paging.start = 0;
        $scope.local.grid_options.fetchData();
      }
    }
  };

  $scope.setNoShippedFilter = function(filter) {
    if($scope.local.grid_options.query.query_filter[filter] == 0) {
      //Reset all others
      for(var key in $scope.local.no_shipped_status) {
        $scope.local.grid_options.query.query_filter[$scope.local.no_shipped_status[key].name] = 0;
      }
      $scope.local.grid_options.query.query_filter[filter] = 1;
      $scope.local.grid_options.query.paging.start = 0;
      $scope.local.grid_options.fetchData();
    }
  };

  $scope.setTodoFilter = function(filter) {
    if($scope.local.grid_options.query.query_filter[filter] == 0) {
      //Reset all others
      for(var key in $scope.local.todo_status) {
        $scope.local.grid_options.query.query_filter[$scope.local.todo_status[key].name] = 0;
      }
      $scope.local.grid_options.query.query_filter[filter] = 1;
      $scope.local.grid_options.query.paging.start = 0;
      $scope.local.grid_options.fetchData();
    }
  };

  $scope.setTodoDateFilter = function(filter) {
    if($scope.local.grid_options.query.query_filter[filter] == 0) {
      //Reset all others
      for(var key in $scope.local.todo_date_status) {
        $scope.local.grid_options.query.query_filter[$scope.local.todo_date_status[key].name] = 0;
      }
      $scope.local.grid_options.query.query_filter[filter] = 1;
      $scope.local.grid_options.query.paging.start = 0;
      $scope.local.grid_options.fetchData();
    }
  };

  $scope.getOrderTotals = function ( supplier_id ) {
    $scope.local.grid_options.query.query_filter.filter_supplier_id = $scope.local.chosen.supplier.supplier_id;
    $scope.local.grid_options.query.query_filter.filter_order_status_id = $scope.local.chosen.order_status.order_status_id;
    var search = angular.copy( $scope.local.grid_options.query.query_filter );
    $http.post( $request_urls.getOrderTotals, {
      query_filter: search
    } ).success( function ( data ) {
                   if( data.code == 200 ) {
                     var counts = {
                       need_refund: 0,
                       not_shipped: 0,
                       question   : 0,
                       todo       : 0,
                       not_shipped_use_date:0,
                       not_shipped_over_ship_date:0,
                       not_shipped_to_ship_date:0,
                       todo_record :0,
                       todo_complain:0,
                       todo_urge:0,
                       todo_bill:0,
                       todo_total:0,
                       todo_edit:0,
                       todo_feedback:0
                     };
                     for( var i in data.data.data ) {
                       var supplier_counts = data.data.data[i];
                       for( var j in supplier_counts ) {
                         counts[j] += parseInt( supplier_counts[j] );
                       }
                     }

                     $scope.setFilterCount( counts );
                   }
                 } );
  };

  $scope.setFilterCount = function ( mid ) {
    $scope.local.order_status[1].count = mid.not_shipped;
    $scope.local.order_status[2].count = mid.need_refund;
    $scope.local.order_status[3].count = mid.question;
    $scope.local.order_status[4].count = mid.todo;
    $scope.local.no_shipped_status[1].count = mid.not_shipped_use_date;
    $scope.local.no_shipped_status[2].count = mid.not_shipped_over_ship_date;
    $scope.local.no_shipped_status[3].count = mid.not_shipped_to_ship_date;
    $scope.local.todo_status[1].count = mid.todo_record;
    $scope.local.todo_status[2].count = mid.todo_urge;
    $scope.local.todo_status[3].count = mid.todo_bill;
    $scope.local.todo_status[4].count = mid.todo_total;
    $scope.local.todo_status[5].count = mid.todo_edit;
    $scope.local.todo_status[6].count = mid.todo_feedback;
    $scope.local.todo_status[7].count = mid.todo_complain;
  };

  $scope.toggleAdvanceSearch = function () {
    $scope.local.show_advance_search = !$scope.local.show_advance_search;
  };

  $scope.toggleSelection = function ( index ) {
    for( var j in $scope.local.visible_search_options ) {
      $scope.local.visible_search_options[j].status = 0;
    }
    $scope.local.visible_search_options[index].status = 1;
    $scope.local.grid_options.query.query_filter.search_field = $scope.local.visible_search_options[index].key;
    if( index == '0' ) {
      $scope.local.show_advance_search = false;
    }
  };

  $scope.search = function () {
    var result;
    var search = {
      search_field: $scope.local.grid_options.query.query_filter.search_field
    };

    if( $scope.local.grid_options.query.query_filter.search_field == 'order_id' &&
        $scope.local.grid_options.query.query_filter.search_text != '' ) {
      window.open( getDetailLink( $scope.local.grid_options.query.query_filter.search_text ), '_blank' );
    } else {
      search.search_text = $scope.local.grid_options.query.query_filter.search_text;
      search.has_combination = 0;

      if( $scope.local.grid_options.query.query_filter.filter_supplier_id > 0 ) {
        search.filter_supplier_id = $scope.local.grid_options.query.query_filter.supplier_id;
      }
      if( $scope.local.search_order_status > 0 ) {
        search[$scope.local.order_status[$scope.local.search_order_status].name] = 1;
      }

      if( $scope.local.show_advance_search ) {
        if( !isEmpty( $scope.local.grid_options.query.query_filter.search_product_text ) ) {
          search.search_product_text = $scope.local.grid_options.query.query_filter.search_product_text;
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.grid_options.query.query_filter.search_added_from_date ) ) {
          search.search_added_from_date = formatDate( $scope.local.grid_options.query.query_filter.search_added_from_date );
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.grid_options.query.query_filter.search_added_to_date ) ) {
          search.search_added_to_date = formatDate( $scope.local.grid_options.query.query_filter.search_added_to_date );
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.grid_options.query.query_filter.search_tour_from_date ) ) {
          search.search_tour_from_date = formatDate( $scope.local.grid_options.query.query_filter.search_tour_from_date );
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.grid_options.query.query_filter.search_tour_to_date ) ) {
          search.search_tour_to_date = formatDate( $scope.local.grid_options.query.query_filter.search_tour_to_date );
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.grid_options.query.query_filter.search_passenger ) ) {
          search.search_passenger = $scope.local.grid_options.query.query_filter.search_passenger;
          search.has_combination = 1;
        }
      }
      result = $request_urls.searchResult + '?search=' + angular.toJson( search );
      window.location = result;
    }
  };

  $scope.clearCriteria = function () {
    for( var j in $scope.local.visible_search_options ) {
      $scope.local.visible_search_options[j].status = 0;
    }
    $scope.local.visible_search_options[0].status = 1;
    $scope.local.grid_options.query.query_filter.search_field = "order_id";
    $scope.local.grid_options.query.query_filter.filter_supplier_id = 0;
    $scope.local.grid_options.query.query_filter.search_product_text = "";
    $scope.local.grid_options.query.query_filter.search_added_from_date = "";
    $scope.local.grid_options.query.query_filter.search_added_to_date = "";
    $scope.local.grid_options.query.query_filter.search_tour_from_date = "";
    $scope.local.grid_options.query.query_filter.search_tour_to_date = "";
    $scope.local.grid_options.query.query_filter.search_text = "";
    $scope.local.grid_options.query.query_filter.search_passenger = "";
    $scope.local.grid_options.query.query_filter.filter_order_status_id = 0;
    $scope.local.search_order_status = 0;
    $scope.local.grid_options.query.query_filter.filterNotShipped = 0;
    $scope.local.grid_options.query.query_filter.filterNeedRefund = 0;
    $scope.local.grid_options.query.query_filter.filterQuestion = 0;
    $scope.local.grid_options.query.query_filter.filterToDo = 0;

    $scope.getOrderTotals( $scope.local.grid_options.query.query_filter.filter_supplier_id );

    $scope.local.grid_options.fetchData();
  };

  $scope.filterSupplier = function () {
    $scope.local.grid_options.query.paging.start = 0;

    $scope.getOrderTotals( $scope.local.grid_options.query.query_filter.filter_supplier_id );
    $scope.local.grid_options.fetchData();
  };

  function getDetailLink( order_id ) {
    return $request_urls.edit.replace( '0000', order_id ) + JSON.stringify( $scope.local.grid_options.query.query_filter );
  }


  $scope.init();

  $scope.$watch( 'local.chosen.supplier.supplier_id', function ( newVal, oldVal ) {
    if( oldVal ) {
      $scope.local.grid_options.query.query_filter.filter_supplier_id = newVal;
    }
  } );
  $scope.$watch( 'local.chosen.order_status.order_status_id', function ( newVal, oldVal ) {
    if( oldVal ) {
      $scope.local.grid_options.query.query_filter.filter_order_status_id = newVal;
    }
  } );
};

app.controller( 'OrderSearchListCtrl', ['$scope', '$http', '$rootScope', 'commonFactory', controllers.OrderSearchListCtrl] );