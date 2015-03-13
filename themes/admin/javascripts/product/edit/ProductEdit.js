var ProductEditCtrl = function( $scope, $rootScope, $http, $location, ProductEditFactory, $modal ) {
  $http.get( request_urls.getProduct, { cache : true } ).success( function( data ) {
    $scope.product_detail = data.data;
    $scope.editing_state_name = $scope.getProductStatus( $scope.product_detail.status );
    $scope.initMenu();
  } );

  var dirtyForm, inProgress = false;
  var editing_item_counts = 0;
  $scope.menu = angular.copy( ProductEditFactory.menu );
  $scope.current_menu = 'editProductInfo';
  $scope.current_section = '';
  $scope.passenger_info_types = ProductEditFactory.passenger_info_types;
    $scope.new_version_url = request_urls.editNew;

  $scope.product_status = [
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

  $scope.initMenu = function() {
    var current_group_index = 0;

    for( var i in $scope.menu ) {
      if ( !$scope.menu[i].group ) {
        $scope.menu[i].display = false;
        if( $scope.menu[i].type.length == 1 && $scope.menu[i].type == ProductEditFactory.menu_type.all ) {
          $scope.menu[i].display = true;
          $scope.menu[current_group_index].display = true;
        } else {
          var type_list = $scope.mergeArray($scope.menu[i].type);
          if (type_list.indexOf($scope.product_detail.type) != -1) {
            $scope.menu[i].display = true;
            $scope.menu[current_group_index].display = true;
          }
        }
      } else {
        current_group_index = i;
        $scope.menu[i].display = false;
      }
    }
  };

  $scope.mergeArray = function( arr ) {
    var new_array = [];
    for (var j in arr) {
      if (typeof arr[j] === 'object') {
        var tmp_arr = $scope.mergeArray(arr[j]);
        new_array = new_array.concat(tmp_arr);
      } else {
        new_array.push(arr[j]);
      }
    }
    return new_array;
  };

  $scope.getProductStatus = function( status_id ) {
    for( var key in $scope.product_status ) {
      if( $scope.product_status[key].status_id == status_id ) return $scope.product_status[key].status_name;
    }
  };
  $scope.changeProductStatus = function( status_id ) {
    $scope.new_status_id = status_id;
    $http.post( request_urls.changeEditingState, {
      status : status_id
    } ).success( function( data ) {
      alert( data.msg );

      if( data.code == 200 ) {
        $scope.product_detail.status = $scope.new_status_id;
        $scope.editing_state_name = $scope.getProductStatus( $scope.product_detail.status );
        window.location.reload();
      }
    } );
  };

  $scope.copyProduct = function() {
    $http.post( request_urls.copyProduct, {} ).success( function( data ) {
      alert( data.msg );
      var new_product_id = data.data;
      $scope.viewNewProduct( new_product_id );
    } );
  };

  $scope.viewNewProduct = function( product_id ) {
    window.open( request_urls.edit + product_id, '_blank' );
  }

  $scope.alerts = [];
  $scope.delAlert = function() {
    $scope.alerts.shift();
    $rootScope.$emit( 'removeAlert' );
  };
  $rootScope.$on( 'publishAlert', function( event, code, message ) {
    $scope.alerts = [
      {
        data : message,
        type : code == 200 ? 'success' : 'danger'
      }
    ];
    $( document ).scrollTop( 0 );
  } );

  $rootScope.$on( 'dirtyForm', function( event, formName ) {
    dirtyForm = formName;
  } );
  $rootScope.$on( 'clearDirty', function() {
    dirtyForm = '';
  } );

  $scope.$on( '$locationChangeStart', function( event, newRoute ) {
    var newPath = newRoute.split( '#/' );
    newPath = newPath[ newPath.length - 1 ];
    if( newPath == 'editShippingConfig' && $scope.product_detail && $scope.product_detail.is_combo == '1' ) {
      event.preventDefault();
      alert( '该商品为组合票，无法配置发货方式。' );
    }
    if( dirtyForm == event.currentScope.current_menu && inProgress == false ) {
      event.preventDefault();
      inProgress = true;

      var modalInstance = $modal.open( {
                                         templateUrl : 'save_modal.html',
                                         controller  : SaveModalCtrl
                                       } );

      modalInstance.result.then( function( action ) {
        if( action == 'save' ) {
          inProgress = false;
          $rootScope.$emit( 'manualSave', dirtyForm );
        } else if( action == 'navigate' ) {
          $location.path( newPath );
        }
      } );
    }
  } );
  $scope.$on( '$routeChangeSuccess', function( event, $newRoute ) {
    dirtyForm = '';
    inProgress = false;
    $scope.alerts = [];

    if( !$newRoute.hasOwnProperty( 'loadedTemplateUrl' ) ) return;
    var finalIndex = $newRoute.loadedTemplateUrl.length - '.html'.length;
    var ctrlName = $newRoute.loadedTemplateUrl.substr( 0, finalIndex );
    $scope.current_menu = ProductEditFactory.getCurrentMenu( ctrlName );
  } );
};

var SaveModalCtrl = function( $scope, $modalInstance ) {
  $scope.saveForm = function() { //Remain on page and trigger save
    $modalInstance.close( 'save' );
  };
  $scope.navigate = function() { //Move away
    $modalInstance.close( 'navigate' );
  };
};

angular.module( 'ProductEditApp' ).controller( 'ProductEditCtrl', [
  '$scope', '$rootScope', '$http', '$location', 'ProductEditFactory', '$modal', ProductEditCtrl
] );

angular.module( 'ProductEditApp' ).controller( 'SaveModalCtrl', [
  '$scope', '$modalInstance', SaveModalCtrl
] );