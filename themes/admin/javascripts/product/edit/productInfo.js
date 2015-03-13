var editProductInfoCtrl = function( $scope, $rootScope, $route, $http, ProductEditFactory, commonFactory ) {
  commonFactory.getAjaxSearchCityList().then( function( data ) {
    $scope.cities = angular.copy( data );
  } );
  commonFactory.getAjaxSearchVendorList().then( function( data ) {
    $scope.vendors = angular.copy( data );
  } );

  var isFirst = true;
  $scope.$watch( 'product_info_form.$pristine', function() {
    if( isFirst ) {
      return isFirst = false;
    } else {
      $rootScope.$emit( 'dirtyForm', 'editProductInfo' );
    }
  } );

  $rootScope.$on( 'manualSave', function( event, ctrl ) {
    if( ctrl == 'editProductInfo' ) {
      $scope.submitChanges();
    }
  } );

  $scope.product_info = angular.copy( $route.current.locals.loadData );
  $scope.other_cities = $scope.product_info['other_cities'] || [];

  $scope.data = {
    vendor         : {
      supplier_id : $scope.product_info.supplier_id
    },
    city           : {
      city_code : $scope.product_info.city_code
    },
    other_city     : {
      city_code : $scope.product_info.city_code
    },
    combo_pid      : '',
    check_progress : false,
    seo            : {},
    gta_status     : ''
  };

  $scope.local = {
    edit_tag             : false,
    current_parent_tag   : -1,
    selected_parent_tags : [],
    selected_sub_tags    : [],
    origin_parent_tags   : [],
    origin_sub_tags      : []
  };

  $scope.import_status = {
    '-1' : {
      label : '未处理过'
    },
    '0'  : {
      label      : '待处理',
      class_name : 'error'
    },
    '1'  : {
      label      : '处理中',
      class_name : 'processing'
    },
    '2'  : {
      label      : '已完成',
      class_name : 'default'
    }
  };
  $scope.types = [
    {
      type_id : '1',
      label   : '单票'
    },
    {
      type_id : '2',
      label   : '组合票'
    },
    {
      type_id : '3',
      label   : '通票'
    },
    {
      type_id : '4',
      label   : '随上随下票'
    },
    {
      type_id : '5',
      label   : 'tour'
    },
    {
      type_id : '6',
      label   : 'Coupon'
    },
    {
      type_id : '7',
      label   : '酒店'
    },
    {
      type_id : '8',
      label   : '酒店套餐'
    },
    {
      type_id : '9',
      label   : '多日游'
    },
    {
      type_id : '10',
      label   : '包车'
    }
  ];
  for( var item in $scope.types ) {
    if( $scope.types[item]['type_id'] == $scope.product_info['type'] ) {
      $scope.data.type = $scope.types[item];
    }
  }

  $scope.combo_radio_options = {
    name  : 'is_combo',
    items : {
      '0' : '不是组合票',
      '1' : '是组合票'
    }
  };

  if( $scope.product_info.supplier_id == 11 && !( !!$scope.product_info.import ) ) {
    $scope.product_info.import = {
      status : '-1'
    };
  }

  $scope.updateImport = function() {
    var url = $scope.product_info.import.status == -1 ? request_urls.gtaImportAdd : request_urls.gtaImportUpdate;
    var post_data = $scope.product_info.import.status == -1 ? {
      item_id   : $scope.product_info.supplier_product_id,
      city_code : $scope.product_info.city_code
    } : {
      auto_id : $scope.product_info.import.auto_id
    };

    $http.post( url, post_data ).success( function( data ) {
      alert( data.msg );
      if( data.code == 200 ) window.location.reload();
    } );
  };

  $scope.addComboProduct = function() {
    $scope.data.check_progress = true;

    if( parseInt( $scope.data.combo_pid, 10 ) < 1 ) {
      $rootScope.$emit( 'publishAlert', 400, '请输入一个商品ID再添加' );
      return;
    }

    $http.post( request_urls.addProductCombo, {
      sub_product_id : $scope.data.combo_pid
    } ).success( function( data ) {
      $scope.data.check_progress = false;

      if( data.code == 200 ) {
        $scope.product_info.combo.push( data.data );
        $scope.data.combo_pid = '';
      } else {
        $rootScope.$emit( 'publishAlert', data.code, data.msg );
      }
    } );
  };

  $scope.delComboProduct = function( product_related_id ) {
      if(!window.confirm('是否确定移除商品？')) return;

    $http.post( request_urls.deleteProductCombo, {
      sub_product_id : product_related_id
    } ).success( function( data ) {
      var index;

      if( data.code == 200 ) {
        for( var key in $scope.product_info.combo ) {
          if( $scope.product_info.combo[key].product_id == product_related_id ) {
            index = key;
            break;
          }
        }
        $scope.product_info.combo.splice( index, 1 );
      } else {
        $rootScope.$emit( 'publishAlert', data.code, data.msg );
      }
    } );
  };

  $scope.addMoreCity = function() {
    if( $scope.data.other_city && $scope.data.other_city.city_code.length > 0 ) {
      if( !$scope.cityAdded() ) {
        $http.post( request_urls.otherCity, {'city_code' : $scope.data.other_city.city_code} ).success( function( data ) {
          if( data.code == 200 ) {
            $scope.other_cities.push( $scope.data.other_city );
          } else {
            alert( data.msg );
          }
        } );
      }
    }
  };

  $scope.deleteCity = function( city_code ) {
    if( window.confirm( '取消与所选城市的关联？' ) ) {
      $http.delete( request_urls.otherCity + city_code ).success( function( data ) {
        if( data.code == 200 ) {
          var len = $scope.other_cities.length;
          for( var i = 0; i < len; i++ ) {
            if( $scope.other_cities[i].city_code == city_code ) {
              $scope.other_cities.splice( i, 1 );
              break;
            }
          }
        } else {
          alert( data.msg );
        }
      } );
    }
  };

  $scope.cityAdded = function() {
    if( $scope.data.other_city.city_code == $scope.data.city.city_code ) {
      return true;
    }
    var len = $scope.other_cities.length;
    for( var i = 0; i < len; i++ ) {
      if( $scope.other_cities[i].city_code == $scope.data.other_city.city_code ) {
        return true;
      }
    }
    return false;
  };

  $scope.submitChanges = function() {
    var postData = angular.copy( $scope.product_info );
    postData.supplier_id = $scope.data.vendor.supplier_id;
    postData.city_code = $scope.data.city.city_code;
    postData.is_combo = $scope.product_info.is_combo;
    postData.type = $scope.data.type.type_id;
    postData.tags = angular.copy( $scope.product_tags );
    $http.post( request_urls.updateProductInfo, postData ).success( function( data ) {
      $rootScope.$emit( 'clearDirty' );
      $rootScope.$emit( 'publishAlert', data.code, data.msg );
    } );
  };

  $scope.initProductTag = function() {
    $scope.product_tags = $scope.product_info.tags;

    for( var n in $scope.product_tags ) {
      if( $scope.product_tags[n].tag.parent_tag_id > 0 ) {
        $scope.local.selected_sub_tags.push( $scope.product_tags[n].tag_id );
        $scope.local.selected_parent_tags.push( $scope.product_tags[n].tag.parent_tag_id );
      } else {
        $scope.local.selected_parent_tags.push( $scope.product_tags[n].tag_id );
      }
    }

    $http.get( request_urls.getTags ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.parent_tag = data.data.parent_tag;
        $scope.sub_tag = data.data.sub_tag;

        for( var i in $scope.parent_tag ) {
          $scope.parent_tag[i].has_child = 0;

          for( var j in $scope.sub_tag ) {
            if( $scope.sub_tag[j].parent_tag_id == $scope.parent_tag[i].tag_id ) {
              $scope.parent_tag[i].has_child = 1;
              break;
            }
          }
        }
      }
    } );
  };

  $scope.selectTag = function( which_level, index ) {
    if( which_level == 'parent' ) {
      $scope.local.current_parent_tag = $scope.parent_tag[index].tag_id;
      if( !$scope.parent_tag[index].has_child ) {
        var tag_index = $scope.local.selected_parent_tags.indexOf( $scope.local.current_parent_tag );
        if( tag_index == -1 ) {
          $scope.local.selected_parent_tags.push( $scope.local.current_parent_tag );
        } else {
          $scope.local.selected_parent_tags.splice( tag_index, 1 );
        }
      }
    } else if( which_level = 'sub' ) {
      var current_sub = $scope.sub_tag[index].tag_id;
      var sub_index = $scope.local.selected_sub_tags.indexOf( current_sub );
      if( sub_index > -1 ) {
        $scope.local.selected_sub_tags.splice( sub_index, 1 );
        var parent_id = $scope.sub_tag[index].parent_tag_id;
        var need_remove_parent = true;
        for( var i in $scope.sub_tag ) {
          if( $scope.sub_tag[i].parent_tag_id == parent_id &&
              $scope.local.selected_sub_tags.indexOf( $scope.sub_tag[i].tag_id ) > -1 ) {
            need_remove_parent = false;
            break;
          }
        }

        if( need_remove_parent ) {
          $scope.local.selected_parent_tags.splice( $scope.local.selected_parent_tags.indexOf( parent_id ), 1 );
        }
      } else {
        $scope.local.selected_sub_tags.push( current_sub );
        var parent_id = $scope.sub_tag[index].parent_tag_id;
        if( $scope.local.selected_parent_tags.indexOf( parent_id ) == -1 ) {
          $scope.local.selected_parent_tags.push( parent_id );
        }
      }
    }
  };

  $scope.editTag = function() {
    $scope.local.edit_tag = true;
    $scope.local.origin_parent_tags = angular.copy( $scope.local.selected_parent_tags );
    $scope.local.origin_sub_tags = angular.copy( $scope.local.selected_sub_tags );
  };

  $scope.closeTagDialog = function() {
    $scope.local.edit_tag = false;
    $scope.local.selected_parent_tags = angular.copy( $scope.local.origin_parent_tags );
    $scope.local.selected_sub_tags = angular.copy( $scope.local.origin_sub_tags );
  };

  $scope.applyTagChange = function() {
    $scope.local.edit_tag = false;
    var result_tag_list = [];

    var tag_template = {
      'product_id' : $scope.product_info.product_id,
      'tag_id'     : '',
      'tag'        : {}
    };

    var candidate_parent_tag = [];
    for( var i in $scope.parent_tag ) {
      if( $scope.local.selected_parent_tags.indexOf( $scope.parent_tag[i].tag_id ) > -1 ) {
        if( $scope.parent_tag[i].has_child == '1' ) {
          candidate_parent_tag.push( angular.copy( $scope.parent_tag[i] ) );
        } else {
          var tmp_tag = angular.copy( tag_template );
          tmp_tag.tag_id = $scope.parent_tag[i].tag_id;
          tmp_tag.tag = angular.copy( $scope.parent_tag[i] );
          result_tag_list.push( tmp_tag );
        }
      }
    }

    for( var j in $scope.sub_tag ) {
      if( $scope.local.selected_sub_tags.indexOf( $scope.sub_tag[j].tag_id ) > -1 ) {
        for( var n in candidate_parent_tag ) {
          if( candidate_parent_tag[n].tag_id == $scope.sub_tag[j].parent_tag_id ) {
            var tmp_tag = angular.copy( tag_template );
            tmp_tag.tag_id = $scope.sub_tag[j].tag_id;
            tmp_tag.tag = angular.copy( $scope.sub_tag[j] );
            tmp_tag.tag.parent_tag_name = candidate_parent_tag[n].name
            result_tag_list.push( tmp_tag );
            break;
          }
        }
      }
    }

    $scope.product_tags = result_tag_list;
  };

  $scope.initProductTag();
};


angular.module( 'ProductEditApp' ).controller( 'editProductInfoCtrl', [
  '$scope', '$rootScope', '$route', '$http', 'ProductEditFactory', 'commonFactory', editProductInfoCtrl
] );