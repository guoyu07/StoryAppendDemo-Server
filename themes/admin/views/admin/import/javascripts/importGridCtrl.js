controllers.ImportGridCtrl = function($scope, $rootScope, $http) {
    $scope.data = {};
    $scope.local = {
        import_data   : {
            city_code : '',
            item_id   : ''
        },
        import_status : {
            '0' : {
                label      : '待处理',
                class_name : 'error'
            },
            '1' : {
                label      : '处理中',
                class_name : 'processing'
            },
            '2' : {
                label      : '已完成',
                class_name : 'default'
            }
        },
        grid_options  : {
            data    : [],
            table   : {
                table_id : 'import_grid'
            },
            label   : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    /* 返回单元内容 */
                    if(col.name == 'city_code') {
                        return record['city_code'] + ' / ' + record['city_name'];
                    } else if(col.name == 'product_name') {
                        return '<a href="' + $request_urls.editProductUrl + record['product_id'] + '">' +
                               record['product_name'] +
                               '</a>';
                    } else if(col.name == 'status') {
                        var status = $scope.local.import_status[record['status']];
                        return '<span class="grid-status ' + status.class_name + '">' + status.label + '</span>';
                    } else if(col.name == 'actions') {
                        var html = '';

                        if(record.status == 2) {
                            /* 在单元里执行一个函数，附带着ng-repeat此循环的内容 */
                            html += '<button class="btn btn-inverse block-action add grid-right" ng-click="options.custom.update(record)">更新</button>';
                        }
                        if(['0', '2'].indexOf(record.status.toString()) > -1) {
                            html += '<button class="btn btn-inverse block-action add" ng-click="options.custom.cancel($index)">取消任务</button>';
                        }

                        return html;
                    } else {
                        return !!record[col.name] ? record[col.name].toString() : '';
                    }
                }
            },
            custom  : {
                update : function(record) {
                    $http.post($request_urls.updateImport, {
                        auto_id : record.auto_id
                    }).success(function(data) {
                        if(data.code == 200) {
                            $scope.local.grid_options.fetchData();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                },
                cancel : function(index) {
                    if(!window.confirm("取消后不会自动更新商品信息。\n点击'确认'取消。")) return;
                    $http.post($request_urls.cancelImport, {
                        //For some reason, it always choose the one before
                        auto_id : $scope.local.grid_options.data[index + 1].auto_id
                    }).success(function(data) {
                        if(data.code == 200) {
                            $scope.local.grid_options.fetchData();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            },
            request : {
                api_url : $request_urls.fetchImportedProducts
            },
            columns : [
                {
                    name     : 'product_id',
                    width    : '6%',
                    label    : '商品ID',
                    use_sort : false
                },
                {
                    name     : 'city_code',
                    width    : '12%',
                    label    : '城市',
                    use_sort : false
                },
                {
                    name     : 'item_code',
                    width    : '15%',
                    label    : 'GTA ID',
                    use_sort : false
                },
                {
                    name     : 'product_name',
                    width    : '26%',
                    label    : '商品名称',
                    use_sort : false
                },
                {
                    name     : 'update_time',
                    width    : '15%',
                    label    : '更新时间',
                    use_sort : false
                },
                {
                    name     : 'status',
                    width    : '8%',
                    label    : '导入状态',
                    use_sort : false
                },
                {
                    name     : 'actions',
                    width    : '18%',
                    label    : '动作',
                    use_sort : false
                }
            ]
        }
    };

    $scope.init = function() {
        $rootScope.$emit('loadStatus', false);
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : 'GTA商品导入'
            }
        });
    };
    $scope.addImport = function() {
        $http.post($request_urls.addImportProduct, $scope.local.import_data).success(function(data) {
            if(data.code == 200) {
                $scope.local.grid_options.fetchData();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.goToExisting = function() {
        if(!!$scope.local.import_data.item_id) {
            var all_records = angular.copy($scope.local.grid_options.data);
            var i = 0, item_index = -1, len = all_records.length;
            for(; i < len; i++) {
                if(all_records[i].item_code.toLowerCase() == $scope.local.import_data.item_id.trim().toLowerCase()) {
                    item_index = i;
                    break;
                }
            }

            if(item_index > -1) {
                $(window).scrollTop($('tr:nth-of-type(' + (item_index + 1) + ')').offset().top);
            }
        }
    };

    $scope.init();
};

app.controller('ImportGridCtrl', ['$scope', '$rootScope', '$http', controllers.ImportGridCtrl]);