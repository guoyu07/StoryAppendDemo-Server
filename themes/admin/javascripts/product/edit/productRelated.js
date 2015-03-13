var editProductRelatedCtrl = function( $scope, $rootScope, $route, $http ) {
  $scope.data = $route.current.locals.loadData;
  $scope.local = {
    input_product          : '',
    check_product_progress : false
  };

  $scope.addProductRelated = function() {
    $scope.local.check_product_progress = true;

    if( parseInt( $scope.local.input_product, 10 ) < 1 ) {
      $rootScope.$emit( 'publishAlert', 400, '请输入一个商品ID再关联' );
      return;
    }

    $http.post( request_urls.addProductRelated, {
      related_id : $scope.local.input_product
    } ).success( function( data ) {
      $scope.local.check_product_progress = false;

      if( data.code == 200 ) {
        $scope.data.push( data.data );
        $scope.local.input_product = '';
      } else {
        $rootScope.$emit( 'publishAlert', data.code, data.msg );
      }
    } );
  };

  $scope.delProductRelated = function( product_related_id ) {
    if(!window.confirm('删除与产品“' + product_related_id + '”的关联？')) { return; }

    $http.post( request_urls.deleteProductRelated, {
      related_id : product_related_id
    } ).success( function( data ) {

      var index;

      if( data.code == 200 ) {
        for( var key in $scope.data ) {
          if( $scope.data[key].product_id == product_related_id ) {
            index = key;
            break;
          }
        }
        $scope.data.splice( index, 1 );
      } else {
        $rootScope.$emit( 'publishAlert', data.code, data.msg );
      }
    } );
  };
};


angular.module( 'ProductEditApp' ).controller( 'editProductRelatedCtrl', [
  '$scope', '$rootScope', '$route', '$http', editProductRelatedCtrl
] );