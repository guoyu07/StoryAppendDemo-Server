controllers.ArticleGridCtrl = function($scope, $http, $rootScope, commonFactory) {
    var paging = {
        start : 0,
        limit : 15
    };

    $scope.data = {};
    $scope.local = {
        search_city      : '',
        search_text      : '',
        grid_options     : {
            data      : [],
            table     : {
                table_id : 'article_grid'
            },
            label     : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    var status_label;
                    for(var key in $scope.local.article_status) {
                        if($scope.local.article_status[key].value == record.status) {
                            status_label = $scope.local.article_status[key].label;
                        }
                    }
                    if(col.name == 'article_id') {
                        return '<a href="' + $request_urls.editArticle + record.article_id + '" target="_Blank">' +
                               record.article_id + '</a>';
                    } else if(col.name == 'category') {
                        return $scope.local.article_category[record.category];
                    } else if(col.name == 'operation') {
                        return '<button class="btn btn-inverse block-action add grid-right" ng-click=" options.operation.deleteArticle($parent.$index) ">删除</button>' +
                               '<div class="dropdown promotion-status" style="display: inline-block;"><button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">' +
                               status_label +
                               '<span class="caret"></span></button><span class="dropdown-arrow"></span><ul class="dropdown-menu"><li ng-repeat="status in options.operation.article_status"><a class="status" ng-click="options.operation.changeStatus( record, status.value )" ng-bind="status.label"></a></li></ul></div>';
                    } else if(col.name == 'title') {
                        return '<a href="' + $request_urls.editArticle + record.article_id + '" target="_Blank">' +
                               record.title +
                               '</a>';
                    } else if(col.name == 'city_code') {
                        return record.city_name;
                    } else {
                        return !!record[col.name] ? record[col.name].toString() : '';
                    }
                }
            },
            query     : {
                sort          : {
                    'date_added' : 0
                },
                paging        : angular.copy(paging),
                query_filter  : {},
                record_filter : []
            },
            operation : {
                deleteArticle  : function(article_index) {
                    if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
                    var article_id = $scope.local.grid_options.data[article_index].article_id;
                    $http.delete($request_urls.article + article_id).success(function(data) {
                        if(data.code == 200) {
                            $scope.local.grid_options.data.splice(article_index, 1);
                        }
                        $rootScope.$emit('notify', {
                            msg : data.msg
                        });
                    });
                },
                changeStatus   : function(record, new_status) {
                    $http.post($request_urls.updateArticleStatus + record.article_id, {
                        status : new_status
                    }).success(function(data) {
                        if(data.code == 200) {
                            $scope.local.grid_options.fetchData();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                },
                article_status : [
                    {
                        value : '0',
                        label : '编辑中'
                    },
                    {
                        value : '1',
                        label : '已生效'
                    }
                ]
            },
            request   : {
                api_url : $request_urls.fetchArticles
            },
            columns   : [
                {
                    name     : 'article_id',
                    width    : '8%',
                    label    : '文章ID',
                    use_sort : false
                },
                {
                    name     : 'city_code',
                    width    : '8%',
                    label    : '城市名称',
                    use_sort : true
                },
                {
                    name     : 'title',
                    width    : '32%',
                    label    : '文章名称',
                    use_sort : true
                },
                {
                    name     : 'category',
                    width    : '10%',
                    label    : '类别',
                    use_sort : false
                },
                {
                    name     : 'date_added',
                    width    : '15%',
                    label    : '创建日期',
                    use_sort : true
                },
                {
                    name     : 'operation',
                    width    : '15%',
                    label    : '操作',
                    use_sort : false
                }
            ]
        },
        article_category : {
            '1' : '热卖',
            '2' : '酒店',
            '3' : '行程',
            '4' : '体验'
        },
        article_status   : [
            {
                value : '0',
                label : '编辑中'
            },
            {
                value : '1',
                label : '已生效'
            }
        ]
    };

    $scope.init = function() {
        commonFactory.getAjaxSearchCityList().then(function(data) {
            $scope.local.cities = data;
            $rootScope.$emit('loadStatus', false);
        });
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '查找文章'
            }
        });
    };
    $scope.goToArticle = function() {
        $http.post($request_urls.article).success(function(data) {
            if(data.code == 200) {
                var article_id = data.data.article_id;
                window.location = $request_urls.editArticle + (article_id || '');
            }
        });
    };
    $scope.searchArticle = function() {
        if(!!$scope.local.search_text || !!$scope.local.search_city) {
            $scope.local.grid_options.query.paging = angular.copy(paging);
            $scope.local.grid_options.query.query_filter = {
                search_text : angular.copy($scope.local.search_text),
                city_code   : $scope.local.search_city
            };
            $scope.local.grid_options.fetchData();
        }
    };

    $scope.init();
};

app.controller('ArticleGridCtrl', ['$scope', '$http', '$rootScope', 'commonFactory', controllers.ArticleGridCtrl]);