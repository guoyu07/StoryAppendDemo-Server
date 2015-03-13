var editProductDescCtrl = function( $scope, $rootScope, $route, $http, $q ) {
  $scope.local = {
    is_package : false,
    is_combo : false,
    sale_in_package : false
  };

  function composeMarkdown(md) {
    return encodeURIComponent(JSON.stringify({md_text : md.md_text, md_html : !!md.md_html && md.md_text.length > 0 ? md.md_html.$$unwrapTrustedValue() : ''}));
  }

  var isFirst = true;
  $scope.$watch( 'product_desc_form.$pristine', function() {
    if( isFirst ) {
      return isFirst = false;
    } else {
      $rootScope.$emit( 'dirtyForm', 'editProductDesc' );
    }
  } );

  $rootScope.$on( 'manualSave', function( event, ctrl ) {
    if( ctrl == 'editProductDesc' ) {
      $scope.submitChanges();
    }
  } );

  $scope.init = function() {
    $http.get( request_urls.getProductDescription ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.desc = data.data;

        try {
          $scope.desc.cn_service_include = JSON.parse( decodeURIComponent( $scope.desc.cn_service_include ) );
        } catch( e ) {
          $scope.desc.cn_service_include = {
            md_text : '',
            md_html : ''
          };
        }
        try {
          $scope.desc.cn_how_it_works = JSON.parse( decodeURIComponent( $scope.desc.cn_how_it_works ) );
        } catch( e ) {
          $scope.desc.cn_how_it_works = {
            md_text : '',
            md_html : ''
          };
        }
      }
    } );

    $scope.getProductType();
  }

  $scope.submitChanges = function() {
    var postData = angular.copy( $scope.desc );
    postData.cn_how_it_works = composeMarkdown( postData.cn_how_it_works );
    postData.cn_service_include = composeMarkdown( postData.cn_service_include );

    if ($scope.local.is_package){
      postData.cn_package_gift = "";
    } else {
      if ($scope.local.is_combo || $scope.local.sale_in_package) {
        postData.cn_package_gift = "";
        postData.cn_package_recommend = "";
      } else {
        postData.cn_package_service = "";
        postData.cn_package_recommend = "";
      }
    }
    $http.post( request_urls.updateProductDescription, postData ).success( function( data ) {
      $rootScope.$emit( 'clearDirty' );
      $rootScope.$emit( 'publishAlert', data.code, data.msg );
    } );
  };

  $scope.getProductType = function() {
    var sale_rule = $http.get( request_urls.getSaleRule );
    var product_info = $http.get( request_urls.getProduct );
    var all_requests = [sale_rule, product_info];
    $q.all( all_requests ).then( function( values ) {
      if( values[0].data.code == 200 && values[1].data.code == 200 ) {
        $scope.local.is_package = values[1].data.data.type == '8';
        if ( !$scope.local.is_package ) {
          $scope.local.is_combo = values[1].data.data.is_combo == '1';
          $scope.local.sale_in_package = values[0].data.data.sale_rule.sale_in_package == '1';
        }
      }
    } );
  };

  $scope.init();
};


angular.module( 'ProductEditApp' ).controller( 'editProductDescCtrl', [
  '$scope', '$rootScope', '$route', '$http', '$q', editProductDescCtrl
] );