controllers.SiteGridCtrl = function($scope, $rootScope) {
    $scope.data = {
        grid : []
    };
    $scope.local = {
        grid_options : {
            data    : [],
            table   : {
                table_id   : 'sample_grid',
                multi_sort : true
            },
            label   : {
                getHead : function(col, i) {
                    if(col.name == 'preview') {
                        return '';
                    } else if(col.name == 'status') {
                        return '';
                    } else {
                        return col.label;
                    }
                },
                getBody : function(col, i, record, j) {
                    if(col.name == 'price') {
                        return record['price'] + ' / ' + record['orig_price'];
                    } else if(col.name == 'preview') {
                        return '<a href="http://hitour.cc/sightseeing/' + record.product_id + '" class="i i-eye"></a>';
                    } else if(col.name == 'status') {
                        return '<div class="dropdown product-status"><button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">状态<span class="caret"></span></button><span class="dropdown-arrow"></span><ul class="dropdown-menu"><li ng-repeat="one_status in dataset.status"><a class="status all" ng-click="dataset.updateStatus(one_status.status_id)" ng-bind="one_status.status_name"></a></li></ul></div>';
                    } else {
                        return record[col.name].toString();
                    }
                }
            },
            request : {
                api_url       : $request_urls.fetchData,
                record_filter : ['product_id', 'product_name', 'price', 'orig_price']
            },
            columns : [
                {
                    name     : 'product_id',
                    width    : '10%',
                    label    : '商品ID',
                    use_sort : false
                },
                {
                    name     : 'product_name',
                    width    : '30%',
                    label    : '商品名称',
                    use_sort : true
                },
                {
                    name     : 'price',
                    width    : '30%',
                    label    : '商品价格',
                    use_sort : true
                },
                {
                    name     : 'preview',
                    width    : '15%',
                    label    : '',
                    use_sort : false
                },
                {
                    name     : 'status',
                    width    : '15%',
                    use_sort : false
                }
            ]
        },
        grid_dataset : {
            status       : [
                {
                    status_id   : 1,
                    status_name : '未上架'
                },
                {
                    status_id   : 2,
                    status_name : '编辑中'
                }
            ],
            updateStatus : function(status_id) {
                alert('you clicked ' + status_id);
                $scope.local.grid_options.query.query_filter = {
                    'status_id' : status_id
                };
                $scope.local.grid_options.fetchData();
            }
        }
    };

    $scope.init = function() {
        $rootScope.$emit('loadStatus', false);
    };

    $scope.init();
};

app.controller('SiteGridCtrl', ['$scope', '$rootScope', controllers.SiteGridCtrl]);