controllers.pricePlanListCtrl = function($scope, $rootScope, $http, $location, pricePlanFactory) {
    $scope.data = {};
    $scope.local = {};


    $scope.init = function() {
        if(!$scope.$parent.result || Object.keys($scope.$parent.result).length == 0) return;

        var key, init_data;
        $scope.local.is_special_plan = angular.copy($scope.$parent.result.is_special_plan);
        $scope.local.title = $scope.local.is_special_plan ? '特价' : '价格';
        $scope.has_default_tickets = $scope.$parent.local.has_default_tickets;

        $scope.local.plan_info = angular.copy($scope.$parent.result.plan_info);
        init_data = pricePlanFactory.initPlanInfo($scope.local.plan_info);

        $scope.data.plans = $scope.$parent.result.price_plans.map(pricePlanFactory.formatPlan);

        for(key in init_data) {
            $scope.local[key] = init_data[key];
        }
    };

    $scope.getPlanRangeLabel = function(plan) {
        return pricePlanFactory.getPlanRangeLabel(plan);
    };
    $scope.getFrequencyLabel = function(frequency) {
        return pricePlanFactory.getFrequencyLabel(frequency);
    };
    $scope.getSpecialCodeLabel = function(special_code) {
        return pricePlanFactory.getSpecialCodeLabel(special_code);
    };
    $scope.getRowSpanByCode = function(special_id) {
        return pricePlanFactory.getRowSpanByCode(special_id);
    };

    $scope.allowEdit = function() {
        if(!$scope.local.is_special_plan) { //不是特价
            return $scope.$parent.isEditable();
        }

        return true;
    };

    $scope.addPlan = function() {
        if(!$scope.allowEdit()) return;
        if($scope.data.plans.length == 1 && $scope.data.plans[0].valid_region == 0) {
            $rootScope.$emit('notify', {msg : '生效区间为整个区间的价格计划只能有一份。'});
            return;
        }

        $scope.goEditPlan();
    };
    $scope.editPlan = function(plan_index) {
        if(!$scope.allowEdit()) return;

        $scope.goEditPlan($scope.data.plans[plan_index].price_plan_id);
    };
    $scope.deletePlan = function(plan_index) {
        if(!$scope.allowEdit() && !window.confirm('删除后数据不可恢复。\n点击“确定”来删除。')) return;

        var url = $scope.local.is_special_plan ? $request_urls.productPricePlanSpecial : $request_urls.productPricePlan;
        url += $scope.data.plans[plan_index].price_plan_id;

        $http.delete(url).success(function(data) {
            if(data.code == 200) {
                $scope.data.plans.splice(plan_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.goEditPlan = function(price_plan_id) {
        price_plan_id = price_plan_id || '';
        $location.path('ProductPrice/' + ($scope.local.is_special_plan ? 'edit_special_price_plan' : 'edit_price_plan') + '/' + price_plan_id);
    };


    $scope.init();
};

app.controller('pricePlanListCtrl', [
    '$scope', '$rootScope', '$http', '$location', 'pricePlanFactory', controllers.pricePlanListCtrl
]);
