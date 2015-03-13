var editProductImageCtrl = function( $scope, $rootScope, $route, $http, $modal ) {

  //Filter
  var errorFunc = function() {
    $rootScope.$emit( 'publishAlert', 400, '更新失败。请稍后再试' );
  };

  //Init
  $scope.init = function() {
    $scope.local = {
      has_album             : $scope.data.hasOwnProperty( 'album_images' ) && $scope.data.album_images.length > 0,
      cover_img_id          : '',
      selected_album_images : []
    };

    // $scope.data.carousel_images.sort( function( a, b ) {
    //   return parseInt( a.sort_order, 10 ) > parseInt( b.sort_order, 10 );
    // } );
    angular.forEach( $scope.data.carousel_images, function( image ) {
      if( image.image_usage == 2 ) {
        $scope.local.selected_album_images.push( image.landinfo_id );
      }
      if( image.as_cover == 1 ) {
        $scope.local.cover_img_id = image.product_image_id;
      }
      image.editing = false;
    } );
  };

  $scope.setAsCover = function( product_image_id ) {
    $scope.local.cover_img_id = product_image_id;
    $http.post( request_urls.productImageSetCover, {
      product_image_id : product_image_id
    } ).success(function( data ) {
      if( data.code != 200 ) {
        $rootScope.$emit( 'publishAlert', data.code, data.msg );
      }
    } ).error( errorFunc );
  };
  $scope.delImage = function( image ) {
    if( !confirm( "删除图片？" ) ) return;

    var postData = {
      image_usage      : image.image_usage,
      product_image_id : image.product_image_id
    };

    $http.post( request_urls.deleteProductImage, postData ).success(function( data ) {
      if( data.code == 200 ) {

        for( var key in $scope.data.carousel_images ) {
          if( $scope.data.carousel_images[key].product_image_id == image.product_image_id ) {
            $scope.data.carousel_images.splice( key, 1 );
            break;
          }
        }

        if( image.image_usage == 2 ) {
          var index = $scope.local.selected_album_images.indexOf( image.landinfo_id );
          $scope.local.selected_album_images.splice( index, 1 );
        }

      } else {

        $rootScope.$emit( 'publishAlert', data.code, data.msg );

      }
    } ).error( errorFunc );

  };
  $scope.toggleState = function( image ) {
    if( image.editing ) {
      $http.post( request_urls.updateProductImage, {
        name             : image.name,
        short_desc       : image.short_desc,
        product_image_id : image.product_image_id
      } ).success(function( data ) {
        if( data.code != 200 ) {
          $rootScope.$emit( 'publishAlert', data.code, data.msg );
        }
      } ).error( errorFunc );
    }
    image.editing = !image.editing;
  };
  $scope.setAsSelected = function( landinfo_id ) {
    $http.post( request_urls.addProductImageOfLandinfo, {
      landinfo_id : landinfo_id
    } ).success( function( data ) {
      if( data.code == 200 ) {
        data.loop_image.editing = false;
        var images = angular.copy( $scope.data.carousel_images );
        images.unshift( data.loop_image );

        $scope.updateOrder( images ).then( function( status ) {
          if( status.data.code != 200 ) {
            $rootScope.$emit( 'publishAlert', status.data.code, status.data.msg );
          } else {
            $scope.data.carousel_images.unshift( data.loop_image );
            $scope.local.selected_album_images.push( landinfo_id );
          }
        } );
      } else {
        $rootScope.$emit( 'publishAlert', data.code, data.msg );
      }
    } );
  };
  $scope.updateOrder = function( images ) {
    images = images || $scope.data.carousel_images;
    var order_info = images.map( function( elem, index ) {
      return {
        sort_order       : index,
        product_image_id : elem.product_image_id
      };
    } );

    return $http.post( request_urls.updateProductImageOrder, {
      order_info : order_info
    } );
  };

  $scope.dndOptions = {
    selector : '.carousel-image',
    offset   : 0
  };
  $scope.dndCallback = function( info, dstIndex ) {
    $scope.data.carousel_images.splice( info.srcIndex, 1 ); //Remove img item
    $scope.data.carousel_images.splice( dstIndex, 0, info.srcItem ); //Add img item
    $scope.updateOrder();
  };

  $rootScope.$on( 'new_sample_image', function( event, image_url ) {
    $scope.data.sample_image = image_url;
  } );
  $rootScope.$on( 'new_carousel_image', function( event, new_image ) {
    $scope.data.carousel_images.unshift( new_image );
    $scope.updateOrder( $scope.data.carousel_images );
    $scope.$apply( 'data', $scope.data );
  } );

  $scope.data = angular.copy( $route.current.locals.loadData );
  $scope.init();

  $scope.$watch( 'data.carousel_images', function() {
    //    $rootScope.$emit( 'dirtyForm', 'editProductImage' );
  }, true );
  $rootScope.$on( 'manualSave', function( event, ctrl ) {
    if( ctrl == 'editProductImage' ) {
      var modalMessage = '';
      if( $scope.data.carousel_images.length < 1 ) {
        modalMessage += "商品需要最少一张轮播图片\n";
      }
      if( !$scope.local.cover_img_id ) {
        modalMessage += '商品需要有封面图';
      }

      if( modalMessage.length > 0 ) {
        var modalInstance = $modal.open( {
                                           templateUrl : 'image_modal.html',
                                           controller  : ImageModalCtrl,
                                           resolve     : {
                                             message : function() {
                                               return modalMessage;
                                             }
                                           }
                                         } );
      }
      $rootScope.$emit( 'clearDirty' );
    }
  } );

};

var imageUploadSampleCtrl = function( $scope, $rootScope, $http, $fileUploader ) {
  var image_filter = function( item ) {
    var type = '|' + item.type.toLowerCase().slice( item.type.lastIndexOf( '/' ) + 1 ) + '|';
    return '|jpg|png|jpeg|bmp|gif|'.indexOf( type ) !== -1;
  };

  $scope.sample_uploader = $fileUploader.create( {
                                                   url     : request_urls.addOrUpdateProductSampleImage,
                                                   scope   : $scope,
                                                   filters : []
                                                 } );
  $scope.sample_uploader.filters.push( image_filter );
  $scope.sample_uploader.bind( 'success', function( event, xhr, item, response ) {
    $scope.sample_uploader.queue = [];
    if( response.code == 200 ) {
      $rootScope.$emit( 'new_sample_image', response.sample_image );
      $rootScope.$emit( 'publishAlert', response.code, response.msg );
    }
  } );
  $scope.sample_uploader.bind( 'afteraddingfile', function( event, item ) {
    item.upload();
  } );

  $scope.triggerSampleUpload = function() {
    $( '#sample-upload' ).trigger( 'click' );
  };
};

var imageUploadCarouselCtrl = function( $scope, $rootScope, $http, $fileUploader ) {
  var image_filter = function( item ) {
    var type = '|' + item.type.toLowerCase().slice( item.type.lastIndexOf( '/' ) + 1 ) + '|';
    return '|jpg|png|jpeg|bmp|gif|'.indexOf( type ) !== -1;
  };

  $scope.carousel_uploader = $fileUploader.create( {
                                                     url     : request_urls.addProductImage,
                                                     scope   : $scope,
                                                     filters : []
                                                   } );
  $scope.carousel_uploader.filters.push( image_filter );

  $scope.carousel_uploader.bind( 'completeall', function( event, items ) {
    $scope.carousel_uploader.queue = [];
    $rootScope.$emit( 'publishAlert', $scope.response_code, $scope.response_msg );
  } );

  $scope.carousel_uploader.bind( 'complete', function( event, xhr, item, response ) {
    if( response.code == 200 ) {
      var result = angular.copy( response.loop_image );
      result.editing = false;

      $rootScope.$emit( 'new_carousel_image', result );
      $scope.response_code = response.code;
      $scope.response_msg = response.msg;
    }
  } );

  $scope.carousel_uploader.bind( 'afteraddingall', function( event, items ) {
    for( var i = 0; i < items.length; i++ ) {
      items[i].upload();
    }
  } );

  $scope.triggerCarouselUpload = function() {
    $( '#carousel-upload' ).trigger( 'click' );
  };
};

var ImageModalCtrl = function( $scope, $modalInstance, message ) {
  $scope.message = message;

  $scope.close = function() {
    $modalInstance.close();
  };
};

angular.module( 'ProductEditApp' ).controller( 'editProductImageCtrl', [
  '$scope', '$rootScope', '$route', '$http', '$modal', editProductImageCtrl
] );

angular.module( 'ProductEditApp' ).controller( 'imageUploadSampleCtrl', [
  '$scope', '$rootScope', '$http', '$fileUploader', imageUploadSampleCtrl
] );

angular.module( 'ProductEditApp' ).controller( 'imageUploadCarouselCtrl', [
  '$scope', '$rootScope', '$http', '$fileUploader', imageUploadCarouselCtrl
] );

angular.module( 'ProductEditApp' ).controller( 'ImageModalCtrl', [
  '$scope', '$modalInstance', ImageModalCtrl
] );
