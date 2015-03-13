directives.hiGridDir = function($http) {
    var linkFunc = function(scope) {
        scope.local = {};

        scope.init = function() {
            scope.options.in_progress = false;
            scope.local.table_id = ( scope.options.table && scope.options.table.table_id ) || randomStr();
            scope.local.multi_sort = scope.options.table && scope.options.table.multi_sort;
            scope.local.paging = {
                all_pages          : [],
                total_pages        : 0,
                current_page       : 0,
                hide_extremity     : scope.options.pagination && scope.options.pagination.hide_extremity,
                start_page_setting : 1,
                total_page_setting : ( scope.options.pagination && scope.options.pagination.total_page_limits ) ?
                                     scope.options.pagination.total_page_limits : 10
            };

            /* 后端请求参数初始值 */
            scope.options.query = scope.options.query || {
                sort          : {},
                paging        : {
                    start : 0,
                    limit : 15
                },
                query_filter  : {},
                record_filter : scope.options.request.record_filter || []
            };

            scope.options.fetchData();
        };

        scope.options.fetchData = function() {
            scope.options.in_progress = true;
            $http.post(scope.options.request.api_url, scope.options.query).then(function(data) {
                var start_page, stop_page, mid_page, max_page_count;
                scope.options.data = angular.copy(data.data.data.data);
                scope.local.paging.all_pages = [];
                scope.local.paging.total_pages = Math.ceil(data.data.data.total_count /
                                                           scope.options.query.paging.limit);
                scope.local.paging.current_page = parseInt(Math.round(scope.options.query.paging.start /
                                                                      scope.options.query.paging.limit), 10) + 1;

                mid_page = scope.local.paging.total_page_setting / 2;
                max_page_count = scope.local.paging.total_page_setting - 1;

                start_page = scope.local.paging.current_page;
                start_page = start_page > mid_page ? start_page - mid_page : scope.local.paging.start_page_setting;

                stop_page = start_page + max_page_count;
                if(stop_page > scope.local.paging.total_pages) { //碰到尾巴，设置为最大
                    stop_page = scope.local.paging.total_pages;
                }

                for(; start_page <= stop_page; start_page++) {
                    scope.local.paging.all_pages.push(start_page);
                }
                scope.options.in_progress = false;
            });
        };

        scope.clickHead = function(index) {
            if(scope.options.columns[index].use_sort) {
                var orig_value = !!scope.options.query.sort[scope.options.columns[index].name];
                if(!scope.local.multi_sort) scope.options.query.sort = {};

                scope.options.query.sort[scope.options.columns[index].name] = orig_value ? 0 : 1;

                scope.options.fetchData();
            }
        };
        scope.selectPage = function(page_no) {
            scope.options.query.paging.start = ( page_no - 1 ) * scope.options.query.paging.limit;
            scope.options.fetchData();
        };

        scope.init();
    };

    return {
        link        : linkFunc,
        scope       : {
            options : '='
        },
        replace     : true,
        restrict    : 'AE',
        templateUrl : pathinfo.module_dir + 'hi_grid/hi_grid.html'
    };
};

app.directive('hiGrid', ['$http', '$sce', '$compile', directives.hiGridDir]);