controllers.StockHistoryCtrl = function($scope, $rootScope, $http) {
    $scope.data = {
        inspect   : {},
        duplicate : {}
    };
    $scope.local = {
        product_url  : $request_urls.editProductUrl,
        overlay      : {},
        stock_status : {
            '0' : {
                label      : '正在处理...',
                class_name : 'processing'
            },
            '1' : {
                label      : '有重复文件',
                class_name : 'error'
            },
            '2' : {
                label      : '等待人工抽检',
                class_name : 'processing'
            },
            '3' : {
                label      : '启用成功',
                class_name : 'default'
            }
        },
        radio_switch : {
            add_stock : {
                value   : {
                    'ticket_type' : ''
                },
                options : {
                    name  : 'ticket_type',
                    items : {}
                }
            }
        },
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '商品库存历史'
            }
        });

        $http.get($request_urls.fetchStockHistory).success(function(data) {
            if(data.code == 200) {
                $scope.data.history = data.data;
                $rootScope.$emit('loadStatus', false);
            }
        });
    };

    $scope.toggleOverlay = function(overlay_name) {
        $scope.local.overlay.has_overlay = !!overlay_name;
        $scope.local.overlay.current_overlay = overlay_name;
        $rootScope.$emit('overlay', !!overlay_name);
    };

    $scope.showInspect = function(record) {
        $http.get($request_urls.fetchInspectStock + record.batch_id).success(function(data) {
            if(data.code == 200) {
                $scope.toggleOverlay('inspect');
                $scope.data.inspect = data.data;
            } else {
                $scope.toggleOverlay('');
            }
        });
    };
    $scope.showDuplicate = function(record) {
        $http.get($request_urls.fetchDuplicatedStock + record.batch_id).success(function(data) {
            if(data.code == 200) {
                $scope.toggleOverlay('duplicate');
                $scope.data.duplicate.current_record = data.data;
            } else {
                $scope.toggleOverlay('');
            }
        });
    };

    $scope.setInspect = function(result) {
        var url = result ? $request_urls.confirmStock : $request_urls.deleteStock;

        $http.post(url + $scope.data.inspect.batch_info.batch_id).success(function(data) {
            $scope.toggleOverlay('');
            if(data.code == 200) {
                window.location.reload();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.init();
};

app.controller('StockHistoryCtrl', ['$scope', '$rootScope', '$http', controllers.StockHistoryCtrl]);