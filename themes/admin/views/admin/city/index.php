<div id="city-search-container" data-ng-controller="CitySearchCtrl">
    <div class="container">
        <div class="col-md-6 col-md-offset-6">
            <h2 class="text-center" style="margin-top: 60px; margin-bottom: 20px;">选择城市</h2>
            <select
                chosen
                style="width: 100%;"
                ng-model="local.selected_city"
                ng-options="city.city_code as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in data.cities"
                data-placeholder="点击选择城市"
                no-results-text="'没有找到'"
                ng-change="doEditCity()"
                >
            </select>
        </div>
        <div class="col-md-12 col-md-offset-3">
            <div class="issue-section grid-top" ng-show="data.new_group_cities.length > 0">
                <h4 class="text-center">有自定义分组城市列表</h4>
                <div class="items">
                    <a ng-repeat="city in data.new_group_cities" ng-bind="city.cn_name"
                       ng-click="doEditCity(city.city_code)"></a>
                </div>
            </div>
            <div class="issue-section grid-top" ng-show="data.incomplete_cities.length > 0">
                <h4 class="text-center">以下城市<span class="highlight">信息不完整(缺少横幅大图，方形小图或者SEO信息)</span></h4>
                <div class="items">
                    <a ng-repeat="city in data.incomplete_cities" ng-click="doEditCity(city.city_code)"
                       ng-bind="city.cn_name"></a>
                </div>
            </div>
            <div class="issue-section grid-top" ng-show="data.missing_cover_cities.length > 0">
                <h4 class="text-center">以下缺少<span class="highlight">分组封面图</span></h4>
                <div class="items">
                    <a ng-repeat="city in data.missing_cover_cities"
                       ng-click="doEditGroup(city.city_code, city.group_id)"
                       ng-bind="city.cn_name + ' － ' + city.group_id"></a>
                </div>
            </div>
        </div>
        <div class="row col-md-12 col-md-offset-3">
            <h2 class="text-center" style="margin-top: 60px; margin-bottom: 20px;">热门城市推荐</h2>
            <div class="row">
                <div class="col-md-8 col-md-offset-4">
                    <select
                        chosen
                        style="width: 100%;"
                        ng-model="local.selected_recommend_city"
                        ng-options="[city.city_code, city.cn_name] as (city.cn_name + ' ' + city.pinyin) group by city.group for city in data.all_cities_have_products_online"
                        data-placeholder="点击选择城市"
                        no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn-inverse block-action btn add" ng-click="addRecommendCities()"
                            ng-disabled="!local.selected_recommend_city.length">添加城市
                    </button>
                </div>
            </div>
            <div class="one-block grid-top recommend-city-block">
                <div class="one-tag selected" ng-repeat="tag in local.recommend_cities" ng-bind="tag[1]"
                     ng-click="delTag( $index )"></div>
            </div>
        </div>
    </div>
</div>