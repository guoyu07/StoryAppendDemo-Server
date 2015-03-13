var editProductAlbumCtrl = function( $scope, $rootScope, $route, $http ) {
  $scope.albums = angular.copy( $route.current.locals.loadData );
  $scope.local_model = {
    need_land          : $scope.albums.land.need_album,
    valid_land         : $scope.albums.land.album_id,
    album_map          : $scope.albums.land.album_map,
    edit_album_map     : 0,
    album_points       : [],
    album_mapinfo      : {zoom : 10, center : []},
    album_map_id       : 'album_map',
    link_progress_land : false,
    landinfo_md_title  : $route.current.locals.loadData.landinfo_md_title
  };
  $scope.radio_options = {
    need_land : {
      name : 'need_land', items : {
        '0' : '不需要', '1' : '需要'
      }
    }
  };

  function composeMarkdown(md) {
    return encodeURIComponent(JSON.stringify({md_text : md.md_text, md_html : !!md.md_html && md.md_text.length > 0 ? md.md_html.$$unwrapTrustedValue() : ''}));
  }

  $scope.lands_list = $route.current.locals.loadData.land_list;

  $scope.addList = function() {
    $scope.lands_list.push( {
                              title : '', list : {
        md_text : '', md_html : ''
      }
                            } );
  };

  $scope.delList = function( index ) {
    $scope.lands_list.splice( index, 1 );
  };

  $scope.updateLandAlbum = function() {
    $scope.local_model.valid_land = false;
    $scope.local_model.link_progress_land = true;
    $http.post( request_urls.updateProductAlbum, {
      need_album : $scope.local_model.need_land, album_id : $scope.albums.land.album_id
    } ).success( function( data ) {
      $scope.local_model.link_progress_land = false;
      if( data.code == 200 ) {
        $scope.albums.land.album_info = data.data;
        $scope.local_model.valid_land = true;
      } else {
        $rootScope.$emit( 'publishAlert', data.code, data.msg );
      }
    } );
  };

  $scope.getAlbumMapPoints = function() {
    // get points of album
    var points = [];
    angular.forEach( $scope.albums.land.album_points, function( value, key ) {
      var location = value.split( ',' );
      if( location.length == 2 ) {
        points.push( location );
      }
    } );

    return points;
  };

  $scope.addAlbumMap = function() {
    $scope.local_model.album_points = $scope.getAlbumMapPoints();

    $scope.local_model.edit_album_map = 1;
  };

  $scope.editAlbumMap = function() {
    $scope.local_model.album_points = $scope.getAlbumMapPoints();
    $scope.local_model.edit_album_map = 1;
  };

  $scope.saveAlbumMap = function() {
    // save map in server end and get the url of pick ticket map.
    $http.post( request_urls.saveAlbumMap, {
      center : $scope.local_model.album_mapinfo.center,
      zoom   : $scope.local_model.album_mapinfo.zoom,
      points : $scope.local_model.album_points
    } ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.albums.land.album_map = data.data.album_map;
        $scope.local_model.album_map = data.data.album_map;
        $scope.local_model.edit_album_map = 0;
      } else {
        alert( data.msg );
        $scope.local_model.edit_album_map = 0;
      }
    } );
  };

  $scope.cancelEditAlbumMap = function() {
    $scope.local_model.edit_album_map = 0;
  };

  $scope.local_model.album_actions = {save : $scope.saveAlbumMap, cancel : $scope.cancelEditAlbumMap};

  $scope.submitChanges = function() {
    //    alert('save! ' + $scope.local_model.landinfo_md_title);
    var message = '';

    var landinfo_groups = $scope.lands_list.map(function(one_list) {
      if(!((typeof one_list.list.md_html=='string')&&(one_list.list.md_html.constructor==String))){
        one_list.list.md_html = one_list.list.md_html.$$unwrapTrustedValue();
        delete one_list.$$hashKey;
      }

      return one_list;
    });

    var postData = {
      need_album        : $scope.local_model.need_land.toString(),
      album_id          : !!$scope.albums.land.album_id ? $scope.albums.land.album_id : "0",
      album_name        : $scope.albums.land.album_name,
      landinfo_md       : encodeURIComponent( JSON.stringify( landinfo_groups ) ),
      landinfo_md_title : $scope.local_model.landinfo_md_title
    };


    if( postData.need_album == '1' && parseInt( postData.album_id, 10 ) == 0 ) {
      message += '景点专辑ID不能为空';
    }

    if( message.length > 0 ) {
      $rootScope.$emit( 'publishAlert', 400, message );
    } else {
      $http.post( request_urls.saveAlbumInfoAll, postData ).success( function( data ) {
        if( data.code == 200 ) {

          $scope.albums.land.album_id = data.data.album_info.album_id;
          if( data.data.album_info.album_id ) {
            $scope.albums.land.album_info.link = data.data.album_info.link;
            $scope.albums.land.album_info.title = data.data.album_info.title;
          }
        }

        $rootScope.$emit( 'publishAlert', data.code, data.msg );
        $rootScope.$emit( 'clearDirty' );
      } );
    }
  };

  var isFirst = true;
  $scope.$watch( 'product_album_form.$pristine', function() {
    if( isFirst ) {
      return isFirst = false;
    } else {
      $rootScope.$emit( 'dirtyForm', 'editProductAlbum' );
    }
  } );
  $rootScope.$on( 'manualSave', function( event, ctrl ) {
    if( ctrl == 'editProductAlbum' ) {
      $scope.submitChanges();
    }
  } );

  if( $scope.local_model.valid_land ) {
    $scope.albums.land.album_id = parseInt( $scope.albums.land.album_id, 10 );
  }

};

angular.module( 'ProductEditApp' ).controller( 'editProductAlbumCtrl', [
  '$scope', '$rootScope', '$route', '$http', editProductAlbumCtrl
] );