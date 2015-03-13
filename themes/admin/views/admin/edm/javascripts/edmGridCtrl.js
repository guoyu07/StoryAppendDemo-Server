controllers.EDMGridCtrl = function($scope, $http, $rootScope) {
    $scope.data = {};
    $scope.local = {
        edm_name     : '',
        has_overlay  : false,
        grid_options : {
            data    : [],
            table   : {
                table_id : 'edm_grid'
            },
            label   : {
                getHead : function(col) {
                    return col.label;
                },
                getBody : function(col, i, record) {
                    if(col.name == 'name') {
                        return '<a href="' + $request_urls.editEdm + record.edm_id + '">' + record[col.name] + '</a>';
                    } else if(col.name == 'action') {
                        return '<button class="btn btn-inverse block-action add grid-right" ng-click="options.custom.edit(record)">编辑</button>' +
                               '<button class="btn btn-inverse block-action add grid-right" ng-click="options.custom.preview(record)">预览</button>' +
                               '<button class="btn btn-inverse block-action add grid-right" ng-click="options.custom.download(record)">下载</button>';
                    } else {
                        return !!record[col.name] ? record[col.name].toString() : '';
                    }
                }
            },
            custom  : {
                edit     : function(record) {
                    window.location = $request_urls.editEdm + record.edm_id;
                },
                preview  : function(record) {
                    window.open($request_urls.previewEdm + record.edm_id, '_blank');
                },
                download : function(record) {
                    window.open($request_urls.downloadEDMTemplate + record.edm_id, "_blank");
                }
            },
            request : {
                api_url : $request_urls.getEdmList
            },
            columns : [
                {
                    name     : 'name',
                    width    : '40%',
                    label    : 'EDM模版名称',
                    use_sort : false
                },
                {
                    name     : 'date_update',
                    width    : '20%',
                    label    : '更新日期',
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
                content : 'EDM模版列表'
            }
        });
    };

    $scope.toggleOverlay = function(has_overlay) {
        $scope.local.has_overlay = !!has_overlay;
        $rootScope.$emit('overlay', !!has_overlay);
    };

    $scope.confirmAdd = function() {
        $http.post($request_urls.addEDM, {
            name : $scope.local.edm_name
        }).success(function(data) {
            if(data.code == 200) {
                window.location = $request_urls.editEdm + data.data;
            }
        });
    };

    $scope.init();
};

app.controller('EDMGridCtrl', ['$scope', '$http', '$rootScope', controllers.EDMGridCtrl]);