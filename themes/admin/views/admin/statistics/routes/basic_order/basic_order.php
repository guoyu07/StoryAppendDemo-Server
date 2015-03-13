<script type="text/ng-template" id="basicOrder.html">
    <div class="states-section">
        <div class="row">
            <ul class="nav nav-tabs duration-selection">
                <li ng-repeat="one_duration in local.durations_filters" ng-class="{active: local.current_duration == one_duration.key}" ng-click="switchDuration(one_duration.key)">
                    <a show-tab data-toggle="tab" ng-bind="one_duration.name"></a>
                </li>
            </ul>
        </div>

        <div class="row duration-selection" ng-show="local.current_duration == 'custom_duration'">
            <div class="col-md-4">
                <quick-datepicker ng-model='local.duration_from_date' on-change='updateDuration()'
                                  disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
            </div>
            <div class="col-md-1 text-right">
                —
            </div>
            <div class="col-md-4">
                <quick-datepicker ng-model='local.duration_to_date' on-change='updateDuration()'
                                  disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label>统计订单量</label>
                <div class="total-static" ng-bind="data.total_order_count"></div>
            </div>
            <div class="col-md-3">
                <label>统计销售金额</label>
                <div class="total-static" ng-bind="data.total_sale_amount"></div>
            </div>
            <div class="col-md-4">
                <label>订单统计日期</label>
                <hi-radio-switch options="local.radio_options" model="local.query_filter"></hi-radio-switch>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-3 grid-top">
                <select chosen
                        style="width: 100%;"
                        ng-model="local.query_filter.country_code"
                        ng-change="filterStatics()"
                        ng-options="country.country_code as country.cn_name group by country.group for country in local.country_list track by country.country_code"
                        data-placeholder="选择国家"
                        no-results-text="'没有找到'"
                    >
                </select>
            </div>
            <div class="form-group col-md-3 grid-top">
                <select chosen
                        style="width: 100%;"
                        ng-model="local.query_filter.city_code"
                        ng-change="filterStatics()"
                        ng-options="city.city_code as city.city_name group by city.group for city in local.city_list track by city.city_code"
                        data-placeholder="选择城市"
                        no-results-text="'没有找到'"
                    >
                </select>
            </div>
            <div class="form-group col-md-3 grid-top">
                <select chosen
                        style="width: 100%;"
                        ng-model="local.query_filter.supplier_id"
                        ng-change="filterStatics()"
                        ng-options="supplier.supplier_id as supplier.name group by supplier.group for supplier in local.supplier_list track by supplier.supplier_id"
                        data-placeholder="选择供应商"
                        no-results-text="'没有找到'"
                    >
                </select>
            </div>
            <div class="form-group col-md-3 grid-top">
                <select chosen
                        style="width: 100%;"
                        ng-model="local.query_filter.product_type_id"
                        ng-change="filterStatics()"
                        ng-options="type.value as type.label for type in local.product_type_list track by type.value"
                        data-placeholder="选择票种"
                        no-results-text="'没有找到'"
                    >
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control grid-top" ng-model="local.query_filter.product_id"
                       placeholder="搜索商品ID" hi-enter="filterStatics()" />
            </div>
            <div class="col-md-1">
                <button class="btn btn-inverse pull-right grid-top" ng-click="filterStatics()">搜索</button>
            </div>
        </div>
        <div class="row" ng-show="local.product_show">
            <div class="col-md-6">
                <h4>
                    国家
                    <span class="i i-refresh refresh-animate" ng-show="local.country_grid_options.in_progress"></span>
                </h4>
                <hi-grid options="local.country_grid_options"></hi-grid>
            </div>
            <div class="col-md-6">
                <h4>
                    城市
                    <span class="i i-refresh refresh-animate" ng-show="local.city_grid_options.in_progress"></span>
                </h4>
                <hi-grid options="local.city_grid_options"></hi-grid>
            </div>
            <div class="col-md-6">
                <h4>
                    供应商
                    <span class="i i-refresh refresh-animate" ng-show="local.supplier_grid_options.in_progress"></span>
                </h4>
                <hi-grid options="local.supplier_grid_options"></hi-grid>
            </div>
        </div>
        <div class="row">
            <div class="col-md-18">
                <h4>
                    商品
                    <span class="i i-refresh refresh-animate" ng-show="local.product_grid_options.in_progress"></span>
                </h4>
                <hi-grid options="local.product_grid_options"></hi-grid>
            </div>
        </div>
    </div>
</script>