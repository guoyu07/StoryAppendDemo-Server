var editProductQnaCtrl = function( $scope, $rootScope, $route, $http ) {
  try {
    $scope.qa = JSON.parse( decodeURIComponent( $route.current.locals.loadData.qa ) );
  } catch( e ) {
    $scope.qa = {
      md_text : '',
      md_html : ''
    };
  }

  function composeMarkdown(md) {
    return encodeURIComponent(JSON.stringify({md_text : md.md_text, md_html : !!md.md_html && md.md_text.length > 0 ? md.md_html.$$unwrapTrustedValue() : ''}));
  }
  
  var isFirst = true;
  $scope.$watch( 'product_qna_form.$pristine', function() {
    if( isFirst ) {
      return isFirst = false;
    } else {
      $rootScope.$emit( 'dirtyForm', 'editProductQna' );
    }
  } );

  $rootScope.$on( 'manualSave', function( event, ctrl ) {
    if( ctrl == 'editProductQna' ) {
      $scope.submitChanges();
    }
  } );

  $scope.submitChanges = function() {
    var newData = $route.current.locals.loadData;
    newData.qa = composeMarkdown( $scope.qa );
    $http.post( request_urls.updateProductQA, newData ).success( function( data ) {
      $rootScope.$emit( 'publishAlert', data.code, data.msg );
    } );
  };
};


angular.module( 'ProductEditApp' ).controller( 'editProductQnaCtrl', [
  '$scope', '$rootScope', '$route', '$http', editProductQnaCtrl
] );