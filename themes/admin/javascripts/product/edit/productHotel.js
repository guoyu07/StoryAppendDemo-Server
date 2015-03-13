var editProductHotelCtrl = function( $scope, $rootScope, $route, $http ) {
  $scope.local = {
    show_additionalInfo : false,
    radio_options : {
      bank_card_type  : {
        name  : 'bank_card_type',
        items : {
          '1' : '仅支持visa',
          '2' : '仅支持银联',
          '3' : '支持visa及银联'
        }
      }
    }
  };
  $scope.onlyNumbers = /^\d+$/;
  $scope.latLng = /^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/;
  if( $route.current.locals.loadData ) {
    $scope.hotel = $route.current.locals.loadData;
  }
  $http.get( request_urls.bankcardItems ).success( function( data ) {
    $scope.bankcard_items = data.data;
  } );
  $http.get( request_urls.rateSources ).success( function( data ) {
    $scope.rateSources = data.data;
  } );

  $scope.toggleBankcard = function( bankcard_id ) {
    if( $scope.hotel.bankcards.hasOwnProperty( bankcard_id ) ) {
      delete $scope.hotel.bankcards[bankcard_id];
    } else {
      $scope.hotel.bankcards[bankcard_id] = {
        product_id   : $scope.local.product_id,
        bankcard_id   : bankcard_id
      };
    }
  };

  $scope.toogleRate = function( source_id ) {
    if( !$scope.hotel.rates.hasOwnProperty( source_id ) ) {
      $scope.hotel.rates[source_id] = {
        product_id   : $scope.local.product_id,
        source_id   : source_id,
        rate : ""
      };
    }
  };

  $scope.submitChanges = function() {
    for( var index in $scope.hotel.rates ) {
      if($scope.hotel.rates[index].rate == ''){
        delete $scope.hotel.rates[index];
      }
    }
    $http.post( request_urls.hotelInfo, $scope.hotel ).success( function( data ) {
      $rootScope.$emit( 'clearDirty' );
      $rootScope.$emit( 'publishAlert', data.code, data.msg );
    } );
  };

  $scope.local.product_id = $scope.hotel.product_id;
};

angular.module( 'ProductEditApp' ).controller( 'editProductHotelCtrl', [
  '$scope', '$rootScope', '$route', '$http', editProductHotelCtrl
] );