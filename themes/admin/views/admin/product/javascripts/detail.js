function getRouteFirstPath(str) {
    return str.split('#/').pop().split('/').shift();
}
function getRouteFullPath(str) {
    return str.split('#/').pop();
}

controllers.ProductEditCtrl = function($scope, $rootScope, $http, $q, $location, commonFactory, ProductEditFactory) {
    $scope.local = {
        overlay            : {
            has_overlay : false
        },
        menu_items         : [],
        breadcrumb         : {},
        search_list        : {},
        current_menu       : false,
        current_status     : {},
        product_status     : {},
        current_menu_class : ''
    };
    $scope.data = {};

    //TODO
    //2. hi-elastic initial height issue

    $scope.init = function() {
        $scope.initMenu();
        $scope.initBreadcrumb();
        $scope.initProductStatus();
    };

    $scope.initMenu = function(cb) {
        var elem;
        var menu_items = ProductEditFactory.getMenuItems();
        var current_menu_value = getRouteFirstPath(window.location.hash);
        $scope.local.menu_items = [];

        for(var i = 0, len = menu_items.length; i < len; i++) {
            elem = menu_items[i];
            if('value' in elem) { //如果不是菜单标题
                if(elem.value == current_menu_value) {
                    $scope.local.current_menu = elem;
                }
                if(!ProductEditFactory.isInBlacklist(elem.value)) {
                    elem.is_visible = true;
                }
            }
            $scope.local.menu_items.push(elem);
        }

        $scope.local.current_menu_class = $scope.getViewClass();


        cb && cb();
    };
    $scope.changeMenu = function(index) {
        var menu_item = $scope.local.menu_items[index];
        if(menu_item.is_old) {
            window.open($request_urls.oldEdit + '#/' + menu_item.value, '_blank');
        } else {
            //只是触发路由更变，真正修改current_menu在routeChangeSuccess事件中
            $location.path('/' + $scope.local.menu_items[index].value);
        }
    };

    $scope.initBreadcrumb = function() {
        var body_crumb = '<a class="pad-right" target="_blank" href="' + $request_urls.viewProductUrl +
                         $rootScope.product.product_id + '">' +
                         '<span class="i i-eye"></span>本站预览' + '</a>' +
                         '<a class="pad-right pad-left" target="_blank" href="' + $request_urls.viewProductOnTestUrl +
                         $rootScope.product.product_id + '">' +
                         '<span class="i i-eye"></span>Test预览' + '</a>' +
                         '<a class="pad-right pad-left" target="_blank" href="' + $request_urls.viewCity + '">' +
                         '<span class="i i-eye"></span>城市预览' + '</a>' +
                         '<a class="pad-right pad-left" target="_blank" href="' + $request_urls.oldEdit + '">' +
                         '旧版详情页' + '</a>' +
                         '<div class="pad-left pad-right product-head-name">' + $rootScope.product.product_id + ' - ' +
                         $rootScope.product.name + '</div>' +
                         '<a class="pad-right pad-left" href="' + 'admin/product/detail?product_id=' + $rootScope.product.product_id + '#/ProductFeedback' + '">' +
                         'Q&A' + '</a>';

        $scope.local.breadcrumb = {
            back : {
                content      : '<span class="i i-arrow-left"></span>',
                clickCb      : function() {
                    window.location = $request_urls.back;
                },
                part_content : $rootScope.product.city_cn_name,
                partClickCb  : function() {
                    window.open($request_urls.viewCity, '_blank');
                }
            },
            body : {
                content       : body_crumb,
                right_content : '＋复制新增',
                rightClickCb  : function() {
                    $scope.copyProduct();
                }
            }
        };

        $rootScope.$emit('setBreadcrumb', $scope.local.breadcrumb);
    };

    $scope.copyProduct = function() {
        $http.post($request_urls.copyProduct, {}).success(function(data) {
            alert(data.msg);
            window.open($request_urls.edit + data.data, '_blank');
        });
    };

    $scope.getViewClass = function() {
        var str = $scope.local.current_menu.value;
        return str && str.replace(/[A-Z]/g, function(match) {
            return '-' + match.toLowerCase();
        }).substr(1);
    };

    $scope.initProductStatus = function() {
        $scope.local.product_status = ProductEditFactory.getProductStatus();
        var status_index = getIndexByProp($scope.local.product_status, 'value', $rootScope.product.status);
        $scope.local.current_status = $scope.local.product_status[status_index];
    };
    $scope.changeProductStatus = function(index) {
        var target_status = $scope.local.product_status[index];
        $http.post($request_urls.updateProductStatus, {
            status : target_status.value
        }).success(function(data) {
            if(data.code == 200) {
                $scope.local.current_status = target_status;
                window.location.reload();
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.isPathDirty = function(path_name) {
        if(!!$rootScope.is_dirty && $rootScope.is_dirty.path_name == path_name) {
            for(var form_index in $rootScope.is_dirty.dirty_forms) {
                if($rootScope.is_dirty.dirty_forms[form_index]) {
                    return true;
                }
            }
        }

        return false;
    };
    $scope.toggleOverlay = function(overlay_name) {
        $scope.local.overlay.has_overlay = !!overlay_name;
        $rootScope.$emit('overlay', overlay_name);
    };
    $scope.confirmSave = function(use_save) {
        $scope.toggleOverlay(false);
        if(use_save) { //保存
            $rootScope.$emit('clearDirty', $rootScope.is_dirty, function() {
                $rootScope.$emit('resetDirty');
                $location.path('/' + $scope.local.new_path);
            });
        } else {
            $scope.local.load_in_progress = false;
        }
    };

    $scope.setUseCache = function(new_route, new_path) {
        var full_path = getRouteFullPath(new_route);

        $rootScope.use_cache = (full_path == new_path);
    };

    $scope.finishLoad = function(priority) {
        if(priority == 20) { //Route Page Loaded
            $rootScope.$emit('loadStatus', false);
            $rootScope.$emit('subPageLoaded');
        } else if(priority == 10) { //Main Page Loaded
            $rootScope.$emit('mainPageLoaded');
        } else { //Custom Loaded Event
            $rootScope.$emit('hasLoaded', priority);
        }
    };


    $rootScope.$on('$locationChangeStart', function(e, new_route, old_route) {
        $rootScope.$emit('errorStatus', false);

        if(!$rootScope.product) return;
        //  new_path 为路径的第一部分，比如 ProductBasicInfo/name 会成为 ProductBasicInfo
        var new_path = getRouteFirstPath(new_route);
        var old_path = getRouteFirstPath(old_route);

        //Set Use Cache
        $scope.setUseCache(new_route, new_path);
        //Blacklist Protection
        if(ProductEditFactory.isInBlacklist(new_path)) {
            e.preventDefault();
            alert('你正在访问无法编辑的页面');
            window.location.href = window.location.href.split('#/').shift();
        }
        //Has Dirty Form
        if($scope.isPathDirty(old_path)) {
            e.preventDefault();
            $scope.local.new_path = getRouteFullPath(new_route);
            $scope.toggleOverlay('dirty_form');
        }
        //Loading
        $scope.local.load_in_progress = true;
    });
    $rootScope.$on('$locationChangeSuccess', function(e, new_route) {
        var done = function() {
            $rootScope.$emit('resetDirty');
            $scope.local.load_in_progress = false;

            var index = getIndexByProp($scope.local.menu_items, 'value', getRouteFirstPath(new_route));
            if(index > -1) {
                $scope.local.current_menu = $scope.local.menu_items[index];
                $scope.local.current_menu_class = $scope.getViewClass();
            }
        };

        if($scope.local.menu_items.length) {
            done();
        } else {
            $scope.initMenu(done);
        }
    });

    $rootScope.$on('resetDirty', function() {
        $rootScope.is_dirty = false;
    });
    $rootScope.$on('setDirty', function(event, is_pristine, options) {
        if(!$rootScope.is_dirty || ($rootScope.is_dirty.path_name != options.path_name)) {
            $rootScope.is_dirty = {
                path_name   : options.path_name,
                dirty_forms : {}
            };
        }
        $rootScope.is_dirty.dirty_forms[options.form_name] = !is_pristine;
    });
    window.onbeforeunload = function(e) {
        if($rootScope.is_dirty) {
            if(!e) e = window.event;
            //e.cancelBubble is supported by IE - this will kill the bubbling process.
            e.cancelBubble = true;
            e.returnValue = '有内容未保存，是否确认离开？';

            if(e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            }
        }
    };

    $rootScope.$on('mainPageLoaded', function() {
        $('#edit-section').css('min-height', $(window).height() - $('#top-header').height() -
                                             $('.breadcrumb').height());
    });


    //Hack - Wait for $rootScope.product before start initiation
    var timer = setInterval(function() {
        if($rootScope.finished) {
            delete $rootScope.finished;
            clearInterval(timer);
            //Murphy's Law
            setTimeout(function() {
                $scope.init();
            }, 100);
        }
    }, 100);
};


app.controller('ProductEditCtrl', [
    '$scope', '$rootScope', '$http', '$q', '$location', 'commonFactory', 'ProductEditFactory',
    controllers.ProductEditCtrl
]);
