<div ng-if="['price_plan_list', 'special_price_plan_list'].indexOf(local.tab_options.current_tab.path) > -1 && !local.is_plan_edit" ng-controller="pricePlanListCtrl">
    <div class="section-head">
        <h2 class="section-title">{{local.title}}计划</h2>
    </div>
    <div class="section-body">
        <h2 class="text-center grid-bottom" ng-show="!(data.plans.length == 1 && data.plans[0].valid_region == 0)">
            添加一个{{local.title}}计划
            <button class="btn btn-inverse block-action plan-action" ng-click="addPlan()">
                添加
            </button>
        </h2>

        <div ng-repeat="plan in data.plans">
            <?php include_once( __DIR__ . '/_table.php' ); ?>
        </div>
    </div>
</div>