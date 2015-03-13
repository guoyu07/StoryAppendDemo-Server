controllers.ProductFeedbackCtrl = function($scope, $rootScope, $route, $http) {

    $scope.data = {};
    $scope.local = {
        radio_switch  : {
            type_switch : {
                name  : 'ask_type',
                items : {
                    '0' : '咨询',
                    '1' : '定制'
                }
            },
            line_switch : {
                name  : 'is_online',
                items : {
                    '0' : '是',
                    '1' : '否'
                }
            }
        },
        contact_types : [
            {
                value : 'contact_phone',
                label : '电话'
            },
            {
                value : 'contact_qq',
                label : 'QQ'
            },
            {
                value : 'contact_weixin',
                label : '微信'
            },
            {
                value : 'contact_mail',
                label : '邮箱'
            }
        ],
        ask_model     : {
            ask_id         : '',
            answer         : '',
            contact_mail   : '',
            contact_name   : '',
            contact_phone  : '',
            contact_qq     : '',
            contact_weixin : '',
            date_added     : '',
            date_expected  : '',
            question       : '',
            is_online      : 0,
            is_edit        : true,
            contact_way    : 'contact_phone',
            ask_type       : 0,
            priority       : '',
            status         : ''
        }
    };

    $scope.init = function() {
        $scope.data.date_form = formatDate($scope.data.date);
        $scope.data.asks = angular.copy($route.current.locals.loadData.feedback.data);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);
        for(var index in $scope.data.asks) {
            $scope.data.asks[index].is_edit = false;
            $scope.data.asks[index].date_added = formatDate($scope.data.asks[index].date_added);
            $scope.data.asks[index].date_expected = formatDate($scope.data.asks[index].date_expected);
            $scope.data.asks[index].contact_way = 'contact_phone';
        }
    };

    $scope.switchContactType = function(type_index, ask_index) {
        $scope.data.asks[ask_index].contact_way = $scope.local.contact_types[type_index].value;
    };

    $scope.addFeedback = function() {
        var new_ask = angular.copy($scope.local.ask_model);
        new_ask.date_added = formatDate(new Date());
        new_ask.date_expected = formatDate(new Date());
        $scope.data.asks.splice(0, 0, new_ask);
    };

    $scope.deleteFeedback = function(ask_index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        if($scope.data.asks[ask_index].ask_id) {
            $http.delete($request_urls.saveProductFeedback + '&ask_id=' +
                         $scope.data.asks[ask_index].ask_id).success(function(data) {
                if(data.code == 200) {
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
        $scope.data.asks.splice(ask_index, 1);
    };

    $scope.toggleEdit = function(ask_index) {
        if(!$scope.data.asks[ask_index].is_edit) {
            $scope.data.asks[ask_index].is_edit = true;
        } else {
            var postData = $scope.data.asks[ask_index];
            $scope.data.asks[ask_index].date_expected = formatDate(postData.date_expected);
            $http.post($request_urls.saveProductFeedback, postData).success(function(data) {
                if(data.code == 200) {
                    $scope.data.asks[ask_index].is_edit = false;
                    $scope.data.asks[ask_index].ask_id = data.data.ask_id;
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
    };

    $scope.init();
};

app.controller('ProductFeedbackCtrl', [
    '$scope', '$rootScope', '$route', '$http', controllers.ProductFeedbackCtrl
]);