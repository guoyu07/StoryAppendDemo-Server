<div id="country-search-container" ng-controller="CountrySearchCtrl">
    <div class="container">
        <div class="col-md-6 col-md-offset-6">
            <h2 class="text-center page-title">选择国家</h2>
            <select
                chosen
                style="width: 100%;"
                ng-model="local.selected_country"
                ng-change="goEditCountry()"
                ng-options="country.country_code as country.cn_name group by country.group for country in data.countries"
                data-placeholder="点击选择国家"
                no-results-text="'没有找到'"
                >
            </select>

            <div class="issue-section grid-top" data-ng-show="data.incomplete_tab_countries.length > 0">
                <h4 class="text-center">以下国家含有<span class="highlight">未完成</span>的Tab</h4>
                <div class="items">
                    <a ng-click="goEditCountry( country.country_code )"
                       ng-repeat="country in data.incomplete_tab_countries" ng-bind="country.cn_name"></a>
                </div>
            </div>
            <div class="issue-section grid-top" data-ng-show="data.incomplete_countries.length > 0">
                <h4 class="text-center">以下国家<span class="highlight">信息不完整</span></h4>
                <div class="items">
                    <a ng-click="goEditCountry( country.country_code )"
                       ng-repeat="country in data.incomplete_countries" ng-bind="country.cn_name"></a>
                </div>
            </div>
        </div>
    </div>
</div>