var editProductCommentCtrl = function( $scope, $rootScope, $route, $http ) {
  $scope.data = {};
  if( $route.current.locals.loadData ) {
    $scope.data.comments = $route.current.locals.loadData.comments;
    for( var i in $scope.data.comments ) {
      $scope.data.comments[i].is_edit = false;
      $scope.data.comments[i].display_content = $scope.data.comments[i].content.replace( /\r\n|\r|\n/g, "<br />" );
    }
  }

  $scope.toggleEdit = function( index ) {
    if( $scope.data.comments[index].is_edit ) {
      if( $scope.data.comments[index].content.length < 10 || $scope.data.comments[index].content.length > 255 ) {
        alert( "请输入长度为10—255之间的评论" );
        return;
      }
      var post_data = angular.copy( $scope.data.comments[index] );
      if( post_data.comment_id == "" ) {
        $http.post( request_urls.productAddComment, post_data ).success( function( value ) {
          if( value.code != 200 ) {
            alert( data.msg );
            return;
          } else {
            $scope.data.comments[index].display_content = $scope.data.comments[index].content.replace( /\r\n|\r|\n/g, "<br />" );
            alert( "保存成功" );
          }
        } );
      } else {
        $http.post( request_urls.productEditComment, post_data ).success( function( data ) {
          if( data.code != 200 ) {
            alert( data.msg );
            return;
          } else {
            $scope.data.comments[index].display_content = $scope.data.comments[index].content.replace( /\r\n|\r|\n/g, "<br />" );
            alert( "保存成功" );
          }
        } );
      }
    }
    $scope.data.comments[index].is_edit = !$scope.data.comments[index].is_edit;
  }

  $scope.toggleCustomer = function( index ) {
    $http.get( request_urls.getRandomCustomer ).success( function( data ) {
      if( data.code == 200 ) {
        var customer = data.data;
        $scope.data.comments[index].customer = customer;
        $scope.data.comments[index].customer_id = customer.customer_id;
      } else {
        alert( data.msg );
      }
    } );
  }

  $scope.delComments = function( index ) {
    if( !window.confirm( "删除后不可恢复。\n点击'确认'删除。" ) )
      return;
    var comment_id = $scope.data.comments[index].comment_id;
    $http.post( request_urls.productDeleteComment + comment_id ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.data.comments.splice( index, 1 );
        alert( "删除成功" );
      } else {
        alert( data.msg );
      }
    } );
  }

  $scope.addComments = function() {
    $http.get( request_urls.getRandomCustomer ).success( function( data ) {
      if( data.code == 200 ) {
        var customer = data.data;

        var new_comments = {
          approved               : "1",
          comment_id             : "",
          content                : "",
          customer               : customer,
          customer_id            : customer.customer_id,
          hitour_service_level   : "5",
          insert_time            : "",
          product_id             : "",
          supplier_service_level : "5",
          is_edit                : true
        };
        $scope.data.comments.splice( 0, 0, new_comments );
      }
    } );
  }

  $scope.changeHitourRate = function( index, rate ) {
    $scope.data.comments[index].hitour_service_level = rate;
  }

  $scope.changeSupplierRate = function( index, rate ) {
    $scope.data.comments[index].supplier_service_level = rate;
  }

};


angular.module( 'ProductEditApp' ).controller( 'editProductCommentCtrl', [
  '$scope', '$rootScope', '$route', '$http', editProductCommentCtrl
] );