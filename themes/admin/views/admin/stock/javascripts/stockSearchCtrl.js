controllers.StockSearchCtrl = function($scope, $rootScope, $http) {
    $scope.data = {
        inspect      : {},
        add_stock    : {
            current_record : {}
        },
        duplicate    : {
            current_record : {}
        },
        insurance    : {
            available_amount : 0
        },
        ticket_types : {}
    };
    $scope.local = {
        overlay          : {
            has_overlay     : false,
            current_overlay : ''
        },
        uploader_options : {
            product   : {
                target      : $request_urls.uploadProductStock,
                input_id    : 'product_upload',
                accept_type : 'application/zip',
                beforeCb    : function(event, item) {
                    item.formData = [
                        {
                            product_id : $scope.data.add_stock.current_record.product_id,
                            ticket_id  : $scope.local.radio_switch.add_stock.value.ticket_type,
                            comment    : $scope.data.add_stock.current_record.comment
                        }
                    ];
                },
                filterCb    : function(item) {
                    var type = $scope.local.uploader_options.product.uploader.isHTML5 ?
                               item.type :
                               '/' + item.value.slice(item.value.lastIndexOf('.') + 1);
                    if(type === "" && $scope.local.uploader_options.product.uploader.isHTML5) {
                        type = item.name.slice(item.name.lastIndexOf('.') + 1);
                    }
                    type = type.toLowerCase().slice(type.lastIndexOf('/') + 1);
                    var ext_result = type.indexOf('zip') !== -1;
                    var size_result = item.size / 1024 / 1024 < 20;

                    if(!ext_result) {
                        $rootScope.$emit('notify', {msg : '请上传zip文件'});
                    }
                    if(!size_result) {
                        $rootScope.$emit('notify', {msg : '上传文件不能超过10MB'});
                    }

                    return ext_result && size_result;
                },
                successCb   : function(event, xhr, item, response, uploader) {
                    $scope.local.uploader_options.product.in_progress = false;
                    uploader.queue = [];
                    $scope.toggleOverlay('');

                    if(response.code == 200) {
                        if(response.data == 1) {
                            $rootScope.$emit('notify', {msg : '处理完毕，有重复文件，请在库存管理中查看！'});
                            $scope.local.product_grid.fetchData();
                        } else if(response.data == 2) {
                            $rootScope.$emit('notify', {msg : '处理完毕，无重复文件。请在库存管理中抽检！'});
                            $scope.local.product_grid.fetchData();
                        } else if(response.data == -1) {
                            $rootScope.$emit('notify', {msg : 'zip文件解压失败。请检查后再重新上传。'});
                        } else {
                            $rootScope.$emit('notify', {msg : response.msg});
                        }
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            },
            insurance : {
                target      : $request_urls.uploadInsuranceStock,
                input_id    : 'insurance_upload',
                accept_type : '*',
                filterCb    : function(item) {
                    var size_result = item.size / 1024 / 1024 < 10;

                    if(!size_result) {
                        $rootScope.$emit('notify', {msg : '上传文件不能超过10MB'});
                    }

                    return size_result;
                },
                successCb   : function(event, xhr, item, response, uploader) {
                    $scope.local.uploader_options.insurance.in_progress = false;
                    uploader.queue = [];

                    if(response.code == 200) {
                        $scope.data.insurance.available_amount += parseInt(response.data);
                    }

                    $rootScope.$emit('notify', {
                        msg : response.code == 200 ? '上传成功' : '上传失败'
                    });
                }
            }
        },
        radio_switch     : {
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
        product_grid     : {
            data    : [],
            table   : {
                table_id : 'product_stock_grid'
            },
            label   : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == 'actions') {
                        return '<button class="btn btn-inverse block-action" ng-click="options.custom.addStock( record )">添加</button>' +
                               '<button class="btn btn-inverse block-action" ng-show="record.status != -1" ng-click="options.custom.showHistory( record )">查看历史</button>' +
                               '<button class="btn btn-inverse block-action" ng-show="record.status == 1" ng-click="options.custom.showDuplicate( record )">查看重复</button>' +
                               '<button class="btn btn-inverse block-action" ng-show="record.status == 2" ng-click="options.custom.showInspect( record )">抽检</button>';
                    } else if(col.name == 'last_status') {
                        var status = $scope.local.product_grid.custom.product_status[record.status];
                        var status_class = record.status == -1 ? "" : status.class_name;
                        var status_label = record.status == -1 ? "" : status.label;
                        return '<span class="grid-status ' + status_class + '">' + status_label + '</span>';
                    } else {
                        return !!record[col.name] ? record[col.name].toString() : '';
                    }
                }
            },
            custom  : {
                product_status : {
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
                addStock       : function(record) {
                    $scope.data.add_stock.current_record = record;
                    $scope.data.add_stock.current_record.comment = '';
                    $scope.local.radio_switch.add_stock.value.ticket_type = '';
                    $scope.local.radio_switch.add_stock.options.items = {};
                    var ticket_ids = record.ticket_ids.split(',');

                    for(var i = 0, len1 = ticket_ids.length; i < len1; i++) {
                        for(var j = 0, len2 = $scope.data.ticket_types.length; j < len2; j++) {
                            if($scope.data.ticket_types[j].ticket_id == ticket_ids[i]) {
                                $scope.local.radio_switch.add_stock.options.items[ticket_ids[i]] = $scope.data.ticket_types[j].cn_name;
                                break;
                            }
                        }
                    }

                    $scope.toggleOverlay('addstock');
                },
                showInspect    : function(record) {
                    $http.get($request_urls.fetchInspectStock + record.batch_id).success(function(data) {
                        if(data.code == 200) {
                            $scope.toggleOverlay('inspect');
                            $scope.data.inspect = data.data;
                        } else {
                            $scope.toggleOverlay('');
                        }
                    });
                },
                showHistory    : function(record) {
                    window.location = $request_urls.viewStockHistoryUrl + record.product_id;
                },
                showDuplicate  : function(record) {
                    $http.get($request_urls.fetchDuplicatedStock + record.batch_id).success(function(data) {
                        if(data.code == 200) {
                            $scope.toggleOverlay('duplicate');
                            $scope.data.duplicate.current_record = data.data;
                        } else {
                            $scope.toggleOverlay('');
                        }
                    });
                }
            },
            request : {
                api_url : $request_urls.fetchProductStock
            },
            columns : [
                {
                    name  : 'city_name',
                    width : '12%',
                    label : '城市'
                },
                {
                    name  : 'supplier_name',
                    width : '10%',
                    label : '供应商'
                },
                {
                    name  : 'product_id',
                    width : '5%',
                    label : '商品ID'
                },
                {
                    name  : 'product_name',
                    width : '26%',
                    label : '商品名称'
                },
                {
                    name  : 'left_ticket',
                    width : '12%',
                    label : '剩余票数'
                },
                {
                    name  : 'last_status',
                    width : '12%',
                    label : '最后上传状态'
                },
                {
                    name  : 'actions',
                    width : '23%',
                    label : '操作'
                }
            ]
        }
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {
                content : false
            },
            body : {
                content : '库存管理'
            }
        });

        $http.get($request_urls.fetchAvailableInsurance).success(function(data) {
            if(data.code == 200) {
                $scope.data.insurance.available_amount = data.data;
                $rootScope.$emit('loadStatus', false);
            }
        });
        $http.get($request_urls.fetchTicketTypes).success(function(data) {
            if(data.code == 200) {
                $scope.data.ticket_types = data.data;
            }
        });
    };

    $scope.toggleOverlay = function(overlay_name) {
        $scope.local.overlay.has_overlay = !!overlay_name;
        $scope.local.overlay.current_overlay = overlay_name;
        $rootScope.$emit('overlay', !!overlay_name);
    };

    $scope.triggerUpload = function(type) {
        if(type == 'product') {
            if(!$scope.local.radio_switch.add_stock.value.ticket_type) {
                alert('请选择票种再上传！');
                return;
            }
        }

        var selector = $scope.local.uploader_options[type].input_id;
        $('#' + selector).trigger('click');
    };

    $scope.setInspect = function(result) {
        var url = result ? $request_urls.confirmStock : $request_urls.deleteStock;

        $http.post(url + $scope.data.inspect.batch_info.batch_id).success(function(data) {
            $scope.toggleOverlay('');
            if(data.code == 200) {
                $scope.local.product_grid.fetchData();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.init();
};

app.controller('StockSearchCtrl', ['$scope', '$rootScope', '$http', controllers.StockSearchCtrl]);