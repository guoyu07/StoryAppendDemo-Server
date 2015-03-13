controllers.OrderSummaryListCtrl = function( $scope, $http, $rootScope, commonFactory ) {
  $scope.data = {};
  $scope.local = {
    show_advance_search    : false,
    visible_search_options : [
      {
        key    : 'order_id',
        value  : '订单号',
        status : 1
      },
      {
        key    : 'contacts_name',
        value  : '联系人',
        status : 0
      },
      {
        key    : 'contacts_telephone',
        value  : '联系电话',
        status : 0
      },
      {
        key    : 'contacts_email',
        value  : '联系人邮箱',
        status : 0
      }
    ],
    search_field           : "order_id",
    search_product_text    : "",
    search_added_from_date : "",
    search_added_to_date   : "",
    search_tour_from_date  : "",
    search_tour_to_date    : "",
    search_text            : "",
    search_passenger_text  : "",
    search_order_status    : 0,
    order_status           : [
      {
        label : '全部',
        name  : 'filterAll',
        key   : 0
      },
      {
        label : '待发货',
        name  : 'filterNotShipped',
        key   : 1
      },
      {
        label : '待退款',
        name  : 'filterNeedRefund',
        key   : 2
      },
      {
        label : '问题',
        name  : 'filterQuestion',
        key   : 3
      },
      {
        label : '待办',
        name  : 'filterToDo',
        key   : 4
      }
    ],
    grid_options           : {
      data    : [],
      table   : {
        table_id : 'summary_grid'
      },
      label   : {
        getHead : function( col, i ) {
          return col.label;
        },
        getBody : function( col, i, record, j ) {
          if( col.name == "name" ) {
            return "<a href='" + $request_urls.searchResult + "?search=" +
                   angular.toJson( $scope.getQuery( record.supplier_id ) ) + "'>" + record[col.name].toString() +
                   "</a>";
          } else if( col.name == "not_shipped" ) {
            return "<a href='" + $request_urls.searchResult + "?search=" +
                   angular.toJson( $scope.getQuery( record.supplier_id, 'filterNotShipped' ) ) + "'>" +
                   record[col.name].toString() + "</a>";
          } else if( col.name == "need_refund" ) {
            return "<a href='" + $request_urls.searchResult + "?search=" +
                   angular.toJson( $scope.getQuery( record.supplier_id, 'filterNeedRefund' ) ) + "'>" +
                   record[col.name].toString() + "</a>";
          } else if( col.name == "todo" ) {
            return "<a href='" + $request_urls.searchResult + "?search=" +
                   angular.toJson( $scope.getQuery( record.supplier_id, 'filterToDo' ) ) + "'>" +
                   record[col.name].toString() + "</a>";
          } else if( col.name == "question" ) {
              if (record[col.name] > 0) {
                  return "<a href='" + $request_urls.searchResult + "?search=" +
                         angular.toJson( $scope.getQuery( record.supplier_id, 'filterQuestion' ) ) + "' style='color:#9d261d'>" +
                         record[col.name].toString() + "</a>";
              }else {
                  return "<a href='" + $request_urls.searchResult + "?search=" +
                         angular.toJson( $scope.getQuery( record.supplier_id, 'filterQuestion' ) ) + "'>" +
                         record[col.name].toString() + "</a>";
              }
          }
        }
      },
      query   : {
        sort         : {
          'not_shipped' : 0
        },
        paging       : {
          start : 0,
          limit : 999
        },
        query_filter : {
          'search_text'            : '',
          'search_product_text'    : '',
          'search_field'           : '',
          'search_added_from_date' : '',
          'search_added_to_date'   : '',
          'search_tour_from_date'  : '',
          'search_tour_to_date'    : '',
          'search_passenger'       : '',
          'filter_supplier_id'     : 0,
          'filter_order_status_id' : '',
          'filterNotShipped'       : 0,
          'filterNeedRefund'       : 0,
          'filterQuestion'         : 0,
          'filterToDo'             : 0,
          'has_combination'        : 0
        }
      },
      request : {
        api_url : $request_urls.getOrderTotals
      },
      columns : [
        {
          name     : 'name',
          width    : '20%',
          label    : '供应商',
          use_sort : false
        },
        {
          name     : 'not_shipped',
          width    : '20%',
          label    : '待发货',
          use_sort : true
        },
        {
          name     : 'need_refund',
          width    : '20%',
          label    : '待退货',
          use_sort : true
        },
        {
          name     : 'todo',
          width    : '20%',
          label    : '待办',
          use_sort : true
        },
        {
          name     : 'question',
          width    : '20%',
          label    : '问题',
          use_sort : true
        }
      ]
    },
    supplier_list          : []
  };

  $scope.init = function() {
    $rootScope.$emit( 'setBreadcrumb', {
      back : {},
      body : {
        content : '订单汇总'
      }
    } );

    commonFactory.getAjaxSearchSupplierList( true ).then( function( data ) {
      $scope.local.supplier_list = data;
    } );

    $rootScope.$emit( 'loadStatus', false );
  };

  $scope.filterSupplier = function() {
    $scope.local.grid_options.fetchData();
  };

  $scope.getQuery = function( supplier_id, status ) {
    var result = {};
    result[ 'filter_supplier_id' ] = supplier_id;

    if( status ) result[ status ] = 1;

    result['has_combination'] = 0;

    return result;
  };

  $scope.toggleAdvanceSearch = function() {
    $scope.local.show_advance_search = !$scope.local.show_advance_search;
  };

  $scope.toggleSelection = function( index ) {
    for( var j in $scope.local.visible_search_options ) {
      $scope.local.visible_search_options[j].status = 0;
    }
    $scope.local.visible_search_options[index].status = 1;
    $scope.local.search_field = $scope.local.visible_search_options[index].key;

    if( index == '0' ) {
      $scope.local.show_advance_search = false;
    }
  };

  $scope.search = function() {
    var result;
    var search = {
      search_field : $scope.local.search_field
    };

    if( $scope.local.search_field == 'order_id' && $scope.local.search_text != '' ) {
      result = $request_urls.edit.replace( '0000', $scope.local.search_text );
      window.open( result, '_blank' );
    } else {
      search.search_text = $scope.local.search_text;
      search.has_combination = 0;

      if( $scope.local.grid_options.query.query_filter.filter_supplier_id > 0 ) {
        search.filter_supplier_id = $scope.local.grid_options.query.query_filter.filter_supplier_id;
      }

      if( $scope.local.show_advance_search ) {
        if( !isEmpty( $scope.local.search_product_text ) ) {
          search.search_product_text = $scope.local.search_product_text;
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.search_added_from_date ) ) {
          search.search_added_from_date = formatDate( $scope.local.search_added_from_date );
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.search_added_to_date ) ) {
          search.search_added_to_date = formatDate( $scope.local.search_added_to_date );
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.search_tour_from_date ) ) {
          search.search_tour_from_date = formatDate( $scope.local.search_tour_from_date );
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.search_tour_to_date ) ) {
          search.search_tour_to_date = formatDate( $scope.local.search_tour_to_date );
          search.has_combination = 1;
        }

        if( !isEmpty( $scope.local.search_passenger_text ) ) {
          search.search_passenger = $scope.local.search_passenger_text;
          search.has_combination = 1;
        }

        if( $scope.local.search_order_status > 0 ) {
          search[$scope.local.order_status[$scope.local.search_order_status].name] = 1;
        }
      }
      result = $request_urls.searchResult + '?search=' + angular.toJson( search );
      window.location = result;
    }
  };

  $scope.clearCriteria = function() {
    for( var j in $scope.local.visible_search_options ) {
      $scope.local.visible_search_options[j].status = 0;
    }
    $scope.local.visible_search_options[0].status = 1;
    $scope.local.search_field = "order_id";
    $scope.local.grid_options.query.query_filter.filter_supplier_id = 0;
    $scope.local.search_product_text = "";
    $scope.local.search_added_from_date = "";
    $scope.local.search_added_to_date = "";
    $scope.local.search_tour_from_date = "";
    $scope.local.search_tour_to_date = "";
    $scope.local.search_text = "";
    $scope.local.search_passenger_text = "";
    $scope.local.search_order_status = 0;

    $scope.local.grid_options.fetchData();
  }

  $scope.init();
};

app.controller( 'OrderSummaryListCtrl', ['$scope', '$http', '$rootScope', 'commonFactory', controllers.OrderSummaryListCtrl] );