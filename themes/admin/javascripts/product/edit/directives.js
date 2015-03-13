var passengerInfoSelectBoxDir = function() {
  return {
    scope       : {
      names         : '=',
      allcriteria   : '=',
      fieldcriteria : '='
    },
    replace     : true,
    restrict    : 'A',
    templateUrl : 'passenger_info_select_box.html',
    link        : function( scope ) {
      scope.hidelist = true;

      scope.isChecked = function( id ) {
        return scope.fieldcriteria.indexOf( id ) > -1;
      };

      scope.uncheckItem = function( id ) {
        scope.fieldcriteria.splice( scope.fieldcriteria.indexOf( id ), 1 );
      };

      scope.toggleItem = function( id, $event ) {
        var checked = !$( $event.target ).hasClass( 'checked' );
        if( checked ) {
          scope.fieldcriteria.push( id );
          scope.fieldcriteria.sort( function( a, b ) {
            return parseInt( a ) - parseInt( b );
          } );
        } else {
          scope.uncheckItem( id );
        }
      };
    }
  };
};

var pageInitDir = function() {
  return function() {
    $( document ).ready( function() {
      $( '#edit-menu' ).css( 'min-height', window.innerHeight - 130 );
    } );
  }
};

var closeDateDir = function() {
  return {
    scope       : {
      model : '='
    },
    replace     : true,
    restrict    : 'E',
    templateUrl : 'close_date.html',
    controller  : function( $scope ) {
      $scope.all_ranges = [];
      $scope.data = {
        range     : [],
        weekday   : [],
        singleday : []
      };
      $scope.count = 0;
      $scope.append = function() {
        $scope.count++;
        $scope.all_ranges.push( {
                                  data         : '',
                                  name         : 'custom' + $scope.count,
                                  items        : items,
                                  current_item : 'singleday'
                                } );
      };

      var isWeekday, isRange;
      var parts = $scope.model ? $scope.model.split( ';' ) : [];
      var items = {
        'singleday' : '单独固定日期',
        'weekday'   : '按周期循环',
        'range'     : '时间段'
      };

      for( var key in parts ) {
        isWeekday = parts[key].match( /周/gi );
        isRange = parts[key].split( '/' );
        if( isWeekday && isWeekday.length > 0 ) {
          $scope.data.weekday.push( parts[key] );
        } else if( isRange.length > 1 ) {
          $scope.data.range.push( isRange );
        } else {
          $scope.data.singleday.push( parts[key] );
        }
      }

      if( $scope.data.weekday.length > 0 ) {
        $scope.count++;
        $scope.all_ranges.push( {
                                  data         : $scope.data.weekday.join( ';' ),
                                  name         : 'weekday',
                                  items        : items,
                                  current_item : 'weekday'
                                } );
      }
      if( $scope.data.singleday.length > 0 ) {
        $scope.count++;
        $scope.all_ranges.push( {
                                  data         : $scope.data.singleday.join( ';' ),
                                  name         : 'singleday',
                                  items        : items,
                                  current_item : 'singleday'
                                } );
      }
      if( $scope.data.range.length > 0 ) {
        for( var key in $scope.data.range ) {
          $scope.count++;
          $scope.all_ranges.push( {
                                    data         : $scope.data.range[key],
                                    name         : 'range' + $scope.count,
                                    items        : items,
                                    current_item : 'range'
                                  } );
        }
      }

      var getDatestr = function( dateObj ) {
        if( !(dateObj instanceof Date) ) dateObj = new Date( dateObj );
        var dateStr = "";
        dateStr += dateObj.getFullYear() + "-";
        dateStr += dateObj.getMonth() < 9 ? "0" + (dateObj.getMonth() + 1) + "-" : (dateObj.getMonth() + 1) + "-";
        dateStr += dateObj.getDate() < 10 ? "0" + dateObj.getDate() : dateObj.getDate();
        return dateStr;
      };

      $scope.$watch( 'all_ranges', function( newValue, oldValue ) {
        var result = '';
        var sameLength = ( newValue.length == oldValue.length );
        var totalCount = newValue.length;

        for( var key in newValue ) {
          if( sameLength && newValue[key].current_item != oldValue[key].current_item ) {
            newValue[key].data = newValue[key].current_item == 'range' ? [new Date(), new Date()] : '';
          }

          if( newValue[key].current_item == 'range' ) {
            result += getDatestr( newValue[key].data[0] ) + '/' + getDatestr( newValue[key].data[1] );
          } else if( newValue[key].data.trim().length > 0 ) {
            result += newValue[key].data;
          }

          if( result[result.length - 1] == ';' ) {
            continue;
          } else {
            if( key + 1 == totalCount ) continue; else result += ';';
          }
        }
        $scope.model = result;
      }, true );

    },
    link        : function( scope, element ) {
      $( element ).on( 'click', '.del-closedate', function() {
        scope.all_ranges.splice( $( this ).data( 'index' ), 1 );
        scope.$apply( 'all_ranges', scope.all_ranges );
      } );
    }
  };
};

var saleRangeTypeDir = function() {
  return {
    scope       : {
      model : '='
    },
    replace     : true,
    restrict    : 'E',
    templateUrl : 'sale_range_type.html',
    controller  : function( $scope ) {
      $scope.name = 'sale_range';
      $scope.monthCount = parseInt( $scope.model.sale_range, 10 );
      $scope.options = {
        '0' : '从现在起几个月内',
        '1' : '固定时间段'
      };
      $scope.changeMonth = function() {
        $scope.model.sale_range = parseInt( $scope.monthCount, 10 ) + 'Month';
      }
    }
  };
};

var sidebarDir = function( ProductEditFactory ) {
  return {
    scope       : {},
    replace     : true,
    restrict    : 'E',
    templateUrl : 'sidebar.html',
    link        : function( scope, element, attrs ) {
      scope.current_section = ProductEditFactory.sections[attrs.name];
    }
  };
};

var closeAnyDateDir = function() {
  return {
    scope       : {
      model   : '=',
      mindate : '=',
      maxdate : '='
    },
    replace     : true,
    restrict    : 'E',
    templateUrl : 'close_any_date.html',
    controller  : function( $scope ) {
      $scope.all_ranges = [];
      $scope.data = {
        range     : [],
        weekday   : [],
        singleday : []
      };
      $scope.count = 0;
      $scope.append = function() {
        $scope.count++;
        $scope.all_ranges.push( {
                                  data         : '',
                                  name         : 'custom' + $scope.count,
                                  items        : items,
                                  current_item : 'singleday'
                                } );
      };

      var isWeekday, isRange;
      var parts = $scope.model ? $scope.model.split( ';' ) : [];
      var items = {
        'singleday' : '单独固定日期',
        'weekday'   : '按周期循环',
        'range'     : '时间段'
      };

      for( var key in parts ) {
        isWeekday = parts[key].match( /周/gi );
        isRange = parts[key].split( '/' );
        if( isWeekday && isWeekday.length > 0 ) {
          $scope.data.weekday.push( parts[key] );
        } else if( isRange.length > 1 ) {
          $scope.data.range.push( isRange );
        } else {
          $scope.data.singleday.push( parts[key] );
        }
      }

      if( $scope.data.weekday.length > 0 ) {
        $scope.count++;
        $scope.all_ranges.push( {
                                  data         : $scope.data.weekday.join( ';' ),
                                  name         : 'weekday',
                                  items        : items,
                                  current_item : 'weekday'
                                } );
      }
      if( $scope.data.singleday.length > 0 ) {
        $scope.count++;
        $scope.all_ranges.push( {
                                  data         : $scope.data.singleday.join( ';' ),
                                  name         : 'singleday',
                                  items        : items,
                                  current_item : 'singleday'
                                } );
      }
      if( $scope.data.range.length > 0 ) {
        for( var key in $scope.data.range ) {
          $scope.count++;
          $scope.all_ranges.push( {
                                    data         : $scope.data.range[key],
                                    name         : 'range' + $scope.count,
                                    items        : items,
                                    current_item : 'range',
                                    min_date     : $scope.mindate,
                                    max_date     : $scope.maxdate
                                  } );
        }
      }

      var getDatestr = function( dateObj ) {
        if( !(dateObj instanceof Date) ) dateObj = new Date( dateObj );
        var dateStr = "";
        dateStr += dateObj.getFullYear() + "-";
        dateStr += dateObj.getMonth() < 9 ? "0" + (dateObj.getMonth() + 1) + "-" : (dateObj.getMonth() + 1) + "-";
        dateStr += dateObj.getDate() < 10 ? "0" + dateObj.getDate() : dateObj.getDate();
        return dateStr;
      };

      $scope.$watch( 'all_ranges', function( newValue, oldValue ) {
        var result = '';
        var sameLength = ( newValue.length == oldValue.length );
        var totalCount = newValue.length;

        for( var key in newValue ) {
          if( sameLength && newValue[key].current_item != oldValue[key].current_item ) {
            newValue[key].data = newValue[key].current_item == 'range' ? [new Date(), new Date()] : '';
          }

          if( newValue[key].current_item == 'range' ) {
            result += getDatestr( newValue[key].data[0] ) + '/' + getDatestr( newValue[key].data[1] );
          } else if( newValue[key].data.trim().length > 0 ) {
            result += newValue[key].data;
          }

          if( result[result.length - 1] == ';' ) {
            continue;
          } else {
            if( key + 1 == totalCount ) continue; else result += ';';
          }
        }
        $scope.model = result;
      }, true );

    },
    link        : function( scope, element ) {
      $( element ).on( 'click', '.del-closedate', function() {
        scope.all_ranges.splice( $( this ).data( 'index' ), 1 );
        scope.$apply( 'all_ranges', scope.all_ranges );
      } );
    }
  };
};

var pickTicketMapDir = function() {
  return {
    scope    : {
      mapid   : '=',
      editing : '=',
      points  : '=',
      mapinfo : '=',
      actions : '='
    },
    replace  : true,
    restrict : 'E',
    template : '<div style="position:fixed; top:0px; left: 0px; width: 100%; height: 100%; z-index: 10000;">' +
               '<div style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background-color: #f0f0f0;"></div>' +
               '<div style="margin: 50px auto; width:1280px; height: 310px;">' +
               '<div id="{{mapid}}" style="width:1280px; height: 310px; margin-bottom: 20px;"></div>' +
               '<button class="btn btn-sharp col-xs-3 col-xs-offset-1" data-ng-click="save()">保存</button>' +
               '<button class="btn btn-sharp col-xs-3 col-xs-offset-1" data-ng-click="cancel()">取消</button>' +
               '</div></div>',

    controller : function( $scope ) {
      $scope.inited = false;
      $scope.featureLayer = L.mapbox.featureLayer();
      $scope.init = function() {
        L.mapbox.accessToken = "pk.eyJ1IjoibmF0ZWN1aSIsImEiOiJrOU9OdTBFIn0.SSxx8lhLixaiWZhXzslJ0g";
        $scope.map = L.mapbox.map( $scope.mapid, "natecui.ig5adgfm", {} );
        L.control.scale().addTo( $scope.map );

        $scope.map.on( 'zoomend', function( e ) {
          $scope.mapinfo.zoom = $scope.map.getZoom();
          console.log( 'zoomend, current zoom: ' + $scope.mapinfo.zoom );
        } );

        $scope.map.on( 'moveend', function( e ) {
          $scope.mapinfo.center = $scope.map.getCenter();
          console.log( 'moveend, current center: ' + $scope.mapinfo.center );
        } );

        $scope.featureLayer.on( 'ready', function() {
          // featureLayer.getBounds() returns the corners of the furthest-out markers,
          // and map.fitBounds() makes sure that the map contains these.
          $scope.map.fitBounds( $scope.featureLayer.getBounds() );
        } );

        $scope.inited = true;
      };

      $scope.save = function() {
        $scope.actions.save();
      };

      $scope.cancel = function() {
        $scope.actions.cancel();
      };

      $scope.$watch( 'editing', function( newValue, oldValue ) {
        if( newValue == 1 && $scope.inited == false ) {
          $scope.init();
        }
      } );

      $scope.$watch( 'points', function( newValue, oldValue ) {
        if( newValue.length > 0 ) {
          console.log( 'newValue: ' + newValue );

          angular.forEach( newValue, function( value, key ) {
            var marker = L.marker( value, {
              icon : L.mapbox.marker.icon( {
                                             'marker-symbol' : 'bus',
                                             'marker-color'  : '#f86767'
                                           } )
            } );

            $scope.featureLayer.addLayer( marker );
            //              marker.addTo($scope.map);

          } );
          $scope.featureLayer.addTo( $scope.map );
          $scope.map.fitBounds( $scope.featureLayer.getBounds() );
        } else {
          // clear markers
          $scope.featureLayer.clearLayers();

        }
      } );
    }
  };
};

var hiDndDir = function() {
  var dnd;
  var linkFunc = function( scope, element ) {
    var el = $( element[0] );
    scope.options.offset = parseInt( scope.options.offset, 10 ) || 0;

    el.attr( 'draggable', true ).on( 'dragstart',function( e ) {
      $( this ).addClass( 'dragging' );
      e.originalEvent.dataTransfer.dropEffect = 'move';

      dnd = {
        src_index : $( this ).attr( 'data-index' ),
        src_item  : scope.item
      };
      if( scope.options.offset > 0 ) {
        dnd.src_index = parseInt( dnd.src_index ) + scope.options.offset;
      }

    } ).on( 'dragover',function( e ) {
      e.preventDefault();

      return false;
    } ).on( 'dragenter',function() {
      if( !$( this ).hasClass( 'dragging' ) ) {
        $( this ).addClass( 'hovering' );
      }
    } ).on( 'dragleave',function() {
      $( this ).removeClass( 'hovering' );
    } ).on( 'dragend',function() {
      $( this ).removeClass( 'hovering' );
      $( this ).removeClass( 'dragging' );
    } ).on( 'drop', function( e ) {
      e.stopPropagation();

      var dsc_index = $( this ).attr( 'data-index' );
      if( scope.options.offset > 0 ) {
        dsc_index = parseInt( dsc_index ) + scope.options.offset;
      }
      if( dnd.src_index != dsc_index ) {
        var param = {info : dnd, dst_index : dsc_index};
        scope.callback( param );
      }

      $( this ).removeClass( 'hovering' );
      $( this ).removeClass( 'dragging' );

      return false;
    } );
  };

  return {
    link     : linkFunc,
    scope    : {
      item     : '=',
      options  : '=',
      callback : '&'
    },
    replace  : false,
    restrict : 'AE'
  };
};


angular.module( 'ProductEditApp' ).directive( 'passengerInfoSelectBox', passengerInfoSelectBoxDir );

angular.module( 'ProductEditApp' ).directive( 'pageInit', pageInitDir );

angular.module( 'ProductEditApp' ).directive( 'closeDate', closeDateDir );

angular.module( 'ProductEditApp' ).directive( 'saleRangeType', saleRangeTypeDir );

angular.module( 'ProductEditApp' ).directive( 'sidebar', ['ProductEditFactory', sidebarDir] );

angular.module( 'ProductEditApp' ).directive( 'closeAnyDate', closeAnyDateDir );

angular.module( 'ProductEditApp' ).directive( 'pickTicketMap', pickTicketMapDir );

angular.module( 'ProductEditApp' ).directive( 'hiDnd', hiDndDir );
