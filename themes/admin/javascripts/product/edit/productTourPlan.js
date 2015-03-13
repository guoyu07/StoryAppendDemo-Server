var productTourPlanCtrl = function( $scope, $http, $fileUploader, $rootScope ) {

  var current_item;
  var current_group;

  $scope.uploader = $fileUploader.create( {
                                            url     : request_urls.uploadImage,
                                            scope   : $scope,
                                            filters : []
                                          } );
  $scope.uploader.filters.push( function( item ) {
    var type = '|' + item.type.toLowerCase().slice( item.type.lastIndexOf( '/' ) + 1 ) + '|';
    return '|jpg|png|jpeg|bmp|gif|'.indexOf( type ) !== -1;
  } );
  $scope.uploader.bind( 'success', function( event, xhr, item, response ) {
    $scope.uploader.queue = [];
    if( response.code == 200 ) {
      if( $scope.info.display_type == 1 ) {
        $scope.plans[$scope.plan_index].groups[current_group].items[current_item].image_url = response.data;
      } else {
        $scope.items[current_item].image_url = response.data;
      }
    }
  } );
  $scope.uploader.bind( 'beforeupload', function( event, item ) {
    var item_id;
    if( $scope.info.display_type == 1 ) {
      item_id = $scope.plans[$scope.plan_index].groups[current_group].items[current_item].item_id;
    } else {
      item_id = $scope.items[current_item].item_id;
    }
    item.formData = [
      { item_id : item_id }
    ];
  } );

  $scope.uploader.bind( 'afteraddingfile', function( event, item ) {
    item.upload();
  } );

  $scope.tourPlanEditing = false;
  $scope.tourPlanEditClick = function() {
    $scope.tourPlanEditing = true;
    $scope.pageHaveEditing();
  }

  $scope.submitTourPlanChanges = function() {
    if( !$scope.cn_schedule || $scope.cn_schedule.trim().length == 0 ) {
      alert( '请输入标题。' );
      return;
    }

    if( $scope.info.display_type == 0 ) {
      $scope.info.total_days = 0;
    } else {
      if( $scope.info.total_days == 0 ) {
        alert( "请选择游玩天数!" );
        return;
      }
    }
    if( !window.confirm( '改变显示样式或减少游玩天数会清除部分原有录入图文内容，是否继续？' ) ) {
      return;
    }
    $http.post( request_urls.addTourPlan, {'total_days' : $scope.info.total_days, 'plans' : $scope.plans, 'cn_schedule' : $scope.cn_schedule ,'is_online': $scope.info.is_online} ).success( function( data ) {
      $scope.tourPlanEditing = false;
      if( data.code == 200 ) {
        $scope.init();
        $scope.pageEndEditing();
        alert( data.msg );
      }
      if( data.code == 401 ) {
        window.location.reload();
      }
    } );
  }

  $scope.radio_options = {
    display_type : {
      name  : 'display_type',
      items : {
        0 : '简单图文',
        1 : '按天显示且带时间轴的图文'
      }
    },
    is_online :{
      name  : 'is_online',
      items : {
        0 : '不上线',
        1 : '上线'
      }
    }
  }

  $scope.init = function() {

    $http.get( request_urls.getTourPlanDetail ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.info = data.data;
        $scope.cn_schedule = data.data.cn_schedule;
        $scope.plans = [];

        if( data.data.plans.length > 0 ) {
          $scope.plans = data.data.plans;
          for( key in $scope.plans ) {
            var groups = $scope.plans[key].groups;
            $scope.plans[key].groups = groups.sort( function( a, b ) {
              return a.display_order - b.display_order;
            } );
          }


          $scope.current_plan_id = data.data.plans[0].plan_id;
          $scope.plan_index = 0;
          if( data.data.total_days == 0 ) {//简单图文
            if( data.data.plans[0].groups.length > 0 && data.data.plans[0].groups[0].items.length > 0 ) {
              $scope.items = data.data.plans[0].groups[0].items;
            } else {
              $scope.items = [];
            }
            for( var i = 0, len = $scope.items.length; i < len; i++ ) {
              $scope.items[i].editing = false;
            }
          } else {
            for( var m = 0, plan_len = $scope.plans.length; m < plan_len; m++ ) {
              for( var n = 0, group_len = $scope.plans[m].groups.length; n < group_len; n++ ) {
                $scope.plans[m].groups[n].editing = false;
                for( var p = 0, item_len = $scope.plans[m].groups[n].items.length; p < item_len; p++ ) {
                  $scope.plans[m].groups[n].items[p].editing = false;
                }
              }
            }
          }
        } else {
          $scope.current_plan_id = 0;
          $scope.tourPlanEditing = true;
        }
      } else {
        $scope.alert = {
          type    : 'danger',
          message : data.msg
        };
      }
    } );
  }

  $scope.init();

  $scope.switchDayPlan = function( index ) {
    $scope.current_plan_id = $scope.plans[index].plan_id;
    $scope.plan_index = index;

  }

  $scope.changeTotalDays = function() {

    if( $scope.info.total_days < $scope.plans.length ) {
      $scope.plans.splice( $scope.info.total_days, $scope.plans.length - $scope.info.total_days );
    }
    var len = $scope.plans.length;
    //$scope.plans = [];
    for( var i = len; i < $scope.info.total_days; i++ ) {
      $scope.plans.push( {
                           plan_id    : '',
                           title      : '',
                           the_day    : i + 1,
                           total_days : $scope.info.total_days
                         } );
    }
  }

  //添加分组
  $scope.addGroup = function( index ) {
    var plan_id = $scope.plans[index].plan_id;
    if( $scope.current_plan_id == 0 ) {
      alert( "请先编辑基本信息！" );
      return;
    }
    $http.post( request_urls.addTourPlanGroup, {'plan_id' : plan_id} ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.plans[index].groups.push( {
                                           group_id : data.data.group_id,
                                           editing  : true
                                         } );
        $scope.pageHaveEditing();
      } else {
        $scope.alert = {
          type    : 'danger',
          message : data.msg
        };
      }
    } );
  };

  //插入分组
  $scope.insertGroup = function( plan, group ) {
    var plan_id = plan.plan_id;
    if( $scope.current_plan_id == 0 ) {
      alert( "请先编辑基本信息！" );
      return;
    }

    var group_index = plan.groups.indexOf( group );
    $http.post( request_urls.insertTourPlanGroup,
                {'plan_id' : plan_id, 'group_id' : group.group_id} )
        .success( function( data ) {
                        if( data.code == 200 ) {
                          plan.groups.splice(group_index + 1, 0, {
                            group_id : data.data.new_group_id,
                            editing  : true,
                            title: '',
                            time: ''
                          });

                          for (var i in data.data.groups) {
                            plan.groups[i].display_order = data.data.groups[i].display_order;
                          }

                          $scope.pageHaveEditing();
                        } else {
                          $scope.alert = {
                            type    : 'danger',
                            message : data.msg
                          };
                        }
                      } );
  }

  //添加图文项
  $scope.addTourPlanItem = function( group_index ) {
    if( $scope.current_plan_id == 0 ) {
      alert( "请先编辑基本信息！" );
      return;
    }
    var post;
    if( $scope.info.display_type == 1 ) {
      var group_id = $scope.plans[$scope.plan_index].groups[group_index].group_id;
      post = {'group_id' : group_id};
    } else {
      post = {'plan_id' : $scope.current_plan_id};
    }
    $http.post( request_urls.addTourPlanItem, post ).success( function( data ) {
      if( data.code == 200 ) {
        if( $scope.info.display_type == 1 ) {
          if( typeof($scope.plans[$scope.plan_index].groups[group_index].items) == 'undefined' ) {
            $scope.plans[$scope.plan_index].groups[group_index].items = [];
          }
          $scope.plans[$scope.plan_index].groups[group_index].items.push( {
                                                                            item_id : data.data.item_id,
                                                                            editing : true
                                                                          } );
        } else {
          $scope.items.push( {
                               item_id : data.data.item_id,
                               editing : true
                             } );
        }
        $scope.pageHaveEditing();
      } else {
        $scope.alert = {
          type    : 'danger',
          message : data.msg
        };
      }
    } );
  };

  //更改图片
  $scope.changeImage = function( group_index, index ) {
    current_item = index;
    current_group = group_index;
    $( '#home-slide-upload' ).trigger( 'click' );
  };

  //编辑文本
  $scope.toggleItem = function( group_index, index ) {

    if( $scope.info.display_type == 1 ) {
      if( $scope.plans[$scope.plan_index].groups[group_index].items[index].editing == false ) {
        $scope.plans[$scope.plan_index].groups[group_index].items[index].editing = !$scope.plans[$scope.plan_index].groups[group_index].items[index].editing;
        $scope.pageHaveEditing();
      } else {
        $http.post( request_urls.updateTourPlanItem, $scope.plans[$scope.plan_index].groups[group_index].items[index] ).success( function( data ) {
          if( data.code != 200 ) {
            alert( data.msg );
          } else {
            $scope.plans[$scope.plan_index].groups[group_index].items[index].editing = !$scope.plans[$scope.plan_index].groups[group_index].items[index].editing;
            $scope.pageEndEditing();
          }
        } );
      }
    } else {
      if( $scope.items[index].editing == false ) {
        $scope.items[index].editing = !$scope.items[index].editing;
        $scope.pageHaveEditing();
      } else {
        $http.post( request_urls.updateTourPlanItem, $scope.items[index] ).success( function( data ) {
          if( data.code != 200 ) {
            alert( data.msg );
          } else {
            $scope.items[index].editing = !$scope.items[index].editing;
            $scope.pageEndEditing();
          }
        } );
      }
    }
  };

  //编辑分组标题
  $scope.toggleGroup = function( group_index ) {
    if( $scope.plans[$scope.plan_index].groups[group_index].editing == false ) {
      $scope.pageHaveEditing();
      $scope.plans[$scope.plan_index].groups[group_index].editing = !$scope.plans[$scope.plan_index].groups[group_index].editing;
    } else {
      $http.post( request_urls.updateTourPlanGroup, $scope.plans[$scope.plan_index].groups[group_index] ).success( function( data ) {
        if( data.code != 200 ) {
          alert( data.msg );
        } else {
          $scope.pageEndEditing();
          $scope.plans[$scope.plan_index].groups[group_index].editing = !$scope.plans[$scope.plan_index].groups[group_index].editing;
        }
      } );
    }
  };

  //删除图文
  $scope.deleteItem = function( group_index, index ) {
    if( !window.confirm( '删除后数据不可恢复。\n点击确定来删除。' ) ) {
      return;
    }
    var url;
    if( $scope.info.display_type == 1 ) {
      url = request_urls.deleteItem + $scope.plans[$scope.plan_index].groups[group_index].items[index].item_id;
    } else {
      url = request_urls.deleteItem + $scope.items[index].item_id;
    }

    $http.post( url ).success( function( data ) {
      if( data.code == 200 ) {
        if( $scope.info.display_type == 1 ) {
          $scope.plans[$scope.plan_index].groups[group_index].items.splice( index, 1 );
        } else {
          $scope.items.splice( index, 1 );
        }
        $scope.pageEndEditing();
      } else {
        $scope.alert = {
          type    : 'danger',
          message : data.msg
        };
      }
    } );
  };

  //删除分组
  $scope.deleteGroup = function( index ) {
    if( !window.confirm( '删除后数据不可恢复。\n点击确定来删除。' ) ) {
      return;
    }
    var url = request_urls.deleteGroup + $scope.plans[$scope.plan_index].groups[index].group_id;

    $http.post( url ).success( function( data ) {
      if( data.code == 200 ) {
        $scope.plans[$scope.plan_index].groups.splice( index, 1 );
        $scope.pageEndEditing();
      } else {
        $scope.alert = {
          type    : 'danger',
          message : data.msg
        };
      }
    } );
  };


  //删除图片
  $scope.deleteImage = function( group_index, index ) {
    if( !window.confirm( '是否删除图片？' ) ) {
      return;
    }
    var url;
    if( $scope.info.display_type == 1 ) {
      url = request_urls.deleteImage + $scope.plans[$scope.plan_index].groups[group_index].items[index].item_id;
    } else {
      url = request_urls.deleteImage + $scope.items[index].item_id;
    }
    $http.post( url ).success( function( data ) {
      if( data.code == 200 ) {
        if( $scope.info.display_type == 1 ) {
          $scope.plans[$scope.plan_index].groups[group_index].items[index].image_url = '';
        } else {
          $scope.items[index].image_url = '';
        }

        alert( "删除图片成功！" );
      } else {
        $scope.alert = {
          type    : 'danger',
          message : data.msg
        };
      }
    } );
  };

  //更改图文顺序
  $scope.updateOrder = function() {
    var item_order = [];
    k = 1;
    if( $scope.info.display_type == 1 ) {
      var current_plan = $scope.plans[$scope.plan_index];
      var groups_len = current_plan.groups.length;
      for( var i = 0; i < groups_len; i++ ) {
        var items_len = current_plan.groups[i].items.length;
        for( var j = 0; j < items_len; j++ ) {
          item_order.push( {
                             item_id       : current_plan.groups[i].items[j].item_id,
                             group_id      : current_plan.groups[i].items[j].group_id,
                             display_order : k
                           } );
          k++;
        }
      }
    } else {
      var items_len = $scope.items.length;
      for( var j = 0; j < items_len; j++ ) {
        item_order.push( {
                           item_id       : $scope.items[j].item_id,
                           group_id      : $scope.items[j].group_id,
                           display_order : k
                         } );
        k++;
      }
    }

    if( item_order.length > 0 ) {
      $http.post( request_urls.updateItemsOrder, item_order ).success( function( data ) {
        if( data.code != 200 ) {
          $scope.alert = {
            type    : 'danger',
            message : data.msg
          };
        } else {
          console.log( data.msg );
        }
      } );
    }
  };

  $scope.slideDndCallback = function( info, dstIndex ) {
    if( angular.isNumber( dstIndex ) || dstIndex.indexOf( '-' ) == -1 ) { // without group
      $scope.items.splice( info.srcIndex, 1 );
      $scope.items.splice( dstIndex, 0, info.srcItem );
    } else {
      var dst_parts = dstIndex.split( '-' );
      var src_parts = info.srcIndex.split( '-' );

      var src_group_index = src_parts[0], src_item_index = src_parts[1];
      var dst_group_index = dst_parts[0], dst_item_index = dst_parts[1];

      $scope.plans[$scope.plan_index].groups[src_group_index].items.splice( src_item_index, 1 );
      var item = info.srcItem;
      item.group_id = $scope.plans[$scope.plan_index].groups[dst_group_index].group_id;

      $scope.plans[$scope.plan_index].groups[dst_group_index].items.splice( dst_item_index, 0, item );

    }
    $scope.updateOrder();

  };
  $scope.slideDndOptions = {
    selector : '.carousel-image',
    offset   : 0
  };

  $scope.pageHaveEditing = function() {
    $rootScope.$emit( 'dirtyForm', 'productTourPlan' );
  }

  $scope.pageEndEditing = function() {
    var is_editing = false;
    for( var i in $scope.plans ) {
      var plan = $scope.plans[i];
      for( var j in plan.groups ) {
        var group = plan.groups[j];
        for (var k in group.items) {
          var item = group.items[k];
          if (item.editing) {
            is_editing = true;
            break;
          }
        }

        if(is_editing) {
          break;
        } else if( group.editing ) {
          is_editing = true;
          break;
        }
      }
      if(is_editing) {
        break;
      }
    }

    if( !$scope.tourPlanEditing && !is_editing ) {
      $rootScope.$emit( 'clearDirty' );
    }
  }
};


angular.module( 'ProductEditApp' ).controller( 'productTourPlanCtrl', [
  '$scope', '$http', '$fileUploader', '$rootScope', productTourPlanCtrl
] );