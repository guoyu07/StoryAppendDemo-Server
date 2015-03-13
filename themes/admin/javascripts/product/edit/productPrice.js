var editProductPriceCtrl = function( $scope, $rootScope, $routeParams, $location, $route, $http ) {
  $scope.menus = {
    '1' : {
      label   : '商品属性',
      alert : false
    },
    '2' : {
      label   : '售卖方式',
      alert : false
    },
    '3' : {
      label   : '价格计划',
      alert : false
    },
    '4' : {
      label : 'Departure Point',
      alert : false
    },
    '5' : {
      label : '特价计划',
      alert : false
    }
  };
  $scope.current_menu = $routeParams.current_page ? $routeParams.current_page : "1";
  $http.get( request_urls.productPricePlanSpecials ).success( function( data ) {
    if(data.code == 200) {
      $scope.plan_special = data.data;
      if($scope.plan_special.length > 0){
        $scope.menus[5].alert = true;
      }
    }
  });

  $scope.changePricePage = function( key ) {
    //TODO: Protection
    if( $scope.menus.hasOwnProperty( key ) ) {
      $location.path( "/editPrice/" + key );
    }
  };
};

angular.module( 'ProductEditApp' ).controller( 'editProductPriceCtrl', [
  '$scope', '$rootScope','$routeParams', '$location', '$route', '$http', editProductPriceCtrl
] );