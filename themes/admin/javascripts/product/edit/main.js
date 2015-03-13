var routes = function( $routeProvider ) {
  $routeProvider.when( '/selectPassengerInfoType', {
    templateUrl : 'selectPassengerInfoType.html',
    controller  : 'selectPassengerInfoTypeCtrl'
  } ).when( '/editPassengerInfo/:type', {
    templateUrl : 'editPassengerInfo.html',
    controller  : 'editPassengerInfoCtrl'
  } ).when( '/editPassengerInfo', {
    templateUrl : 'editPassengerInfo.html',
    controller  : 'editPassengerInfoCtrl'
  } ).when( '/editProductRules', {
    templateUrl : 'editProductRules.html',
    controller  : 'editProductRulesCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();
        var initDate = function( datestr ) {
          return !datestr || datestr == '0000-00-00' ? new Date( 0 ) : new Date( datestr );
        };

        $http.get( request_urls.getProductRules ).success( function( data ) {

          if( data.code == 200 ) {
            data = data.data;
          } else {
            defer.reject( data.msg );
          }

          for( var key in data.sale_limit ) {
            data.sale_limit[key] = parseInt( data.sale_limit[key], 10 ) || data.sale_limit[key];
          }

          if( data.hasOwnProperty( 'redeem_limit' ) ) data.redeem_limit.expire_date = initDate( data.redeem_limit.expire_date );
          if( data.hasOwnProperty( 'sale_date_rule' ) ) {
            data.sale_date_rule.from_date =
            ( data.sale_date_rule.from_date == '0000-00-00' ) ? new Date() : new Date( data.sale_date_rule.from_date );
            data.sale_date_rule.to_date = initDate( data.sale_date_rule.to_date );
          }

          defer.resolve( data );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editProductQna', {
    templateUrl : 'editProductQna.html',
    controller  : 'editProductQnaCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.getProductQA ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editProductPrice', {
    templateUrl : 'editProductPrice.html',
    controller  : 'editProductPriceCtrl',
    resolve     : {
    }
  } ).when( '/editProductInfo', {
    templateUrl : 'editProductInfo.html',
    controller  : 'editProductInfoCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.getProductInfo ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editProductDesc', {
    templateUrl : 'editProductDesc.html',
    controller  : 'editProductDescCtrl'
  } ).when( '/productTourPlan', {
    templateUrl : 'productTourPlan.html',
    controller  : 'productTourPlanCtrl'
  } ).when( '/editProductAlbum', {
    templateUrl : 'editProductAlbum.html',
    controller  : 'editProductAlbumCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        return getProductAlbum( $http, $q );
      }
    }
  } ).when( '/editProductImage', {
    templateUrl : 'editProductImage.html',
    controller  : 'editProductImageCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.getProductImages ).success( function( data ) {
          var result = {};

          if( data.code == 200 ) {
            result.sample_image = data.data.sample_image;
            result.has_sample = angular.isObject( data.data.sample_image );

            result.carousel_images = data.data.loop_images;
            result.has_carousel = (data.data.loop_images.length > 0);

            result.album_images = data.data.landinfos;
            result.has_album = ( data.data.landinfos.length > 0 );
          } else {

            alert( data.msg );

          }

          defer.resolve( result );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editProductRelated', {
    templateUrl : 'editProductRelated.html',
    controller  : 'editProductRelatedCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.getProductRelated ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editVoucherRule', {
    templateUrl : 'editVoucherRule.html',
    controller  : 'editVoucherRuleCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.getVoucherRules ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editShippingConfig', {
    templateUrl : 'editShippingConfig.html',
    controller  : 'editShippingConfigCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.getShippingConfigurations ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editPrice/:current_page', {
    templateUrl : 'editProductPrice.html',
    controller  : 'editProductPriceCtrl'
  } ).when( '/editProductSeo', {
    templateUrl : 'editProductSeo.html',
    controller  : 'editProductSeoCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.productSeo ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editProductComment', {
    templateUrl : 'editProductComment.html',
    controller  : 'editProductCommentCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.productComments ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editProductCoupon', {
    templateUrl : 'editProductCoupon.html',
    controller  : 'editProductCouponCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();
        //var rules = $http.get( request_urls.productCouponRule );
        var templates = $http.get( request_urls.getProductCouponTemplateList );

        $q.all( [templates] ).then( function( values ) {
          var result = {};
          if( values[0].data.code == 200 ) {
            result.templates = values[0].data.data;
          }
          defer.resolve( result );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editProductBundle', {
    templateUrl : 'editProductBundle.html',
    controller  : 'editProductBundleCtrl'
  } ).when( '/editProductHotel', {
    templateUrl : 'editProductHotel.html',
    controller  : 'editProductHotelCtrl',
    resolve     : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.hotelInfo ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editHotelRoomType', {
    templateUrl : 'editHotelRoom.html',
    controller  : 'editHotelRoomCtrl'
  } ).when( '/editProductTripPlan', {
    templateUrl : 'editProductTripPlan.html',
    controller  : 'editProductTripPlanCtrl',
    resolve : {
      loadData : function( $http, $q ) {
        return getTripPlan($http, $q);
      }
    }
  } ).when( '/editProductIntroduction/:tab_name', {
    templateUrl : 'editProductIntroduction.html',
    controller : 'editProductIntroductionCtrl',
    resolve : {
      loadData : function( $http, $q ) {
        var defer = $q.defer();

        $http.get( request_urls.getProduct ).success( function( data ) {
          defer.resolve( data.code == 200 ? data.data : [] );
        } );

        return defer.promise;
      }
    }
  } ).when( '/editProductIntroduction', {
      templateUrl : 'editProductIntroduction.html',
      controller : 'editProductIntroductionCtrl'
    } );
};


var getTripPlan = function($http, $q) {
  var defer = $q.defer();
          var ajax_plan = $http.get( request_urls.getTripPlan );
          var ajax_list = $http.get( request_urls.getBundleList );

          $q.all( [ajax_plan, ajax_list] ).then( function( values ) {
            if( values[0].data.code == 200 && values[1].data.code == 200 ) {
              var result = {};
              result.is_online = values[0].data.data.is_online;
              result.plan_days = angular.copy( values[0].data.data.data );
              result.bundle_list = [];
              result.hotel_list = [];
              result.product_list = [];

              for( var i in values[1].data.data ) {
                for( var j in values[1].data.data[i].items ) {
                  var the_value = values[1].data.data[i].items[j].product;
                  the_value.selected = false;
                  result.bundle_list.push( the_value );
                  if( the_value.type == 7 ) {
                    result.hotel_list.push( the_value );
                  } else {
                    result.product_list.push( the_value );
                  }
                }
              }

              defer.resolve( result );
            } else if( values[0].data.code != 200 ) {
              alert( values[0].data.msg );
            } else if( values[1].data.code != 200 ) {
              alert( values[1].data.msg );
            }
          } );

          return defer.promise;
}

var getProductAlbumOld = function($http, $q) {
  var defer = $q.defer();

  var landreq = $http.get( request_urls.getProductAlbum );
  var specialreq = $http.get( request_urls.getProductPickTicketAlbum );

  $q.all( [landreq, specialreq] ).then( function( values ) {
    var productLandAlbum = values[0].data.code == 200 ? values[0].data.data : [];
    var productSpecialAlbum = [];
    var landList;
    var landinfo_md_title;

    if( values[1].data.code == 200 ) {
      productSpecialAlbum = {
        album_id        : parseInt( values[1].data.data.pick_ticket_album_id, 10 ),
        album_info      : values[1].data.data.pick_ticket_album_info,
        pick_ticket_map : values[1].data.data.pick_ticket_map,
        group           : values[1].data.data.pt_group_info ? JSON.parse( values[1].data.data.pt_group_info ) : [],
        landinfos       : values[1].data.data.landinfos,
        need_album      : values[1].data.data.need_pick_ticket_album
      };
      if( !angular.isArray( productSpecialAlbum.group ) ) {
        productSpecialAlbum.group = [];
      }
      landinfo_md_title = values[0].data.data.landinfo_md_title;
      try {
        landList = JSON.parse( decodeURIComponent( values[0].data.data.landinfo_md ) );
      } catch( e ) {
      }
    }

    productLandAlbum.album_id = parseInt( productLandAlbum.album_id, 10 );

    var result = {
      land              : productLandAlbum,
      special           : productSpecialAlbum,
      land_list         : landList || [],
      landinfo_md_title : landinfo_md_title
    };

    defer.resolve( result );
  } );

  return defer.promise;
}

var getProductAlbum = function($http, $q) {
  var defer = $q.defer();

  var landreq = $http.get( request_urls.getProductAlbum );

  $q.all( [landreq] ).then( function( values ) {
    var productLandAlbum = values[0].data.code == 200 ? values[0].data.data : [];
    var productSpecialAlbum = [];
    var landList;
    var landinfo_md_title;

    landinfo_md_title = values[0].data.data.landinfo_md_title;
    try {
      landList = JSON.parse( decodeURIComponent( values[0].data.data.landinfo_md ) );
    } catch( e ) {
    }

    productLandAlbum.album_id = parseInt( productLandAlbum.album_id, 10 );

    var result = {
      land              : productLandAlbum,
      land_list         : landList || [],
      landinfo_md_title : landinfo_md_title
    };

    defer.resolve( result );
  } );

  return defer.promise;
}

var routesRun = function( $location ) {

  //Force default path - For some reason otherwise does not work
  if( $location.$$path.length == 0 ) {
    $location.path( '/editProductInfo' ).replace();
  }

};


var module = angular.module( 'ProductEditApp', [
  'ngRoute', 'ngAnimate', 'ngResource', 'histeria.factory', 'histeria.directive', 'ui.bootstrap',
  'localytics.directives', 'angularFileUpload'
] );

module.config( ['$routeProvider', routes] ).run( ['$location', routesRun] );