controllers.OperationTodoCtrl = function( $scope, $rootScope, $http ) {
  $scope.data = {};
  $scope.local = {};

  $scope.init = function() {
    $http.get( $request_urls.fetchIncompleteSeo ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.data = angular.copy( data.data );

        $rootScope.$emit( 'setBreadcrumb', {
          back : {},
          body : {
            content : '待办事项'
          }
        } );
        $rootScope.$emit( 'loadStatus', false );
      } else {
        $rootScope.$emit( 'notify', { msg: data.msg } );
      }
    } );
  };
  $scope.goEditSeo = function( type, identifier, identifier2 ) {
    var link_url;
    if( type == 'home' ) {
      link_url = $request_urls.editHomeUrl;
    } else if( type == 'country' ) {
      link_url = $request_urls.editCountryUrl + identifier;
    } else if( type == 'city' ) {
      link_url = $request_urls.editCityUrl + identifier;
    } else if( type == 'product' ) {
      link_url = $request_urls.editProductUrl + identifier + '#editProductSeo';
    } else if( type == 'product_group' ) {
      link_url = $request_urls.editCityUrl + identifier + '&group_id=' + identifier2;
    } else if( type == 'promotion' ) {
      link_url = $request_urls.editPromotionUrl + identifier;
    }

    window.location = link_url;
  };

  $scope.init();
};

app.controller( 'OperationTodoCtrl', ['$scope', '$rootScope', '$http', controllers.OperationTodoCtrl] );