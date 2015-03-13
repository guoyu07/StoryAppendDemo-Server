var ProductApp = angular.module( 'ProductApp', ['ngResource', 'histeria.factory', 'histeria.directive', 'localytics.directives'] );

ProductApp.controller( 'productListCtrl', function( $scope, $http, commonFactory ) {

  $scope.totalPages = 0;
  $scope.itemsPerPage = 20;
  $scope.customersCount = 0;
  $scope.headers = [
    {
      title : '货号',
      value : 'product_id',
      width : '6%'
    },
    {
      title : '名称',
      value : 'name',
      width : '45%'
    },
    {
      title : '售卖价格',
      value : 'price',
      width : '13%'
    },
    {
      title : '所属城市',
      value : 'city_cn_name',
      width : '8%'
    },
    {
      title : '供应商',
      value : 'm_name',
      width : '8%'
    }
  ];
  $scope.product_type = [
    {
      value : '0',
      label : '所有类型'
    },
    {
      value : '1',
      label : '单票'
    },
    {
      value : '2',
      label : '组合票'
    },
    {
      value : '3',
      label : '通票'
    },
    {
      value : '4',
      label : '随上随下票'
    },
    {
      value : '5',
      label : 'Tour'
    },
    {
      value : '6',
      label : 'Coupon'
    },
    {
      value : '7',
      label : '酒店'
    },
    {
      value : '8',
      label : '酒店套餐'
    },
    {
      value : '9',
      label : '多日游'
    },
    {
        value : '10',
        label : '包车'
    }
  ];
  $scope.status = [
    {
      class       : 'all',
      status_id   : '',
      status_name : '所有状态'
    },
    {
      class       : 'edit',
      status_id   : '1',
      status_name : '编辑中'
    },
    {
      class       : 'review',
      status_id   : '2',
      status_name : '待审核'
    },
    {
      class       : 'onsale',
      status_id   : '3',
      status_name : '已上架'
    },
    {
      class       : 'offsale',
      status_id   : '4',
      status_name : '禁用'
    }
  ];
  $scope.statusConvert = ['', '编辑中', '待审核', '已上架', '禁用'];
  commonFactory.getAjaxSearchCityList( true ).then( function( data ) {
    $scope.cities = data;
  } );
  commonFactory.getAjaxSearchVendorList( true ).then( function( data ) {
    $scope.vendors = data;
  } );

  $scope.filterCriteria = {
    pageNumber : 1,
    sortDir    : 'desc',
    sortedBy   : 'product_id',
    supplier   : {supplier_id : '0'},
    city       : {city_code : '0'},
    type       : {value : '0'}
  };
  // TODO update filterCriteria by qs
  if( qs != '' ) {
    var parts = qs.split( ';' );
    for( var i = 0; i < parts.length; i++ ) {
      var kv = parts[i].split( ':' );
      if( kv[0] == 'city_code' ) {
        $scope.filterCriteria.city.city_code = kv[1];
      } else if( kv[0] == 'supplier_id' ) {
        $scope.filterCriteria.supplier.supplier_id = kv[1];
      } else if( kv[0] == 'product' ) {
        $scope.filterCriteria.product = kv[1];
      } else if( kv[0] == 'status' ) {
        $scope.filterCriteria.status = kv[1];
      } else if( kv[0] == 'type' ) {
        $scope.filterCriteria.type.value = kv[1];
      }else if( kv[0] == 'sortDir' ) {
        $scope.filterCriteria.sortDir = kv[1];
      } else if( kv[0] == 'sortedBy' ) {
        $scope.filterCriteria.sortedBy = kv[1];
      } else if( kv[0] == 'pn' ) {
        $scope.filterCriteria.pageNumber = kv[1];
      }
    }
  }

  $scope.fetchResult = function() {


    return $http.post( request_urls.getProducts, $scope.filterCriteria ).success(function( data ) {

      // TODO update qs according to filterCriteria
      var tmp_qs = '';
      if( $scope.filterCriteria.pageNumber > 1 ) {
        tmp_qs += 'qn:' + $scope.filterCriteria.pageNumber + ';';
      }
      if( $scope.filterCriteria.sortedBy ) {
        tmp_qs += 'sortedBy:' + $scope.filterCriteria.sortedBy + ';' + 'sortDir:' + $scope.filterCriteria.sortDir + ';';
      }
      if( $scope.filterCriteria.city.city_code ) {
        tmp_qs += 'city_code:' + $scope.filterCriteria.city.city_code + ';';
      }
      if( $scope.filterCriteria.supplier.supplier_id ) {
        tmp_qs += 'supplier_id:' + $scope.filterCriteria.supplier.supplier_id + ';';
      }
      if( $scope.filterCriteria.status ) {
        tmp_qs += 'status:' + $scope.filterCriteria.status + ';';
      }
      if( $scope.filterCriteria.product ) {
        tmp_qs += 'product:' + $scope.filterCriteria.product + ';';
      }
      if( $scope.filterCriteria.type ) {
        tmp_qs += 'type:' + $scope.filterCriteria.type.value + ';';
      }
      $scope.qs = tmp_qs;

      $scope.products = data.code == 200 ? data.data : [];
      $scope.productsCount = data.total;
      $scope.totalPages = Math.ceil( $scope.productsCount / $scope.itemsPerPage );

      angular.forEach( $scope.products, function( value, key ) {
        $scope.products[key].price = '¥ ' + parseInt( value.price, 10 ) + ' / ¥ ' + parseInt( value.orig_price, 10 );
      }, $scope.products );
    } ).error( function() {
      $scope.products = [];
      $scope.productsCount = 0;
      $scope.totalPages = 0;
      $scope.filterCriteria.pageNumber = 1;

    } );
  };

  //called when navigate to another page in the pagination
  $scope.selectPage = function( page ) {
    $scope.filterCriteria.pageNumber = page;
    $scope.fetchResult();
  };

  $scope.ctrl = {};
  $scope.ctrl.selectPage = $scope.selectPage;

  //Will be called when filtering the grid, will reset the page number to one
  $scope.filterResult = function() {
    $scope.filterCriteria.pageNumber = 1;
    $scope.fetchResult().then( function() {
      //The request fires correctly but sometimes the ui doesn't update, that's a fix
      $scope.filterCriteria.pageNumber = 1;
    } );
  };

  //call back function that we passed to our custom directive sortBy, will be called when clicking on any field to sort
  $scope.onSort = function( sortedBy, sortDir ) {
    $scope.filterCriteria.sortDir = sortDir;
    $scope.filterCriteria.sortedBy = sortedBy;
    $scope.filterCriteria.pageNumber = 1;
    $scope.fetchResult().then( function() {
      //The request fires correctly but sometimes the ui doesn't update, that's a fix
      $scope.filterCriteria.pageNumber = 1;
    } );
  };

  $scope.updateStatus = function( statusType ) {
    $scope.filterCriteria.status = statusType;
    $scope.filterResult();
  };

  //manually select a page to trigger an ajax request to populate the grid on page load
  $scope.selectPage( 1 );

  $scope.editProduct = function( product_id ) {
    window.open( request_urls.edit + product_id + '&qs=' + $scope.qs );
  };

  $scope.goToImport = function() {
    window.location = request_urls.gtaImportUrl;
  };

  $scope.productCheck = function() {
    window.location = request_urls.productCheck;
  };

} );

ProductApp.directive( 'onBlurChange', function( $parse ) {
  return function( scope, element, attr ) {
    var fn = $parse( attr['onBlurChange'] );
    var hasChanged = false;
    element.on( 'change', function( event ) {
      hasChanged = true;
    } );

    element.on( 'blur', function( event ) {
      if( hasChanged ) {
        scope.$apply( function() {
          fn( scope, {$event : event} );
        } );
        hasChanged = false;
      }
    } );
  };
} );

ProductApp.directive( 'onEnterBlur', function() {
  return function( scope, element ) {
    element.bind( "keydown keypress", function( event ) {
      if( event.which === 13 ) {
        element.blur();
        event.preventDefault();
      }
    } );
    element.on( "chosen:updated", function() {
      element.blur();
    } );
  };
} );
