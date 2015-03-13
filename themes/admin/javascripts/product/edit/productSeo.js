var editProductSeoCtrl = function( $scope, $rootScope, $route, $http ) {
  $scope.data = {};
  if( $route.current.locals.loadData ) {
    $scope.data.seo = $route.current.locals.loadData;
  } else {
    $scope.data.seo = {
      title : '',
      description : '',
      keywords : ''
    };
  }

  var isFirst = true;
  $scope.$watch( 'product_seo.$pristine', function() {
    if( isFirst ) {
      return isFirst = false;
    } else {
      $rootScope.$emit( 'dirtyForm', 'editProductSeo' );
    }
  } );

  $rootScope.$on( 'manualSave', function( event, ctrl ) {
    if( ctrl == 'editProductSeo' ) {
      $scope.submitChanges();
    }
  } );

  $scope.submitChanges = function() {
    $scope.data.seo.keywords = $scope.data.seo.keywords.replace( /，/g, ',' ).split( ',' ).map(function( elem ) {
      return elem.trim();
    } ).filter(function( elem ) {
      return elem.length > 0;
    } ).join( ',' );

    $http.post( request_urls.productSeo, $scope.data.seo ).success( function( data ) {
      $rootScope.$emit( 'publishAlert', data.code, data.msg );
    } );
  };
};


angular.module( 'ProductEditApp' ).controller( 'editProductSeoCtrl', [
  '$scope', '$rootScope', '$route', '$http', editProductSeoCtrl
] );