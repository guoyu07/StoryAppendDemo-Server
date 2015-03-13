controllers.PromotionGridCtrl = function($scope, $rootScope, $http) {
    $scope.data = {};
    $scope.local = {
        promotion_name : '',
        has_overlay    : false,
        grid_options   : {
            data    : [],
            table   : {
                table_id : 'promotion_grid'
            },
            label   : {
                getHead : function(col) {
                    return col.label;
                },
                getBody : function(col, i, record) {
                    if(col.name == 'name') {
                        return '<a href="' + $request_urls.editPromotion + record.promotion_id + '">' +
                               record[col.name] + '</a>';
                    } else if(col.name == 'action') {
                        var status_label;
                        for(var key in $scope.local.grid_options.custom.status) {
                            if($scope.local.grid_options.custom.status[key].value == record.status) {
                                status_label = $scope.local.grid_options.custom.status[key].label;
                            }
                        }
                        return '<button class="btn btn-inverse block-action add grid-right" ng-click="options.custom.edit(record)">编辑</button>' +
                               '<button class="btn btn-inverse block-action add grid-right" ng-click="options.custom.preview(record)">预览</button>' +
                               '<div class="dropdown promotion-status" style="display: inline-block;"><button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">' +
                               status_label +
                               '<span class="caret"></span></button><span class="dropdown-arrow"></span><ul class="dropdown-menu"><li ng-repeat="status in options.custom.status"><a class="status" ng-click="options.custom.changeStatus( record, status.value )" ng-bind="status.label"></a></li></ul></div>';
                    } else {
                        return !!record[col.name] ? record[col.name].toString() : '';
                    }
                }
            },
            custom  : {
                status       : [
                    {
                        value : '0',
                        label : '编辑中'
                    },
                    {
                        value : '1',
                        label : '已生效'
                    }
                ],
                edit         : function(record) {
                    window.location = $request_urls.editPromotion + record.promotion_id;
                },
                preview      : function(record) {
                    window.open(($request_urls.baseUrl +
                                 $request_urls.previewPromotion).replace('000', record.promotion_id), '_blank');
                },
                changeStatus : function(record, new_status) {
                    $http.post($request_urls.changePromotionStatus, {
                        promotion_id : record.promotion_id,
                        status       : new_status
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
                api_url : $request_urls.getPromotionList
            },
            columns : [
                {
                    name     : 'name',
                    width    : '40%',
                    label    : '活动名称',
                    use_sort : false
                },
                {
                    name     : 'action',
                    width    : '40%',
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
                content : '活动列表'
            }
        });
    };

    $scope.toggleOverlay = function(has_overlay) {
        $scope.local.has_overlay = !!has_overlay;
        $rootScope.$emit('overlay', !!has_overlay);
    };

    $scope.confirmAdd = function() {
        $http.post($request_urls.promotion, {
            name : $scope.local.promotion_name
        }).success(function(data) {
            if(data.code == 200) {
                window.location = $request_urls.editPromotion + data.data;
            }
        });
    };

    $scope.init();
};

app.controller('PromotionGridCtrl', ['$scope', '$rootScope', '$http', controllers.PromotionGridCtrl]);