controllers.ProductCommentCtrl = function($scope, $rootScope, $route, $http) {

    $scope.data = {};
    $scope.local = {
        comment_scores : [5.0, 4.5, 4.0, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5, 0]
    };

    function replaceSpace(str) {
        return str.replace(/\r\n|\r|\n/g, '<br />');
    }

    $scope.init = function() {
        $scope.data = angular.copy($route.current.locals.loadData.comments);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);

        for(var i in $scope.data.comments) {
            $scope.data.comments[i].is_edit = false;
            $scope.data.comments[i].hitour_service_level = parseInt($scope.data.comments[i].hitour_service_level, 10);
            //$scope.data.comments[i].display_content = replaceSpace($scope.data.comments[i].content);
        }
    };

    //评论保存与编辑
    $scope.toggleEdit = function(index) {
        if($scope.data.comments[index].is_edit) {
            var post_data = $scope.data.comments[index];
            $http.post($request_urls.productComment, post_data).success(function(data) {
                if(data.code == 200) {
                    $scope.data.comments[index].is_edit = false;
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
        $scope.data.comments[index].is_edit = !$scope.data.comments[index].is_edit;
    };

    //更改作者
    $scope.toggleCustomer = function(index) {
        $http.get($request_urls.getRandomCustomer).success(function(data) {
            if(data.code == 200) {
                var customer = data.data;
                $scope.data.comments[index].customer = customer;
                $scope.data.comments[index].customer_id = customer.customer_id;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //删除评论
    $scope.delComments = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;

        var comment_id = $scope.data.comments[index].comment_id;
        $http.delete($request_urls.productComment + comment_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.comments.splice(index, 1);
            }
            $rootScope.$emit('notify', {msg : data.msg});
        });
    };

    //添加评论（页面新增）
    $scope.addComments = function() {
        $http.get($request_urls.getRandomCustomer).success(function(data) {
            if(data.code == 200) {
                var customer = data.data,
                    new_comments = {
                        approved             : '1',
                        comment_id           : '',
                        content              : '',
                        customer             : customer,
                        customer_id          : customer.customer_id,
                        insert_time          : '',
                        product_id           : '',
                        hitour_service_level : 5.0, //default 5.0
                        is_edit              : true
                    };
                $scope.data.comments.unshift(new_comments);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.init();
};

app.controller('ProductCommentCtrl', [
    '$scope', '$rootScope', '$route', '$http', controllers.ProductCommentCtrl
]);