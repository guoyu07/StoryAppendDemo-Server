controllers.ErrorPageGridCtrl = function($scope, $http, $rootScope) {
    $scope.data = {};
    $scope.local = {
        grid_options : {
            data    : [],
            table   : {
                table_id : 'error_page_grid'
            },
            label   : {
                getHead : function(col) {
                    return col.label;
                },
                getBody : function(col, i, record) {
                    if(col.name == 'product_name' || col.name == 'error_page_id') {
                        return '<a href="' + $request_urls.edit + record.error_page_id + '">' + record[col.name] +
                               '</a>';
                    } else if(col.name == 'status') {
                        return record[col.name] == "0" ? "禁用" : "启用";
                    } else if(col.name == 'preview') {
                        return '<a href="' + $request_urls.linkBaseUrl + '/' + 'site/error?error_page_id=' +
                               record.error_page_id + '">' + '预览' + '</a>';
                    } else {
                        return !!record[col.name] ? record[col.name].toString() : '';
                    }
                }
            },
            request : {
                api_url : $request_urls.getErrorPageList
            },
            columns : [
                {
                    name     : 'product_id',
                    width    : '10%',
                    label    : '商品ID',
                    use_sort : false
                },
                {
                    name     : 'error_page_id',
                    width    : '15%',
                    label    : '错误页面ID',
                    use_sort : false
                },
                {
                    name     : 'product_name',
                    width    : '50%',
                    label    : '页面商品名称',
                    use_sort : false
                },
                {
                    name     : 'status',
                    width    : '15%',
                    label    : '状态',
                    use_sort : false
                },
                {
                    name     : 'preview',
                    width    : '10%',
                    label    : '预览',
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
                content : '错误页面列表'
            }
        });
    };

    $scope.addErrorPage = function() {
        $scope.local.grid_options.in_progress = true;
        $http.post($request_urls.addErrorPage, {}).success(function(data) {
            if(data.code == 200) {
                $scope.local.grid_options.in_progress = false;
                window.location = $request_urls.edit + data.data.error_page_id;
            }
        });
    };

    $scope.init();
};

app.controller('ErrorPageGridCtrl', ['$scope', '$http', '$rootScope', controllers.ErrorPageGridCtrl]);