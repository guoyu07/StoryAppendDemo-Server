<div id="operation-todo-container" class="container page-container" data-ng-controller="OperationTodoCtrl">
    <h2 class="text-center grid-bottom">运营小组</h2>
    <div class="issue-section grid-bottom col-md-10 col-md-offset-4" ng-show="data.home">
        <h4 class="text-center">首页SEO不完整</h4>
        <div class="items grid-bottom">
            <a ng-click="goEditSeo( 'home' )">首页</a>
        </div>
    </div>
    <div class="issue-section grid-bottom col-md-10 col-md-offset-4" ng-show="data.countries.length > 0">
        <h4 class="text-center">国家的SEO不完整</h4>
        <div class="items grid-bottom">
            <a ng-click="goEditSeo( 'country', country.country_code )"
               ng-repeat="country in data.countries" ng-bind="country.cn_name"></a>
        </div>
    </div>
    <div class="issue-section grid-bottom col-md-10 col-md-offset-4" ng-show="data.cities.length > 0">
        <h4 class="text-center">城市的SEO不完整</h4>
        <div class="items grid-bottom">
            <a ng-click="goEditSeo( 'city', city.city_code )"
               ng-repeat="city in data.cities" ng-bind="city.cn_name"></a>
        </div>
    </div>
    <div class="issue-section grid-bottom col-md-10 col-md-offset-4" ng-show="data.product_groups.length > 0">
        <h4 class="text-center">城市的分组SEO不完整</h4>
        <div class="items grid-bottom">
            <a ng-click="goEditSeo( 'product_group', group.city_code, group.group_id )"
               ng-repeat="group in data.product_groups" ng-bind="group.city_name + '_' + group.name"></a>
        </div>
    </div>
    <div class="issue-section grid-bottom col-md-10 col-md-offset-4" ng-show="data.promotions.length > 0">
        <h4 class="text-center">活动的SEO不完整</h4>
        <div class="items grid-bottom">
            <a ng-click="goEditSeo( 'promotion', promotion.promotion_id )"
               ng-repeat="promotion in data.promotions" ng-bind="promotion.promotion_id + ' ' + promotion.name"></a>
        </div>
    </div>
    <!--<div class="issue-section grid-bottom col-md-10 col-md-offset-4" ng-show="data.products.length > 0">
        <h4 class="text-center">上线商品的SEO不完整</h4>
        <div class="items grid-bottom">
            <a ng-click="goEditSeo( 'product', product.product_id )"
               ng-repeat="product in data.products" ng-bind="product.product_id + ' ' + product.name"></a>
        </div>
    </div>-->
</div>